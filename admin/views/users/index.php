<?php
/**
 * Vista Admin: Lista de Usuarios Administradores
 * Archivo: admin/views/users/index.php
 */
$tituloAdmin = 'Administradores';
$modulo      = 'usuarios';
$breadcrumb  = [['label' => 'Administradores']];
require BASE_PATH . '/admin/views/partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-800 mb-0">Usuarios Administradores</h1>
    <a href="<?= BASE_URL ?>/admin/usuarios/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Nuevo Admin</a>
</div>
<div class="admin-card">
    <div class="card-body p-0">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th class="ps-3">Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Último acceso</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td class="ps-3">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:<?= $u['rol'] === 'superadmin' ? '#dc3545' : ($u['rol'] === 'admin' ? '#0d6efd' : '#6c757d') ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                            <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                        </div>
                        <span class="fw-600 small"><?= e($u['nombre'] . ' ' . $u['apellido']) ?></span>
                    </div>
                </td>
                <td class="small text-muted"><?= e($u['email']) ?></td>
                <td>
                    <span class="badge <?= $u['rol'] === 'superadmin' ? 'bg-danger' : ($u['rol'] === 'admin' ? 'bg-primary' : 'bg-secondary') ?>">
                        <?= ucfirst($u['rol']) ?>
                    </span>
                </td>
                <td class="small text-muted"><?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca' ?></td>
                <td><span class="badge <?= $u['activo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="<?= BASE_URL ?>/admin/usuarios/editar/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <?php if ($u['id'] !== (int)$_SESSION['admin_id']): ?>
                        <a href="<?= BASE_URL ?>/admin/usuarios/eliminar/<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           data-confirm="¿Eliminar al usuario <?= e($u['nombre']) ?>?">
                            <i class="bi bi-trash3"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
