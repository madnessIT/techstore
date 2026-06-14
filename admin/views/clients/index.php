<?php
/**
 * Vista Admin: Lista de Clientes
 * Archivo: admin/views/clients/index.php
 */
$tituloAdmin = 'Clientes';
$modulo      = 'clientes';
$breadcrumb  = [['label' => 'Clientes']];
require BASE_PATH . '/admin/views/partials/header.php';
$clientes     = $resultado['items'];
$paginaActual = $resultado['pagina'];
$totalPaginas = $resultado['total_paginas'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">Clientes</h1>
        <p class="text-muted small mb-0"><?= number_format($resultado['total']) ?> clientes registrados</p>
    </div>
</div>
<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Cliente</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Registrado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($clientes)): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>No hay clientes</td></tr>
                <?php else: ?>
                <?php foreach ($clientes as $c): ?>
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.9rem;flex-shrink:0;">
                                <?= strtoupper(substr($c['nombre'], 0, 1)) ?>
                            </div>
                            <span class="fw-600 small"><?= e($c['nombre'] . ' ' . $c['apellido']) ?></span>
                        </div>
                    </td>
                    <td class="small text-muted"><?= e($c['email']) ?></td>
                    <td class="small"><?= e($c['telefono'] ?? '—') ?></td>
                    <td class="small"><?= e($c['ciudad'] ?? '—') ?></td>
                    <td>
                        <span class="badge <?= $c['activo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $c['activo'] ? 'Activo' : 'Inactivo' ?></span>
                        <?php if ($c['verificado']): ?><span class="badge bg-info ms-1" style="font-size:.65rem;">✓ Verificado</span><?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="<?= BASE_URL ?>/admin/clientes/ver/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="<?= BASE_URL ?>/admin/clientes/toggle/<?= $c['id'] ?>"
                               class="btn btn-sm <?= $c['activo'] ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                               data-confirm="<?= $c['activo'] ? '¿Desactivar este cliente?' : '¿Activar este cliente?' ?>">
                                <i class="bi bi-<?= $c['activo'] ? 'pause-circle' : 'play-circle' ?>"></i>
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
<?php if ($totalPaginas > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination gap-1">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <li class="page-item <?= $i === $paginaActual ? 'active' : '' ?>">
            <a class="page-link rounded-2" href="<?= BASE_URL ?>/admin/clientes?pagina=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
