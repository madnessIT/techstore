<?php
/**
 * TechStore - Vista Admin: Dashboard
 * Archivo: admin/views/dashboard.php
 */
$tituloAdmin = 'Dashboard';
$modulo      = 'dashboard';
require BASE_PATH . '/admin/views/partials/header.php';

$p = $stats['pedidos'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">Dashboard</h1>
        <p class="text-muted small mb-0">Resumen del negocio · <?= date('d \d\e F, Y') ?></p>
    </div>
    <a href="<?= BASE_URL ?>/admin/productos/crear" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
    </a>
</div>

<!-- ===== STAT CARDS ===== -->
<div class="row g-3 mb-4">
    <?php
    $statCards = [
        ['icon'=>'bi-currency-dollar','color'=>'#0d6efd','bg'=>'#e8f0fe','label'=>'Ventas del Mes','value'=>'Bs. '.number_format($p['total_mes'],2,'.', ','),'sub'=>$p['pedidos_mes'].' pedidos este mes'],
        ['icon'=>'bi-calendar-check','color'=>'#198754','bg'=>'#d1e7dd','label'=>'Ventas de Hoy','value'=>'Bs. '.number_format($p['total_hoy'],2,'.',','),'sub'=>$p['pedidos_hoy'].' pedidos hoy'],
        ['icon'=>'bi-clock-history','color'=>'#ffc107','bg'=>'#fff3cd','label'=>'Pedidos Pendientes','value'=>$p['pendientes'],'sub'=>$p['procesando'].' en proceso'],
        ['icon'=>'bi-people-fill','color'=>'#0dcaf0','bg'=>'#cff4fc','label'=>'Clientes Activos','value'=>number_format($stats['clientes']),'sub'=>'clientes registrados'],
        ['icon'=>'bi-box-seam','color'=>'#6f42c1','bg'=>'#e2d9f3','label'=>'Total Productos','value'=>$stats['productos']['total'],'sub'=>$stats['productos']['en_oferta'].' en oferta'],
        ['icon'=>'bi-exclamation-triangle','color'=>'#dc3545','bg'=>'#f8d7da','label'=>'Stock Bajo','value'=>$stats['productos']['stock_bajo'],'sub'=>$stats['productos']['sin_stock'].' sin stock'],
    ];
    foreach ($statCards as $s): ?>
    <div class="col-6 col-lg-4 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon" style="background:<?= $s['bg'] ?>;color:<?= $s['color'] ?>;">
                <i class="bi <?= $s['icon'] ?>"></i>
            </div>
            <div class="min-w-0 overflow-hidden">
                <p class="stat-value mb-0" style="color:<?= $s['color'] ?>;font-size:1.2rem;"><?= $s['value'] ?></p>
                <p class="stat-label mb-0 text-truncate"><?= $s['label'] ?></p>
                <p class="mb-0" style="font-size:.7rem;color:#aaa;"><?= $s['sub'] ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- Últimos pedidos -->
    <div class="col-xl-8">
        <div class="admin-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bag-check me-2 text-primary"></i>Últimos Pedidos</span>
                <a href="<?= BASE_URL ?>/admin/pedidos" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Orden</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($stats['ultimos_pedidos'] as $ped): ?>
                        <tr>
                            <td class="ps-3"><code class="small"><?= e($ped['numero_orden']) ?></code></td>
                            <td class="small"><?= e($ped['cliente_nombre'] ?? $ped['nombre_envio']) ?></td>
                            <td class="fw-700 small"><?= formatearPrecio($ped['total']) ?></td>
                            <td>
                                <span class="badge estado-<?= e($ped['estado']) ?> rounded-pill">
                                    <?= ucfirst($ped['estado']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y', strtotime($ped['created_at'])) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/pedidos/ver/<?= $ped['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar derecha -->
    <div class="col-xl-4">
        <!-- Stock bajo -->
        <div class="admin-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle text-warning me-2"></i>Stock Bajo</span>
                <a href="<?= BASE_URL ?>/admin/productos" class="btn btn-sm btn-outline-warning">Ver</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($stats['productos_bajo_stock'])): ?>
                <p class="text-center text-muted py-3 small">✅ Todo el stock es suficiente</p>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($stats['productos_bajo_stock'] as $pr): ?>
                    <li class="list-group-item d-flex align-items-center gap-2 px-3 py-2">
                        <img src="<?= imgProducto($pr['imagen_principal'] ?? null) ?>"
                             style="width:36px;height:36px;object-fit:contain;background:#f8f9fa;border-radius:6px;">
                        <div class="flex-grow-1 min-w-0">
                            <p class="mb-0 small fw-600 text-truncate"><?= e($pr['nombre']) ?></p>
                        </div>
                        <span class="badge <?= $pr['stock'] === 0 ? 'bg-danger' : 'bg-warning text-dark' ?> rounded-pill">
                            <?= (int)$pr['stock'] ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Productos -->
        <div class="admin-card">
            <div class="card-header">
                <i class="bi bi-trophy text-warning me-2"></i>Top Productos Vendidos
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($stats['top_productos'] as $i => $tp): ?>
                    <li class="list-group-item d-flex gap-2 align-items-center px-3 py-2">
                        <span class="badge rounded-pill" style="background:<?= ['#ffd700','#c0c0c0','#cd7f32','#0d6efd','#6c757d'][$i] ?? '#6c757d' ?>; min-width:22px;"><?= $i+1 ?></span>
                        <img src="<?= imgProducto($tp['imagen_principal'] ?? null) ?>"
                             style="width:32px;height:32px;object-fit:contain;background:#f8f9fa;border-radius:6px;">
                        <div class="flex-grow-1 min-w-0">
                            <p class="mb-0 small fw-600 text-truncate"><?= e($tp['nombre']) ?></p>
                            <p class="mb-0" style="font-size:.72rem;color:#aaa;"><?= (int)$tp['total_vendido'] ?> unidades</p>
                        </div>
                        <small class="fw-700 text-primary"><?= formatearPrecio($tp['total_ingresos']) ?></small>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
