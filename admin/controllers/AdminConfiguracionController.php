<?php
/**
 * TechStore - AdminConfiguracionController
 * Archivo: admin/controllers/AdminConfiguracionController.php
 * Gestiona: IVA global, IVA por producto, zonas de envío
 */

class AdminConfiguracionController {

    private ConfiguracionModel $model;

    public function __construct() {
        $this->model = new ConfiguracionModel();
        if (!adminCan('admin')) {
            adminSetFlash('danger', 'No tienes permisos para acceder a la configuración.');
            adminRedirect('/');
        }
    }

    // ============================================================
    //  PÁGINA PRINCIPAL: IVA + Envío
    // ============================================================

    public function index(): void {
        $config = $this->model->obtener();
        $zonas  = $this->model->obtenerZonas();

        // Estadísticas IVA (seguras si aún no existe la columna)
        $db = Database::getInstance();
        try {
            $statsIva = [
                'con_iva'           => (int)$db->scalar("SELECT COUNT(*) FROM productos WHERE activo=1 AND tiene_iva=1"),
                'sin_iva'           => (int)$db->scalar("SELECT COUNT(*) FROM productos WHERE activo=1 AND tiene_iva=0"),
                'iva_personalizado' => (int)$db->scalar("SELECT COUNT(*) FROM productos WHERE activo=1 AND porcentaje_iva IS NOT NULL"),
            ];
        } catch (\Exception $e) {
            // La columna aún no existe — mostrar aviso
            $statsIva = ['con_iva' => 0, 'sin_iva' => 0, 'iva_personalizado' => 0, 'migracion_pendiente' => true];
        }

        $tituloAdmin = 'Configuración: IVA y Envío';
        $modulo      = 'configuracion';
        $breadcrumb  = [['label' => 'Configuración IVA y Envío']];
        require BASE_PATH . '/admin/views/config/index.php';
    }

    // ============================================================
    //  GUARDAR CONFIGURACIÓN DE ENVÍO
    // ============================================================

    public function guardarEnvio(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminRedirect('/configuracion');
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) {
            adminSetFlash('danger', 'Error de seguridad (CSRF).');
            adminRedirect('/configuracion');
        }

        $ok = $this->model->actualizarEnvio($_POST);

        // Limpiar caché de sesión de envío para que la tienda use valores frescos
        unset($_SESSION['_config_envio_cache'], $_SESSION['_config_envio_exp']);

        adminSetFlash($ok ? 'success' : 'danger',
            $ok ? '✅ Configuración de envío guardada correctamente.' : 'Error al guardar la configuración.');
        adminRedirect('/configuracion');
    }

    // ============================================================
    //  GUARDAR IVA GLOBAL
    // ============================================================

    public function guardarIva(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminRedirect('/configuracion');
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) {
            adminSetFlash('danger', 'Error de seguridad.');
            adminRedirect('/configuracion');
        }

        $porcentaje = max(0, min(100, (float)($_POST['iva_porcentaje'] ?? 13)));
        $ok = $this->model->actualizarIvaGlobal($porcentaje);
        adminSetFlash($ok ? 'success' : 'danger',
            $ok ? "✅ IVA global actualizado a {$porcentaje}%." : 'Error al actualizar el IVA.');
        adminRedirect('/configuracion');
    }

    // ============================================================
    //  IVA POR PRODUCTO (actualización masiva o individual)
    // ============================================================

    public function actualizarIvaProductos(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminRedirect('/configuracion');
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) {
            adminSetFlash('danger', 'Error de seguridad.');
            adminRedirect('/configuracion');
        }

        $db           = Database::getInstance();
        $accion       = $_POST['accion_masiva'] ?? '';
        $productosIds = $_POST['productos_ids'] ?? [];
        $actualizados = 0;

        // Verificar que las columnas existen antes de operar
        try {
            $db->scalar("SELECT tiene_iva FROM productos LIMIT 1");
        } catch (\Exception $e) {
            adminSetFlash('danger',
                '❌ Las columnas tiene_iva y porcentaje_iva no existen en la tabla productos. ' .
                'Ejecuta el archivo <strong>migracion_iva_envio.sql</strong> en phpMyAdmin primero.');
            adminRedirect('/configuracion');
        }

        if ($accion === 'todos_con_iva') {
            $actualizados = $db->execute("UPDATE productos SET tiene_iva = 1");
            adminSetFlash('success', "✅ IVA activado en todos los productos ($actualizados).");

        } elseif ($accion === 'todos_sin_iva') {
            $actualizados = $db->execute("UPDATE productos SET tiene_iva = 0");
            adminSetFlash('success', "✅ IVA desactivado en todos los productos ($actualizados).");

        } elseif ($accion === 'reset_iva_global') {
            $actualizados = $db->execute("UPDATE productos SET porcentaje_iva = NULL");
            adminSetFlash('success', "✅ IVA personalizado reseteado en $actualizados productos. Ahora todos usan el IVA global.");

        } elseif (!empty($productosIds) && is_array($productosIds)) {
            foreach ($productosIds as $id => $valores) {
                $id       = (int)$id;
                if ($id <= 0) continue;

                $tieneIva = !empty($valores['tiene_iva']) ? 1 : 0;
                $pctIva   = (isset($valores['porcentaje_iva']) && trim($valores['porcentaje_iva']) !== '')
                            ? (float)$valores['porcentaje_iva']
                            : null;

                // Validar rango
                if ($pctIva !== null) {
                    $pctIva = max(0, min(100, $pctIva));
                }

                $rows = $db->execute(
                    "UPDATE productos SET tiene_iva = :iva, porcentaje_iva = :pct WHERE id = :id",
                    ['iva' => $tieneIva, 'pct' => $pctIva, 'id' => $id]
                );
                $actualizados += $rows;
            }
            adminSetFlash('success', "✅ IVA actualizado en $actualizados producto(s).");

        } else {
            adminSetFlash('warning', 'No se realizó ninguna acción. Selecciona productos o una acción masiva.');
        }

        adminRedirect('/configuracion/iva-productos');
    }

    public function ivaProductos(): void {
        $config    = $this->model->obtener();
        $db        = Database::getInstance();

        // Verificar que la migración fue ejecutada
        $migracionPendiente = false;
        try {
            $db->scalar("SELECT tiene_iva FROM productos LIMIT 1");
        } catch (\Exception $e) {
            $migracionPendiente = true;
        }

        if ($migracionPendiente) {
            adminSetFlash('danger',
                '❌ Ejecuta <strong>migracion_iva_envio.sql</strong> en phpMyAdmin antes de usar esta sección.');
            adminRedirect('/configuracion');
        }

        $pagina    = max(1, (int)($_GET['pagina'] ?? 1));
        $busqueda  = trim($_GET['q'] ?? '');
        $filtroIva = $_GET['iva'] ?? '';

        $donde  = ['p.activo = 1'];
        $params = [];
        if ($busqueda) {
            $donde[]         = '(p.nombre LIKE :q OR p.marca LIKE :q2 OR p.sku LIKE :q3)';
            $params['q']     = "%$busqueda%";
            $params['q2']    = "%$busqueda%";
            $params['q3']    = "%$busqueda%";
        }
        if ($filtroIva === 'con')  $donde[] = 'p.tiene_iva = 1';
        if ($filtroIva === 'sin')  $donde[] = 'p.tiene_iva = 0';
        if ($filtroIva === 'personalizado') $donde[] = 'p.porcentaje_iva IS NOT NULL';

        $where  = implode(' AND ', $donde);
        $offset = ($pagina - 1) * ADMIN_ITEMS_POR_PAGINA;
        $total  = (int)$db->scalar("SELECT COUNT(*) FROM productos p WHERE $where", $params);

        $params['l'] = ADMIN_ITEMS_POR_PAGINA;
        $params['o'] = $offset;
        $productos = $db->query(
            "SELECT p.id, p.nombre, p.sku, p.marca, p.precio, p.precio_oferta,
                    p.tiene_iva, p.porcentaje_iva, c.nombre AS categoria_nombre
             FROM productos p
             LEFT JOIN categorias c ON p.categoria_id = c.id
             WHERE $where
             ORDER BY p.nombre ASC
             LIMIT :l OFFSET :o",
            $params
        );

        $resultado = ['items' => $productos, 'total' => $total, 'pagina' => $pagina,
                      'total_paginas' => ceil($total / ADMIN_ITEMS_POR_PAGINA)];

        $tituloAdmin = 'IVA por Producto';
        $modulo      = 'configuracion';
        $breadcrumb  = [
            ['label' => 'Configuración IVA y Envío', 'url' => BASE_URL . '/admin/configuracion'],
            ['label' => 'IVA por Producto'],
        ];
        require BASE_PATH . '/admin/views/config/iva-productos.php';
    }

    // ============================================================
    //  CRUD ZONAS DE ENVÍO
    // ============================================================

    public function crearZona(): void {
        $zona   = null;
        $errores = [];
        $tituloAdmin = 'Nueva Zona de Envío';
        $modulo      = 'configuracion';
        $breadcrumb  = [
            ['label' => 'Config IVA y Envío', 'url' => BASE_URL . '/admin/configuracion'],
            ['label' => 'Nueva Zona'],
        ];
        require BASE_PATH . '/admin/views/config/zona-form.php';
    }

    public function editarZona(int $id): void {
        $zona = $this->model->obtenerZonaPorId($id);
        if (!$zona) { adminSetFlash('danger', 'Zona no encontrada.'); adminRedirect('/configuracion'); }
        $errores     = [];
        $tituloAdmin = 'Editar Zona de Envío';
        $modulo      = 'configuracion';
        $breadcrumb  = [
            ['label' => 'Config IVA y Envío', 'url' => BASE_URL . '/admin/configuracion'],
            ['label' => 'Editar: ' . e($zona['nombre'])],
        ];
        require BASE_PATH . '/admin/views/config/zona-form.php';
    }

    public function guardarZona(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') adminRedirect('/configuracion');
        if (!adminCsrfCheck($_POST['_csrf'] ?? '')) {
            adminSetFlash('danger', 'Error de seguridad.');
            adminRedirect('/configuracion');
        }

        $id      = (int)($_POST['id'] ?? 0);
        $errores = [];
        if (empty(trim($_POST['nombre'] ?? ''))) $errores[] = 'El nombre es requerido.';
        if (!is_numeric($_POST['costo'] ?? ''))   $errores[] = 'El costo debe ser un número.';

        if (!empty($errores)) {
            $zona        = $id ? $this->model->obtenerZonaPorId($id) : null;
            $tituloAdmin = $id ? 'Editar Zona' : 'Nueva Zona';
            $modulo      = 'configuracion';
            $breadcrumb  = [['label' => 'Config', 'url' => BASE_URL . '/admin/configuracion'], ['label' => $tituloAdmin]];
            require BASE_PATH . '/admin/views/config/zona-form.php';
            return;
        }

        if ($id) {
            $ok = $this->model->actualizarZona($id, $_POST);
        } else {
            $ok = (bool)$this->model->crearZona($_POST);
        }

        adminSetFlash($ok ? 'success' : 'danger', $ok ? '✅ Zona guardada correctamente.' : 'Error al guardar.');
        adminRedirect('/configuracion');
    }

    public function eliminarZona(int $id): void {
        $ok = $this->model->eliminarZona($id);
        adminSetFlash($ok ? 'success' : 'danger', $ok ? 'Zona eliminada.' : 'Error al eliminar.');
        adminRedirect('/configuracion');
    }

    public function toggleZona(int $id): void {
        $this->model->toggleZona($id);
        adminSetFlash('success', 'Estado de la zona actualizado.');
        adminRedirect('/configuracion');
    }
}
