<?php
/**
 * TechStore - Modelo de Productos
 * Archivo: models/ProductoModel.php
 */

require_once BASE_PATH . '/config/Database.php';

class ProductoModel {
    
    private Database $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtener todos los productos con filtros
     */
    public function obtenerTodos(array $filtros = [], int $pagina = 1, int $porPagina = ITEMS_POR_PAGINA): array {
        $donde = ['p.activo = 1'];
        $params = [];
        
        if (!empty($filtros['categoria'])) {
            $donde[] = 'p.categoria_id = :categoria_id';
            $params['categoria_id'] = $filtros['categoria'];
        }
        
        if (!empty($filtros['busqueda'])) {
            $donde[] = '(p.nombre LIKE :busqueda OR p.descripcion_corta LIKE :busqueda2 OR p.marca LIKE :busqueda3)';
            $params['busqueda']  = '%' . $filtros['busqueda'] . '%';
            $params['busqueda2'] = '%' . $filtros['busqueda'] . '%';
            $params['busqueda3'] = '%' . $filtros['busqueda'] . '%';
        }
        
        if (!empty($filtros['precio_min'])) {
            $donde[] = 'COALESCE(p.precio_oferta, p.precio) >= :precio_min';
            $params['precio_min'] = (float)$filtros['precio_min'];
        }
        
        if (!empty($filtros['precio_max'])) {
            $donde[] = 'COALESCE(p.precio_oferta, p.precio) <= :precio_max';
            $params['precio_max'] = (float)$filtros['precio_max'];
        }
        
        if (!empty($filtros['marca'])) {
            $donde[] = 'p.marca = :marca';
            $params['marca'] = $filtros['marca'];
        }
        
        if (isset($filtros['destacado']) && $filtros['destacado'] === true) {
            $donde[] = 'p.destacado = 1';
        }
        
        // Ordenamiento
        $orden = match ($filtros['orden'] ?? '') {
            'precio_asc'   => 'COALESCE(p.precio_oferta, p.precio) ASC',
            'precio_desc'  => 'COALESCE(p.precio_oferta, p.precio) DESC',
            'nombre_asc'   => 'p.nombre ASC',
            'nombre_desc'  => 'p.nombre DESC',
            'mas_nuevos'   => 'p.created_at DESC',
            'mas_vendidos' => 'ventas DESC',
            default        => 'p.destacado DESC, p.created_at DESC',
        };
        
        $clausulaDonde = implode(' AND ', $donde);
        $offset = ($pagina - 1) * $porPagina;
        
        // Total de registros para paginación
        $sqlTotal = "SELECT COUNT(*) FROM productos p WHERE $clausulaDonde";
        $total = (int) $this->db->scalar($sqlTotal, $params);
        
        $sql = "SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    COALESCE(p.precio_oferta, p.precio) AS precio_final,
                    CASE WHEN p.precio_oferta IS NOT NULL 
                         THEN ROUND((1 - p.precio_oferta/p.precio)*100) 
                         ELSE 0 END AS descuento_pct,
                    (SELECT COUNT(*) FROM detalle_pedidos dp WHERE dp.producto_id = p.id) AS ventas
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE $clausulaDonde
                ORDER BY $orden
                LIMIT :limit OFFSET :offset";
        
        $params['limit']  = $porPagina;
        $params['offset'] = $offset;
        
        $productos = $this->db->query($sql, $params);
        
        return [
            'items'       => $productos,
            'total'       => $total,
            'pagina'      => $pagina,
            'por_pagina'  => $porPagina,
            'total_paginas' => ceil($total / $porPagina),
        ];
    }
    
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    COALESCE(p.precio_oferta, p.precio) AS precio_final,
                    CASE WHEN p.precio_oferta IS NOT NULL 
                         THEN ROUND((1 - p.precio_oferta/p.precio)*100) 
                         ELSE 0 END AS descuento_pct
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = :id";
        
        $producto = $this->db->queryOne($sql, ['id' => $id]);
        
        if ($producto) {
            // Decodificar especificaciones JSON
            if (!empty($producto['especificaciones'])) {
                $producto['especificaciones'] = json_decode($producto['especificaciones'], true);
            }
            // Cargar imágenes adicionales
            $producto['imagenes'] = $this->obtenerImagenes($id);
        }
        
        return $producto;
    }
    
    /**
     * Obtener producto por slug
     */
    public function obtenerPorSlug(string $slug): ?array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    COALESCE(p.precio_oferta, p.precio) AS precio_final,
                    CASE WHEN p.precio_oferta IS NOT NULL 
                         THEN ROUND((1 - p.precio_oferta/p.precio)*100) 
                         ELSE 0 END AS descuento_pct
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.slug = :slug AND p.activo = 1";
        
        $producto = $this->db->queryOne($sql, ['slug' => $slug]);
        
        if ($producto) {
            if (!empty($producto['especificaciones'])) {
                $producto['especificaciones'] = json_decode($producto['especificaciones'], true);
            }
            $producto['imagenes'] = $this->obtenerImagenes($producto['id']);
            // Incrementar visitas
            $this->incrementarVisitas($producto['id']);
        }
        
        return $producto;
    }
    
    /**
     * Obtener productos destacados
     */
    public function obtenerDestacados(int $limite = 8): array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug,
                    COALESCE(p.precio_oferta, p.precio) AS precio_final,
                    CASE WHEN p.precio_oferta IS NOT NULL 
                         THEN ROUND((1 - p.precio_oferta/p.precio)*100) 
                         ELSE 0 END AS descuento_pct
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1 AND p.destacado = 1
                ORDER BY p.created_at DESC
                LIMIT :limite";
        
        return $this->db->query($sql, ['limite' => $limite]);
    }
    
    /**
     * Obtener productos en oferta
     */
    public function obtenerOfertas(int $limite = 6): array {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre,
                    ROUND((1 - p.precio_oferta/p.precio)*100) AS descuento_pct
                FROM productos p
                INNER JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1 AND p.precio_oferta IS NOT NULL
                ORDER BY descuento_pct DESC
                LIMIT :limite";
        
        return $this->db->query($sql, ['limite' => $limite]);
    }
    
    /**
     * Obtener productos relacionados por categoría
     */
    public function obtenerRelacionados(int $categoriaId, int $excludeId, int $limite = 4): array {
        $sql = "SELECT p.*, COALESCE(p.precio_oferta, p.precio) AS precio_final
                FROM productos p
                WHERE p.activo = 1 
                  AND p.categoria_id = :categoria_id 
                  AND p.id != :exclude_id
                ORDER BY RAND()
                LIMIT :limite";
        
        return $this->db->query($sql, [
            'categoria_id' => $categoriaId,
            'exclude_id'   => $excludeId,
            'limite'       => $limite,
        ]);
    }
    
    /**
     * Obtener imágenes de un producto
     */
    public function obtenerImagenes(int $productoId): array {
        $sql = "SELECT * FROM imagenes_productos WHERE producto_id = :id ORDER BY orden ASC";
        return $this->db->query($sql, ['id' => $productoId]);
    }
    
    /**
     * Buscar productos (autocomplete)
     */
    public function buscar(string $termino, int $limite = 5): array {
        $sql = "SELECT id, nombre, slug, imagen_principal, 
                    COALESCE(precio_oferta, precio) AS precio_final
                FROM productos
                WHERE activo = 1 
                  AND (nombre LIKE :t1 OR marca LIKE :t2 OR modelo LIKE :t3)
                ORDER BY nombre ASC
                LIMIT :limite";
        
        $t = '%' . $termino . '%';
        return $this->db->query($sql, ['t1' => $t, 't2' => $t, 't3' => $t, 'limite' => $limite]);
    }
    
    /**
     * Incrementar contador de visitas
     */
    private function incrementarVisitas(int $id): void {
        $this->db->execute("UPDATE productos SET visitas = visitas + 1 WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Crear nuevo producto (Admin)
     */
    public function crear(array $datos): int|false {
        $sql = "INSERT INTO productos 
                    (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, 
                     especificaciones, precio, precio_oferta, tiene_iva, porcentaje_iva,
                     stock, stock_minimo, imagen_principal, marca, modelo, garantia,
                     destacado, activo)
                VALUES 
                    (:categoria_id, :nombre, :slug, :sku, :descripcion_corta, :descripcion,
                     :especificaciones, :precio, :precio_oferta, :tiene_iva, :porcentaje_iva,
                     :stock, :stock_minimo, :imagen_principal, :marca, :modelo, :garantia,
                     :destacado, :activo)";

        return $this->db->insert($sql, [
            'categoria_id'      => $datos['categoria_id'],
            'nombre'            => $datos['nombre'],
            'slug'              => $this->generarSlug($datos['nombre']),
            'sku'               => strtoupper($datos['sku']),
            'descripcion_corta' => $datos['descripcion_corta'] ?? null,
            'descripcion'       => $datos['descripcion'] ?? null,
            'especificaciones'  => !empty($datos['especificaciones']) ? json_encode($datos['especificaciones']) : null,
            'precio'            => (float)$datos['precio'],
            'precio_oferta'     => !empty($datos['precio_oferta']) ? (float)$datos['precio_oferta'] : null,
            'tiene_iva'         => isset($datos['tiene_iva']) ? 1 : 0,
            'porcentaje_iva'    => (isset($datos['porcentaje_iva']) && $datos['porcentaje_iva'] !== '')
                                    ? (float)$datos['porcentaje_iva'] : null,
            'stock'             => (int)$datos['stock'],
            'stock_minimo'      => (int)($datos['stock_minimo'] ?? 5),
            'imagen_principal'  => $datos['imagen_principal'] ?? null,
            'marca'             => $datos['marca'] ?? null,
            'modelo'            => $datos['modelo'] ?? null,
            'garantia'          => $datos['garantia'] ?? null,
            'destacado'         => isset($datos['destacado']) ? 1 : 0,
            'activo'            => isset($datos['activo']) ? 1 : 0,
        ]);
    }
    
    /**
     * Actualizar producto (Admin)
     */
    public function actualizar(int $id, array $datos): bool {
        $sql = "UPDATE productos SET
                    categoria_id        = :categoria_id,
                    nombre              = :nombre,
                    slug                = :slug,
                    sku                 = :sku,
                    descripcion_corta   = :descripcion_corta,
                    descripcion         = :descripcion,
                    especificaciones    = :especificaciones,
                    precio              = :precio,
                    precio_oferta       = :precio_oferta,
                    tiene_iva           = :tiene_iva,
                    porcentaje_iva      = :porcentaje_iva,
                    stock               = :stock,
                    stock_minimo        = :stock_minimo,
                    marca               = :marca,
                    modelo              = :modelo,
                    garantia            = :garantia,
                    destacado           = :destacado,
                    activo              = :activo
                WHERE id = :id";

        $rows = $this->db->execute($sql, [
            'id'                => $id,
            'categoria_id'      => $datos['categoria_id'],
            'nombre'            => $datos['nombre'],
            'slug'              => $this->generarSlug($datos['nombre'], $id),
            'sku'               => strtoupper($datos['sku']),
            'descripcion_corta' => $datos['descripcion_corta'] ?? null,
            'descripcion'       => $datos['descripcion'] ?? null,
            'especificaciones'  => !empty($datos['especificaciones']) ? json_encode($datos['especificaciones']) : null,
            'precio'            => (float)$datos['precio'],
            'precio_oferta'     => !empty($datos['precio_oferta']) ? (float)$datos['precio_oferta'] : null,
            'tiene_iva'         => isset($datos['tiene_iva']) ? 1 : 0,
            'porcentaje_iva'    => (isset($datos['porcentaje_iva']) && $datos['porcentaje_iva'] !== '')
                                    ? (float)$datos['porcentaje_iva'] : null,
            'stock'             => (int)$datos['stock'],
            'stock_minimo'      => (int)($datos['stock_minimo'] ?? 5),
            'marca'             => $datos['marca'] ?? null,
            'modelo'            => $datos['modelo'] ?? null,
            'garantia'          => $datos['garantia'] ?? null,
            'destacado'         => isset($datos['destacado']) ? 1 : 0,
            'activo'            => isset($datos['activo']) ? 1 : 0,
        ]);

        return $rows > 0;
    }
    
    /**
     * Actualizar imagen principal
     */
    public function actualizarImagen(int $id, string $imagen): bool {
        $rows = $this->db->execute(
            "UPDATE productos SET imagen_principal = :imagen WHERE id = :id",
            ['id' => $id, 'imagen' => $imagen]
        );
        return $rows > 0;
    }
    
    /**
     * Eliminar producto (soft delete)
     */
    public function eliminar(int $id): bool {
        $rows = $this->db->execute(
            "UPDATE productos SET activo = 0 WHERE id = :id",
            ['id' => $id]
        );
        return $rows > 0;
    }
    
    /**
     * Verificar si SKU existe
     */
    public function skuExiste(string $sku, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM productos WHERE sku = :sku AND id != :id";
        return (int) $this->db->scalar($sql, ['sku' => $sku, 'id' => $excludeId]) > 0;
    }
    
    /**
     * Actualizar stock
     */
    public function actualizarStock(int $id, int $cantidad): bool {
        $rows = $this->db->execute(
            "UPDATE productos SET stock = stock - :cantidad WHERE id = :id AND stock >= :cantidad",
            ['id' => $id, 'cantidad' => $cantidad]
        );
        return $rows > 0;
    }
    
    /**
     * Obtener estadísticas para dashboard
     */
    public function estadisticas(): array {
        return [
            'total'         => (int) $this->db->scalar("SELECT COUNT(*) FROM productos WHERE activo = 1"),
            'destacados'    => (int) $this->db->scalar("SELECT COUNT(*) FROM productos WHERE activo = 1 AND destacado = 1"),
            'stock_bajo'    => (int) $this->db->scalar("SELECT COUNT(*) FROM productos WHERE activo = 1 AND stock <= stock_minimo"),
            'sin_stock'     => (int) $this->db->scalar("SELECT COUNT(*) FROM productos WHERE activo = 1 AND stock = 0"),
            'en_oferta'     => (int) $this->db->scalar("SELECT COUNT(*) FROM productos WHERE activo = 1 AND precio_oferta IS NOT NULL"),
        ];
    }
    
    /**
     * Generar slug único
     */
    private function generarSlug(string $texto, int $excludeId = 0): string {
        $slug = strtolower(trim($texto));
        $slug = preg_replace('/[áàäâã]/u', 'a', $slug);
        $slug = preg_replace('/[éèëê]/u', 'e', $slug);
        $slug = preg_replace('/[íìïî]/u', 'i', $slug);
        $slug = preg_replace('/[óòöôõ]/u', 'o', $slug);
        $slug = preg_replace('/[úùüû]/u', 'u', $slug);
        $slug = preg_replace('/[ñ]/u', 'n', $slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Verificar unicidad
        $original = $slug;
        $i = 1;
        while ($this->slugExiste($slug, $excludeId)) {
            $slug = $original . '-' . $i;
            $i++;
        }
        
        return $slug;
    }
    
    private function slugExiste(string $slug, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM productos WHERE slug = :slug AND id != :id";
        return (int) $this->db->scalar($sql, ['slug' => $slug, 'id' => $excludeId]) > 0;
    }
}
