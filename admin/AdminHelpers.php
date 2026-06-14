<?php
/**
 * TechStore - Admin Helpers & Controllers
 * Archivo: admin/AdminHelpers.php
 */

function adminLogueado(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function adminRedirect(string $url): void {
    header('Location: ' . BASE_URL . '/admin' . $url);
    exit;
}

function adminSetFlash(string $tipo, string $mensaje): void {
    $_SESSION['admin_flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function adminGetFlash(): ?array {
    if (isset($_SESSION['admin_flash'])) {
        $f = $_SESSION['admin_flash'];
        unset($_SESSION['admin_flash']);
        return $f;
    }
    return null;
}

function adminCsrfGen(): string {
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function adminCsrfCheck(string $token): bool {
    return isset($_SESSION['admin_csrf']) && hash_equals($_SESSION['admin_csrf'], $token);
}

function e(mixed $v): string {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function formatearPrecio(float $p): string {
    return MONEDA . ' ' . number_format($p, 2, '.', ',');
}

function imgProducto(?string $img, string $tipo = 'default', string $marca = ''): string {
    if (empty($img)) {
        return BASE_URL . '/assets/images/products/img.php?f=no-image.jpg&t=' . urlencode($tipo) . '&m=' . urlencode($marca);
    }
    $ruta = PRODUCTS_IMG_PATH . $img;
    if (file_exists($ruta)) {
        return PRODUCTS_IMG_URL . $img;
    }
    return BASE_URL . '/assets/images/products/img.php?f=' . urlencode($img) . '&t=' . urlencode($tipo) . '&m=' . urlencode($marca);
}

function adminCan(string $rol): bool {
    $roles = ['superadmin' => 3, 'admin' => 2, 'vendedor' => 1];
    $miRol = $_SESSION['admin_rol'] ?? 'vendedor';
    return ($roles[$miRol] ?? 0) >= ($roles[$rol] ?? 99);
}
