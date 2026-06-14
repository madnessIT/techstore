<?php
/**
 * TechStore - Vista: Detalle de Producto
 * Archivo: views/catalog/detalle.php
 */
$titulo   = e($producto['nombre']) . ' - TechStore Bolivia';
$metaDesc = e($producto['descripcion_corta'] ?? $producto['nombre']);
$precioFinal    = (float)($producto['precio_oferta'] ?? $producto['precio_final'] ?? $producto['precio']);
$precioOriginal = (float)$producto['precio'];
$tieneOferta    = !empty($producto['precio_oferta']) && (float)$producto['precio_oferta'] < $precioOriginal;
$descuento      = $tieneOferta ? round((1 - $precioFinal / $precioOriginal) * 100) : 0;
$sinStock       = (int)($producto['stock'] ?? 0) <= 0;
$tipoCat        = tipoImagenCategoria($producto['categoria_nombre'] ?? '');
$marcaProd      = $producto['marca'] ?? '';
$imgPrincipal   = imgProducto($producto['imagen_principal'] ?? null, $tipoCat, $marcaProd);
require BASE_PATH . '/views/partials/header.php';
?>

<!-- Breadcrumb -->
<div class="ts-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/" class="text-primary">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/catalogo" class="text-primary">Catálogo</a></li>
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>/categoria/<?= e($producto['categoria_slug']) ?>" class="text-primary">
                        <?= e($producto['categoria_nombre']) ?>
                    </a>
                </li>
                <li class="breadcrumb-item active"><?= e($producto['nombre']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">

        <!-- ========== GALERÍA DE IMÁGENES ========== -->
        <div class="col-lg-5">
            <!-- Imagen principal -->
            <div class="ts-detail-img-main position-relative">
                <?php if ($tieneOferta): ?>
                <div style="position:absolute;top:15px;left:15px;z-index:3;background:var(--ts-danger);color:white;padding:.4rem .8rem;border-radius:50px;font-weight:700;font-size:.85rem;">
                    -<?= $descuento ?>% OFF
                </div>
                <?php endif; ?>
                <img id="mainProductImg"
                     src="<?= $imgPrincipal ?>"
                     alt="<?= e($producto['nombre']) ?>"
                     onerror="this.onerror=null;this.src='<?= BASE_URL ?>/assets/images/products/img.php?f=no-image.jpg&t=<?= urlencode($tipoCat) ?>&m=<?= urlencode($marcaProd) ?>'"
                     style="transition:opacity .3s ease;">
            </div>

            <!-- Miniaturas -->
            <?php if (!empty($producto['imagenes'])): ?>
            <div class="ts-detail-thumbs">
                <!-- Miniatura imagen principal -->
                <div class="ts-thumb active" data-src="<?= $imgPrincipal ?>">
                    <img src="<?= $imgPrincipal ?>"
                         alt="Principal"
                         onerror="this.onerror=null;this.src='<?= BASE_URL ?>/assets/images/products/img.php?f=no-image.jpg&t=<?= urlencode($tipoCat) ?>'">
                </div>
                <?php foreach ($producto['imagenes'] as $img): ?>
                <div class="ts-thumb"
                     data-src="<?= BASE_URL ?>/assets/uploads/<?= e($img['imagen']) ?>">
                    <img src="<?= BASE_URL ?>/assets/uploads/<?= e($img['imagen']) ?>"
                         alt="<?= e($img['alt_text'] ?? $producto['nombre']) ?>"
                         onerror="this.onerror=null;this.src='<?= BASE_URL ?>/assets/images/products/img.php?f=no-image.jpg'">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Compartir -->
            <div class="d-flex align-items-center gap-2 mt-3">
                <span class="text-muted small">Compartir:</span>
                <a href="https://wa.me/?text=<?= urlencode($producto['nombre'] . ' - ' . BASE_URL . '/producto/' . $producto['slug']) ?>"
                   target="_blank" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-whatsapp"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . '/producto/' . $producto['slug']) ?>"
                   target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(BASE_URL . '/producto/' . $producto['slug']) ?>&text=<?= urlencode($producto['nombre']) ?>"
                   target="_blank" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-twitter-x"></i>
                </a>
            </div>
        </div>

        <!-- ========== INFORMACIÓN DEL PRODUCTO ========== -->
        <div class="col-lg-7">
            <!-- Categoría y SKU -->
            <div class="d-flex align-items-center gap-3 mb-2">
                <a href="<?= BASE_URL ?>/categoria/<?= e($producto['categoria_slug']) ?>"
                   class="badge text-decoration-none"
                   style="background:rgba(13,110,253,.1);color:var(--ts-primary);font-size:.75rem;padding:.4rem .8rem;">
                    <i class="bi bi-tag me-1"></i><?= e($producto['categoria_nombre']) ?>
                </a>
                <span class="text-muted small">SKU: <?= e($producto['sku']) ?></span>
            </div>

            <!-- Nombre -->
            <h1 class="fw-800 mb-3" style="font-size:1.6rem;line-height:1.3;">
                <?= e($producto['nombre']) ?>
            </h1>

            <!-- Marca / Modelo / Garantía -->
            <div class="d-flex flex-wrap gap-3 mb-3">
                <?php if (!empty($producto['marca'])): ?>
                <span class="d-flex align-items-center gap-1 text-muted small">
                    <i class="bi bi-award-fill text-primary"></i>
                    <strong><?= e($producto['marca']) ?></strong>
                    <?php if (!empty($producto['modelo'])): ?> · <?= e($producto['modelo']) ?><?php endif; ?>
                </span>
                <?php endif; ?>
                <?php if (!empty($producto['garantia'])): ?>
                <span class="d-flex align-items-center gap-1 text-muted small">
                    <i class="bi bi-shield-check-fill text-success"></i>
                    <?= e($producto['garantia']) ?>
                </span>
                <?php endif; ?>
                <span class="d-flex align-items-center gap-1 text-muted small">
                    <i class="bi bi-eye text-primary"></i>
                    <?= (int)$producto['visitas'] ?> visitas
                </span>
            </div>

            <!-- Descripción corta -->
            <?php if (!empty($producto['descripcion_corta'])): ?>
            <p class="text-secondary" style="font-size:.95rem;line-height:1.7;">
                <?= e($producto['descripcion_corta']) ?>
            </p>
            <?php endif; ?>

            <hr>

            <!-- PRECIO -->
            <div class="mb-4">
                <div class="d-flex align-items-baseline gap-3 mb-1">
                    <span class="fw-800 text-primary" style="font-size:2.2rem;font-family:'Space Mono',monospace;">
                        <?= formatearPrecio($precioFinal) ?>
                    </span>
                    <?php if ($tieneOferta): ?>
                    <span class="text-muted text-decoration-line-through" style="font-size:1.1rem;">
                        <?= formatearPrecio($precioOriginal) ?>
                    </span>
                    <span class="badge bg-danger fs-6">AHORRAS <?= formatearPrecio($precioOriginal - $precioFinal) ?></span>
                    <?php endif; ?>
                </div>
                <small class="text-muted">Precio incluye IVA</small>
            </div>

            <!-- Stock -->
            <div class="mb-4">
                <?php if ($sinStock): ?>
                <div class="alert alert-warning py-2 d-inline-flex align-items-center gap-2 mb-0">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Producto agotado temporalmente</span>
                </div>
                <?php elseif ((int)$producto['stock'] <= (int)($producto['stock_minimo'] ?? 5)): ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>¡Solo quedan <?= (int)$producto['stock'] ?> unidades!
                    </span>
                </div>
                <?php else: ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>En stock
                    </span>
                    <span class="text-muted small"><?= (int)$producto['stock'] ?> disponibles</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Agregar al carrito -->
            <?php if (!$sinStock): ?>
            <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                <!-- Cantidad -->
                <div class="ts-qty-control">
                    <button class="ts-qty-btn" id="qtyDec" type="button" aria-label="Reducir">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" id="qty" class="ts-qty-input" value="1" min="1"
                           max="<?= (int)$producto['stock'] ?>"
                           data-stock="<?= (int)$producto['stock'] ?>">
                    <button class="ts-qty-btn" id="qtyInc" type="button" aria-label="Aumentar">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>

                <!-- Botón agregar -->
                <button class="btn btn-primary btn-lg px-4 fw-600 flex-grow-1"
                        data-add-cart="<?= (int)$producto['id'] ?>"
                        style="border-radius:10px;max-width:300px;">
                    <i class="bi bi-cart-plus me-2"></i>Agregar al Carrito
                </button>

                <!-- Comprar ya -->
                <a href="<?= BASE_URL ?>/checkout" class="btn btn-dark btn-lg px-4 fw-600"
                   onclick="event.preventDefault(); document.querySelector('[data-add-cart]').click(); setTimeout(()=>window.location.href='<?= BASE_URL ?>/carrito',1200);"
                   style="border-radius:10px;">
                    <i class="bi bi-lightning-fill me-1"></i>Comprar
                </a>
            </div>
            <?php endif; ?>

            <!-- Beneficios de compra -->
            <div class="row g-2">
                <?php
                $beneficios = [
                    ['bi-truck',        'Envío Gratis',      'en compras +Bs.500','success'],
                    ['bi-shield-check', 'Garantía Oficial',  $producto['garantia'] ?? '1 año','primary'],
                    ['bi-arrow-repeat', 'Devolución',        '30 días','info'],
                    ['bi-headset',      'Soporte Técnico',   '24/7 disponible','warning'],
                ];
                foreach ($beneficios as $b): ?>
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-light">
                        <i class="bi <?= $b[0] ?> text-<?= $b[3] ?>"></i>
                        <div>
                            <p class="mb-0 fw-600" style="font-size:.78rem;"><?= $b[1] ?></p>
                            <p class="mb-0 text-muted" style="font-size:.72rem;"><?= $b[2] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ========== TABS: DESCRIPCIÓN + ESPECIFICACIONES ========== -->
    <div class="mt-5">
        <ul class="nav nav-tabs" id="productTabs">
            <li class="nav-item">
                <button class="nav-link active fw-600" data-bs-toggle="tab" data-bs-target="#tabDesc">
                    <i class="bi bi-file-text me-2"></i>Descripción
                </button>
            </li>
            <?php if (!empty($producto['especificaciones'])): ?>
            <li class="nav-item">
                <button class="nav-link fw-600" data-bs-toggle="tab" data-bs-target="#tabSpecs">
                    <i class="bi bi-list-check me-2"></i>Especificaciones Técnicas
                </button>
            </li>
            <?php endif; ?>
        </ul>
        <div class="tab-content border border-top-0 rounded-bottom p-4">
            <!-- Descripción -->
            <div class="tab-pane fade show active" id="tabDesc">
                <?php if (!empty($producto['descripcion'])): ?>
                <div style="line-height:1.8;color:#444;font-size:.95rem;">
                    <?= nl2br(e($producto['descripcion'])) ?>
                </div>
                <?php else: ?>
                <p class="text-muted">No hay descripción disponible para este producto.</p>
                <?php endif; ?>
            </div>

            <!-- Especificaciones -->
            <?php if (!empty($producto['especificaciones'])): ?>
            <div class="tab-pane fade" id="tabSpecs">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table ts-specs-table">
                            <tbody>
                                <?php foreach ($producto['especificaciones'] as $key => $val): ?>
                                <tr>
                                    <td><?= e($key) ?></td>
                                    <td><?= e($val) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========== PRODUCTOS RELACIONADOS ========== -->
    <?php if (!empty($relacionados)): ?>
    <div class="mt-5">
        <h2 class="ts-section-title mb-4">Productos Relacionados</h2>
        <div class="row g-3">
            <?php foreach ($relacionados as $p): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <?php include BASE_PATH . '/views/partials/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require BASE_PATH . '/views/partials/footer.php'; ?>
