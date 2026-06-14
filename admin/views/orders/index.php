<?php
/**
 * Vista Admin: Lista de Pedidos
 * Archivo: admin/views/orders/index.php
 */
$tituloAdmin = 'Pedidos';
$modulo      = 'pedidos';
$breadcrumb  = [['label' => 'Pedidos']];
require BASE_PATH . '/admin/views/partials/header.php';
$pedidos      = $resultado['items'];
$paginaActual = $resultado['pagina'];
$totalPaginas = $resultado['total_paginas'];
$estados = ['','pendiente','confirmado','procesando','enviado','entregado','cancelado','reembolsado'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-800 mb-0">Pedidos</h1>
    <span class="badge bg-secondary fs-6"><?= number_format($resultado['total']) ?> total</span>
</div>

<!-- Filtros por estado -->
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="<?= BASE_URL ?>/admin/pedidos" class="btn btn-sm <?= empty($_GET['estado']) ? 'btn-primary' : 'btn-outline-secondary' ?>">Todos</a>
    <?php foreach (array_slice($estados,1) as $est): ?>
    <a href="<?= BASE_URL ?>/admin/pedidos?estado=<?= $est ?>"
       class="btn btn-sm <?= ($_GET['estado'] ?? '') === $est ? 'btn-primary' : 'btn-outline-secondary' ?>">
        <?= ucfirst($est) ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">N° Orden</th>
                        <th>Cliente</th>
                        <th>Ciudad</th>
                        <th>Total</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($pedidos)): ?>
                <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No hay pedidos</td></tr>
                <?php else: ?>
                <?php foreach ($pedidos as $p): ?>
                <tr>
                    <td class="ps-3"><code class="small fw-700"><?= e($p['numero_orden']) ?></code></td>
                    <td>
                        <p class="mb-0 small fw-600"><?= e($p['cliente_nombre'] ?? $p['nombre_envio']) ?></p>
                        <small class="text-muted"><?= e($p['email_envio']) ?></small>
                    </td>
                    <td class="small"><?= e($p['ciudad_envio']) ?></td>
                    <td class="fw-800 small text-primary"><?= formatearPrecio($p['total']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= ucfirst($p['metodo_pago'] ?? 'efectivo') ?></span></td>
                    <td><span class="badge estado-<?= $p['estado'] ?> rounded-pill px-2"><?= ucfirst($p['estado']) ?></span></td>
                    <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>/admin/pedidos/ver/<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($totalPaginas > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination gap-1">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
            <a class="page-link rounded-2" href="<?= BASE_URL ?>/admin/pedidos?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
