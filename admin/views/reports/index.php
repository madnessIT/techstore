<?php
/**
 * Vista Admin: Reportes de Ventas
 * Archivo: admin/views/reports/index.php
 */
$tituloAdmin = 'Reportes';
$modulo      = 'reportes';
$breadcrumb  = [['label' => 'Reportes']];
$extraHead   = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>';
require BASE_PATH . '/admin/views/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">Reportes de Ventas</h1>
        <p class="text-muted small mb-0">Análisis de rendimiento del negocio</p>
    </div>
</div>

<!-- Resumen estados -->
<div class="row g-3 mb-4">
    <?php
    $colores = ['pendiente'=>'warning','confirmado'=>'info','procesando'=>'primary','enviado'=>'success','entregado'=>'success','cancelado'=>'danger','reembolsado'=>'secondary'];
    foreach ($reportes['resumen_estados'] as $est):
        $color = $colores[$est['estado']] ?? 'secondary';
    ?>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--bs-<?= $color ?>-bg-subtle,#e8f0fe);color:var(--bs-<?= $color ?>);">
                <i class="bi bi-bag-check"></i>
            </div>
            <div>
                <p class="stat-value mb-0" style="font-size:1.3rem;"><?= number_format($est['total']) ?></p>
                <p class="stat-label mb-0 text-capitalize"><?= $est['estado'] ?></p>
                <p class="mb-0" style="font-size:.7rem;color:#aaa;"><?= formatearPrecio($est['monto'] ?? 0) ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <!-- Ventas por mes -->
    <div class="col-lg-7">
        <div class="admin-card h-100">
            <div class="card-header"><i class="bi bi-bar-chart-line text-primary me-2"></i>Ventas por Mes (últimos 12 meses)</div>
            <div class="card-body">
                <canvas id="chartVentas" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Ventas por categoría -->
    <div class="col-lg-5">
        <div class="admin-card h-100">
            <div class="card-header"><i class="bi bi-pie-chart text-primary me-2"></i>Ventas por Categoría</div>
            <div class="card-body">
                <canvas id="chartCategorias" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top productos -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="card-header"><i class="bi bi-trophy text-warning me-2"></i>Top 10 Productos Vendidos</div>
            <div class="card-body p-0">
                <table class="table admin-table mb-0">
                    <thead><tr><th class="ps-3">#</th><th>Producto</th><th>SKU</th><th>Unidades</th><th>Ingresos</th></tr></thead>
                    <tbody>
                    <?php foreach ($reportes['top_productos'] as $i => $pr): ?>
                    <tr>
                        <td class="ps-3 fw-700"><?= $i + 1 ?></td>
                        <td class="small fw-600"><?= e($pr['nombre']) ?></td>
                        <td><code class="small"><?= e($pr['sku']) ?></code></td>
                        <td><span class="badge bg-primary rounded-pill"><?= number_format($pr['total_vendido']) ?></span></td>
                        <td class="fw-700 text-primary small"><?= formatearPrecio($pr['total_ingresos']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Clientes frecuentes -->
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="card-header"><i class="bi bi-people text-primary me-2"></i>Clientes Más Frecuentes</div>
            <div class="card-body p-0">
                <table class="table admin-table mb-0">
                    <thead><tr><th class="ps-3">Cliente</th><th>Pedidos</th><th>Total Gastado</th></tr></thead>
                    <tbody>
                    <?php foreach ($reportes['clientes_frecuentes'] as $cf): ?>
                    <tr>
                        <td class="ps-3">
                            <p class="mb-0 small fw-600"><?= e($cf['cliente']) ?></p>
                            <small class="text-muted"><?= e($cf['email']) ?></small>
                        </td>
                        <td><span class="badge bg-secondary rounded-pill"><?= $cf['total_pedidos'] ?></span></td>
                        <td class="fw-700 text-primary small"><?= formatearPrecio($cf['total_gastado']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Gráfico ventas por mes
const ventasData = <?= json_encode($reportes['ventas_por_mes']) ?>;
new Chart(document.getElementById('chartVentas'), {
    type: 'bar',
    data: {
        labels: ventasData.map(v => v.mes),
        datasets: [
            {
                label: 'Ventas (Bs.)',
                data: ventasData.map(v => parseFloat(v.total_ventas)),
                backgroundColor: 'rgba(13,110,253,.7)',
                borderColor: '#0d6efd',
                borderWidth: 2,
                borderRadius: 6,
                yAxisID: 'y'
            },
            {
                label: 'Pedidos',
                data: ventasData.map(v => parseInt(v.total_pedidos)),
                type: 'line',
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,.1)',
                borderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y:  { type: 'linear', display: true, position: 'left',  grid: { color: 'rgba(0,0,0,.05)' } },
            y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
        }
    }
});

// Gráfico categorías
const catData = <?= json_encode($reportes['ventas_por_categoria']) ?>;
const catColors = ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1','#fd7e14','#20c997','#6c757d','#d63384'];
new Chart(document.getElementById('chartCategorias'), {
    type: 'doughnut',
    data: {
        labels: catData.map(c => c.nombre),
        datasets: [{
            data: catData.map(c => parseFloat(c.total_ventas)),
            backgroundColor: catColors,
            hoverOffset: 8,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15, font: { size: 11 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ` Bs. ${ctx.parsed.toLocaleString('es-BO', {minimumFractionDigits:2})}`
                }
            }
        }
    }
});
</script>

<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
