<?php
/**
 * TechStore - Vista: Mi Cuenta
 * Archivo: views/auth/mi-cuenta.php
 */
$titulo = 'Mi Cuenta - TechStore';
require BASE_PATH . '/views/partials/header.php';
?>
<div class="ts-breadcrumb"><div class="container">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/" class="text-primary">Inicio</a></li>
        <li class="breadcrumb-item active">Mi Cuenta</li>
    </ol>
</div></div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center p-4">
                    <div style="width:72px;height:72px;background:var(--ts-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:1.75rem;margin:0 auto 1rem;">
                        <?= strtoupper(substr($cliente['nombre'], 0, 1)) ?>
                    </div>
                    <h5 class="fw-800 mb-0"><?= e($cliente['nombre'] . ' ' . $cliente['apellido']) ?></h5>
                    <p class="text-muted small mb-3"><?= e($cliente['email']) ?></p>
                    <span class="badge bg-success">Cliente Verificado</span>
                </div>
                <div class="list-group list-group-flush rounded-bottom">
                    <a href="#perfil" class="list-group-item list-group-item-action fw-600 py-3">
                        <i class="bi bi-person me-2 text-primary"></i>Mi Perfil
                    </a>
                    <a href="#pedidos" class="list-group-item list-group-item-action fw-600 py-3">
                        <i class="bi bi-bag-check me-2 text-primary"></i>Mis Pedidos
                        <span class="badge bg-primary rounded-pill ms-2"><?= count($pedidos) ?></span>
                    </a>
                    <a href="<?= BASE_URL ?>/logout" class="list-group-item list-group-item-action fw-600 py-3 text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenido -->
        <div class="col-lg-9">
            <!-- Perfil -->
            <div class="card border-0 shadow-sm rounded-3 mb-4" id="perfil">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-700"><i class="bi bi-person text-primary me-2"></i>Mis Datos</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6"><label class="text-muted small d-block">Nombre</label><strong><?= e($cliente['nombre']) ?></strong></div>
                        <div class="col-sm-6"><label class="text-muted small d-block">Apellido</label><strong><?= e($cliente['apellido']) ?></strong></div>
                        <div class="col-sm-6"><label class="text-muted small d-block">Email</label><strong><?= e($cliente['email']) ?></strong></div>
                        <div class="col-sm-6"><label class="text-muted small d-block">Teléfono</label><strong><?= e($cliente['telefono'] ?? 'No indicado') ?></strong></div>
                        <div class="col-sm-6"><label class="text-muted small d-block">Ciudad</label><strong><?= e($cliente['ciudad'] ?? 'No indicada') ?></strong></div>
                        <div class="col-sm-6"><label class="text-muted small d-block">Miembro desde</label><strong><?= date('d/m/Y', strtotime($cliente['created_at'])) ?></strong></div>
                    </div>
                </div>
            </div>

            <!-- Pedidos -->
            <div class="card border-0 shadow-sm rounded-3" id="pedidos">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-700"><i class="bi bi-bag-check text-primary me-2"></i>Historial de Pedidos</h5>
                </div>
                <?php if (empty($pedidos)): ?>
                <div class="card-body">
                    <div class="ts-empty py-4">
                        <i class="bi bi-bag-x"></i>
                        <h5>Aún no tienes pedidos</h5>
                        <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-primary btn mt-2"><i class="bi bi-shop me-2"></i>Ir al Catálogo</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">N° Orden</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pedidos as $p): ?>
                            <tr>
                                <td class="ps-4"><code class="fw-700"><?= e($p['numero_orden']) ?></code></td>
                                <td class="text-muted small"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                                <td class="fw-700 text-primary"><?= formatearPrecio($p['total']) ?></td>
                                <td class="small text-capitalize"><?= e($p['metodo_pago'] ?? 'efectivo') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($p['estado']) {
                                        'entregado' => 'success', 'enviado' => 'info',
                                        'procesando','confirmado' => 'primary',
                                        'cancelado','reembolsado' => 'danger',
                                        default => 'warning text-dark'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?> rounded-pill"><?= ucfirst($p['estado']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/views/partials/footer.php'; ?>
