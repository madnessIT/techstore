<?php
/**
 * Vista Admin: Configuración IVA + Envío
 * Archivo: admin/views/config/index.php
 */
require BASE_PATH . '/admin/views/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-800 mb-0">Configuración: IVA y Costos de Envío</h1>
        <p class="text-muted small mb-0">Gestiona impuestos y tarifas de envío por zona geográfica</p>
    </div>
</div>

<?php if (!empty($statsIva['migracion_pendiente'])): ?>
<div class="alert alert-danger d-flex gap-3 align-items-start mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 mt-1"></i>
    <div>
        <strong>⚠️ Migración de base de datos pendiente</strong>
        <p class="mb-2 mt-1">
            Las columnas <code>tiene_iva</code> y <code>porcentaje_iva</code> no existen en la tabla 
            <code>productos</code>. El módulo de IVA no funcionará hasta ejecutar la migración.
        </p>
        <strong>Solución:</strong> Abre <strong>phpMyAdmin</strong> → selecciona la BD 
        <code>techstore</code> → pestaña <strong>Importar</strong> → sube el archivo 
        <code>sql/migracion_iva_envio.sql</code>
    </div>
</div>
<?php endif; ?>

<!-- STAT CARDS resumen -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0fe;color:#0d6efd;">
                <i class="bi bi-percent"></i>
            </div>
            <div>
                <p class="stat-value mb-0" style="font-size:1.4rem;color:#0d6efd;"><?= e($config['iva_porcentaje']) ?>%</p>
                <p class="stat-label mb-0">IVA Global</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1e7dd;color:#198754;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <p class="stat-value mb-0" style="font-size:1.4rem;color:#198754;"><?= $statsIva['con_iva'] ?></p>
                <p class="stat-label mb-0">Productos con IVA</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3cd;color:#856404;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <p class="stat-value mb-0" style="font-size:1.4rem;color:#856404;"><?= $statsIva['sin_iva'] ?></p>
                <p class="stat-label mb-0">Exentos de IVA</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e2d9f3;color:#6f42c1;">
                <i class="bi bi-sliders"></i>
            </div>
            <div>
                <p class="stat-value mb-0" style="font-size:1.4rem;color:#6f42c1;"><?= $statsIva['iva_personalizado'] ?></p>
                <p class="stat-label mb-0">IVA Personalizado</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- ===== COLUMNA IZQUIERDA: IVA ===== -->
    <div class="col-lg-5">

        <!-- IVA Global -->
        <div class="admin-card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-percent text-primary fs-5"></i>
                <span>IVA Global</span>
                <span class="badge bg-primary ms-auto"><?= e($config['iva_porcentaje']) ?>% actual</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Este porcentaje se aplica a <strong>todos los productos</strong> que tienen IVA activado 
                    y no tienen un IVA personalizado configurado.
                </p>
                <form method="POST" action="<?= BASE_URL ?>/admin/configuracion/guardar-iva">
                    <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
                    <div class="mb-3">
                        <label class="admin-form-label">Porcentaje de IVA (%)</label>
                        <div class="input-group">
                            <input type="number" name="iva_porcentaje" class="form-control admin-form-control"
                                   value="<?= e($config['iva_porcentaje']) ?>"
                                   min="0" max="100" step="0.01" required>
                            <span class="input-group-text fw-700 text-primary">%</span>
                        </div>
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <?php foreach ([0, 5, 10, 13, 16, 18, 19, 21] as $pct): ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="document.querySelector('[name=iva_porcentaje]').value='<?= $pct ?>'">
                                <?= $pct ?>%
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-700">
                        <i class="bi bi-check-lg me-2"></i>Actualizar IVA Global
                    </button>
                </form>
            </div>
        </div>

        <!-- Gestión IVA por producto -->
        <div class="admin-card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-box-seam text-primary fs-5"></i>
                <span>IVA por Producto</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Configura qué productos aplican IVA y si necesitan una tasa diferente a la global.
                </p>

                <!-- Acciones masivas rápidas -->
                <div class="mb-3 p-3 bg-light rounded-3">
                    <p class="small fw-700 mb-2">⚡ Acciones rápidas:</p>
                    <form method="POST" action="<?= BASE_URL ?>/admin/configuracion/actualizar-iva-productos">
                        <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" name="accion_masiva" value="todos_con_iva"
                                    class="btn btn-sm btn-success"
                                    onclick="return confirm('¿Activar IVA en TODOS los productos?')">
                                <i class="bi bi-check-all me-1"></i>Todos con IVA
                            </button>
                            <button type="submit" name="accion_masiva" value="todos_sin_iva"
                                    class="btn btn-sm btn-warning"
                                    onclick="return confirm('¿Quitar IVA de TODOS los productos?')">
                                <i class="bi bi-x-circle me-1"></i>Todos sin IVA
                            </button>
                            <button type="submit" name="accion_masiva" value="reset_iva_global"
                                    class="btn btn-sm btn-outline-secondary"
                                    onclick="return confirm('¿Resetear todos los IVA personalizados al global?')">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset personalizado
                            </button>
                        </div>
                    </form>
                </div>

                <a href="<?= BASE_URL ?>/admin/configuracion/iva-productos"
                   class="btn btn-outline-primary w-100">
                    <i class="bi bi-list-check me-2"></i>Editar IVA Producto por Producto
                    <span class="badge bg-primary ms-2"><?= $statsIva['con_iva'] + $statsIva['sin_iva'] ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- ===== COLUMNA DERECHA: ENVÍO ===== -->
    <div class="col-lg-7">

        <!-- Configuración general de envío -->
        <div class="admin-card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-truck text-primary fs-5"></i>
                <span>Configuración General de Envío</span>
                <span class="badge <?= $config['envio_activo'] ? 'bg-success' : 'bg-secondary' ?> ms-auto">
                    <?= $config['envio_activo'] ? 'Envío activo' : 'Sin envío' ?>
                </span>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/admin/configuracion/guardar-envio">
                    <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="admin-form-label">Costo de Envío por Defecto (Bs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" name="costo_envio" class="form-control admin-form-control"
                                       value="<?= e($config['costo_envio']) ?>"
                                       min="0" step="0.50" required>
                            </div>
                            <small class="text-muted">Se usa si la ciudad no coincide con ninguna zona</small>
                        </div>
                        <div class="col-sm-6">
                            <label class="admin-form-label">Monto Mínimo para Envío Gratis (Bs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" name="envio_gratis_desde" class="form-control admin-form-control"
                                       value="<?= e($config['envio_gratis_desde']) ?>"
                                       min="0" step="10">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="admin-form-label">Tiempo estimado de entrega</label>
                            <input type="text" name="envio_tiempo_estimado" class="form-control admin-form-control"
                                   value="<?= e($config['envio_tiempo_estimado'] ?? '1-3 días hábiles') ?>"
                                   placeholder="Ej: 1-3 días hábiles">
                        </div>
                        <div class="col-sm-6">
                            <label class="admin-form-label">Nota informativa de envío</label>
                            <input type="text" name="envio_nota" class="form-control admin-form-control"
                                   value="<?= e($config['envio_nota'] ?? '') ?>"
                                   placeholder="Ej: Solo entregamos en área urbana">
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="envio_activo" id="chkEnvioActivo"
                                           <?= !empty($config['envio_activo']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-600" for="chkEnvioActivo">
                                        Ofrecer servicio de envío
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="envio_gratis_activo" id="chkGratis"
                                           <?= !empty($config['envio_gratis_activo']) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-600" for="chkGratis">
                                        Activar envío gratis por monto mínimo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 fw-700 px-4">
                        <i class="bi bi-check-lg me-2"></i>Guardar Configuración de Envío
                    </button>
                </form>
            </div>
        </div>

        <!-- Zonas de envío -->
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt text-primary fs-5"></i>
                    <span>Zonas de Envío</span>
                    <span class="badge bg-secondary"><?= count($zonas) ?></span>
                </div>
                <a href="<?= BASE_URL ?>/admin/configuracion/zona-crear"
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Zona
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Zona</th>
                                <th>Ciudades</th>
                                <th>Costo</th>
                                <th>Express</th>
                                <th>Tiempo</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($zonas)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-geo-alt fs-3 d-block mb-2 opacity-25"></i>
                                No hay zonas configuradas.
                                <a href="<?= BASE_URL ?>/admin/configuracion/zona-crear">Crear primera zona</a>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($zonas as $zona): ?>
                        <?php
                            $ciudadesZona = !empty($zona['ciudades']) ? json_decode($zona['ciudades'], true) : null;
                        ?>
                        <tr>
                            <td class="ps-3">
                                <strong class="small"><?= e($zona['nombre']) ?></strong>
                                <?php if (!empty($zona['descripcion'])): ?>
                                <br><small class="text-muted"><?= e($zona['descripcion']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($ciudadesZona)): ?>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach (array_slice($ciudadesZona, 0, 3) as $c): ?>
                                    <span class="badge bg-light text-dark border" style="font-size:.68rem;"><?= e($c) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($ciudadesZona) > 3): ?>
                                    <span class="badge bg-secondary" style="font-size:.68rem;">+<?= count($ciudadesZona) - 3 ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <span class="badge bg-info text-dark" style="font-size:.7rem;">🌍 Nacional</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-700 text-primary small">Bs. <?= number_format($zona['costo'], 2) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($zona['costo_express'])): ?>
                                <span class="small text-warning fw-600">Bs. <?= number_format($zona['costo_express'], 2) ?></span>
                                <?php else: ?>
                                <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= e($zona['tiempo_estimado']) ?></small>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/admin/configuracion/zona-toggle/<?= $zona['id'] ?>"
                                   class="badge text-decoration-none <?= $zona['activa'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $zona['activa'] ? 'Activa' : 'Inactiva' ?>
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="<?= BASE_URL ?>/admin/configuracion/zona-editar/<?= $zona['id'] ?>"
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/configuracion/zona-eliminar/<?= $zona['id'] ?>"
                                       class="btn btn-sm btn-outline-danger" title="Eliminar"
                                       data-confirm="¿Eliminar la zona '<?= e($zona['nombre']) ?>'?">
                                        <i class="bi bi-trash3"></i>
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

    </div>
</div>

<?php require BASE_PATH . '/admin/views/partials/footer.php'; ?>
