<?php
/**
 * TechStore - Modelo de Configuración (IVA + Envío)
 * Archivo: models/ConfiguracionModel.php
 */

require_once BASE_PATH . '/config/Database.php';

class ConfiguracionModel {

    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ============================================================
    //  CONFIGURACIÓN GENERAL DE EMPRESA
    // ============================================================

    public function obtener(): ?array {
        return $this->db->queryOne("SELECT * FROM configuracion_empresa WHERE id = 1");
    }

    public function actualizarEnvio(array $datos): bool {
        $sql = "UPDATE configuracion_empresa SET
                    costo_envio          = :costo_envio,
                    envio_gratis_desde   = :envio_gratis_desde,
                    envio_activo         = :envio_activo,
                    envio_gratis_activo  = :envio_gratis_activo,
                    envio_tiempo_estimado = :tiempo,
                    envio_nota           = :nota
                WHERE id = 1";
        return $this->db->execute($sql, [
            'costo_envio'         => (float)($datos['costo_envio'] ?? 25),
            'envio_gratis_desde'  => (float)($datos['envio_gratis_desde'] ?? 500),
            'envio_activo'        => isset($datos['envio_activo']) ? 1 : 0,
            'envio_gratis_activo' => isset($datos['envio_gratis_activo']) ? 1 : 0,
            'tiempo'              => trim($datos['envio_tiempo_estimado'] ?? '1-3 días hábiles'),
            'nota'                => trim($datos['envio_nota'] ?? ''),
        ]) > 0;
    }

    public function actualizarIvaGlobal(float $porcentaje): bool {
        return $this->db->execute(
            "UPDATE configuracion_empresa SET iva_porcentaje = :p WHERE id = 1",
            ['p' => $porcentaje]
        ) > 0;
    }

    // ============================================================
    //  ZONAS DE ENVÍO
    // ============================================================

    public function obtenerZonas(bool $soloActivas = false): array {
        $where = $soloActivas ? 'WHERE activa = 1' : '';
        return $this->db->query(
            "SELECT * FROM zonas_envio $where ORDER BY orden ASC, nombre ASC"
        );
    }

    public function obtenerZonaPorId(int $id): ?array {
        $zona = $this->db->queryOne("SELECT * FROM zonas_envio WHERE id = :id", ['id' => $id]);
        if ($zona && !empty($zona['ciudades'])) {
            $zona['ciudades'] = json_decode($zona['ciudades'], true) ?? [];
        }
        return $zona;
    }

    public function crearZona(array $datos): int|false {
        $ciudades = $this->parsearCiudades($datos['ciudades'] ?? '');
        $sql = "INSERT INTO zonas_envio 
                    (nombre, descripcion, ciudades, costo, costo_express, tiempo_estimado, activa, orden)
                VALUES
                    (:nombre, :desc, :ciudades, :costo, :express, :tiempo, :activa, :orden)";
        return $this->db->insert($sql, [
            'nombre'   => trim($datos['nombre']),
            'desc'     => trim($datos['descripcion'] ?? ''),
            'ciudades' => !empty($ciudades) ? json_encode($ciudades) : null,
            'costo'    => (float)$datos['costo'],
            'express'  => !empty($datos['costo_express']) ? (float)$datos['costo_express'] : null,
            'tiempo'   => trim($datos['tiempo_estimado'] ?? '1-3 días hábiles'),
            'activa'   => isset($datos['activa']) ? 1 : 0,
            'orden'    => (int)($datos['orden'] ?? 0),
        ]);
    }

    public function actualizarZona(int $id, array $datos): bool {
        $ciudades = $this->parsearCiudades($datos['ciudades'] ?? '');
        $sql = "UPDATE zonas_envio SET
                    nombre          = :nombre,
                    descripcion     = :desc,
                    ciudades        = :ciudades,
                    costo           = :costo,
                    costo_express   = :express,
                    tiempo_estimado = :tiempo,
                    activa          = :activa,
                    orden           = :orden
                WHERE id = :id";
        return $this->db->execute($sql, [
            'id'       => $id,
            'nombre'   => trim($datos['nombre']),
            'desc'     => trim($datos['descripcion'] ?? ''),
            'ciudades' => !empty($ciudades) ? json_encode($ciudades) : null,
            'costo'    => (float)$datos['costo'],
            'express'  => !empty($datos['costo_express']) ? (float)$datos['costo_express'] : null,
            'tiempo'   => trim($datos['tiempo_estimado'] ?? '1-3 días hábiles'),
            'activa'   => isset($datos['activa']) ? 1 : 0,
            'orden'    => (int)($datos['orden'] ?? 0),
        ]) > 0;
    }

    public function eliminarZona(int $id): bool {
        return $this->db->execute("DELETE FROM zonas_envio WHERE id = :id", ['id' => $id]) > 0;
    }

    public function toggleZona(int $id): bool {
        return $this->db->execute(
            "UPDATE zonas_envio SET activa = NOT activa WHERE id = :id",
            ['id' => $id]
        ) > 0;
    }

    /**
     * Obtener costo de envío para una ciudad específica
     */
    public function calcularEnvio(string $ciudad, bool $express = false): array {
        $config = $this->obtener();

        // Si el envío está desactivado
        if (empty($config['envio_activo'])) {
            return ['costo' => 0, 'tiempo' => 'Solo retiro en tienda', 'zona' => null, 'gratis' => false];
        }

        $zonas = $this->obtenerZonas(true);
        $ciudadLower = strtolower(trim($ciudad));

        foreach ($zonas as $zona) {
            $ciudadesZona = !empty($zona['ciudades']) ? json_decode($zona['ciudades'], true) : null;

            // Si tiene ciudades específicas, verificar si coincide
            if (!empty($ciudadesZona)) {
                foreach ($ciudadesZona as $c) {
                    if (strtolower(trim($c)) === $ciudadLower) {
                        $costo = $express && !empty($zona['costo_express'])
                            ? (float)$zona['costo_express']
                            : (float)$zona['costo'];
                        return [
                            'costo'   => $costo,
                            'tiempo'  => $zona['tiempo_estimado'],
                            'zona'    => $zona['nombre'],
                            'express' => $express && !empty($zona['costo_express']),
                            'gratis'  => false,
                        ];
                    }
                }
            }
        }

        // Fallback: zona cobertura nacional (ciudades = null)
        foreach ($zonas as $zona) {
            $ciudadesZona = !empty($zona['ciudades']) ? json_decode($zona['ciudades'], true) : null;
            if (empty($ciudadesZona)) {
                return [
                    'costo'   => (float)$zona['costo'],
                    'tiempo'  => $zona['tiempo_estimado'],
                    'zona'    => $zona['nombre'],
                    'express' => false,
                    'gratis'  => false,
                ];
            }
        }

        // Si no hay zona, usar costo por defecto
        return [
            'costo'  => (float)($config['costo_envio'] ?? 25),
            'tiempo' => $config['envio_tiempo_estimado'] ?? '1-3 días hábiles',
            'zona'   => 'General',
            'gratis' => false,
        ];
    }

    // ============================================================
    //  IVA POR PRODUCTO
    // ============================================================

    /**
     * Calcular IVA de un producto específico
     */
    public function calcularIvaProducto(array $producto, float $ivaGlobal): float {
        if (empty($producto['tiene_iva'])) return 0.0;
        $porcentaje = isset($producto['porcentaje_iva']) && $producto['porcentaje_iva'] !== null
            ? (float)$producto['porcentaje_iva']
            : $ivaGlobal;
        $precio = (float)($producto['precio_oferta'] ?? $producto['precio']);
        return round($precio * ($porcentaje / 100), 2);
    }

    /**
     * Calcular totales de un carrito aplicando IVA por producto
     */
    public function calcularTotalesCarrito(array $items, string $ciudad = ''): array {
        $config  = $this->obtener();
        $ivaGlobal = (float)($config['iva_porcentaje'] ?? 13);

        $subtotal       = 0;
        $totalIva       = 0;
        $itemsDetallado = [];

        foreach ($items as $item) {
            $precioUnit = (float)$item['precio_unitario'];
            $cantidad   = (int)$item['cantidad'];
            $subItem    = $precioUnit * $cantidad;

            // IVA del item
            $tieneIva    = isset($item['tiene_iva']) ? (bool)$item['tiene_iva'] : true;
            $pctIva      = isset($item['porcentaje_iva']) && $item['porcentaje_iva'] !== null
                ? (float)$item['porcentaje_iva']
                : $ivaGlobal;
            $ivaItem     = $tieneIva ? round($subItem * ($pctIva / 100), 2) : 0;

            $subtotal  += $subItem;
            $totalIva  += $ivaItem;

            $itemsDetallado[] = array_merge($item, [
                'subtotal_item' => $subItem,
                'tiene_iva'     => $tieneIva,
                'porcentaje_iva'=> $tieneIva ? $pctIva : 0,
                'iva_item'      => $ivaItem,
            ]);
        }

        // Calcular envío por zona
        $infoEnvio = $ciudad ? $this->calcularEnvio($ciudad) : ['costo' => (float)($config['costo_envio'] ?? 25), 'tiempo' => '', 'zona' => ''];

        // Envío gratis si aplica
        $costoEnvio = 0;
        if (!empty($config['envio_gratis_activo']) && $subtotal >= (float)($config['envio_gratis_desde'] ?? 500)) {
            $costoEnvio = 0;
            $infoEnvio['gratis'] = true;
        } else {
            $costoEnvio = $infoEnvio['costo'] ?? (float)($config['costo_envio'] ?? 25);
        }

        $total = $subtotal + $costoEnvio;

        return [
            'items'            => $itemsDetallado,
            'subtotal'         => round($subtotal, 2),
            'iva'              => round($totalIva, 2),
            'costo_envio'      => round($costoEnvio, 2),
            'total'            => round($total, 2),
            'envio_gratis'     => !empty($infoEnvio['gratis']),
            'zona_envio'       => $infoEnvio['zona'] ?? '',
            'tiempo_envio'     => $infoEnvio['tiempo'] ?? '',
            'envio_gratis_desde' => (float)($config['envio_gratis_desde'] ?? 500),
            'iva_global'       => $ivaGlobal,
        ];
    }

    // ============================================================
    //  HELPERS
    // ============================================================

    private function parsearCiudades(string $texto): array {
        if (empty(trim($texto))) return [];
        // Aceptar separados por coma o salto de línea
        $partes = preg_split('/[\n,]+/', $texto);
        return array_values(array_filter(array_map('trim', $partes)));
    }
}
