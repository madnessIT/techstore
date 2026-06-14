<?php
/**
 * Vista Admin: IVA por Producto
 * Archivo: admin/views/config/iva-productos.php
 */
require BASE_PATH . '/admin/views/partials/header.php';
$productos    = $resultado['items'];
$paginaActual = $resultado['pagina'];
$totalPaginas = $resultado['total_paginas'];
$ivaGlobal    = (float)($config['iva_porcentaje'] ?? 13);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">IVA por Producto</h1>
        <p class="text-muted small mb-0">
            IVA Global actual: <strong class="text-primary"><?= $ivaGlobal ?>%</strong>
            · <?= number_format($resultado['total']) ?> productos
        </p>
    </div>
    <a href="<?= BASE_URL ?>/admin/configuracion" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<!-- Filtros -->
<div class="admin-card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="<?= BASE_URL ?>/admin/configuracion/iva-productos"
              class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Buscar producto..."
                           value="<?= e($_GET['q'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="iva" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="con"          <?= ($_GET['iva'] ?? '') === 'con' ? 'selected' : '' ?>>Con IVA</option>
                    <option value="sin"          <?= ($_GET['iva'] ?? '') === 'sin' ? 'selected' : '' ?>>Sin IVA (exentos)</option>
                    <option value="personalizado" <?= ($_GET['iva'] ?? '') === 'personalizado' ? 'selected' : '' ?>>IVA personalizado</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
            </div>
            <div class="col-md-2">
                <a href="<?= BASE_URL ?>/admin/configuracion/iva-productos" class="btn btn-outline-secondary btn-sm w-100">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Leyenda -->
<div class="d-flex gap-3 mb-3 flex-wrap align-items-center">
    <small class="text-muted fw-600">Leyenda:</small>
    <span class="badge bg-success">Con IVA (<?= $ivaGlobal ?>%)</span>
    <span class="badge bg-secondary">Exento</span>
    <span class="badge bg-warning text-dark">IVA Personalizado</span>
    <small class="text-muted ms-auto">
        <i class="bi bi-info-circle me-1"></i>
        Deja el campo % vacío para usar el IVA global
    </small>
</div>

<form method="POST" action="<?= BASE_URL ?>/admin/configuracion/actualizar-iva-productos" id="ivaForm">
    <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">

    <div class="admin-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Producto</th>
                            <th>SKU</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th class="text-center" style="width:140px">
                                ¿Aplica IVA?
                                <br><small class="fw-400 text-muted">activar/desactivar</small>
                            </th>
                            <th style="width:140px">
                                % IVA
                                <br><small class="fw-400 text-muted">vacío = global</small>
                            </th>
                            <th>IVA calculado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                            No se encontraron productos
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($productos as $p):
                        $tieneIva  = (bool)$p['tiene_iva'];
                        $pctProd   = $p['porcentaje_iva'] !== null ? (float)$p['porcentaje_iva'] : null;
                        $pctActivo = $pctProd ?? $ivaGlobal;
                        $precio    = (float)($p['precio_oferta'] ?? $p['precio']);
                        $ivaCalc   = $tieneIva ? round($precio * ($pctActivo / 100), 2) : 0;
                        $esPersonalizado = $pctProd !== null;
                    ?>
                    <tr class="<?= !$tieneIva ? 'table-light' : '' ?>">
                        <td class="ps-3">
                            <p class="mb-0 small fw-600"><?= e($p['nombre']) ?></p>
                            <?php if (!empty($p['marca'])): ?>
                            <small class="text-muted"><?= e($p['marca']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><code class="small"><?= e($p['sku']) ?></code></td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                                <?= e($p['categoria_nombre']) ?>
                            </span>
                        </td>
                        <td class="small fw-700 text-primary">
                            Bs. <?= number_format($precio, 2) ?>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="productos_ids[<?= $p['id'] ?>][tiene_iva]"
                                       value="1"
                                       id="iva_<?= $p['id'] ?>"
                                       <?= $tieneIva ? 'checked' : '' ?>
                                       onchange="toggleIvaRow(<?= $p['id'] ?>, this.checked)">
                            </div>
                            <label class="small <?= $tieneIva ? 'text-success' : 'text-muted' ?>" id="lbl_<?= $p['id'] ?>">
                                <?= $tieneIva ? 'Con IVA' : 'Exento' ?>
                            </label>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number"
                                       name="productos_ids[<?= $p['id'] ?>][porcentaje_iva]"
                                       class="form-control admin-form-control"
                                       id="pct_<?= $p['id'] ?>"
                                       value="<?= $pctProd !== null ? $pctProd : '' ?>"
                                       min="0" max="100" step="0.01"
                                       placeholder="<?= $ivaGlobal ?>%"
                                       <?= !$tieneIva ? 'disabled' : '' ?>>
                                <span class="input-group-text">%</span>
                            </div>
                            <?php if ($esPersonalizado): ?>
                            <small class="text-warning fw-600">
                                <i class="bi bi-sliders me-1"></i>Personalizado
                            </small>
                            <?php else: ?>
                            <small class="text-muted">Global</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span id="ivaCalc_<?= $p['id'] ?>"
                                  class="fw-600 <?= $tieneIva ? 'text-primary' : 'text-muted' ?>">
                                <?= $tieneIva ? 'Bs. ' . number_format($ivaCalc, 2) : 'Exento' ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($productos)): ?>
        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
            <small class="text-muted">
                Mostrando <?= count($productos) ?> de <?= number_format($resultado['total']) ?> productos
            </small>
            <button type="submit" class="btn btn-primary fw-700 px-4">
                <i class="bi bi-check-lg me-2"></i>Guardar Cambios de IVA
            </button>
        </div>
        <?php endif; ?>
    </div>
</form>

<!-- Paginación -->
<?php if ($totalPaginas > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination gap-1">
        <?php for ($i = max(1, $paginaActual - 2); $i <= min($totalPaginas, $paginaActual + 2); $i++): ?>
        <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
            <a class="page-link rounded-2"
               href="<?= BASE_URL ?>/admin/configuracion/iva-productos?pagina=<?= $i ?>&<?= http_build_query(array_filter(['q' => $_GET['q'] ?? '', 'iva' => $_GET['iva'] ?? ''])) ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<script>
const ivaGlobal = <?= $ivaGlobal ?>;

function toggleIvaRow(id, tieneIva) {
    const lbl     = document.getElementById('lbl_' + id);
    const pctInput = document.getElementById('pct_' + id);
    const calcEl  = document.getElementById('ivaCalc_' + id);

    if (lbl) { lbl.textContent = tieneIva ? 'Con IVA' : 'Exento'; lbl.className = 'small ' + (tieneIva ? 'text-success' : 'text-muted'); }
    if (pctInput) pctInput.disabled = !tieneIva;

    recalcularIva(id);
}

function recalcularIva(id) {
    const chk      = document.getElementById('iva_' + id);
    const pctInput = document.getElementById('pct_' + id);
    const calcEl   = document.getElementById('ivaCalc_' + id);
    if (!chk || !calcEl) return;

    const tieneIva  = chk.checked;
    const pct       = pctInput && pctInput.value !== '' ? parseFloat(pctInput.value) : ivaGlobal;

    // Obtener precio desde la columna (data- attr)
    const row       = chk.closest('tr');
    const precioEl  = row?.querySelector('td:nth-child(4)');
    const precioTxt = precioEl?.textContent?.replace('Bs.','').replace(',','').trim();
    const precio    = parseFloat(precioTxt) || 0;

    if (tieneIva) {
        const iva = (precio * pct / 100).toFixed(2);
        calcEl.textContent = 'Bs. ' + parseFloat(iva).toLocaleString('es-BO', {minimumFractionDigits:2});
        calcEl.className = 'fw-600 text-primary';
    } else {
        calcEl.textContent = 'Exento';
        calcEl.className = 'fw-600 text-muted';
    }
}

// Actualizar cálculo en tiempo real al cambiar %
document.querySelectorAll('[id^="pct_"]').forEach(input => {
    const id = input.id.replace('pct_', '');
    input.addEventListener('input', () => recalcularIva(id));
});
</script>

<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
