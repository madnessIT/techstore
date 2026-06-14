<?php
/**
 * TechStore - Vista: Error 404
 * Archivo: views/404.php
 */
$titulo = 'Página no encontrada - TechStore';
require BASE_PATH . '/views/partials/header.php';
?>
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:3rem 1rem;">
    <div>
        <div style="font-size:7rem;font-weight:900;color:#e9ecef;line-height:1;margin-bottom:1rem;font-family:'Space Mono',monospace;">404</div>
        <h1 class="h3 fw-800 mb-3">Página no encontrada</h1>
        <p class="text-muted mb-4 fs-5">El producto o página que buscas no existe o fue removido.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= BASE_URL ?>/" class="btn-ts-primary btn btn-lg"><i class="bi bi-house me-2"></i>Ir al Inicio</a>
            <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-outline btn btn-lg"><i class="bi bi-shop me-2"></i>Ver Catálogo</a>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/views/partials/footer.php'; ?>
