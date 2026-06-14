<?php
/**
 * Vista Admin: Detalle de Pedido
 * Archivo: admin/views/orders/ver.php
 */
$tituloAdmin = 'Pedido ' . e($pedido['numero_orden']);
$modulo      = 'pedidos';
$breadcrumb  = [['label'=>'Pedidos','url'=> BASE_URL.'/admin/pedidos'],['label'=> $pedido['numero_orden']]];
require BASE_PATH . '/admin/views/partials/header.php';
$estados = ['pendiente','confirmado','procesando','enviado','entregado','cancelado','reembolsado'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">Pedido <?= e($pedido['numero_orden']) ?></h1>
        <p class="text-muted small mb-0"><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/pedidos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
        <span class="badge estado-<?= $pedido['estado'] ?> rounded-pill px-3 py-2 fs-6">
            <?= ucfirst($pedido['estado']) ?>
        </span>
    </div>
</div>

<div class="row g-4">
    <!-- Detalle izquierda -->
    <div class="col-lg-8">
        <!-- Productos -->
        <div class="admin-card mb-4">
            <div class="card-header"><i class="bi bi-box-seam text-primary me-2"></i>Productos del Pedido</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th>SKU</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pedido['detalle'] as $item): ?>
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= imgProducto($item['imagen_principal'] ?? null) ?>"
                                         style="width:40px;height:40px;object-fit:contain;background:#f8f9fa;border-radius:6px;">
                                    <span class="small fw-600"><?= e($item['nombre_producto']) ?></span>
                                </div>
                            </td>
                            <td><code class="small"><?= e($item['sku_producto'] ?? '—') ?></code></td>
                            <td class="text-center"><?= (int)$item['cantidad'] ?></td>
                            <td class="text-end"><?= formatearPrecio($item['precio_unitario']) ?></td>
                            <td class="text-end pe-3 fw-700"><?= formatearPrecio($item['subtotal']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr><td colspan="4" class="text-end pe-3 small text-muted">Subtotal:</td><td class="text-end pe-3 fw-600"><?= formatearPrecio($pedido['subtotal']) ?></td></tr>
                            <tr><td colspan="4" class="text-end pe-3 small text-muted">Envío:</td><td class="text-end pe-3"><?= $pedido['costo_envio'] > 0 ? formatearPrecio($pedido['costo_envio']) : '<span class="text-success">Gratis</span>' ?></td></tr>
                            <tr><td colspan="4" class="text-end pe-3 fw-800 fs-6">Total:</td><td class="text-end pe-3 fw-800 text-primary fs-6"><?= formatearPrecio($pedido['total']) ?></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notas -->
        <?php if (!empty($pedido['notas'])): ?>
        <div class="admin-card mb-4">
            <div class="card-header"><i class="bi bi-chat-text text-primary me-2"></i>Notas del Cliente</div>
            <div class="card-body"><p class="mb-0"><?= nl2br(e($pedido['notas'])) ?></p></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar derecha -->
    <div class="col-lg-4">
        <!-- Cambiar estado -->
        <div class="admin-card mb-4">
            <div class="card-header"><i class="bi bi-arrow-repeat text-primary me-2"></i>Cambiar Estado</div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/admin/pedidos/estado/<?= (int)$pedido['id'] ?>">
                    <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
                    <select name="estado" class="form-select admin-form-control mb-3">
                        <?php foreach ($estados as $est): ?>
                        <option value="<?= $est ?>" <?= $pedido['estado'] === $est ? 'selected' : '' ?>><?= ucfirst($est) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-2"></i>Actualizar Estado
                    </button>
                </form>
            </div>
        </div>

        <!-- Info cliente -->
        <div class="admin-card mb-4">
            <div class="card-header"><i class="bi bi-person text-primary me-2"></i>Cliente</div>
            <div class="card-body">
                <p class="fw-700 mb-1"><?= e($pedido['cliente_nombre']) ?></p>
                <p class="text-muted small mb-2"><?= e($pedido['cliente_email']) ?></p>
                <a href="<?= BASE_URL ?>/admin/clientes/ver/<?= (int)$pedido['cliente_id'] ?>"
                   class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-person-lines-fill me-1"></i>Ver Perfil
                </a>
            </div>
        </div>

        <!-- Dirección de envío -->
        <div class="admin-card mb-4">
            <div class="card-header"><i class="bi bi-truck text-primary me-2"></i>Envío</div>
            <div class="card-body">
                <p class="fw-700 mb-1"><?= e($pedido['nombre_envio']) ?></p>
                <p class="small text-muted mb-1"><i class="bi bi-telephone me-1"></i><?= e($pedido['telefono_envio'] ?? '—') ?></p>
                <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?= e($pedido['direccion_envio']) ?></p>
                <p class="small text-muted mb-0"><i class="bi bi-building me-1"></i><?= e($pedido['ciudad_envio']) ?><?= !empty($pedido['departamento_envio']) ? ', ' . e($pedido['departamento_envio']) : '' ?></p>
            </div>
        </div>

        <!-- Pago -->
        <div class="admin-card">
            <div class="card-header"><i class="bi bi-credit-card text-primary me-2"></i>Pago</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Método</span>
                    <span class="fw-600 text-capitalize"><?= e($pedido['metodo_pago'] ?? 'Efectivo') ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Total</span>
                    <span class="fw-800 text-primary"><?= formatearPrecio($pedido['total']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
