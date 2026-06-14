<?php
/**
 * TechStore - CatalogoController
 */
class CatalogoController {
    private ProductoModel  $productoModel;
    private CategoriaModel $categoriaModel;
    
    public function __construct() {
        $this->productoModel  = new ProductoModel();
        $this->categoriaModel = new CategoriaModel();
    }
    
    public function home(): void {
        $destacados  = $this->productoModel->obtenerDestacados(8);
        $ofertas     = $this->productoModel->obtenerOfertas(6);
        $categorias  = $this->categoriaModel->obtenerTodas();
        require BASE_PATH . '/views/home.php';
    }
    
    public function index(): void {
        $filtros = [
            'categoria'  => isset($_GET['categoria']) ? (int)$_GET['categoria'] : null,
            'busqueda'   => trim($_GET['q'] ?? ''),
            'precio_min' => $_GET['precio_min'] ?? null,
            'precio_max' => $_GET['precio_max'] ?? null,
            'marca'      => $_GET['marca'] ?? null,
            'orden'      => $_GET['orden'] ?? '',
        ];
        
        $pagina     = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado  = $this->productoModel->obtenerTodos($filtros, $pagina);
        $categorias = $this->categoriaModel->obtenerTodas();
        
        require BASE_PATH . '/views/catalog/catalogo.php';
    }
    
    public function detalle(string $slug): void {
        $producto = $this->productoModel->obtenerPorSlug($slug);
        if (!$producto) {
            http_response_code(404);
            require BASE_PATH . '/views/404.php';
            return;
        }
        $relacionados = $this->productoModel->obtenerRelacionados(
            $producto['categoria_id'], $producto['id'], 4
        );
        require BASE_PATH . '/views/catalog/detalle.php';
    }
    
    public function porCategoria(string $slug): void {
        $categoria = $this->categoriaModel->obtenerPorSlug($slug);
        if (!$categoria) redirect('/catalogo');
        
        $filtros   = ['categoria' => $categoria['id'], 'orden' => $_GET['orden'] ?? ''];
        $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado = $this->productoModel->obtenerTodos($filtros, $pagina);
        $categorias = $this->categoriaModel->obtenerTodas();
        
        require BASE_PATH . '/views/catalog/catalogo.php';
    }
    
    public function buscar(): void {
        $termino = trim($_GET['q'] ?? '');
        if (empty($termino)) redirect('/catalogo');
        
        $filtros   = ['busqueda' => $termino, 'orden' => $_GET['orden'] ?? ''];
        $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado = $this->productoModel->obtenerTodos($filtros, $pagina);
        $categorias = $this->categoriaModel->obtenerTodas();
        
        require BASE_PATH . '/views/catalog/catalogo.php';
    }
}


/**
 * TechStore - CarritoController
 */
class CarritoController {
    private CarritoModel  $carritoModel;
    private ProductoModel $productoModel;
    
    public function __construct() {
        $this->carritoModel  = new CarritoModel();
        $this->productoModel = new ProductoModel();
    }
    
    private function getSessionId(): string {
        if (empty($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['cart_session_id'];
    }
    
    public function index(): void {
        $clienteId = clienteLogueado() ? $_SESSION['cliente_id'] : null;
        $sessionId = $this->getSessionId();
        $items     = $this->carritoModel->obtener($clienteId, $sessionId);

        // Calcular totales leyendo config desde BD
        $subtotal   = array_sum(array_map(fn($i) => $i['precio_unitario'] * $i['cantidad'], $items));
        $cfgEnvio   = getConfigEnvio();
        $infoEnvio  = calcularCostoEnvio($subtotal);
        $envio      = $infoEnvio['costo'];
        $iva        = round($subtotal * ($cfgEnvio['iva_porcentaje'] / 100), 2);
        $total      = $subtotal + $envio;
        $envioGratisDesdeBD = $cfgEnvio['envio_gratis_desde'];

        require BASE_PATH . '/views/cart/carrito.php';
    }
    
    public function agregar(): void {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
            return;
        }
        
        $productoId = (int)($_POST['producto_id'] ?? 0);
        $cantidad   = max(1, (int)($_POST['cantidad'] ?? 1));
        
        $producto = $this->productoModel->obtenerPorId($productoId);
        if (!$producto || !$producto['activo']) {
            echo json_encode(['ok' => false, 'msg' => 'Producto no disponible']);
            return;
        }
        if ($producto['stock'] < $cantidad) {
            echo json_encode(['ok' => false, 'msg' => 'Stock insuficiente. Disponible: ' . $producto['stock']]);
            return;
        }
        
        $clienteId = clienteLogueado() ? $_SESSION['cliente_id'] : null;
        $sessionId = $this->getSessionId();
        
        $ok = $this->carritoModel->agregar([
            'cliente_id'  => $clienteId,
            'session_id'  => $sessionId,
            'producto_id' => $productoId,
            'cantidad'    => $cantidad,
            'precio'      => (float)($producto['precio_oferta'] ?? $producto['precio']),
        ]);
        
        $total_items = $this->carritoModel->contarItems($clienteId, $sessionId);
        echo json_encode(['ok' => $ok, 'msg' => $ok ? '¡Producto agregado!' : 'Error al agregar', 'total' => $total_items]);
    }
    
    public function actualizar(): void {
        header('Content-Type: application/json');
        
        $itemId   = (int)($_POST['item_id'] ?? 0);
        $cantidad = max(1, (int)($_POST['cantidad'] ?? 1));
        
        $ok = $this->carritoModel->actualizarCantidad($itemId, $cantidad);
        echo json_encode(['ok' => $ok]);
    }
    
    public function eliminar(): void {
        $itemId = (int)($_POST['item_id'] ?? $_GET['id'] ?? 0);
        $this->carritoModel->eliminarItem($itemId);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
        } else {
            setFlash('info', 'Producto eliminado del carrito.');
            redirect('/carrito');
        }
    }
    
    public function contar(): void {
        header('Content-Type: application/json');
        $clienteId = clienteLogueado() ? $_SESSION['cliente_id'] : null;
        $sessionId = $this->getSessionId();
        $total     = $this->carritoModel->contarItems($clienteId, $sessionId);
        echo json_encode(['total' => $total]);
    }
}


/**
 * TechStore - CheckoutController
 */
class CheckoutController {
    private CarritoModel  $carritoModel;
    private PedidoModel   $pedidoModel;
    
    public function __construct() {
        $this->carritoModel = new CarritoModel();
        $this->pedidoModel  = new PedidoModel();
    }
    
    private function getSessionId(): string {
        return $_SESSION['cart_session_id'] ?? '';
    }
    
    public function index(): void {
        if (!clienteLogueado()) {
            $_SESSION['redirect_after_login'] = '/checkout';
            redirect('/login');
        }

        $clienteId = $_SESSION['cliente_id'];
        $items     = $this->carritoModel->obtener($clienteId, null);

        if (empty($items)) {
            setFlash('warning', 'Tu carrito está vacío.');
            redirect('/carrito');
        }

        $clienteModel = new ClienteModel();
        $cliente      = $clienteModel->obtenerPorId($clienteId);

        // Calcular envío desde BD usando ciudad del cliente si la tiene
        $cfgEnvio  = getConfigEnvio();
        $ciudadCliente = $cliente['ciudad'] ?? '';
        $subtotal  = array_sum(array_map(fn($i) => $i['precio_unitario'] * $i['cantidad'], $items));
        $infoEnvio = calcularCostoEnvio($subtotal, $ciudadCliente);
        $envio     = $infoEnvio['costo'];
        $iva       = round($subtotal * ($cfgEnvio['iva_porcentaje'] / 100), 2);
        $total     = $subtotal + $envio;

        require BASE_PATH . '/views/checkout/checkout.php';
    }
    
    public function confirmar(): void {
        if (!clienteLogueado()) redirect('/login');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/checkout');
        if (!verificarCsrf($_POST['_csrf'] ?? '')) {
            setFlash('danger', 'Error de seguridad.');
            redirect('/checkout');
        }

        $clienteId = $_SESSION['cliente_id'];
        $items     = $this->carritoModel->obtener($clienteId, null);
        if (empty($items)) redirect('/carrito');

        // Calcular envío desde BD usando la ciudad que ingresó el cliente en el form
        $cfgEnvio  = getConfigEnvio();
        $ciudad    = trim($_POST['ciudad'] ?? '');
        $subtotal  = array_sum(array_map(fn($i) => $i['precio_unitario'] * $i['cantidad'], $items));
        $infoEnvio = calcularCostoEnvio($subtotal, $ciudad);
        $envio     = $infoEnvio['costo'];
        $iva       = round($subtotal * ($cfgEnvio['iva_porcentaje'] / 100), 2);
        $total     = $subtotal + $envio;
        
        // Preparar items para el pedido
        $itemsPedido = array_map(fn($i) => [
            'producto_id'   => $i['producto_id'],
            'nombre'        => $i['nombre'],
            'sku'           => $i['sku'] ?? null,
            'cantidad'      => $i['cantidad'],
            'precio_unitario' => $i['precio_unitario'],
            'subtotal'      => $i['precio_unitario'] * $i['cantidad'],
        ], $items);
        
        $datos = [
            'cliente_id'       => $clienteId,
            'subtotal'         => $subtotal,
            'iva'              => $iva,
            'costo_envio'      => $envio,
            'total'            => $total,
            'nombre_envio'     => trim($_POST['nombre'] . ' ' . $_POST['apellido']),
            'email_envio'      => $_SESSION['cliente_email'],
            'telefono_envio'   => trim($_POST['telefono'] ?? ''),
            'direccion_envio'  => trim($_POST['direccion'] ?? ''),
            'ciudad_envio'     => trim($_POST['ciudad'] ?? ''),
            'departamento_envio' => trim($_POST['departamento'] ?? ''),
            'notas'            => trim($_POST['notas'] ?? ''),
            'metodo_pago'      => $_POST['metodo_pago'] ?? 'efectivo',
        ];
        
        $pedidoId = $this->pedidoModel->crear($datos, $itemsPedido);
        
        if ($pedidoId) {
            $this->carritoModel->vaciar($clienteId, null);
            $pedido = $this->pedidoModel->obtenerPorId($pedidoId);
            redirect('/checkout/gracias/' . $pedido['numero_orden']);
        } else {
            setFlash('danger', 'Error al procesar el pedido. Intente de nuevo.');
            redirect('/checkout');
        }
    }
    
    public function gracias(?string $numeroOrden): void {
        if (!clienteLogueado() || !$numeroOrden) redirect('/');
        
        $pedido = $this->pedidoModel->obtenerPorNumero($numeroOrden);
        if (!$pedido || $pedido['cliente_id'] != $_SESSION['cliente_id']) redirect('/');
        
        require BASE_PATH . '/views/checkout/gracias.php';
    }
}
