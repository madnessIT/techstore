<?php
/**
 * TechStore - Router Principal (Front Controller)
 * Archivo: index.php
 * Descripción: Punto de entrada de la aplicación con routing MVC
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/ProductoModel.php';
require_once __DIR__ . '/models/Models.php';
require_once __DIR__ . '/models/ConfiguracionModel.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CatalogoController.php';
// Nota: CarritoController y CheckoutController están definidos dentro de CatalogoController.php

// ============================================================
// INICIAR SESIÓN
// ============================================================
ini_set('session.name', SESSION_NAME);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Regenerar session ID periódicamente
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// ============================================================
// ROUTING SIMPLE
// ============================================================
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$script = dirname($_SERVER['SCRIPT_NAME']);
$ruta   = '/' . trim(str_replace($script, '', $uri), '/');
$partes = array_values(array_filter(explode('/', trim($ruta, '/'))));

$controlador = $partes[0] ?? 'home';
$accion      = $partes[1] ?? 'index';
$param       = $partes[2] ?? null;

// Función helper para flash messages
function setFlash(string $tipo, string $mensaje): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Función helper para verificar login cliente
function clienteLogueado(): bool {
    return isset($_SESSION['cliente_id']) && !empty($_SESSION['cliente_id']);
}

// Función helper para obtener cliente actual
function clienteActual(): array {
        return [
        'id'       => $_SESSION['cliente_id'] ?? 0,
        'nombre'   => $_SESSION['cliente_nombre'] ?? '',
        'apellido' => $_SESSION['cliente_apellido'] ?? '',
        'email'    => $_SESSION['cliente_email'] ?? '',
    ];
}

// Función helper para redirect
function redirect(string $url): void {
    header("Location: " . BASE_URL . $url);
    exit;
}

// Función helper escape HTML
function e(mixed $valor): string {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

// Función para formatear precio
function formatearPrecio(float $precio): string {
    return MONEDA . ' ' . number_format($precio, 2, '.', ',');
}

// Función para URL de imagen de producto con fallback SVG automático
function imgProducto(string|null $img, string $tipo = 'default', string $marca = ''): string {
    if (empty($img)) {
        return BASE_URL . '/assets/images/products/img.php?f=no-image.jpg&t=' . urlencode($tipo) . '&m=' . urlencode($marca);
    }
    $ruta = PRODUCTS_IMG_PATH . $img;
    if (file_exists($ruta)) {
        return PRODUCTS_IMG_URL . $img;
    }
    // Fallback al servicio dinámico SVG
    return BASE_URL . '/assets/images/products/img.php?f=' . urlencode($img) . '&t=' . urlencode($tipo) . '&m=' . urlencode($marca);
}

// Función para determinar tipo de imagen según categoría
function tipoImagenCategoria(string|null $categoriaNombre): string {
    if (!$categoriaNombre) return 'default';
    $n = strtolower($categoriaNombre);
    if (str_contains($n, 'laptop'))     return 'laptop';
    if (str_contains($n, 'gaming'))     return 'gaming';
    if (str_contains($n, 'escritorio') || str_contains($n, 'desktop')) return 'pc';
    if (str_contains($n, 'monitor'))    return 'monitor';
    if (str_contains($n, 'impresora')) return 'printer';
    if (str_contains($n, 'red')  || str_contains($n, 'router') || str_contains($n, 'switch')) return 'router';
    if (str_contains($n, 'teclado') || str_contains($n, 'accesorio')) return 'keyboard';
    if (str_contains($n, 'auricular') || str_contains($n, 'audio')) return 'headset';
    if (str_contains($n, 'componente') || str_contains($n, 'procesador')) return 'cpu';
    if (str_contains($n, 'almacenamiento') || str_contains($n, 'ssd')) return 'ssd';
    return 'default';
}

/**
 * Obtener configuración de envío desde la BD (con caché en sesión de 5 minutos)
 * Siempre usa valores frescos de la BD, nunca las constantes de config.php
 */
function getConfigEnvio(): array {
    $cacheKey = '_config_envio_cache';
    $cacheExp = '_config_envio_exp';

    // Usar caché de sesión si es reciente (5 minutos)
    if (
        !empty($_SESSION[$cacheKey]) &&
        !empty($_SESSION[$cacheExp]) &&
        time() < $_SESSION[$cacheExp]
    ) {
        return $_SESSION[$cacheKey];
    }

    // Leer desde la BD
    try {
        $db = Database::getInstance();
        $config = $db->queryOne("SELECT costo_envio, envio_gratis_desde, envio_activo, envio_gratis_activo, iva_porcentaje, envio_tiempo_estimado, envio_nota FROM configuracion_empresa WHERE id = 1");

        if ($config) {
            $resultado = [
                'costo_envio'          => (float)$config['costo_envio'],
                'envio_gratis_desde'   => (float)$config['envio_gratis_desde'],
                'envio_activo'         => (bool)($config['envio_activo'] ?? true),
                'envio_gratis_activo'  => (bool)($config['envio_gratis_activo'] ?? true),
                'iva_porcentaje'       => (float)($config['iva_porcentaje'] ?? IVA_PORCENTAJE),
                'tiempo_estimado'      => $config['envio_tiempo_estimado'] ?? '1-3 días hábiles',
                'envio_nota'           => $config['envio_nota'] ?? '',
            ];
        } else {
            // Fallback a constantes si no hay config en BD
            $resultado = [
                'costo_envio'         => COSTO_ENVIO,
                'envio_gratis_desde'  => ENVIO_GRATIS_DESDE,
                'envio_activo'        => true,
                'envio_gratis_activo' => true,
                'iva_porcentaje'      => IVA_PORCENTAJE,
                'tiempo_estimado'     => '1-3 días hábiles',
                'envio_nota'          => '',
            ];
        }
    } catch (\Exception $e) {
        $resultado = [
            'costo_envio'         => COSTO_ENVIO,
            'envio_gratis_desde'  => ENVIO_GRATIS_DESDE,
            'envio_activo'        => true,
            'envio_gratis_activo' => true,
            'iva_porcentaje'      => IVA_PORCENTAJE,
            'tiempo_estimado'     => '1-3 días hábiles',
            'envio_nota'          => '',
        ];
    }

    // Guardar en caché de sesión por 5 minutos
    $_SESSION[$cacheKey] = $resultado;
    $_SESSION[$cacheExp] = time() + 300;

    return $resultado;
}

/**
 * Calcular costo de envío según subtotal y ciudad (opcional)
 * Usa siempre valores de la BD
 */
function calcularCostoEnvio(float $subtotal, string $ciudad = ''): array {
    $cfg = getConfigEnvio();

    if (!$cfg['envio_activo']) {
        return ['costo' => 0, 'gratis' => false, 'motivo' => 'solo_retiro'];
    }

    // Verificar envío gratis por monto mínimo
    if ($cfg['envio_gratis_activo'] && $subtotal >= $cfg['envio_gratis_desde'] && $subtotal > 0) {
        return ['costo' => 0, 'gratis' => true, 'motivo' => 'monto_minimo', 'desde' => $cfg['envio_gratis_desde']];
    }

    if ($subtotal <= 0) {
        return ['costo' => 0, 'gratis' => false, 'motivo' => 'carrito_vacio'];
    }

    // Si hay ciudad, buscar zona específica
    if (!empty($ciudad)) {
        try {
            $db = Database::getInstance();
            $zonas = $db->query("SELECT * FROM zonas_envio WHERE activa = 1 ORDER BY orden ASC");
            $ciudadLower = strtolower(trim($ciudad));

            foreach ($zonas as $zona) {
                if (empty($zona['ciudades'])) continue;
                $ciudadesZona = json_decode($zona['ciudades'], true) ?? [];
                foreach ($ciudadesZona as $c) {
                    if (strtolower(trim($c)) === $ciudadLower) {
                        return [
                            'costo'   => (float)$zona['costo'],
                            'gratis'  => false,
                            'motivo'  => 'zona',
                            'zona'    => $zona['nombre'],
                            'tiempo'  => $zona['tiempo_estimado'],
                            'desde'   => $cfg['envio_gratis_desde'],
                        ];
                    }
                }
            }
            // Zona nacional (sin ciudades)
            foreach ($zonas as $zona) {
                if (empty($zona['ciudades'])) {
                    return [
                        'costo'  => (float)$zona['costo'],
                        'gratis' => false,
                        'motivo' => 'zona_nacional',
                        'zona'   => $zona['nombre'],
                        'tiempo' => $zona['tiempo_estimado'],
                        'desde'  => $cfg['envio_gratis_desde'],
                    ];
                }
            }
        } catch (\Exception $e) {
            // Si falla la consulta de zonas, usar costo por defecto
        }
    }

    return [
        'costo'  => $cfg['costo_envio'],
        'gratis' => false,
        'motivo' => 'default',
        'desde'  => $cfg['envio_gratis_desde'],
    ];
}

// CSRF Token
function generarCsrf(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verificarCsrf(string $token): bool {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// ============================================================
// DISPATCH DE RUTAS
// ============================================================
switch ($controlador) {
    
    // ---- AUTENTICACIÓN ----
    case 'login':
        $ctrl = new AuthController();
        $ctrl->loginCliente();
        break;
    
    case 'registro':
        $ctrl = new AuthController();
        $ctrl->registroCliente();
        break;
    
    case 'logout':
        $ctrl = new AuthController();
        $ctrl->logout();
        break;
    
    case 'mi-cuenta':
        $ctrl = new AuthController();
        $ctrl->miCuenta();
        break;
    
    // ---- CATÁLOGO ----
    case 'catalogo':
        $ctrl = new CatalogoController();
        $ctrl->index();
        break;
    
    case 'producto':
        $ctrl = new CatalogoController();
        $ctrl->detalle($accion); // accion = slug del producto
        break;
    
    case 'categoria':
        $ctrl = new CatalogoController();
        $ctrl->porCategoria($accion); // accion = slug de categoría
        break;
    
    case 'buscar':
        $ctrl = new CatalogoController();
        $ctrl->buscar();
        break;
    
    // ---- AJAX: calcular envío por ciudad ----
    case 'api':
        if ($accion === 'envio' && isset($_GET['ciudad'])) {
            header('Content-Type: application/json');
            $ciudad   = trim($_GET['ciudad'] ?? '');
            $subtotal = (float)($_GET['subtotal'] ?? 0);
            $info     = calcularCostoEnvio($subtotal, $ciudad);
            $cfg      = getConfigEnvio();
            echo json_encode([
                'costo'   => $info['costo'],
                'gratis'  => $info['gratis'],
                'zona'    => $info['zona'] ?? '',
                'tiempo'  => $info['tiempo'] ?? $cfg['tiempo_estimado'],
                'desde'   => $cfg['envio_gratis_desde'],
            ]);
            exit;
        }
        break;

    // ---- CARRITO ----
    case 'carrito':
        $ctrl = new CarritoController();
        switch ($accion) {
            case 'agregar':  $ctrl->agregar();  break;
            case 'actualizar': $ctrl->actualizar(); break;
            case 'eliminar': $ctrl->eliminar(); break;
            case 'contar':   $ctrl->contar();   break;
            default:         $ctrl->index();    break;
        }
        break;
    
    // ---- CHECKOUT ----
    case 'checkout':
        $ctrl = new CheckoutController();
        switch ($accion) {
            case 'confirmar': $ctrl->confirmar(); break;
            case 'gracias':   $ctrl->gracias($param); break;
            default:          $ctrl->index();    break;
        }
        break;
    
    // ---- HOME ----
    default:
        $ctrl = new CatalogoController();
        $ctrl->home();
        break;
}
