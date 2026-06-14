<?php
/**
 * TechStore - Vista Admin: Lista de Categorías
 * Archivo: admin/views/categories/index.php
 */
$tituloAdmin = 'Categorías';
$modulo      = 'categorias';
$breadcrumb  = [['label' => 'Categorías']];
require BASE_PATH . '/admin/views/partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 fw-800 mb-0">Categorías</h1>
    <a href="<?= BASE_URL ?>/admin/categorias/crear" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Nueva Categoría
    </a>
</div>
<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Icono</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Productos</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($categorias as $cat): ?>
                <tr>
                    <td class="ps-3">
                        <div style="width:40px;height:40px;background:#e8f0fe;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#0d6efd;font-size:1.1rem;">
                            <i class="bi <?= e($cat['icono'] ?? 'bi-tag') ?>"></i>
                        </div>
                    </td>
                    <td><strong><?= e($cat['nombre']) ?></strong></td>
                    <td><code class="small"><?= e($cat['slug']) ?></code></td>
                    <td><span class="badge bg-primary rounded-pill"><?= (int)$cat['total_productos'] ?></span></td>
                    <td><?= (int)$cat['orden'] ?></td>
                    <td><span class="badge <?= $cat['activa'] ? 'bg-success' : 'bg-secondary' ?>"><?= $cat['activa'] ? 'Activa' : 'Inactiva' ?></span></td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="<?= BASE_URL ?>/categoria/<?= e($cat['slug']) ?>" target="_blank"
                               class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                            <a href="<?= BASE_URL ?>/admin/categorias/editar/<?= $cat['id'] ?>"
                               class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <a href="<?= BASE_URL ?>/admin/categorias/eliminar/<?= $cat['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="¿Eliminar la categoría '<?= e($cat['nombre']) ?>'?">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
