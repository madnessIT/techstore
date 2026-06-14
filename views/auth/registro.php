<?php
/**
 * TechStore - Vista: Registro de Cliente
 * Archivo: views/auth/registro.php
 */
$titulo = 'Crear Cuenta - TechStore';
require BASE_PATH . '/views/partials/header.php';
?>
<div style="min-height:80vh;display:flex;align-items:center;padding:3rem 0;background:linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%);">
<div class="container">
<div class="row justify-content-center">
<div class="col-md-10 col-lg-7 col-xl-6">
<div class="ts-auth-card">
    <div class="text-center mb-4">
        <a href="<?= BASE_URL ?>/" class="ts-brand justify-content-center text-decoration-none d-flex mb-3">
            <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
            <span class="brand-text" style="color:var(--ts-dark);">Tech<span class="brand-highlight">Store</span></span>
        </a>
        <h1 class="h4 fw-800 mb-1">Crea tu cuenta</h1>
        <p class="text-muted small">Regístrate para comprar más fácil y llevar el historial de tus pedidos</p>
    </div>

    <?php if (!empty($errores)): ?>
    <div class="alert alert-danger py-2 mb-3">
        <ul class="mb-0 ps-3">
            <?php foreach ($errores as $err): ?>
            <li class="small"><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/registro" novalidate>
        <input type="hidden" name="_csrf" value="<?= generarCsrf() ?>">

        <div class="row g-3">
            <div class="col-sm-6">
                <label class="ts-form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control ts-form-input"
                       value="<?= e($datos['nombre'] ?? '') ?>"
                       placeholder="Tu nombre" required minlength="2">
            </div>
            <div class="col-sm-6">
                <label class="ts-form-label">Apellido *</label>
                <input type="text" name="apellido" class="form-control ts-form-input"
                       value="<?= e($datos['apellido'] ?? '') ?>"
                       placeholder="Tu apellido" required minlength="2">
            </div>
            <div class="col-12">
                <label class="ts-form-label">Correo Electrónico *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control ts-form-input border-start-0"
                           value="<?= e($datos['email'] ?? '') ?>"
                           placeholder="tu@correo.com" required autocomplete="email">
                </div>
            </div>
            <div class="col-sm-6">
                <label class="ts-form-label">Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" id="password"
                           class="form-control ts-form-input border-start-0 border-end-0"
                           placeholder="Mínimo 8 caracteres" required minlength="8" autocomplete="new-password">
                    <button class="input-group-text bg-light" type="button" data-toggle-password="password">
                        <i class="bi bi-eye text-muted"></i>
                    </button>
                </div>
            </div>
            <div class="col-sm-6">
                <label class="ts-form-label">Confirmar Contraseña *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                    <input type="password" name="password2" id="password2"
                           class="form-control ts-form-input border-start-0 border-end-0"
                           placeholder="Repite tu contraseña" required minlength="8" autocomplete="new-password">
                    <button class="input-group-text bg-light" type="button" data-toggle-password="password2">
                        <i class="bi bi-eye text-muted"></i>
                    </button>
                </div>
            </div>
            <div class="col-sm-6">
                <label class="ts-form-label">Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-phone text-muted"></i></span>
                    <input type="tel" name="telefono" class="form-control ts-form-input border-start-0"
                           value="<?= e($datos['telefono'] ?? '') ?>" placeholder="7XXXXXXX">
                </div>
            </div>
            <div class="col-sm-6">
                <label class="ts-form-label">Ciudad</label>
                <select name="ciudad" class="form-select ts-form-input">
                    <option value="">Seleccionar...</option>
                    <?php foreach (['La Paz','El Alto','Cochabamba','Santa Cruz','Oruro','Potosí','Sucre','Trinidad','Cobija'] as $c): ?>
                    <option value="<?= $c ?>" <?= ($datos['ciudad'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
                    <label class="form-check-label small text-muted" for="terminos">
                        Acepto los <a href="#" class="text-primary">Términos y Condiciones</a> y la
                        <a href="#" class="text-primary">Política de Privacidad</a>
                    </label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-700 fs-5">
                    <i class="bi bi-person-plus me-2"></i>Crear Cuenta Gratis
                </button>
            </div>
        </div>
    </form>

    <hr class="my-4">
    <p class="text-center text-muted mb-0">
        ¿Ya tienes cuenta?
        <a href="<?= BASE_URL ?>/login" class="text-primary fw-600 text-decoration-none">Iniciar Sesión</a>
    </p>
</div>
</div>
</div>
</div>
</div>
<?php require BASE_PATH . '/views/partials/footer.php'; ?>
