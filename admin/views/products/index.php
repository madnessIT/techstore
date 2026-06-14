<?php
/**
 * TechStore - Vista Admin: Lista de Productos
 * Archivo: admin/views/products/index.php
 */
$tituloAdmin = 'Gestión de Productos';
$modulo      = 'productos';
$breadcrumb  = [['label' => 'Productos']];
require BASE_PATH . '/admin/views/partials/header.php';

$productos    = $resultado['items'];
$totalItems   = $resultado['total'];
$paginaActual = $resultado['pagina'];
$totalPaginas = $resultado['total_paginas'];
$baseUrl      = BASE_URL . '/admin/productos?' . http_build_query(array_filter(['q' => $_GET['q'] ?? ''])) . '&pagina=';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">Productos</h1>
        <p class="text-muted small mb-0"><?= number_format($totalItems) ?> productos en total</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/productos/crear" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Nuevo Producto
    </a>
</div>

<!-- Filtros -->
<div class="admin-card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?= BASE_URL ?>/admin/productos" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, SKU o marca..."
                           value="<?= e($_GET['q'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="categoria" class="form-select" onchange="this.form.submit()">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($_GET['categoria'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="activo"    <?= ($_GET['estado'] ?? '') === 'activo'    ? 'selected' : '' ?>>Activos</option>
                    <option value="inactivo"  <?= ($_GET['estado'] ?? '') === 'inactivo'  ? 'selected' : '' ?>>Inactivos</option>
                    <option value="destacado" <?= ($_GET['estado'] ?? '') === 'destacado' ? 'selected' : '' ?>>Destacados</option>
                    <option value="oferta"    <?= ($_GET['estado'] ?? '') === 'oferta'    ? 'selected' : '' ?>>En Oferta</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>Buscar
                </button>
                <a href="<?= BASE_URL ?>/admin/productos" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:60px">Img</th>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($productos)): ?>
                <tr><td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No se encontraron productos
                </td></tr>
                <?php else: ?>
                <?php foreach ($productos as $p): ?>
                <tr>
                    <td class="ps-3">
                        <img src="<?= imgProducto($p['imagen_principal'] ?? null) ?>"
                             style="width:48px;height:48px;object-fit:contain;background:#f8f9fa;border-radius:8px;padding:3px;"
                             alt="<?= e($p['nombre']) ?>">
                    </td>
                    <td>
                        <p class="mb-0 fw-600 small"><?= e($p['nombre']) ?></p>
                        <small class="text-muted"><?= e($p['marca'] ?? '') ?> <?= e($p['modelo'] ?? '') ?></small>
                        <?php if ($p['destacado']): ?>
                        <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;">★ Destacado</span>
                        <?php endif; ?>
                    </td>
                    <td><code class="small"><?= e($p['sku']) ?></code></td>
                    <td><span class="badge bg-light text-dark border"><?= e($p['categoria_nombre']) ?></span></td>
                    <td>
                        <span class="fw-700 small text-primary"><?= formatearPrecio($p['precio_final'] ?? $p['precio']) ?></span>
                        <?php if (!empty($p['precio_oferta'])): ?>
                        <br><small class="text-muted text-decoration-line-through"><?= formatearPrecio($p['precio']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge rounded-pill <?= $p['stock'] == 0 ? 'bg-danger' : ($p['stock'] <= ($p['stock_minimo'] ?? 5) ? 'bg-warning text-dark' : 'bg-success') ?>">
                            <?= (int)$p['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $p['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="<?= BASE_URL ?>/producto/<?= e($p['slug']) ?>" target="_blank"
                               class="btn btn-sm btn-outline-info" title="Ver en tienda">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/productos/editar/<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/productos/eliminar/<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-danger" title="Eliminar"
                               data-confirm="¿Eliminar el producto '<?= e($p['nombre']) ?>'? Esta acción no se puede deshacer.">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginación -->
<?php if ($totalPaginas > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination gap-1 mb-0">
        <li class="page-item <?= $paginaActual <= 1 ? 'disabled' : '' ?>">
            <a class="page-link rounded-2" href="<?= $baseUrl ?><?= $paginaActual - 1 ?>">‹</a>
        </li>
        <?php for ($i = max(1, $paginaActual - 2); $i <= min($totalPaginas, $paginaActual + 2); $i++): ?>
        <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
            <a class="page-link rounded-2" href="<?= $baseUrl ?><?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= $paginaActual >= $totalPaginas ? 'disabled' : '' ?>">
            <a class="page-link rounded-2" href="<?= $baseUrl ?><?= $paginaActual + 1 ?>">›</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
