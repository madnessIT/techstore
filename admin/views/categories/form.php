<?php
/**
 * TechStore - Vista Admin: Formulario Categoría
 * Archivo: admin/views/categories/form.php
 */
$esEditar    = !empty($categoria);
$tituloAdmin = $esEditar ? 'Editar Categoría' : 'Nueva Categoría';
$modulo      = 'categorias';
$breadcrumb  = [['label'=>'Categorías','url'=> BASE_URL.'/admin/categorias'],['label'=>$tituloAdmin]];
require BASE_PATH . '/admin/views/partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-800 mb-0"><?= $tituloAdmin ?></h1>
    <a href="<?= BASE_URL ?>/admin/categorias" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<?php if (!empty($errores)): ?>
<div class="alert alert-danger mb-4"><ul class="mb-0 ps-3"><?php foreach ($errores as $em): ?><li><?= e($em) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<div class="row justify-content-center">
<div class="col-lg-6">
<div class="admin-card">
    <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/admin/categorias/guardar">
        <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
        <?php if ($esEditar): ?><input type="hidden" name="id" value="<?= (int)$categoria['id'] ?>"><?php endif; ?>
        <div class="mb-3">
            <label class="admin-form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-control admin-form-control"
                   value="<?= e($categoria['nombre'] ?? '') ?>" required placeholder="Ej: Laptops">
        </div>
        <div class="mb-3">
            <label class="admin-form-label">Descripción</label>
            <textarea name="descripcion" class="form-control admin-form-control" rows="3"><?= e($categoria['descripcion'] ?? '') ?></textarea>
        </div>
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="admin-form-label">Icono Bootstrap</label>
                <div class="input-group">
                    <span class="input-group-text" id="iconoPreview">
                        <i class="bi <?= e($categoria['icono'] ?? 'bi-tag') ?>"></i>
                    </span>
                    <input type="text" name="icono" class="form-control admin-form-control"
                           value="<?= e($categoria['icono'] ?? 'bi-tag') ?>"
                           placeholder="bi-laptop" id="iconoInput">
                </div>
                <small class="text-muted">Ver íconos en <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a></small>
            </div>
            <div class="col-sm-6">
                <label class="admin-form-label">Orden</label>
                <input type="number" name="orden" class="form-control admin-form-control"
                       value="<?= e($categoria['orden'] ?? 0) ?>" min="0">
            </div>
        </div>
        <div class="form-check form-switch mt-3 mb-4">
            <input class="form-check-input" type="checkbox" name="activa" id="chkActiva"
                   <?= ($categoria['activa'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label fw-600" for="chkActiva">Categoría Activa</label>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary fw-700">
                <i class="bi bi-check-lg me-2"></i><?= $esEditar ? 'Guardar Cambios' : 'Crear Categoría' ?>
            </button>
            <a href="<?= BASE_URL ?>/admin/categorias" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
    </div>
</div>
</div>
</div>
<script>
document.getElementById('iconoInput')?.addEventListener('input', function() {
    const el = document.getElementById('iconoPreview')?.querySelector('i');
    if (el) { el.className = 'bi ' + this.value; }
});
</script>
<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
