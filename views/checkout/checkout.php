<?php
/**
 * TechStore - Vista: Proceso de Pago (Checkout)
 * Archivo: views/checkout/checkout.php
 */
$titulo = 'Finalizar Compra - TechStore';
require BASE_PATH . '/views/partials/header.php';
?>
<div class="ts-breadcrumb"><div class="container">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/" class="text-primary">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/carrito" class="text-primary">Carrito</a></li>
        <li class="breadcrumb-item active">Finalizar Compra</li>
    </ol>
</div></div>

<!-- Pasos del proceso -->
<div class="bg-light border-bottom py-3">
    <div class="container">
        <div class="d-flex justify-content-center gap-5 align-items-center">
            <?php foreach ([['1','bi-cart','Carrito','done'],['2','bi-truck','Envío','active'],['3','bi-check2-circle','Confirmado','']] as $paso): ?>
            <div class="d-flex align-items-center gap-2 <?= $paso[3] === 'active' ? 'text-primary' : ($paso[3] === 'done' ? 'text-success' : 'text-muted') ?>">
                <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;
                     background:<?= $paso[3] === 'active' ? 'var(--ts-primary)' : ($paso[3] === 'done' ? 'var(--ts-success)' : '#dee2e6') ?>;
                     color:<?= $paso[3] ? 'white' : 'var(--ts-secondary)' ?>">
                    <?= $paso[3] === 'done' ? '<i class="bi bi-check-lg"></i>' : $paso[0] ?>
                </div>
                <span class="fw-600 d-none d-sm-inline" style="font-size:.9rem;"><?= $paso[2] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Formulario de envío -->
        <div class="col-lg-7">
            <form id="checkoutForm" method="POST" action="<?= BASE_URL ?>/checkout/confirmar" novalidate>
                <input type="hidden" name="_csrf" value="<?= generarCsrf() ?>">

                <!-- Datos de envío -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="mb-0 fw-700">
                            <i class="bi bi-truck text-primary me-2"></i>Datos de Envío
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="ts-form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control ts-form-input"
                                       value="<?= e($cliente['nombre'] ?? '') ?>" required>
                                <div class="invalid-feedback">Nombre requerido</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="ts-form-label">Apellido *</label>
                                <input type="text" name="apellido" class="form-control ts-form-input"
                                       value="<?= e($cliente['apellido'] ?? '') ?>" required>
                                <div class="invalid-feedback">Apellido requerido</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="ts-form-label">Teléfono de Contacto *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">+591</span>
                                    <input type="tel" name="telefono" class="form-control ts-form-input"
                                           value="<?= e($cliente['telefono'] ?? '') ?>"
                                           placeholder="7XXXXXXX" required>
                                </div>
                                <div class="invalid-feedback">Teléfono requerido</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="ts-form-label">Ciudad *</label>
                                <select name="ciudad" class="form-select ts-form-input" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach (['La Paz','El Alto','Cochabamba','Santa Cruz','Oruro','Potosí','Sucre','Trinidad','Cobija'] as $c): ?>
                                    <option value="<?= $c ?>" <?= ($cliente['ciudad'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Selecciona una ciudad</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="ts-form-label">Departamento</label>
                                <select name="departamento" class="form-select ts-form-input">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach (['La Paz','Cochabamba','Santa Cruz','Oruro','Potosí','Chuquisaca','Beni','Pando','Tarija'] as $d): ?>
                                    <option value="<?= $d ?>"><?= $d ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="ts-form-label">Dirección de Entrega *</label>
                                <textarea name="direccion" class="form-control ts-form-input" rows="2"
                                          placeholder="Av./Calle, Número, Zona/Barrio" required><?= e($cliente['direccion'] ?? '') ?></textarea>
                                <div class="invalid-feedback">Dirección requerida</div>
                            </div>
                            <div class="col-12">
                                <label class="ts-form-label">Notas adicionales</label>
                                <textarea name="notas" class="form-control ts-form-input" rows="2"
                                          placeholder="Instrucciones especiales para el repartidor..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Método de pago -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h5 class="mb-0 fw-700"><i class="bi bi-credit-card text-primary me-2"></i>Método de Pago</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        $metodos = [
                            ['efectivo','bi-cash','Efectivo en Entrega','Paga al recibir tu pedido en casa'],
                            ['transferencia','bi-bank','Transferencia Bancaria','Datos de cuenta al confirmar el pedido'],
                            ['qr','bi-qr-code','Pago QR','Escanea el código QR con tu app bancaria'],
                        ];
                        foreach ($metodos as $i => $m): ?>
                        <div class="form-check p-3 border rounded-3 mb-2 <?= $i === 0 ? 'border-primary bg-primary bg-opacity-5' : '' ?>"
                             style="transition:all .2s;">
                            <input class="form-check-input mt-1" type="radio" name="metodo_pago"
                                   id="pago_<?= $m[0] ?>" value="<?= $m[0] ?>"
                                   <?= $i === 0 ? 'checked' : '' ?>
                                   onchange="document.querySelectorAll('.form-check').forEach(el=>el.classList.remove('border-primary','bg-primary','bg-opacity-5')); this.closest('.form-check').classList.add('border-primary','bg-primary','bg-opacity-5')">
                            <label class="form-check-label d-flex align-items-center gap-3 ms-2 cursor-pointer" for="pago_<?= $m[0] ?>">
                                <i class="bi <?= $m[1] ?> text-primary fs-4"></i>
                                <div>
                                    <strong class="d-block"><?= $m[2] ?></strong>
                                    <small class="text-muted"><?= $m[3] ?></small>
                                </div>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-700 py-3">
                    <i class="bi bi-lock-fill me-2"></i>Confirmar Pedido
                    <span class="ms-2 badge bg-white text-primary"><?= formatearPrecio($total) ?></span>
                </button>
                <p class="text-center text-muted small mt-3">
                    <i class="bi bi-shield-lock me-1"></i>
                    Al confirmar aceptas nuestros <a href="#" class="text-primary">Términos y Condiciones</a>
                </p>
            </form>
        </div>

        <!-- Resumen del pedido -->
        <div class="col-lg-5">
            <div class="ts-order-summary">
                <h5 class="fw-800 mb-4"><i class="bi bi-bag-check text-primary me-2"></i>Tu Pedido</h5>
                <?php foreach ($items as $item): ?>
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                    <img src="<?= imgProducto($item['imagen_principal'] ?? null) ?>"
                         alt="<?= e($item['nombre']) ?>"
                         style="width:56px;height:56px;object-fit:contain;background:#f8f9fa;border-radius:8px;padding:4px;"
                         onerror="this.onerror=null;this.src='<?= BASE_URL ?>/assets/images/products/img.php?f=no-image.jpg'">
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-600 small"><?= e($item['nombre']) ?></p>
                        <small class="text-muted">Cant: <?= (int)$item['cantidad'] ?></small>
                    </div>
                    <span class="fw-700 small"><?= formatearPrecio($item['precio_unitario'] * $item['cantidad']) ?></span>
                </div>
                <?php endforeach; ?>

                <div class="ts-summary-row"><span class="text-muted">Subtotal</span><span><?= formatearPrecio($subtotal) ?></span></div>
                <div class="ts-summary-row">
                    <span class="text-muted">Envío</span>
                    <span id="checkoutEnvioValor" class="<?= $envio === 0 ? 'text-success fw-600' : '' ?>">
                        <?= $envio === 0 ? 'Gratis' : formatearPrecio($envio) ?>
                    </span>
                </div>
                <div id="checkoutZonaInfo" class="text-muted" style="font-size:.75rem;margin-top:-6px;margin-bottom:6px;<?= empty($infoEnvio['zona']) ? 'display:none' : '' ?>">
                    <i class="bi bi-geo-alt me-1"></i>
                    <span id="checkoutZonaNombre"><?= e($infoEnvio['zona'] ?? '') ?></span>
                    <?php if (!empty($infoEnvio['tiempo'])): ?>
                    · <span id="checkoutZonaTiempo"><?= e($infoEnvio['tiempo']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="ts-summary-row">
                    <span class="text-muted">IVA (<?= $cfgEnvio['iva_porcentaje'] ?>%)</span>
                    <span><?= formatearPrecio($iva) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <span class="fw-800 fs-5">Total</span>
                    <span class="ts-summary-total" id="checkoutTotal"><?= formatearPrecio($total) ?></span>
                </div>
            </div>

            <!-- Datos para JS -->
            <div hidden
                 id="checkoutData"
                 data-subtotal="<?= $subtotal ?>"
                 data-iva-pct="<?= $cfgEnvio['iva_porcentaje'] ?>"
                 data-envio-desde="<?= $cfgEnvio['envio_gratis_desde'] ?>"
                 data-base-url="<?= BASE_URL ?>">
            </div>
        </div>
    </div>
</div>

<script>
// Actualizar envío dinámicamente al cambiar ciudad en el checkout
(function() {
    const ciudadSelect = document.querySelector('[name="ciudad"]');
    const dataEl       = document.getElementById('checkoutData');
    if (!ciudadSelect || !dataEl) return;

    const subtotal    = parseFloat(dataEl.dataset.subtotal || 0);
    const ivaPct      = parseFloat(dataEl.dataset.ivaPct || 13);
    const baseUrl     = dataEl.dataset.baseUrl;

    let timer;

    function actualizarEnvio(ciudad) {
        clearTimeout(timer);
        if (!ciudad) return;

        const envioValorEl = document.getElementById('checkoutEnvioValor');
        const totalEl      = document.getElementById('checkoutTotal');
        const zonaInfo     = document.getElementById('checkoutZonaInfo');
        const zonaNombre   = document.getElementById('checkoutZonaNombre');
        const zonaTiempo   = document.getElementById('checkoutZonaTiempo');
        const submitBtn    = document.querySelector('#checkoutForm [type="submit"]');

        if (envioValorEl) envioValorEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(`${baseUrl}/api/envio?ciudad=${encodeURIComponent(ciudad)}&subtotal=${subtotal}`);
                const data = await res.json();

                const costo = data.costo || 0;
                const total = subtotal + costo;

                // Actualizar envío
                if (envioValorEl) {
                    if (data.gratis) {
                        envioValorEl.innerHTML = '<span class="text-success fw-600">Gratis</span>';
                    } else {
                        envioValorEl.innerHTML = 'Bs. ' + costo.toLocaleString('es-BO', {minimumFractionDigits:2});
                        envioValorEl.className = '';
                    }
                }

                // Mostrar zona y tiempo
                if (zonaInfo && data.zona) {
                    if (zonaNombre) zonaNombre.textContent = data.zona;
                    if (zonaTiempo) zonaTiempo.textContent = data.tiempo || '';
                    zonaInfo.style.display = '';
                } else if (zonaInfo) {
                    zonaInfo.style.display = 'none';
                }

                // Actualizar total
                if (totalEl) {
                    totalEl.textContent = 'Bs. ' + total.toLocaleString('es-BO', {minimumFractionDigits:2});
                }

                // Actualizar badge del botón
                if (submitBtn) {
                    const badge = submitBtn.querySelector('.badge');
                    if (badge) badge.textContent = 'Bs. ' + total.toLocaleString('es-BO', {minimumFractionDigits:2});
                }

            } catch(e) {
                if (envioValorEl) envioValorEl.textContent = 'Error al calcular';
            }
        }, 400);
    }

    ciudadSelect.addEventListener('change', () => actualizarEnvio(ciudadSelect.value));

    // Calcular al cargar si ya hay ciudad seleccionada
    if (ciudadSelect.value) actualizarEnvio(ciudadSelect.value);
})();
</script>

<?php require BASE_PATH . '/views/partials/footer.php'; ?>
