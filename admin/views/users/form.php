<?php
/**
 * Vista Admin: Formulario Usuario Administrador
 * Archivo: admin/views/users/form.php
 */
$esEditar    = !empty($usuario);
$tituloAdmin = $esEditar ? 'Editar Administrador' : 'Nuevo Administrador';
$modulo      = 'usuarios';
$breadcrumb  = [['label'=>'Administradores','url'=>BASE_URL.'/admin/usuarios'],['label'=>$tituloAdmin]];
require BASE_PATH . '/admin/views/partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-800 mb-0"><?= $tituloAdmin ?></h1>
    <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<?php if (!empty($errores)): ?>
<div class="alert alert-danger mb-4"><ul class="mb-0 ps-3"><?php foreach ($errores as $em): ?><li><?= e($em) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<div class="row justify-content-center"><div class="col-lg-6">
<div class="admin-card">
    <div class="card-body">
    <form method="POST" action="<?= BASE_URL ?>/admin/usuarios/guardar">
        <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
        <?php if ($esEditar): ?><input type="hidden" name="id" value="<?= (int)$usuario['id'] ?>"><?php endif; ?>
        <div class="row g-3">
            <div class="col-sm-6">
                <label class="admin-form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control admin-form-control" value="<?= e($usuario['nombre'] ?? '') ?>" required>
            </div>
            <div class="col-sm-6">
                <label class="admin-form-label">Apellido *</label>
                <input type="text" name="apellido" class="form-control admin-form-control" value="<?= e($usuario['apellido'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="admin-form-label">Email *</label>
                <input type="email" name="email" class="form-control admin-form-control" value="<?= e($usuario['email'] ?? '') ?>" required>
            </div>
            <div class="col-sm-6">
                <label class="admin-form-label">Contraseña <?= $esEditar ? '(dejar vacío para no cambiar)' : '*' ?></label>
                <input type="password" name="password" class="form-control admin-form-control"
                       placeholder="Mínimo 8 caracteres" <?= !$esEditar ? 'required minlength="8"' : '' ?>>
            </div>
            <div class="col-sm-6">
                <label class="admin-form-label">Rol *</label>
                <select name="rol" class="form-select admin-form-control">
                    <?php foreach (['vendedor'=>'Vendedor','admin'=>'Administrador','superadmin'=>'Super Admin'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= ($usuario['rol'] ?? 'vendedor') === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary fw-700">
                <i class="bi bi-check-lg me-2"></i><?= $esEditar ? 'Guardar Cambios' : 'Crear Usuario' ?>
            </button>
            <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
    </div>
</div>
</div></div>
<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
