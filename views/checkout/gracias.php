<?php
/**
 * TechStore - Vista: Pedido Confirmado (Gracias)
 * Archivo: views/checkout/gracias.php
 */
$titulo = '¡Pedido Confirmado! - TechStore';
require BASE_PATH . '/views/partials/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <!-- Header de éxito -->
            <div class="text-center mb-5">
                <div style="width:90px;height:90px;background:#d1e7dd;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;animation:pulse-ring 2s ease infinite;">
                    <i class="bi bi-check-lg text-success" style="font-size:2.5rem;"></i>
                </div>
                <h1 class="fw-800 text-success mb-2">¡Pedido Confirmado!</h1>
                <p class="text-muted fs-5">Tu pedido ha sido recibido correctamente</p>
                <div class="badge bg-primary px-4 py-2 fs-6">
                    <i class="bi bi-receipt me-2"></i>Orden: <?= e($pedido['numero_orden']) ?>
                </div>
            </div>

            <!-- Detalles del pedido -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Fecha del Pedido</small>
                            <strong><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Método de Pago</small>
                            <strong class="text-capitalize"><?= e($pedido['metodo_pago'] ?? 'Efectivo') ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Estado</small>
                            <span class="badge bg-warning text-dark">Pendiente de Confirmación</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Total</small>
                            <strong class="text-primary fs-5 fw-800"><?= formatearPrecio($pedido['total']) ?></strong>
                        </div>
                    </div>

                    <!-- Dirección de envío -->
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <h6 class="fw-700 mb-2"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Dirección de Entrega</h6>
                        <p class="mb-0 small">
                            <?= e($pedido['nombre_envio']) ?><br>
                            <?= e($pedido['direccion_envio']) ?>, <?= e($pedido['ciudad_envio']) ?><br>
                            Tel: <?= e($pedido['telefono_envio'] ?? 'No indicado') ?>
                        </p>
                    </div>

                    <!-- Productos -->
                    <h6 class="fw-700 mb-3"><i class="bi bi-box-seam text-primary me-2"></i>Productos</h6>
                    <?php foreach ($pedido['detalle'] as $item): ?>
                    <div class="d-flex gap-3 align-items-center mb-3 pb-3 border-bottom">
                        <img src="<?= imgProducto($item['imagen_principal'] ?? null) ?>"
                             style="width:50px;height:50px;object-fit:contain;background:#f8f9fa;border-radius:8px;padding:4px;"
                             alt="<?= e($item['nombre_producto']) ?>"
                             onerror="this.onerror=null;this.src='<?= BASE_URL ?>/assets/images/products/img.php?f=no-image.jpg'">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-600 small"><?= e($item['nombre_producto']) ?></p>
                            <small class="text-muted">Cantidad: <?= (int)$item['cantidad'] ?> × <?= formatearPrecio($item['precio_unitario']) ?></small>
                        </div>
                        <strong><?= formatearPrecio($item['subtotal']) ?></strong>
                    </div>
                    <?php endforeach; ?>

                    <!-- Totales -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-1 small text-muted">
                            <span>Subtotal</span><span><?= formatearPrecio($pedido['subtotal']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small text-muted">
                            <span>Envío</span>
                            <span><?= $pedido['costo_envio'] > 0 ? formatearPrecio($pedido['costo_envio']) : 'Gratis' ?></span>
                        </div>
                        <div class="d-flex justify-content-between fw-800 fs-5 mt-2">
                            <span>Total Pagado</span>
                            <span class="text-primary"><?= formatearPrecio($pedido['total']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info de seguimiento -->
            <div class="alert alert-info d-flex gap-3 align-items-start">
                <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
                <div>
                    <strong>¿Qué sigue?</strong>
                    <ul class="mb-0 mt-1 small">
                        <li>Recibirás un correo de confirmación en <strong><?= e($pedido['email_envio']) ?></strong></li>
                        <li>Un agente se contactará para coordinar la entrega</li>
                        <li>Puedes seguir el estado en tu cuenta</li>
                    </ul>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                <a href="<?= BASE_URL ?>/mi-cuenta" class="btn-ts-primary btn">
                    <i class="bi bi-person me-2"></i>Ver Mis Pedidos
                </a>
                <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-outline btn">
                    <i class="bi bi-shop me-2"></i>Seguir Comprando
                </a>
                <a href="https://wa.me/59171234567?text=Hola, mi número de orden es: <?= e($pedido['numero_orden']) ?>"
                   class="btn btn-success" target="_blank">
                    <i class="bi bi-whatsapp me-2"></i>Consultar por WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/views/partials/footer.php'; ?>
