<?php
/**
 * TechStore - Configuración de la Aplicación
 * Archivo: config/config.php
 * Descripción: Constantes globales de configuración
 */

// ============================================================
// ENTORNO
// ============================================================
define('ENTORNO', getenv('ENTORNO') ?: 'desarrollo'); // 'desarrollo' | 'produccion'

// ============================================================
// BASE DE DATOS
// ============================================================
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'techstore');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'M4nd4m4s'); // Cambiar en producción
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// ============================================================
// URL BASE (sin barra final)
// ============================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptPath = ($scriptPath === '\\' || $scriptPath === '/') ? '' : $scriptPath;
define('BASE_URL', getenv('BASE_URL') ?: $protocol . $host . $scriptPath);
define('BASE_PATH', dirname(__DIR__)); // Ruta absoluta al proyecto

// ============================================================
// RUTAS DE ARCHIVOS
// ============================================================
define('UPLOADS_PATH', BASE_PATH . '/assets/uploads/');
define('PRODUCTS_IMG_PATH', BASE_PATH . '/assets/images/products/');
define('UPLOADS_URL', BASE_URL . '/assets/uploads/');
define('PRODUCTS_IMG_URL', BASE_URL . '/assets/images/products/');

// ============================================================
// SESIONES
// ============================================================
define('SESSION_NAME', 'techstore_session');
define('SESSION_LIFETIME', 7200); // 2 horas en segundos
define('ADMIN_SESSION', 'techstore_admin');
define('CART_SESSION', 'cart');

// ============================================================
// SEGURIDAD
// ============================================================
define('SALT', 'TechStore@2025#SecureKey!Bolivia');
define('CSRF_TOKEN_NAME', '_csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos

// ============================================================
// PAGINACIÓN
// ============================================================
define('ITEMS_POR_PAGINA', 12);
define('ADMIN_ITEMS_POR_PAGINA', 15);

// ============================================================
// CORREO (configurar con datos reales)
// ============================================================
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'noreply@techstore.bo');
define('MAIL_PASS', 'tu_password_aqui');
define('MAIL_FROM_NAME', 'TechStore Bolivia');

// ============================================================
// EMPRESA
// ============================================================
define('EMPRESA_NOMBRE', 'TechStore Bolivia');
define('EMPRESA_EMAIL', 'info@techstore.bo');
define('EMPRESA_TELEFONO', '+591 2 123-4567');
define('MONEDA', 'Bs.');
define('IVA_PORCENTAJE', 13.00);
define('ENVIO_GRATIS_DESDE', 500.00);
define('COSTO_ENVIO', 25.00);

// ============================================================
// MENSAJES DE ERROR
// ============================================================
if (ENTORNO === 'desarrollo') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}
