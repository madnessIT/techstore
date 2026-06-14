<?php
/**
 * TechStore - Vista Admin: Formulario Producto
 * Archivo: admin/views/products/form.php
 */
$esEditar    = !empty($producto);
$tituloAdmin = $esEditar ? 'Editar Producto' : 'Nuevo Producto';
$modulo      = 'productos';
$breadcrumb  = [
    ['label' => 'Productos', 'url' => BASE_URL . '/admin/productos'],
    ['label' => $tituloAdmin],
];
require BASE_PATH . '/admin/views/partials/header.php';

// Especificaciones actuales
$specs = [];
if ($esEditar && !empty($producto['especificaciones'])) {
    $decoded = is_array($producto['especificaciones'])
        ? $producto['especificaciones']
        : json_decode($producto['especificaciones'], true);
    $specs = $decoded ?? [];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0"><?= $tituloAdmin ?></h1>
        <p class="text-muted small mb-0"><?= $esEditar ? 'Modifica los datos del producto' : 'Completa el formulario para agregar un nuevo producto' ?></p>
    </div>
    <a href="<?= BASE_URL ?>/admin/productos" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<?php if (!empty($errores)): ?>
<div class="alert alert-danger mb-4">
    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-2 ps-3">
        <?php foreach ($errores as $e_msg): ?>
        <li><?= e($e_msg) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/admin/productos/guardar"
      enctype="multipart/form-data" novalidate id="productForm">
    <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
    <?php if ($esEditar): ?>
    <input type="hidden" name="id" value="<?= (int)$producto['id'] ?>">
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna principal -->
        <div class="col-lg-8">

            <!-- Información básica -->
            <div class="admin-card mb-4">
                <div class="card-header"><i class="bi bi-info-circle text-primary me-2"></i>Información Básica</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="admin-form-label">Nombre del Producto *</label>
                            <input type="text" name="nombre" class="form-control admin-form-control"
                                   value="<?= e($producto['nombre'] ?? $_POST['nombre'] ?? '') ?>"
                                   placeholder="Ej: Laptop HP Pavilion 15 Intel Core i7" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="admin-form-label">Marca</label>
                            <input type="text" name="marca" class="form-control admin-form-control"
                                   value="<?= e($producto['marca'] ?? $_POST['marca'] ?? '') ?>"
                                   placeholder="HP, Dell, ASUS..." list="marcasList">
                            <datalist id="marcasList">
                                <?php foreach (['HP','Dell','ASUS','Lenovo','Samsung','LG','Intel','NVIDIA','Kingston','TP-Link','Epson','Logitech','Sony','AMD','MSI','Gigabyte'] as $m): ?>
                                <option value="<?= $m ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="col-sm-6">
                            <label class="admin-form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control admin-form-control"
                                   value="<?= e($producto['modelo'] ?? $_POST['modelo'] ?? '') ?>"
                                   placeholder="Ej: Pavilion 15-eh2001la">
                        </div>
                        <div class="col-12">
                            <label class="admin-form-label">Descripción Corta</label>
                            <textarea name="descripcion_corta" class="form-control admin-form-control" rows="2"
                                      maxlength="500" placeholder="Resumen en máximo 500 caracteres..."><?= e($producto['descripcion_corta'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="admin-form-label">Descripción Completa</label>
                            <textarea name="descripcion" class="form-control admin-form-control" rows="5"
                                      placeholder="Descripción detallada del producto..."><?= e($producto['descripcion'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Especificaciones técnicas -->
            <div class="admin-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check text-primary me-2"></i>Especificaciones Técnicas</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addSpec">
                        <i class="bi bi-plus-lg me-1"></i>Agregar
                    </button>
                </div>
                <div class="card-body">
                    <div id="specsContainer">
                        <?php if (!empty($specs)): ?>
                        <?php foreach ($specs as $key => $val): ?>
                        <div class="row g-2 mb-2 spec-row">
                            <div class="col-5">
                                <input type="text" name="spec_key[]" class="form-control admin-form-control form-control-sm"
                                       value="<?= e($key) ?>" placeholder="Ej: Procesador">
                            </div>
                            <div class="col-6">
                                <input type="text" name="spec_val[]" class="form-control admin-form-control form-control-sm"
                                       value="<?= e($val) ?>" placeholder="Ej: Intel Core i7-1165G7">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-sm btn-outline-danger removeSpec w-100">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="row g-2 mb-2 spec-row">
                            <div class="col-5"><input type="text" name="spec_key[]" class="form-control admin-form-control form-control-sm" placeholder="Ej: Procesador"></div>
                            <div class="col-6"><input type="text" name="spec_val[]" class="form-control admin-form-control form-control-sm" placeholder="Ej: Intel Core i7"></div>
                            <div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger removeSpec w-100"><i class="bi bi-x"></i></button></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Se mostrarán en la ficha del producto</p>
                </div>
            </div>

        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">

            <!-- Estado y categoría -->
            <div class="admin-card mb-4">
                <div class="card-header"><i class="bi bi-toggles text-primary me-2"></i>Configuración</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="admin-form-label">Categoría *</label>
                        <select name="categoria_id" class="form-select admin-form-control" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                    <?= ((int)($producto['categoria_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['nombre']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="admin-form-label">SKU *</label>
                        <input type="text" name="sku" class="form-control admin-form-control"
                               value="<?= e($producto['sku'] ?? '') ?>"
                               placeholder="Ej: LP-HP-001" required style="font-family:monospace;text-transform:uppercase;">
                    </div>
                    <div class="mb-3">
                        <label class="admin-form-label">Garantía</label>
                        <input type="text" name="garantia" class="form-control admin-form-control"
                               value="<?= e($producto['garantia'] ?? '') ?>"
                               placeholder="Ej: 1 año garantía HP">
                    </div>

                    <!-- IVA del producto -->
                    <div class="mb-3 p-3 rounded-3" style="background:#f0f7ff;border:1.5px solid #c7d9fc;">
                        <p class="fw-700 small mb-2">
                            <i class="bi bi-percent text-primary me-1"></i>Configuración de IVA
                        </p>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox"
                                   name="tiene_iva" id="chkTieneIva"
                                   <?= ($producto['tiene_iva'] ?? 1) ? 'checked' : '' ?>
                                   onchange="document.getElementById('ivaPersonalizadoWrap').style.display=this.checked?'':'none'">
                            <label class="form-check-label fw-600 small" for="chkTieneIva">
                                Aplica IVA a este producto
                            </label>
                        </div>
                        <div id="ivaPersonalizadoWrap"
                             style="<?= ($producto['tiene_iva'] ?? 1) ? '' : 'display:none' ?>">
                            <label class="admin-form-label small">
                                % IVA personalizado
                                <span class="text-muted fw-400">(dejar vacío = usar IVA global)</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number"
                                       name="porcentaje_iva"
                                       class="form-control admin-form-control"
                                       value="<?= e($producto['porcentaje_iva'] ?? '') ?>"
                                       min="0" max="100" step="0.01"
                                       placeholder="Ej: 13 (vacío = IVA global)">
                                <span class="input-group-text">%</span>
                            </div>
                            <?php if (!empty($producto['porcentaje_iva'])): ?>
                            <small class="text-warning fw-600 d-block mt-1">
                                <i class="bi bi-sliders me-1"></i>
                                IVA personalizado: <?= e($producto['porcentaje_iva']) ?>%
                            </small>
                            <?php else: ?>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Usando IVA global del sistema
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" id="chkActivo"
                                   <?= ($producto['activo'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-600" for="chkActivo">Activo</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="destacado" id="chkDestacado"
                                   <?= ($producto['destacado'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-600" for="chkDestacado">Destacado</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Precio y stock -->
            <div class="admin-card mb-4">
                <div class="card-header"><i class="bi bi-cash-stack text-primary me-2"></i>Precio y Stock</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="admin-form-label">Precio Regular (Bs.) *</label>
                        <div class="input-group">
                            <span class="input-group-text">Bs.</span>
                            <input type="number" name="precio" class="form-control admin-form-control"
                                   value="<?= e($producto['precio'] ?? '') ?>"
                                   min="0" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="admin-form-label">Precio de Oferta (Bs.) <small class="text-muted">opcional</small></label>
                        <div class="input-group">
                            <span class="input-group-text text-danger">%</span>
                            <input type="number" name="precio_oferta" class="form-control admin-form-control"
                                   value="<?= e($producto['precio_oferta'] ?? '') ?>"
                                   min="0" step="0.01" placeholder="0.00 (dejar vacío si no hay oferta)">
                        </div>
                        <small class="text-muted">Dejar vacío para sin oferta</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="admin-form-label">Stock</label>
                            <input type="number" name="stock" class="form-control admin-form-control"
                                   value="<?= e($producto['stock'] ?? 0) ?>" min="0">
                        </div>
                        <div class="col-6">
                            <label class="admin-form-label">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" class="form-control admin-form-control"
                                   value="<?= e($producto['stock_minimo'] ?? 5) ?>" min="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Imagen -->
            <div class="admin-card mb-4">
                <div class="card-header"><i class="bi bi-image text-primary me-2"></i>Imagen Principal</div>
                <div class="card-body text-center">
                    <?php if ($esEditar && !empty($producto['imagen_principal'])): ?>
                    <img id="imgPreview"
                         src="<?= imgProducto($producto['imagen_principal']) ?>"
                         style="max-height:160px;max-width:100%;object-fit:contain;border-radius:10px;background:#f8f9fa;padding:8px;"
                         class="mb-3 d-block mx-auto">
                    <?php else: ?>
                    <img id="imgPreview" src="" style="display:none;max-height:160px;border-radius:10px;" class="mb-3 d-block mx-auto">
                    <?php endif; ?>
                    <label class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-upload me-2"></i>
                        <?= $esEditar ? 'Cambiar Imagen' : 'Subir Imagen' ?>
                        <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"
                               class="d-none" data-preview="imgPreview">
                    </label>
                    <small class="text-muted d-block mt-2">JPG, PNG o WebP · Máx. 5MB</small>
                </div>
            </div>

            <!-- Botones -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg fw-700">
                    <i class="bi bi-<?= $esEditar ? 'check-lg' : 'plus-lg' ?> me-2"></i>
                    <?= $esEditar ? 'Guardar Cambios' : 'Crear Producto' ?>
                </button>
                <a href="<?= BASE_URL ?>/admin/productos" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</form>

<script>
// Agregar fila de especificación
document.getElementById('addSpec').addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 spec-row';
    row.innerHTML = `
        <div class="col-5"><input type="text" name="spec_key[]" class="form-control admin-form-control form-control-sm" placeholder="Ej: Procesador"></div>
        <div class="col-6"><input type="text" name="spec_val[]" class="form-control admin-form-control form-control-sm" placeholder="Ej: Intel Core i7"></div>
        <div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger removeSpec w-100"><i class="bi bi-x"></i></button></div>`;
    document.getElementById('specsContainer').appendChild(row);
    row.querySelector('input').focus();
});

// Eliminar fila spec
document.getElementById('specsContainer').addEventListener('click', e => {
    if (e.target.closest('.removeSpec')) {
        const rows = document.querySelectorAll('.spec-row');
        if (rows.length > 1) e.target.closest('.spec-row').remove();
    }
});

// Auto-uppercase SKU
document.querySelector('[name="sku"]')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
