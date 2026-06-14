<?php
/**
 * TechStore - Vista: Login Cliente
 * Archivo: views/auth/login.php
 */
$titulo = 'Iniciar Sesión - TechStore';
require BASE_PATH . '/views/partials/header.php';
?>
<div style="min-height:80vh;display:flex;align-items:center;padding:3rem 0;background:linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <div class="ts-auth-card">
                    <div class="text-center mb-4">
                        <a href="<?= BASE_URL ?>/" class="ts-brand justify-content-center text-decoration-none d-flex">
                            <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
                            <span class="brand-text" style="color:var(--ts-dark);">Tech<span class="brand-highlight">Store</span></span>
                        </a>
                        <h1 class="h4 fw-800 mt-3 mb-1">Bienvenido de vuelta</h1>
                        <p class="text-muted small mb-0">Ingresa tus credenciales para continuar</p>
                    </div>

                    <?php if ($error): ?>
                    <div class="alert alert-danger py-2 d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-x-circle-fill"></i>
                        <span><?= e($error) ?></span>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/login" novalidate>
                        <input type="hidden" name="_csrf" value="<?= generarCsrf() ?>">

                        <div class="mb-3">
                            <label class="ts-form-label" for="email">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope text-muted"></i>
                                </span>
                                <input type="email" name="email" id="email"
                                       class="form-control ts-form-input border-start-0"
                                       placeholder="tu@correo.com"
                                       value="<?= e($_POST['email'] ?? '') ?>"
                                       required autocomplete="email">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <label class="ts-form-label" for="password">Contraseña</label>
                                <a href="#" class="text-primary small text-decoration-none">¿Olvidaste tu contraseña?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" id="password"
                                       class="form-control ts-form-input border-start-0 border-end-0"
                                       placeholder="Tu contraseña"
                                       required autocomplete="current-password">
                                <button class="input-group-text bg-light cursor-pointer border-start-0"
                                        type="button" data-toggle-password="password">
                                    <i class="bi bi-eye text-muted"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="recordar" name="recordar">
                            <label class="form-check-label text-muted small" for="recordar">Mantener sesión iniciada</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-700 fs-5">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center text-muted mb-0">
                        ¿No tienes cuenta?
                        <a href="<?= BASE_URL ?>/registro" class="text-primary fw-600 text-decoration-none">
                            Regístrate gratis
                        </a>
                    </p>
                    <p class="text-center mt-2">
                        <a href="<?= BASE_URL ?>/" class="text-muted small text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/views/partials/footer.php'; ?>
