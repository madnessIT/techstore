<?php
/**
 * TechStore - Partial: Tarjeta de Producto
 * Archivo: views/partials/product-card.php
 * Variable requerida: $p (array del producto)
 */
$precioFinal    = (float)($p['precio_oferta'] ?? $p['precio_final'] ?? $p['precio']);
$precioOriginal = (float)$p['precio'];
$tieneOferta    = !empty($p['precio_oferta']) && (float)$p['precio_oferta'] < $precioOriginal;
$descuento      = $tieneOferta ? round((1 - $precioFinal / $precioOriginal) * 100) : 0;
$stockBajo      = isset($p['stock']) && (int)$p['stock'] <= (int)($p['stock_minimo'] ?? 5) && (int)$p['stock'] > 0;
$sinStock       = isset($p['stock']) && (int)$p['stock'] <= 0;
$esNuevo        = isset($p['created_at']) && strtotime($p['created_at']) > strtotime('-30 days');
$stockMax       = max(1, (int)($p['stock_minimo'] ?? 5) * 4);
$stockPct       = min(100, max(0, (int)(((int)($p['stock'] ?? 0) / $stockMax) * 100)));
$tipoCat        = tipoImagenCategoria($p['categoria_nombre'] ?? '');
$marcaProd      = $p['marca'] ?? '';
$imgUrl         = imgProducto($p['imagen_principal'] ?? null, $tipoCat, $marcaProd);
?>
<div class="ts-product-card h-100">

    <!-- Imagen -->
    <div class="ts-product-img-wrap">
        <a href="<?= BASE_URL ?>/producto/<?= e($p['slug']) ?>">
            <img src="<?= $imgUrl ?>"
                 alt="<?= e($p['nombre']) ?>"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='<?= BASE_URL ?>/assets/images/products/img.php?f=no-image.jpg&t=<?= urlencode($tipoCat) ?>&m=<?= urlencode($marcaProd) ?>'">
        </a>

        <!-- Badges -->
        <div class="ts-badge-wrap">
            <?php if ($tieneOferta && $descuento > 0): ?>
            <span class="ts-badge ts-badge-sale">-<?= $descuento ?>%</span>
            <?php endif; ?>
            <?php if ($esNuevo && !$tieneOferta): ?>
            <span class="ts-badge ts-badge-new">Nuevo</span>
            <?php endif; ?>
            <?php if (!empty($p['destacado'])): ?>
            <span class="ts-badge ts-badge-hot">★ Top</span>
            <?php endif; ?>
            <?php if ($stockBajo && !$sinStock): ?>
            <span class="ts-badge ts-badge-stock">Pocas ud.</span>
            <?php endif; ?>
            <?php if ($sinStock): ?>
            <span class="ts-badge" style="background:#6c757d;color:white;">Agotado</span>
            <?php endif; ?>
        </div>

        <!-- Quick Add -->
        <?php if (!$sinStock): ?>
        <button class="ts-quick-add"
                data-add-cart="<?= (int)$p['id'] ?>"
                data-qty="1"
                title="Agregar al carrito">
            <i class="bi bi-cart-plus"></i>
        </button>
        <?php endif; ?>
    </div>

    <!-- Body -->
    <div class="ts-product-body">
        <div class="ts-product-cat"><?= e($p['categoria_nombre'] ?? '') ?></div>

        <h3 class="ts-product-name">
            <a href="<?= BASE_URL ?>/producto/<?= e($p['slug']) ?>"><?= e($p['nombre']) ?></a>
        </h3>

        <?php if (!empty($p['marca'])): ?>
        <small class="text-muted d-block mb-2" style="font-size:.75rem;">
            <i class="bi bi-award-fill text-primary me-1"></i><?= e($p['marca']) ?>
            <?php if (!empty($p['garantia'])): ?> · <?= e($p['garantia']) ?><?php endif; ?>
        </small>
        <?php endif; ?>

        <!-- Precio -->
        <div class="ts-price-wrap mt-auto">
            <span class="ts-price-main"><?= formatearPrecio($precioFinal) ?></span>
            <?php if ($tieneOferta): ?>
            <span class="ts-price-original"><?= formatearPrecio($precioOriginal) ?></span>
            <span class="ts-discount-badge">-<?= $descuento ?>%</span>
            <?php endif; ?>
        </div>

        <!-- Stock bar -->
        <?php if (!$sinStock): ?>
        <div class="ts-stock-bar mt-2" title="<?= (int)($p['stock'] ?? 0) ?> en stock">
            <div class="ts-stock-fill <?= $stockBajo ? 'low' : '' ?>"
                 style="width:<?= $stockPct ?>%"></div>
        </div>
        <?php else: ?>
        <p class="text-danger small mt-2 mb-0">
            <i class="bi bi-x-circle me-1"></i>Agotado temporalmente
        </p>
        <?php endif; ?>
    </div>
</div>
