<?php
/**
 * TechStore - Panel Administrativo (Admin Router)
 * Archivo: admin/index.php
 * Descripción: Front Controller del panel de administración
 */

define('ADMIN_PANEL', true);

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/models/ProductoModel.php';
require_once dirname(__DIR__) . '/models/Models.php';
require_once dirname(__DIR__) . '/models/ConfiguracionModel.php';

// Funciones helpers (reutilizadas del index principal)
require_once __DIR__ . '/AdminHelpers.php';

ini_set('session.name', ADMIN_SESSION);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_start();

// ============================================================
// DISPATCH ADMIN
// ============================================================
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script = dirname($_SERVER['SCRIPT_NAME']);
$ruta   = '/' . trim(str_replace($script, '', $uri), '/');
$partes = array_values(array_filter(explode('/', trim($ruta, '/'))));

$modulo = $partes[0] ?? 'dashboard';
$accion = $partes[1] ?? 'index';
$param  = $partes[2] ?? null;

// Verificar autenticación (excepto login)
if ($modulo !== 'login' && !adminLogueado()) {
    adminRedirect('/login');
}

// Todos los controladores admin están definidos en un solo archivo
require_once __DIR__ . '/controllers/AdminAuthController.php';

switch ($modulo) {
    case 'login':
        $ctrl = new AdminAuthController();
        $ctrl->login();
        break;

    case 'logout':
        session_destroy();
        adminRedirect('/login');
        break;

    case 'productos':
        $ctrl = new AdminProductoController();
        match ($accion) {
            'crear'    => $ctrl->crear(),
            'editar'   => $ctrl->editar((int)$param),
            'eliminar' => $ctrl->eliminar((int)$param),
            'guardar'  => $ctrl->guardar(),
            default    => $ctrl->index(),
        };
        break;

    case 'categorias':
        $ctrl = new AdminCategoriaController();
        match ($accion) {
            'crear'    => $ctrl->crear(),
            'editar'   => $ctrl->editar((int)$param),
            'eliminar' => $ctrl->eliminar((int)$param),
            'guardar'  => $ctrl->guardar(),
            default    => $ctrl->index(),
        };
        break;

    case 'pedidos':
        $ctrl = new AdminPedidoController();
        match ($accion) {
            'ver'    => $ctrl->ver((int)$param),
            'estado' => $ctrl->cambiarEstado((int)$param),
            default  => $ctrl->index(),
        };
        break;

    case 'clientes':
        $ctrl = new AdminClienteController();
        match ($accion) {
            'ver'    => $ctrl->ver((int)$param),
            'toggle' => $ctrl->toggle((int)$param),
            default  => $ctrl->index(),
        };
        break;

    case 'usuarios':
        $ctrl = new AdminUsuarioController();
        match ($accion) {
            'crear'    => $ctrl->crear(),
            'editar'   => $ctrl->editar((int)$param),
            'eliminar' => $ctrl->eliminar((int)$param),
            'guardar'  => $ctrl->guardar(),
            default    => $ctrl->index(),
        };
        break;

    case 'reportes':
        $ctrl = new AdminReporteController();
        $ctrl->index();
        break;

    case 'configuracion':
        require __DIR__ . '/controllers/AdminConfiguracionController.php';
        $ctrl = new AdminConfiguracionController();
        match ($accion) {
            'guardar-iva'              => $ctrl->guardarIva(),
            'guardar-envio'            => $ctrl->guardarEnvio(),
            'iva-productos'            => $ctrl->ivaProductos(),
            'actualizar-iva-productos' => $ctrl->actualizarIvaProductos(),
            'zona-crear'               => $ctrl->crearZona(),
            'zona-editar'              => $ctrl->editarZona((int)$param),
            'zona-guardar'             => $ctrl->guardarZona(),
            'zona-eliminar'            => $ctrl->eliminarZona((int)$param),
            'zona-toggle'              => $ctrl->toggleZona((int)$param),
            default                    => $ctrl->index(),
        };
        break;

    default: // dashboard
        $ctrl = new AdminDashboardController();
        $ctrl->index();
        break;
}
