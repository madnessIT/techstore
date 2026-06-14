<?php
/**
 * TechStore - Vista: Carrito de Compras
 * Archivo: views/cart/carrito.php
 */
$titulo = 'Mi Carrito - TechStore';
require BASE_PATH . '/views/partials/header.php';
?>
<div class="ts-breadcrumb"><div class="container">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/" class="text-primary">Inicio</a></li>
        <li class="breadcrumb-item active">Carrito</li>
    </ol>
</div></div>

<div class="container py-5">
    <h1 class="h3 fw-800 mb-4"><i class="bi bi-cart3 me-2 text-primary"></i>Mi Carrito</h1>

    <?php if (empty($items)): ?>
    <div class="ts-empty py-5">
        <i class="bi bi-cart-x"></i>
        <h4>Tu carrito está vacío</h4>
        <p class="text-muted">¡Explora nuestro catálogo y encuentra el producto perfecto!</p>
        <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-primary btn mt-2">
            <i class="bi bi-shop me-2"></i>Ir al Catálogo
        </a>
    </div>
    <?php else: ?>
    <div class="row g-4" id="cartContainer">
        <!-- Items -->
        <div class="col-lg-8">
            <?php foreach ($items as $item): ?>
            <div class="ts-cart-item" data-precio="<?= e($item['precio_unitario']) ?>">
                <div class="d-flex gap-3 align-items-start">
                    <a href="<?= BASE_URL ?>/producto/<?= e($item['slug']) ?>">
                        <img class="ts-cart-img"
                             src="<?= imgProducto($item['imagen_principal'] ?? null, 'default', $item['marca'] ?? '') ?>"
                             alt="<?= e($item['nombre']) ?>"
                             onerror="this.onerror=null;this.src='<?= BASE_URL ?>/assets/images/products/img.php?f=no-image.jpg'">
                    </a>
                    <div class="flex-grow-1 min-w-0">
                        <a href="<?= BASE_URL ?>/producto/<?= e($item['slug']) ?>" class="text-decoration-none">
                            <h6 class="fw-700 mb-1 text-dark"><?= e($item['nombre']) ?></h6>
                        </a>
                        <?php if (!empty($item['marca'])): ?>
                        <small class="text-muted d-block mb-2"><i class="bi bi-award-fill text-primary me-1"></i><?= e($item['marca']) ?></small>
                        <?php endif; ?>

                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <!-- Cantidad -->
                            <div class="ts-qty-control">
                                <button class="ts-qty-btn" data-action="dec" type="button"><i class="bi bi-dash"></i></button>
                                <input type="number" class="ts-qty-input" value="<?= (int)$item['cantidad'] ?>"
                                       min="1" max="<?= (int)$item['stock'] ?>"
                                       data-item-id="<?= (int)$item['id'] ?>"
                                       data-max="<?= (int)$item['stock'] ?>">
                                <button class="ts-qty-btn" data-action="inc" type="button"><i class="bi bi-plus"></i></button>
                            </div>

                            <!-- Precio unitario -->
                            <span class="text-muted small">
                                <?= formatearPrecio($item['precio_unitario']) ?> c/u
                            </span>

                            <!-- Subtotal -->
                            <span class="fw-800 text-primary ms-auto" data-subtotal>
                                <?= formatearPrecio($item['precio_unitario'] * $item['cantidad']) ?>
                            </span>
                        </div>
                    </div>
                    <!-- Eliminar -->
                    <button class="btn btn-sm btn-outline-danger ms-2"
                            data-remove-item="<?= (int)$item['id'] ?>"
                            title="Eliminar producto">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>

                <?php if ((int)$item['stock'] <= 5): ?>
                <div class="mt-2">
                    <small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Solo quedan <?= (int)$item['stock'] ?> unidades</small>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <!-- Botones inferiores -->
            <div class="d-flex gap-3 mt-3 flex-wrap">
                <a href="<?= BASE_URL ?>/catalogo" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Seguir Comprando
                </a>
            </div>
        </div>

        <!-- Resumen del pedido -->
        <div class="col-lg-4">
            <div class="ts-order-summary">
                <h5 class="fw-800 mb-4"><i class="bi bi-receipt me-2 text-primary"></i>Resumen del Pedido</h5>

                <div class="ts-summary-row">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-600" id="summarySubtotal"><?= formatearPrecio($subtotal) ?></span>
                </div>
                <div class="ts-summary-row">
                    <span class="text-muted">Envío</span>
                    <span class="fw-600" id="summaryEnvio">
                        <?= $envio === 0 ? '<span class="text-success">¡Gratis!</span>' : formatearPrecio($envio) ?>
                    </span>
                </div>
                <div class="ts-summary-row">
                    <span class="text-muted small">IVA (<?= $cfgEnvio['iva_porcentaje'] ?>%)</span>
                    <span class="text-muted small"><?= formatearPrecio($iva) ?></span>
                </div>

                <div class="d-flex justify-content-between align-items-center py-3 border-top mt-2">
                    <span class="fw-800 fs-5">Total</span>
                    <span class="ts-summary-total" id="summaryTotal"><?= formatearPrecio($total) ?></span>
                </div>

                <!-- Envío info -->
                <div class="alert alert-light p-2 mb-3" id="envioMessage" style="font-size:.82rem;">
                    <?php if ($envio === 0 && $subtotal > 0): ?>
                    <i class="bi bi-check-circle text-success me-1"></i>¡Envío gratis aplicado!
                    <?php elseif ($subtotal > 0): ?>
                    <?php $faltante = $envioGratisDesdeBD - $subtotal; ?>
                    <i class="bi bi-truck me-1"></i>Agrega <?= formatearPrecio(max(0, $faltante)) ?> más para envío gratis
                    <?php endif; ?>
                </div>

                <a href="<?= BASE_URL ?>/checkout" class="btn btn-primary btn-lg w-100 fw-700">
                    <i class="bi bi-lock-fill me-2"></i>Proceder al Pago
                </a>
                <p class="text-center text-muted small mt-3 mb-0">
                    <i class="bi bi-shield-lock me-1"></i>Compra 100% segura y protegida
                </p>
            </div>
        </div>
    </div>

    <!-- Datos para recalculo JS (valores desde BD) -->
    <span id="envioGratis" data-desde="<?= $envioGratisDesdeBD ?>" data-costo="<?= $cfgEnvio['costo_envio'] ?>" hidden></span>
    <?php endif; ?>
</div>
<?php require BASE_PATH . '/views/partials/footer.php'; ?>
