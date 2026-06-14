<?php
/**
 * Vista Admin: Formulario Zona de Envío
 * Archivo: admin/views/config/zona-form.php
 */
$esEditar = !empty($zona);
require BASE_PATH . '/admin/views/partials/header.php';
$ciudadesTexto = '';
if ($esEditar && !empty($zona['ciudades'])) {
    $ciudadesTexto = is_array($zona['ciudades'])
        ? implode("\n", $zona['ciudades'])
        : implode("\n", json_decode($zona['ciudades'], true) ?? []);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-800 mb-0"><?= $tituloAdmin ?></h1>
    <a href="<?= BASE_URL ?>/admin/configuracion" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<?php if (!empty($errores)): ?>
<div class="alert alert-danger mb-4">
    <ul class="mb-0 ps-3">
        <?php foreach ($errores as $em): ?><li class="small"><?= e($em) ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="admin-card">
    <div class="card-header">
        <i class="bi bi-geo-alt text-primary me-2"></i>
        <?= $esEditar ? 'Editar zona: ' . e($zona['nombre']) : 'Datos de la nueva zona' ?>
    </div>
    <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/admin/configuracion/zona-guardar">
        <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
        <?php if ($esEditar): ?>
        <input type="hidden" name="id" value="<?= (int)$zona['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-12">
                <label class="admin-form-label">Nombre de la zona *</label>
                <input type="text" name="nombre" class="form-control admin-form-control"
                       value="<?= e($zona['nombre'] ?? '') ?>"
                       placeholder="Ej: La Paz / El Alto" required>
            </div>

            <div class="col-12">
                <label class="admin-form-label">Descripción</label>
                <input type="text" name="descripcion" class="form-control admin-form-control"
                       value="<?= e($zona['descripcion'] ?? '') ?>"
                       placeholder="Ej: Entrega en área metropolitana de La Paz">
            </div>

            <div class="col-12">
                <label class="admin-form-label">
                    Ciudades incluidas
                    <small class="text-muted fw-400">(una por línea o separadas por coma)</small>
                </label>
                <textarea name="ciudades" class="form-control admin-form-control" rows="4"
                          placeholder="La Paz&#10;El Alto&#10;Viacha"><?= e($ciudadesTexto) ?></textarea>
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Deja vacío para que aplique como zona de <strong>cobertura nacional</strong> (cualquier ciudad no listada en otras zonas).
                </small>
            </div>

            <div class="col-sm-6">
                <label class="admin-form-label">Costo de Envío Normal (Bs.) *</label>
                <div class="input-group">
                    <span class="input-group-text">Bs.</span>
                    <input type="number" name="costo" class="form-control admin-form-control"
                           value="<?= e($zona['costo'] ?? '') ?>"
                           min="0" step="0.50" placeholder="25.00" required>
                </div>
            </div>

            <div class="col-sm-6">
                <label class="admin-form-label">
                    Costo Express (Bs.)
                    <small class="text-muted fw-400">opcional</small>
                </label>
                <div class="input-group">
                    <span class="input-group-text text-warning"><i class="bi bi-lightning-fill"></i></span>
                    <input type="number" name="costo_express" class="form-control admin-form-control"
                           value="<?= e($zona['costo_express'] ?? '') ?>"
                           min="0" step="0.50" placeholder="50.00 (dejar vacío si no aplica)">
                </div>
            </div>

            <div class="col-sm-8">
                <label class="admin-form-label">Tiempo estimado de entrega</label>
                <input type="text" name="tiempo_estimado" class="form-control admin-form-control"
                       value="<?= e($zona['tiempo_estimado'] ?? '1-3 días hábiles') ?>"
                       placeholder="Ej: 1-3 días hábiles">
                <div class="mt-2 d-flex gap-1 flex-wrap">
                    <?php foreach (['Mismo día', '1-2 días hábiles', '2-3 días hábiles', '3-5 días hábiles', '5-7 días hábiles'] as $t): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0"
                            onclick="document.querySelector('[name=tiempo_estimado]').value='<?= $t ?>'">
                        <?= $t ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-sm-4">
                <label class="admin-form-label">Orden</label>
                <input type="number" name="orden" class="form-control admin-form-control"
                       value="<?= e($zona['orden'] ?? 0) ?>" min="0">
                <small class="text-muted">Menor número = aparece primero</small>
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="activa" id="chkActiva"
                           <?= (!$esEditar || !empty($zona['activa'])) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-600" for="chkActiva">Zona Activa</label>
                </div>
            </div>
        </div>

        <!-- Vista previa del costo -->
        <div class="mt-4 p-3 bg-light rounded-3" id="previewZona">
            <p class="small fw-700 mb-2"><i class="bi bi-eye me-1"></i>Vista previa:</p>
            <div class="d-flex gap-3 flex-wrap">
                <div>
                    <small class="text-muted d-block">Envío normal</small>
                    <span class="fw-800 text-primary fs-5" id="prevCosto">
                        Bs. <?= number_format($zona['costo'] ?? 0, 2) ?>
                    </span>
                </div>
                <div id="prevExpressWrap" style="<?= empty($zona['costo_express']) ? 'display:none' : '' ?>">
                    <small class="text-muted d-block">Express</small>
                    <span class="fw-800 text-warning fs-5" id="prevExpress">
                        Bs. <?= number_format($zona['costo_express'] ?? 0, 2) ?>
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block">Tiempo</small>
                    <span class="fw-600 small" id="prevTiempo"><?= e($zona['tiempo_estimado'] ?? '1-3 días hábiles') ?></span>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg fw-700">
                <i class="bi bi-check-lg me-2"></i><?= $esEditar ? 'Guardar Cambios' : 'Crear Zona de Envío' ?>
            </button>
            <a href="<?= BASE_URL ?>/admin/configuracion" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
    </div>
</div>
</div>
</div>

<script>
// Preview en tiempo real
const costoInput   = document.querySelector('[name=costo]');
const expressInput = document.querySelector('[name=costo_express]');
const tiempoInput  = document.querySelector('[name=tiempo_estimado]');

function actualizarPreview() {
    const costo   = parseFloat(costoInput?.value || 0);
    const express = parseFloat(expressInput?.value || 0);
    const tiempo  = tiempoInput?.value || '';

    const prevCosto = document.getElementById('prevCosto');
    const prevExpress = document.getElementById('prevExpress');
    const prevExpressWrap = document.getElementById('prevExpressWrap');
    const prevTiempo = document.getElementById('prevTiempo');

    if (prevCosto) prevCosto.textContent = 'Bs. ' + costo.toLocaleString('es-BO', {minimumFractionDigits:2});
    if (prevExpress) prevExpress.textContent = 'Bs. ' + express.toLocaleString('es-BO', {minimumFractionDigits:2});
    if (prevExpressWrap) prevExpressWrap.style.display = express > 0 ? '' : 'none';
    if (prevTiempo) prevTiempo.textContent = tiempo;
}

costoInput?.addEventListener('input', actualizarPreview);
expressInput?.addEventListener('input', actualizarPreview);
tiempoInput?.addEventListener('input', actualizarPreview);
</script>

<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
