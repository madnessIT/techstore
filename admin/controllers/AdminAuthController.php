<?php
/**
 * TechStore - AdminAuthController
 * Archivo: admin/controllers/AdminAuthController.php
 */
class AdminAuthController {
    public function login(): void {
        if (adminLogueado()) adminRedirect('/');
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!adminCsrfCheck($_POST['_csrf'] ?? '')) {
                $error = 'Error de seguridad.';
            } else {
                $email    = strtolower(trim($_POST['email'] ?? ''));
                $password = $_POST['password'] ?? '';
                $model    = new UsuarioModel();
                $usuario  = $model->obtenerPorEmail($email);
                if ($usuario && $usuario['activo'] && $model->verificarPassword($password, $usuario['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id']     = $usuario['id'];
                    $_SESSION['admin_nombre'] = $usuario['nombre'];
                    $_SESSION['admin_email']  = $usuario['email'];
                    $_SESSION['admin_rol']    = $usuario['rol'];
                    $model->actualizarUltimoAcceso($usuario['id']);
                    adminRedirect('/');
                } else {
                    $error = 'Credenciales incorrectas o usuario inactivo.';
                }
            }
        }
        require BASE_PATH . '/admin/views/login.php';
    }
}


/**
 * TechStore - AdminDashboardController
 */
class AdminDashboardController {
    public function index(): void {
        $pedidoModel  = new PedidoModel();
        $productoModel = new ProductoModel();
        $clienteModel  = new ClienteModel();
        $db = Database::getInstance();

        $stats = [
            'pedidos'     => $pedidoModel->estadisticasDashboard(),
            'productos'   => $productoModel->estadisticas(),
            'clientes'    => (int)$db->scalar("SELECT COUNT(*) FROM clientes WHERE activo=1"),
            'ventas_mes'  => $pedidoModel->ventasPorMes(6),
            'ultimos_pedidos' => $pedidoModel->obtenerTodos([], 1)['items'],
            'productos_bajo_stock' => $db->query(
                "SELECT id, nombre, stock, stock_minimo, imagen_principal FROM productos WHERE activo=1 AND stock <= stock_minimo ORDER BY stock ASC LIMIT 5"
            ),
            'top_productos' => $db->query(
                "SELECT p.nombre, p.imagen_principal, SUM(dp.cantidad) AS total_vendido, SUM(dp.subtotal) AS total_ingresos
                 FROM detalle_pedidos dp INNER JOIN productos p ON dp.producto_id=p.id
                 GROUP BY p.id ORDER BY total_vendido DESC LIMIT 5"
            ),
        ];

        require BASE_PATH . '/admin/views/dashboard.php';
    }
}


/**
 * TechStore - AdminProductoController
 */
class AdminProductoController {
    private ProductoModel  $model;
    private CategoriaModel $catModel;

    public function __construct() {
        $this->model    = new ProductoModel();
        $this->catModel = new CategoriaModel();
    }

    public function index(): void {
        $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
        $busqueda  = trim($_GET['q'] ?? '');
        $filtros   = array_filter(['busqueda' => $busqueda]);
        $resultado = $this->model->obtenerTodos($filtros, $pagina, ADMIN_ITEMS_POR_PAGINA);
        $categorias = $this->catModel->obtenerTodas(false);
        require BASE_PATH . '/admin/views/products/index.php';
    }

    public function crear(): void {
        $categorias = $this->catModel->obtenerTodas(false);
        $producto   = null;
        $errores    = [];
        require BASE_PATH . '/admin/views/products/form.php';
    }

    public function editar(int $id): void {
        $producto = $this->model->obtenerPorId($id);
        if (!$producto) { adminSetFlash('danger','Producto no encontrado'); adminRedirect('/productos'); }
        $categorias = $this->catModel->obtenerTodas(false);
        $errores    = [];
        require BASE_PATH . '/admin/views/products/form.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminRedirect('/productos');
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) { adminSetFlash('danger','CSRF error'); adminRedirect('/productos'); }

        $id      = (int)($_POST['id'] ?? 0);
        $datos   = $_POST;
        $errores = $this->validar($datos, $id);

        if (!empty($errores)) {
            $categorias = $this->catModel->obtenerTodas(false);
            $producto   = $id ? $this->model->obtenerPorId($id) : null;
            require BASE_PATH . '/admin/views/products/form.php';
            return;
        }

        // Manejo de imagen
        if (!empty($_FILES['imagen']['name'])) {
            $imagen = $this->subirImagen($_FILES['imagen']);
            if ($imagen) $datos['imagen_principal'] = $imagen;
        }

        // Especificaciones como JSON
        if (!empty($datos['spec_key']) && is_array($datos['spec_key'])) {
            $specs = [];
            foreach ($datos['spec_key'] as $i => $key) {
                if (!empty(trim($key))) $specs[trim($key)] = trim($datos['spec_val'][$i] ?? '');
            }
            $datos['especificaciones'] = $specs;
        }

        if ($id) {
            $ok = $this->model->actualizar($id, $datos);
            $msg = $ok ? 'Producto actualizado correctamente' : 'Error al actualizar';
            adminSetFlash($ok ? 'success' : 'danger', $msg);
        } else {
            $newId = $this->model->crear($datos);
            adminSetFlash($newId ? 'success' : 'danger', $newId ? 'Producto creado correctamente' : 'Error al crear');
        }
        adminRedirect('/productos');
    }

    public function eliminar(int $id): void {
        $ok = $this->model->eliminar($id);
        adminSetFlash($ok ? 'success' : 'danger', $ok ? 'Producto eliminado' : 'Error al eliminar');
        adminRedirect('/productos');
    }

    private function validar(array $datos, int $id = 0): array {
        $errores = [];
        if (empty($datos['nombre']))           $errores[] = 'El nombre es requerido';
        if (empty($datos['categoria_id']))     $errores[] = 'La categoría es requerida';
        if (empty($datos['sku']))              $errores[] = 'El SKU es requerido';
        if (!is_numeric($datos['precio'] ?? '')) $errores[] = 'El precio debe ser numérico';
        if ($this->model->skuExiste(strtoupper($datos['sku'] ?? ''), $id))
            $errores[] = 'El SKU ya existe, elige otro';
        return $errores;
    }

    private function subirImagen(array $file): ?string {
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($ext, $allowed) || $file['size'] > 5 * 1024 * 1024) return null;

        $nombre   = 'prod_' . uniqid() . '.' . $ext;
        $destino  = PRODUCTS_IMG_PATH . $nombre;
        return move_uploaded_file($file['tmp_name'], $destino) ? $nombre : null;
    }
}


/**
 * TechStore - AdminCategoriaController
 */
class AdminCategoriaController {
    private CategoriaModel $model;

    public function __construct() {
        $this->model = new CategoriaModel();
    }

    public function index(): void {
        $categorias = $this->model->obtenerTodas(false);
        require BASE_PATH . '/admin/views/categories/index.php';
    }

    public function crear(): void {
        $categoria = null; $errores = [];
        require BASE_PATH . '/admin/views/categories/form.php';
    }

    public function editar(int $id): void {
        $categoria = $this->model->obtenerPorId($id);
        if (!$categoria) { adminSetFlash('danger','Categoría no encontrada'); adminRedirect('/categorias'); }
        $errores = [];
        require BASE_PATH . '/admin/views/categories/form.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminRedirect('/categorias');
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) { adminSetFlash('danger','CSRF error'); adminRedirect('/categorias'); }

        $id    = (int)($_POST['id'] ?? 0);
        $datos = $_POST;
        $errores = empty($datos['nombre']) ? ['El nombre es requerido'] : [];

        if (!empty($errores)) {
            $categoria = $id ? $this->model->obtenerPorId($id) : null;
            require BASE_PATH . '/admin/views/categories/form.php';
            return;
        }

        if ($id) {
            $ok = $this->model->actualizar($id, $datos);
        } else {
            $ok = (bool)$this->model->crear($datos);
        }
        adminSetFlash($ok ? 'success' : 'danger', $ok ? 'Categoría guardada' : 'Error al guardar');
        adminRedirect('/categorias');
    }

    public function eliminar(int $id): void {
        $ok  = $this->model->eliminar($id);
        $msg = $ok ? 'Categoría eliminada' : 'No se puede eliminar: tiene productos asociados';
        adminSetFlash($ok ? 'success' : 'warning', $msg);
        adminRedirect('/categorias');
    }
}


/**
 * TechStore - AdminPedidoController
 */
class AdminPedidoController {
    private PedidoModel $model;

    public function __construct() {
        $this->model = new PedidoModel();
    }

    public function index(): void {
        $filtros = ['estado' => $_GET['estado'] ?? ''];
        $pagina  = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado = $this->model->obtenerTodos($filtros, $pagina);
        require BASE_PATH . '/admin/views/orders/index.php';
    }

    public function ver(int $id): void {
        $pedido = $this->model->obtenerPorId($id);
        if (!$pedido) { adminSetFlash('danger','Pedido no encontrado'); adminRedirect('/pedidos'); }
        require BASE_PATH . '/admin/views/orders/ver.php';
    }

    public function cambiarEstado(int $id): void {
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) { adminSetFlash('danger','Error CSRF'); adminRedirect('/pedidos'); }
        $estado   = $_POST['estado'] ?? '';
        $estados  = ['pendiente','confirmado','procesando','enviado','entregado','cancelado','reembolsado'];
        if (!in_array($estado, $estados)) { adminSetFlash('danger','Estado inválido'); adminRedirect('/pedidos'); }
        $ok = $this->model->actualizarEstado($id, $estado);
        adminSetFlash($ok ? 'success' : 'danger', $ok ? 'Estado actualizado' : 'Error al actualizar');
        adminRedirect('/pedidos/ver/' . $id);
    }
}


/**
 * TechStore - AdminClienteController
 */
class AdminClienteController {
    private ClienteModel $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    public function index(): void {
        $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado = $this->model->obtenerTodos($pagina);
        require BASE_PATH . '/admin/views/clients/index.php';
    }

    public function ver(int $id): void {
        $cliente = $this->model->obtenerPorId($id);
        if (!$cliente) { adminSetFlash('danger','Cliente no encontrado'); adminRedirect('/clientes'); }
        $pedidoModel = new PedidoModel();
        $pedidos     = $pedidoModel->obtenerDeCliente($id);
        require BASE_PATH . '/admin/views/clients/ver.php';
    }

    public function toggle(int $id): void {
        $this->model->toggleActivo($id);
        adminSetFlash('success', 'Estado del cliente actualizado');
        adminRedirect('/clientes');
    }
}


/**
 * TechStore - AdminUsuarioController
 */
class AdminUsuarioController {
    private UsuarioModel $model;

    public function __construct() {
        $this->model = new UsuarioModel();
        if (!adminCan('admin')) { adminSetFlash('danger','Sin permisos'); adminRedirect('/'); }
    }

    public function index(): void {
        $usuarios = $this->model->obtenerTodos();
        require BASE_PATH . '/admin/views/users/index.php';
    }

    public function crear(): void {
        $usuario = null; $errores = [];
        require BASE_PATH . '/admin/views/users/form.php';
    }

    public function editar(int $id): void {
        $usuario = $this->model->obtenerPorId($id);
        if (!$usuario) { adminSetFlash('danger','Usuario no encontrado'); adminRedirect('/usuarios'); }
        $errores = [];
        require BASE_PATH . '/admin/views/users/form.php';
    }

    public function guardar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminRedirect('/usuarios');
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) adminRedirect('/usuarios');

        $id      = (int)($_POST['id'] ?? 0);
        $datos   = $_POST;
        $errores = [];

        if (empty($datos['nombre']))   $errores[] = 'Nombre requerido';
        if (empty($datos['apellido'])) $errores[] = 'Apellido requerido';
        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
            $errores[] = 'Email inválido';
        if (!$id && (strlen($datos['password'] ?? '') < 8))
            $errores[] = 'Contraseña mínimo 8 caracteres';
        if ($this->model->emailExiste($datos['email'], $id))
            $errores[] = 'Email ya en uso';

        if (!empty($errores)) {
            $usuario = $id ? $this->model->obtenerPorId($id) : null;
            require BASE_PATH . '/admin/views/users/form.php';
            return;
        }

        if ($id) {
            $ok = $this->model->actualizar($id, $datos);
            if (!empty($datos['password'])) $this->model->cambiarPassword($id, $datos['password']);
        } else {
            $ok = (bool)$this->model->crear($datos);
        }
        adminSetFlash($ok ? 'success' : 'danger', $ok ? 'Usuario guardado' : 'Error al guardar');
        adminRedirect('/usuarios');
    }

    public function eliminar(int $id): void {
        if ($id === (int)$_SESSION['admin_id']) {
            adminSetFlash('danger','No puedes eliminarte a ti mismo');
            adminRedirect('/usuarios');
        }
        $db  = Database::getInstance();
        $ok  = $db->execute("DELETE FROM usuarios WHERE id=:id", ['id' => $id]) > 0;
        adminSetFlash($ok ? 'success' : 'danger', $ok ? 'Usuario eliminado' : 'Error al eliminar');
        adminRedirect('/usuarios');
    }
}


/**
 * TechStore - AdminReporteController
 */
class AdminReporteController {
    public function index(): void {
        $db  = Database::getInstance();
        $pm  = new PedidoModel();

        $reportes = [
            'ventas_por_mes'       => $pm->ventasPorMes(12),
            'ventas_por_categoria' => $db->query(
                "SELECT c.nombre, COUNT(dp.id) AS total_items, SUM(dp.subtotal) AS total_ventas
                 FROM detalle_pedidos dp
                 INNER JOIN productos p ON dp.producto_id = p.id
                 INNER JOIN categorias c ON p.categoria_id = c.id
                 INNER JOIN pedidos ped ON dp.pedido_id = ped.id
                 WHERE ped.estado NOT IN ('cancelado','reembolsado')
                 GROUP BY c.id ORDER BY total_ventas DESC"
            ),
            'top_productos' => $db->query(
                "SELECT p.nombre, p.sku, SUM(dp.cantidad) AS total_vendido, SUM(dp.subtotal) AS total_ingresos
                 FROM detalle_pedidos dp
                 INNER JOIN productos p ON dp.producto_id = p.id
                 INNER JOIN pedidos ped ON dp.pedido_id = ped.id
                 WHERE ped.estado NOT IN ('cancelado','reembolsado')
                 GROUP BY p.id ORDER BY total_vendido DESC LIMIT 10"
            ),
            'clientes_frecuentes' => $db->query(
                "SELECT CONCAT(c.nombre,' ',c.apellido) AS cliente, c.email,
                     COUNT(p.id) AS total_pedidos, SUM(p.total) AS total_gastado
                 FROM pedidos p INNER JOIN clientes c ON p.cliente_id = c.id
                 WHERE p.estado NOT IN ('cancelado','reembolsado')
                 GROUP BY c.id ORDER BY total_gastado DESC LIMIT 10"
            ),
            'resumen_estados' => $db->query(
                "SELECT estado, COUNT(*) AS total, SUM(total) AS monto
                 FROM pedidos GROUP BY estado ORDER BY total DESC"
            ),
        ];

        require BASE_PATH . '/admin/views/reports/index.php';
    }
}
