<?php
/**
 * TechStore - Vista: Catálogo de Productos
 * Archivo: views/catalog/catalogo.php
 */
$titulo   = (!empty($filtros['busqueda']) ? 'Búsqueda: ' . e($filtros['busqueda']) : (!empty($categoria) ? e($categoria['nombre']) : 'Catálogo')) . ' - TechStore';
$metaDesc = 'Catálogo completo de productos tecnológicos en TechStore Bolivia.';
require BASE_PATH . '/views/partials/header.php';

$totalItems   = $resultado['total'];
$totalPaginas = $resultado['total_paginas'];
$paginaActual = $resultado['pagina'];
$productos    = $resultado['items'];

// Construir URL base de paginación (preservar filtros)
$queryParams = $_GET;
unset($queryParams['pagina']);
$baseQuery = http_build_query($queryParams);
$baseUrl   = BASE_URL . '/catalogo' . ($baseQuery ? '?' . $baseQuery . '&' : '?');
?>

<!-- Breadcrumb -->
<div class="ts-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/" class="text-primary">Inicio</a></li>
                <?php if (!empty($categoria)): ?>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/catalogo" class="text-primary">Catálogo</a></li>
                <li class="breadcrumb-item active"><?= e($categoria['nombre']) ?></li>
                <?php elseif (!empty($filtros['busqueda'])): ?>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/catalogo" class="text-primary">Catálogo</a></li>
                <li class="breadcrumb-item active">Búsqueda: "<?= e($filtros['busqueda']) ?>"</li>
                <?php else: ?>
                <li class="breadcrumb-item active">Catálogo</li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        <!-- ========== SIDEBAR FILTROS ========== -->
        <div class="col-lg-3">
            <!-- Toggle mobile -->
            <div class="d-lg-none mb-3">
                <button class="btn btn-outline-primary w-100" id="btnFiltros" type="button">
                    <i class="bi bi-funnel me-2"></i>Filtros
                    <?php if ($totalItems > 0): ?><span class="badge bg-primary ms-2"><?= $totalItems ?></span><?php endif; ?>
                </button>
            </div>

            <div class="ts-filter-card" id="filtrosPanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="ts-filter-title mb-0">Filtros</h5>
                    <?php if (!empty(array_filter($filtros))): ?>
                    <a href="<?= BASE_URL ?>/catalogo" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>Limpiar
                    </a>
                    <?php endif; ?>
                </div>

                <form action="<?= BASE_URL ?>/catalogo" method="GET" id="filtrosForm">

                    <!-- Categorías -->
                    <div class="ts-filter-section">
                        <h6>Categorías</h6>
                        <?php foreach ($categorias as $cat): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="categoria"
                                   id="cat_<?= $cat['id'] ?>" value="<?= $cat['id'] ?>"
                                   <?= (isset($filtros['categoria']) && (int)$filtros['categoria'] === (int)$cat['id']) ? 'checked' : '' ?>
                                   onchange="this.form.submit()">
                            <label class="form-check-label d-flex justify-content-between" for="cat_<?= $cat['id'] ?>">
                                <span>
                                    <i class="bi <?= e($cat['icono'] ?? 'bi-tag') ?> me-1 text-primary" style="font-size:.8rem;"></i>
                                    <?= e($cat['nombre']) ?>
                                </span>
                                <span class="badge bg-light text-secondary rounded-pill"><?= (int)$cat['total_productos'] ?></span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!empty($filtros['categoria'])): ?>
                        <div class="mt-1">
                            <input type="radio" name="categoria" value="" id="cat_all" class="form-check-input" onchange="this.form.submit()">
                            <label class="form-check-label text-primary" for="cat_all"><small>Ver todas</small></label>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Rango de Precio -->
                    <div class="ts-filter-section border-top pt-3">
                        <h6>Rango de Precio</h6>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">Mínimo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="number" name="precio_min" id="precioMin" class="form-control"
                                           value="<?= e($filtros['precio_min'] ?? '') ?>" min="0" step="50" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">Máximo</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="number" name="precio_max" id="precioMax" class="form-control"
                                           value="<?= e($filtros['precio_max'] ?? '') ?>" min="0" step="50" placeholder="10000">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <?php
                            $rangos = [['0','500','Hasta Bs.500'],['500','1500','Bs.500-1.5k'],['1500','5000','Bs.1.5k-5k'],['5000','','Más de Bs.5k']];
                            foreach ($rangos as $r): ?>
                            <a href="<?= BASE_URL ?>/catalogo?precio_min=<?= $r[0] ?>&precio_max=<?= $r[1] ?><?= !empty($filtros['categoria']) ? '&categoria=' . $filtros['categoria'] : '' ?>"
                               class="badge <?= ($filtros['precio_min'] ?? '') == $r[0] && ($filtros['precio_max'] ?? '') == $r[1] ? 'bg-primary' : 'bg-light text-dark' ?> text-decoration-none"
                               style="font-size:.72rem;">
                                <?= $r[2] ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Marcas destacadas -->
                    <div class="ts-filter-section border-top pt-3">
                        <h6>Marcas</h6>
                        <?php
                        $db = Database::getInstance();
                        $marcasFiltro = $db->query("SELECT DISTINCT marca FROM productos WHERE activo=1 AND marca IS NOT NULL ORDER BY marca LIMIT 12");
                        foreach ($marcasFiltro as $m): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="marca"
                                   id="marca_<?= e($m['marca']) ?>" value="<?= e($m['marca']) ?>"
                                   <?= (isset($filtros['marca']) && $filtros['marca'] === $m['marca']) ? 'checked' : '' ?>
                                   onchange="this.form.submit()">
                            <label class="form-check-label" for="marca_<?= e($m['marca']) ?>">
                                <?= e($m['marca']) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Busqueda oculta si existe -->
                    <?php if (!empty($filtros['busqueda'])): ?>
                    <input type="hidden" name="q" value="<?= e($filtros['busqueda']) ?>">
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="bi bi-funnel-fill me-2"></i>Aplicar Filtros
                    </button>
                </form>
            </div>
        </div>

        <!-- ========== PRODUCTOS ========== -->
        <div class="col-lg-9">
            <!-- Barra superior -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <?php if (!empty($filtros['busqueda'])): ?>
                    <h1 class="h5 mb-0">
                        Resultados para "<strong><?= e($filtros['busqueda']) ?></strong>"
                        <span class="text-muted fs-6">(<?= $totalItems ?> productos)</span>
                    </h1>
                    <?php elseif (!empty($categoria)): ?>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi <?= e($categoria['icono'] ?? 'bi-tag') ?> text-primary fs-5"></i>
                        <h1 class="h5 mb-0"><?= e($categoria['nombre']) ?></h1>
                        <span class="badge bg-primary"><?= $totalItems ?></span>
                    </div>
                    <?php else: ?>
                    <h1 class="h5 mb-0">Catálogo de Productos
                        <span class="text-muted fs-6">(<?= $totalItems ?> productos)</span>
                    </h1>
                    <?php endif; ?>
                </div>

                <!-- Ordenamiento -->
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small d-none d-sm-inline">Ordenar:</span>
                    <select class="form-select form-select-sm" style="width:auto;"
                            onchange="window.location.href = '<?= BASE_URL ?>/catalogo?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), orden: this.value}).toString()">
                        <?php
                        $ordenes = [
                            ''             => 'Relevancia',
                            'precio_asc'   => 'Precio: Menor a Mayor',
                            'precio_desc'  => 'Precio: Mayor a Menor',
                            'nombre_asc'   => 'Nombre A-Z',
                            'mas_nuevos'   => 'Más Nuevos',
                            'mas_vendidos' => 'Más Vendidos',
                        ];
                        foreach ($ordenes as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($filtros['orden'] ?? '') === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Grid de productos -->
            <?php if (!empty($productos)): ?>
            <div class="row g-3" id="productGrid">
                <?php foreach ($productos as $p): ?>
                <div class="col-6 col-md-4 col-xl-3">
                    <?php include BASE_PATH . '/views/partials/product-card.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
            <nav class="mt-5 d-flex justify-content-center" aria-label="Paginación">
                <ul class="pagination ts-pagination gap-1">
                    <li class="page-item <?= $paginaActual <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl ?>pagina=<?= $paginaActual - 1 ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php
                    $inicio = max(1, $paginaActual - 2);
                    $fin    = min($totalPaginas, $paginaActual + 2);
                    if ($inicio > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?= $baseUrl ?>pagina=1">1</a></li>
                    <?php if ($inicio > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                    <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl ?>pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($fin < $totalPaginas):
                    if ($fin < $totalPaginas - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?= $baseUrl ?>pagina=<?= $totalPaginas ?>"><?= $totalPaginas ?></a></li>
                    <?php endif; ?>

                    <li class="page-item <?= $paginaActual >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl ?>pagina=<?= $paginaActual + 1 ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

            <?php else: ?>
            <div class="ts-empty py-5">
                <i class="bi bi-search"></i>
                <h4>No encontramos resultados</h4>
                <p class="text-muted">Intenta con otros filtros o busca en todo el catálogo</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap mt-3">
                    <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-primary btn">Ver Todo el Catálogo</a>
                    <a href="<?= BASE_URL ?>/" class="btn-ts-outline btn">Ir al Inicio</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/views/partials/footer.php'; ?>
