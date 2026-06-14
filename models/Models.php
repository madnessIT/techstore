<?php
/**
 * TechStore - Modelo de Categorías
 * Archivo: models/CategoriaModel.php
 */

require_once BASE_PATH . '/config/Database.php';

class CategoriaModel {
    private Database $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function obtenerTodas(bool $soloActivas = true): array {
        $sql = "SELECT c.*, 
                    (SELECT COUNT(*) FROM productos p WHERE p.categoria_id = c.id AND p.activo = 1) AS total_productos
                FROM categorias c
                WHERE 1=1 " . ($soloActivas ? " AND c.activa = 1" : "") . "
                ORDER BY c.orden ASC, c.nombre ASC";
        return $this->db->query($sql);
    }
    
    public function obtenerPorId(int $id): ?array {
        return $this->db->queryOne("SELECT * FROM categorias WHERE id = :id", ['id' => $id]);
    }
    
    public function obtenerPorSlug(string $slug): ?array {
        return $this->db->queryOne("SELECT * FROM categorias WHERE slug = :slug AND activa = 1", ['slug' => $slug]);
    }
    
    public function crear(array $datos): int|false {
        $sql = "INSERT INTO categorias (nombre, slug, descripcion, icono, activa, orden) 
                VALUES (:nombre, :slug, :descripcion, :icono, :activa, :orden)";
        return $this->db->insert($sql, [
            'nombre'      => $datos['nombre'],
            'slug'        => $this->generarSlug($datos['nombre']),
            'descripcion' => $datos['descripcion'] ?? null,
            'icono'       => $datos['icono'] ?? null,
            'activa'      => isset($datos['activa']) ? 1 : 0,
            'orden'       => (int)($datos['orden'] ?? 0),
        ]);
    }
    
    public function actualizar(int $id, array $datos): bool {
        $sql = "UPDATE categorias SET nombre=:nombre, slug=:slug, descripcion=:descripcion, 
                icono=:icono, activa=:activa, orden=:orden WHERE id=:id";
        $rows = $this->db->execute($sql, [
            'id'          => $id,
            'nombre'      => $datos['nombre'],
            'slug'        => $this->generarSlug($datos['nombre'], $id),
            'descripcion' => $datos['descripcion'] ?? null,
            'icono'       => $datos['icono'] ?? null,
            'activa'      => isset($datos['activa']) ? 1 : 0,
            'orden'       => (int)($datos['orden'] ?? 0),
        ]);
        return $rows > 0;
    }
    
    public function eliminar(int $id): bool {
        $total = (int) $this->db->scalar("SELECT COUNT(*) FROM productos WHERE categoria_id = :id", ['id' => $id]);
        if ($total > 0) return false;
        $rows = $this->db->execute("DELETE FROM categorias WHERE id = :id", ['id' => $id]);
        return $rows > 0;
    }
    
    private function generarSlug(string $texto, int $excludeId = 0): string {
        $slug = strtolower(trim($texto));
        $mapa = ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u'];
        $slug = strtr($slug, $mapa);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        $original = $slug; $i = 1;
        while ($this->slugExiste($slug, $excludeId)) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
    
    private function slugExiste(string $slug, int $excludeId = 0): bool {
        return (int) $this->db->scalar(
            "SELECT COUNT(*) FROM categorias WHERE slug=:slug AND id!=:id",
            ['slug' => $slug, 'id' => $excludeId]
        ) > 0;
    }
}


/**
 * TechStore - Modelo de Clientes
 * Archivo: models/ClienteModel.php
 */
class ClienteModel {
    private Database $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function obtenerTodos(int $pagina = 1, int $porPagina = ADMIN_ITEMS_POR_PAGINA): array {
        $offset = ($pagina - 1) * $porPagina;
        $total  = (int) $this->db->scalar("SELECT COUNT(*) FROM clientes");
        $items  = $this->db->query(
            "SELECT id, nombre, apellido, email, telefono, ciudad, activo, verificado, created_at 
             FROM clientes ORDER BY created_at DESC LIMIT :l OFFSET :o",
            ['l' => $porPagina, 'o' => $offset]
        );
        return ['items' => $items, 'total' => $total, 'pagina' => $pagina,
                'por_pagina' => $porPagina, 'total_paginas' => ceil($total / $porPagina)];
    }
    
    public function obtenerPorId(int $id): ?array {
        return $this->db->queryOne("SELECT * FROM clientes WHERE id = :id", ['id' => $id]);
    }
    
    public function obtenerPorEmail(string $email): ?array {
        return $this->db->queryOne("SELECT * FROM clientes WHERE email = :email", ['email' => $email]);
    }
    
    public function emailExiste(string $email, int $excludeId = 0): bool {
        return (int) $this->db->scalar(
            "SELECT COUNT(*) FROM clientes WHERE email=:email AND id!=:id",
            ['email' => $email, 'id' => $excludeId]
        ) > 0;
    }
    
    public function registrar(array $datos): int|false {
        $sql = "INSERT INTO clientes (nombre, apellido, email, password, telefono, ciudad, departamento, verificado)
                VALUES (:nombre, :apellido, :email, :password, :telefono, :ciudad, :departamento, 1)";
        return $this->db->insert($sql, [
            'nombre'       => $datos['nombre'],
            'apellido'     => $datos['apellido'],
            'email'        => strtolower(trim($datos['email'])),
            'password'     => password_hash($datos['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'telefono'     => $datos['telefono'] ?? null,
            'ciudad'       => $datos['ciudad'] ?? null,
            'departamento' => $datos['departamento'] ?? null,
        ]);
    }
    
    public function actualizar(int $id, array $datos): bool {
        $sql = "UPDATE clientes SET nombre=:nombre, apellido=:apellido, telefono=:telefono,
                ciudad=:ciudad, departamento=:departamento, direccion=:direccion WHERE id=:id";
        $rows = $this->db->execute($sql, [
            'id'           => $id,
            'nombre'       => $datos['nombre'],
            'apellido'     => $datos['apellido'],
            'telefono'     => $datos['telefono'] ?? null,
            'ciudad'       => $datos['ciudad'] ?? null,
            'departamento' => $datos['departamento'] ?? null,
            'direccion'    => $datos['direccion'] ?? null,
        ]);
        return $rows > 0;
    }
    
    public function cambiarPassword(int $id, string $nuevaPassword): bool {
        $rows = $this->db->execute(
            "UPDATE clientes SET password=:pass WHERE id=:id",
            ['id' => $id, 'pass' => password_hash($nuevaPassword, PASSWORD_BCRYPT, ['cost' => 12])]
        );
        return $rows > 0;
    }
    
    public function verificarPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public function actualizarUltimoAcceso(int $id): void {
        $this->db->execute(
            "UPDATE clientes SET ultimo_acceso=NOW() WHERE id=:id",
            ['id' => $id]
        );
    }
    
    public function toggleActivo(int $id): bool {
        $rows = $this->db->execute(
            "UPDATE clientes SET activo = NOT activo WHERE id=:id",
            ['id' => $id]
        );
        return $rows > 0;
    }
}


/**
 * TechStore - Modelo de Carrito
 */
class CarritoModel {
    private Database $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function obtener(int|null $clienteId, string|null $sessionId): array {
        $sql = "SELECT c.*, p.nombre, p.imagen_principal, p.stock,
                    p.slug, p.marca,
                    COALESCE(p.precio_oferta, p.precio) AS precio_actual
                FROM carrito c
                INNER JOIN productos p ON c.producto_id = p.id
                WHERE " . ($clienteId ? "c.cliente_id = :cliente_id" : "c.session_id = :session_id");
        
        $params = $clienteId ? ['cliente_id' => $clienteId] : ['session_id' => $sessionId];
        return $this->db->query($sql, $params);
    }
    
    public function agregar(array $datos): bool {
        // Verificar si ya existe el item
        $condicion = $datos['cliente_id'] ? 
            "cliente_id = :cid AND producto_id = :pid" : 
            "session_id = :sid AND producto_id = :pid";
        
        $params = ['pid' => $datos['producto_id']];
        if ($datos['cliente_id']) $params['cid'] = $datos['cliente_id'];
        else $params['sid'] = $datos['session_id'];
        
        $existente = $this->db->queryOne("SELECT id, cantidad FROM carrito WHERE $condicion", $params);
        
        if ($existente) {
            $rows = $this->db->execute(
                "UPDATE carrito SET cantidad = cantidad + :cant WHERE id = :id",
                ['cant' => $datos['cantidad'], 'id' => $existente['id']]
            );
            return $rows > 0;
        }
        
        $sql = "INSERT INTO carrito (cliente_id, session_id, producto_id, cantidad, precio_unitario)
                VALUES (:cliente_id, :session_id, :producto_id, :cantidad, :precio)";
        $id = $this->db->insert($sql, [
            'cliente_id'   => $datos['cliente_id'],
            'session_id'   => $datos['session_id'],
            'producto_id'  => $datos['producto_id'],
            'cantidad'     => $datos['cantidad'],
            'precio'       => $datos['precio'],
        ]);
        return $id !== false;
    }
    
    public function actualizarCantidad(int $itemId, int $cantidad): bool {
        $rows = $this->db->execute(
            "UPDATE carrito SET cantidad=:q WHERE id=:id",
            ['q' => $cantidad, 'id' => $itemId]
        );
        return $rows > 0;
    }
    
    public function eliminarItem(int $itemId): bool {
        $rows = $this->db->execute("DELETE FROM carrito WHERE id=:id", ['id' => $itemId]);
        return $rows > 0;
    }
    
    public function vaciar(int|null $clienteId, string|null $sessionId): void {
        if ($clienteId) {
            $this->db->execute("DELETE FROM carrito WHERE cliente_id=:id", ['id' => $clienteId]);
        } else {
            $this->db->execute("DELETE FROM carrito WHERE session_id=:id", ['id' => $sessionId]);
        }
    }
    
    public function contarItems(int|null $clienteId, string|null $sessionId): int {
        $sql = "SELECT COALESCE(SUM(cantidad), 0) FROM carrito WHERE " .
               ($clienteId ? "cliente_id=:id" : "session_id=:id");
        return (int) $this->db->scalar($sql, ['id' => $clienteId ?? $sessionId]);
    }
    
    public function migrarSesionACliente(string $sessionId, int $clienteId): void {
        // Mover items de sesión anónima al cliente registrado
        $items = $this->db->query(
            "SELECT * FROM carrito WHERE session_id=:sid",
            ['sid' => $sessionId]
        );
        foreach ($items as $item) {
            $this->agregar([
                'cliente_id'  => $clienteId,
                'session_id'  => null,
                'producto_id' => $item['producto_id'],
                'cantidad'    => $item['cantidad'],
                'precio'      => $item['precio_unitario'],
            ]);
        }
        $this->db->execute("DELETE FROM carrito WHERE session_id=:sid", ['sid' => $sessionId]);
    }
}


/**
 * TechStore - Modelo de Pedidos
 */
class PedidoModel {
    private Database $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function obtenerTodos(array $filtros = [], int $pagina = 1): array {
        $donde = ['1=1'];
        $params = [];
        
        if (!empty($filtros['estado'])) {
            $donde[] = 'p.estado = :estado';
            $params['estado'] = $filtros['estado'];
        }
        if (!empty($filtros['cliente_id'])) {
            $donde[] = 'p.cliente_id = :cliente_id';
            $params['cliente_id'] = $filtros['cliente_id'];
        }
        
        $where  = implode(' AND ', $donde);
        $offset = ($pagina - 1) * ADMIN_ITEMS_POR_PAGINA;
        $total  = (int) $this->db->scalar("SELECT COUNT(*) FROM pedidos p WHERE $where", $params);
        
        $sql = "SELECT p.*, CONCAT(c.nombre,' ',c.apellido) AS cliente_nombre
                FROM pedidos p 
                INNER JOIN clientes c ON p.cliente_id = c.id
                WHERE $where
                ORDER BY p.created_at DESC
                LIMIT :l OFFSET :o";
        $params['l'] = ADMIN_ITEMS_POR_PAGINA;
        $params['o'] = $offset;
        
        return ['items' => $this->db->query($sql, $params), 'total' => $total,
                'pagina' => $pagina, 'total_paginas' => ceil($total / ADMIN_ITEMS_POR_PAGINA)];
    }
    
    public function obtenerPorId(int $id): ?array {
        $pedido = $this->db->queryOne(
            "SELECT p.*, CONCAT(c.nombre,' ',c.apellido) AS cliente_nombre, c.email AS cliente_email
             FROM pedidos p INNER JOIN clientes c ON p.cliente_id = c.id WHERE p.id=:id",
            ['id' => $id]
        );
        if ($pedido) {
            $pedido['detalle'] = $this->obtenerDetalle($id);
        }
        return $pedido;
    }
    
    public function obtenerPorNumero(string $numero): ?array {
        $pedido = $this->db->queryOne(
            "SELECT * FROM pedidos WHERE numero_orden=:n",
            ['n' => $numero]
        );
        if ($pedido) {
            $pedido['detalle'] = $this->obtenerDetalle($pedido['id']);
        }
        return $pedido;
    }
    
    public function obtenerDeCliente(int $clienteId): array {
        return $this->db->query(
            "SELECT * FROM pedidos WHERE cliente_id=:id ORDER BY created_at DESC",
            ['id' => $clienteId]
        );
    }
    
    public function obtenerDetalle(int $pedidoId): array {
        return $this->db->query(
            "SELECT dp.*, p.imagen_principal, p.slug FROM detalle_pedidos dp 
             LEFT JOIN productos p ON dp.producto_id = p.id 
             WHERE dp.pedido_id=:id",
            ['id' => $pedidoId]
        );
    }
    
    public function crear(array $datos, array $items): int|false {
        $db = $this->db;
        $db->beginTransaction();
        
        try {
            $numero = $this->generarNumeroOrden();
            
            $sql = "INSERT INTO pedidos (numero_orden, cliente_id, estado, subtotal, iva, costo_envio,
                        descuento, total, nombre_envio, email_envio, telefono_envio, direccion_envio,
                        ciudad_envio, departamento_envio, notas, metodo_pago, ip_cliente)
                    VALUES (:numero, :cliente_id, 'pendiente', :subtotal, :iva, :envio, :descuento,
                        :total, :nombre, :email, :telefono, :direccion, :ciudad, :dpto, :notas, :pago, :ip)";
            
            $pedidoId = $db->insert($sql, [
                'numero'     => $numero,
                'cliente_id' => $datos['cliente_id'],
                'subtotal'   => $datos['subtotal'],
                'iva'        => $datos['iva'],
                'envio'      => $datos['costo_envio'],
                'descuento'  => $datos['descuento'] ?? 0,
                'total'      => $datos['total'],
                'nombre'     => $datos['nombre_envio'],
                'email'      => $datos['email_envio'],
                'telefono'   => $datos['telefono_envio'] ?? null,
                'direccion'  => $datos['direccion_envio'],
                'ciudad'     => $datos['ciudad_envio'],
                'dpto'       => $datos['departamento_envio'] ?? null,
                'notas'      => $datos['notas'] ?? null,
                'pago'       => $datos['metodo_pago'] ?? 'efectivo',
                'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
            
            if (!$pedidoId) throw new Exception("Error creando pedido");
            
            // Insertar detalle
            foreach ($items as $item) {
                $db->execute(
                    "INSERT INTO detalle_pedidos (pedido_id, producto_id, nombre_producto, sku_producto, cantidad, precio_unitario, subtotal)
                     VALUES (:pid, :prod, :nombre, :sku, :cant, :precio, :sub)",
                    [
                        'pid'    => $pedidoId,
                        'prod'   => $item['producto_id'],
                        'nombre' => $item['nombre'],
                        'sku'    => $item['sku'] ?? null,
                        'cant'   => $item['cantidad'],
                        'precio' => $item['precio_unitario'],
                        'sub'    => $item['subtotal'],
                    ]
                );
                // Reducir stock
                $db->execute(
                    "UPDATE productos SET stock = stock - :cant WHERE id=:id",
                    ['cant' => $item['cantidad'], 'id' => $item['producto_id']]
                );
            }
            
            $db->commit();
            return $pedidoId;
        } catch (Exception $e) {
            $db->rollback();
            error_log("Error creando pedido: " . $e->getMessage());
            return false;
        }
    }
    
    public function actualizarEstado(int $id, string $estado): bool {
        $rows = $this->db->execute(
            "UPDATE pedidos SET estado=:estado WHERE id=:id",
            ['estado' => $estado, 'id' => $id]
        );
        return $rows > 0;
    }
    
    public function estadisticasDashboard(): array {
        $hoy   = date('Y-m-d');
        $mes   = date('Y-m');
        return [
            'total_hoy'      => (float) $this->db->scalar("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE DATE(created_at)='$hoy'"),
            'total_mes'      => (float) $this->db->scalar("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE DATE_FORMAT(created_at,'%Y-%m')='$mes'"),
            'pedidos_hoy'    => (int)   $this->db->scalar("SELECT COUNT(*) FROM pedidos WHERE DATE(created_at)='$hoy'"),
            'pedidos_mes'    => (int)   $this->db->scalar("SELECT COUNT(*) FROM pedidos WHERE DATE_FORMAT(created_at,'%Y-%m')='$mes'"),
            'pendientes'     => (int)   $this->db->scalar("SELECT COUNT(*) FROM pedidos WHERE estado='pendiente'"),
            'procesando'     => (int)   $this->db->scalar("SELECT COUNT(*) FROM pedidos WHERE estado IN('confirmado','procesando')"),
        ];
    }
    
    public function ventasPorMes(int $meses = 6): array {
        $sql = "SELECT DATE_FORMAT(created_at,'%Y-%m') AS mes, 
                    COUNT(*) AS total_pedidos,
                    SUM(total) AS total_ventas
                FROM pedidos
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :m MONTH)
                GROUP BY mes ORDER BY mes ASC";
        return $this->db->query($sql, ['m' => $meses]);
    }
    
    private function generarNumeroOrden(): string {
        $anio   = date('Y');
        $ultimo = $this->db->scalar(
            "SELECT MAX(CAST(SUBSTRING(numero_orden, 9) AS UNSIGNED)) FROM pedidos WHERE numero_orden LIKE :p",
            ['p' => "TS-$anio-%"]
        );
        $siguiente = ((int)($ultimo ?? 0)) + 1;
        return sprintf("TS-%s-%06d", $anio, $siguiente);
    }
}


/**
 * TechStore - Modelo de Usuarios Administradores
 */
class UsuarioModel {
    private Database $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function obtenerTodos(): array {
        return $this->db->query("SELECT id, nombre, apellido, email, rol, activo, ultimo_acceso, created_at FROM usuarios ORDER BY nombre");
    }
    
    public function obtenerPorId(int $id): ?array {
        return $this->db->queryOne("SELECT * FROM usuarios WHERE id=:id", ['id' => $id]);
    }
    
    public function obtenerPorEmail(string $email): ?array {
        return $this->db->queryOne("SELECT * FROM usuarios WHERE email=:email", ['email' => $email]);
    }
    
    public function verificarPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public function actualizarUltimoAcceso(int $id): void {
        $this->db->execute("UPDATE usuarios SET ultimo_acceso=NOW() WHERE id=:id", ['id' => $id]);
    }
    
    public function crear(array $datos): int|false {
        $sql = "INSERT INTO usuarios (nombre, apellido, email, password, rol, activo) 
                VALUES (:nombre, :apellido, :email, :password, :rol, 1)";
        return $this->db->insert($sql, [
            'nombre'   => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'email'    => strtolower(trim($datos['email'])),
            'password' => password_hash($datos['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'rol'      => $datos['rol'] ?? 'vendedor',
        ]);
    }
    
    public function actualizar(int $id, array $datos): bool {
        $sql = "UPDATE usuarios SET nombre=:nombre, apellido=:apellido, email=:email, rol=:rol WHERE id=:id";
        return $this->db->execute($sql, [
            'id' => $id, 'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'email' => $datos['email'], 'rol' => $datos['rol'],
        ]) > 0;
    }
    
    public function cambiarPassword(int $id, string $password): bool {
        return $this->db->execute(
            "UPDATE usuarios SET password=:p WHERE id=:id",
            ['id' => $id, 'p' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])]
        ) > 0;
    }
    
    public function emailExiste(string $email, int $excludeId = 0): bool {
        return (int) $this->db->scalar(
            "SELECT COUNT(*) FROM usuarios WHERE email=:e AND id!=:id",
            ['e' => $email, 'id' => $excludeId]
        ) > 0;
    }
}
