/**
 * TechStore Bolivia - JavaScript Principal
 * Archivo: assets/js/techstore.js
 * Descripción: Funcionalidades interactivas del eCommerce
 */

'use strict';

const TechStore = (() => {

    const BASE_URL = document.querySelector('meta[name="base-url"]')?.content
        || window.location.origin + (window.location.pathname.split('/')[1] ? '/' + window.location.pathname.split('/')[1] : '');

    /* ----------------------------------------------------------------
       NAVBAR: scroll effect + back-to-top
    ---------------------------------------------------------------- */
    function initNavbar() {
        const navbar  = document.getElementById('mainNavbar');
        const backTop = document.getElementById('backToTop');

        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            if (navbar)  navbar.classList.toggle('scrolled', scrollY > 40);
            if (backTop) backTop.classList.toggle('show', scrollY > 300);
        }, { passive: true });

        backTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    /* ----------------------------------------------------------------
       AUTOCOMPLETADO DE BÚSQUEDA
    ---------------------------------------------------------------- */
    function initSearchAutocomplete() {
        const inputs = [
            { input: document.getElementById('searchInputDesktop'), box: document.getElementById('autocompleteDesktop') },
        ];

        inputs.forEach(({ input, box }) => {
            if (!input || !box) return;
            let timer;

            input.addEventListener('input', () => {
                clearTimeout(timer);
                const q = input.value.trim();
                if (q.length < 2) { box.classList.remove('show'); return; }

                timer = setTimeout(async () => {
                    try {
                        const res  = await fetch(`${BASE_URL}/buscar?q=${encodeURIComponent(q)}&ajax=1`);
                        const data = await res.json();
                        renderAutocomplete(box, data);
                    } catch { box.classList.remove('show'); }
                }, 280);
            });

            document.addEventListener('click', e => {
                if (!input.contains(e.target)) box.classList.remove('show');
            });
        });
    }

    function renderAutocomplete(box, items) {
        if (!items?.length) { box.classList.remove('show'); return; }
        box.innerHTML = items.slice(0, 6).map(p => `
            <a href="${BASE_URL}/producto/${p.slug}" class="ts-autocomplete-item">
                <img src="${BASE_URL}/assets/images/products/${p.imagen_principal || 'no-image.jpg'}" 
                     alt="${escHtml(p.nombre)}" 
                     onerror="this.src='${BASE_URL}/assets/images/products/no-image.jpg'">
                <div>
                    <div class="item-name">${escHtml(p.nombre)}</div>
                    <div class="item-price">Bs. ${formatNum(p.precio_final)}</div>
                </div>
            </a>`).join('');
        box.classList.add('show');
    }

    /* ----------------------------------------------------------------
       CARRITO: agregar, actualizar, eliminar, contador
    ---------------------------------------------------------------- */
    function initCarrito() {
        // Delegación de eventos: botones "Agregar al carrito"
        document.addEventListener('click', async e => {
            const btn = e.target.closest('[data-add-cart]');
            if (!btn) return;
            e.preventDefault();

            const pid  = btn.dataset.addCart;
            const qty  = parseInt(btn.dataset.qty || document.getElementById('qty')?.value || 1);

            btn.disabled = true;
            const original = btn.innerHTML;
            btn.innerHTML  = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const fd = new FormData();
                fd.append('producto_id', pid);
                fd.append('cantidad', qty);

                const res  = await fetch(`${BASE_URL}/carrito/agregar`, { method: 'POST', body: fd });
                const data = await res.json();

                if (data.ok) {
                    actualizarContadorCarrito(data.total);
                    showToast(data.msg || '¡Producto agregado al carrito!', 'success');
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Agregado';
                    btn.classList.add('btn-success');
                    setTimeout(() => {
                        btn.innerHTML = original;
                        btn.classList.remove('btn-success');
                        btn.disabled = false;
                    }, 2000);
                } else {
                    showToast(data.msg || 'Error al agregar', 'danger');
                    btn.innerHTML = original;
                    btn.disabled  = false;
                }
            } catch {
                showToast('Error de conexión', 'danger');
                btn.innerHTML = original;
                btn.disabled  = false;
            }
        });
    }

    function actualizarContadorCarrito(count) {
        if (count !== undefined) {
            const badges = document.querySelectorAll('#cartBadge, .ts-cart-badge');
            badges.forEach(b => { b.textContent = count; b.style.display = count > 0 ? '' : 'none'; });
            return;
        }
        fetch(`${BASE_URL}/carrito/contar`)
            .then(r => r.json())
            .then(d => {
                const badges = document.querySelectorAll('#cartBadge, .ts-cart-badge');
                badges.forEach(b => {
                    b.textContent = d.total;
                    b.style.display = d.total > 0 ? '' : 'none';
                });
            })
            .catch(() => {});
    }

    /* ----------------------------------------------------------------
       CARRITO PAGE: controles de cantidad
    ---------------------------------------------------------------- */
    function initCartPage() {
        // Aumentar / Disminuir
        document.addEventListener('click', async e => {
            const btn = e.target.closest('.ts-qty-btn');
            if (!btn) return;

            const wrap    = btn.closest('.ts-cart-item') || btn.closest('[data-item-wrap]');
            const input   = wrap?.querySelector('.ts-qty-input');
            if (!input) return;

            const itemId  = input.dataset.itemId;
            let qty       = parseInt(input.value);
            const max     = parseInt(input.dataset.max || 99);

            if (btn.dataset.action === 'inc') qty = Math.min(qty + 1, max);
            if (btn.dataset.action === 'dec') qty = Math.max(qty - 1, 1);

            input.value = qty;
            await actualizarItemCarrito(itemId, qty, wrap);
        });

        // Cambio manual
        document.addEventListener('change', async e => {
            const input = e.target.closest('.ts-qty-input');
            if (!input) return;

            const wrap   = input.closest('.ts-cart-item') || input.closest('[data-item-wrap]');
            const itemId = input.dataset.itemId;
            const qty    = Math.max(1, parseInt(input.value) || 1);
            input.value  = qty;
            await actualizarItemCarrito(itemId, qty, wrap);
        });

        // Eliminar
        document.addEventListener('click', async e => {
            const btn = e.target.closest('[data-remove-item]');
            if (!btn) return;
            e.preventDefault();

            const itemId = btn.dataset.removeItem;
            const wrap   = btn.closest('.ts-cart-item');

            if (!confirm('¿Eliminar este producto del carrito?')) return;

            try {
                const fd = new FormData();
                fd.append('item_id', itemId);
                const res  = await fetch(`${BASE_URL}/carrito/eliminar`, { method: 'POST', body: fd });
                const data = await res.json();
                if (data.ok) {
                    wrap?.remove();
                    actualizarContadorCarrito();
                    recalcularTotales();
                    checkCarritoVacio();
                    showToast('Producto eliminado', 'info');
                }
            } catch { showToast('Error al eliminar', 'danger'); }
        });
    }

    async function actualizarItemCarrito(itemId, qty, wrap) {
        try {
            const fd = new FormData();
            fd.append('item_id', itemId);
            fd.append('cantidad', qty);
            await fetch(`${BASE_URL}/carrito/actualizar`, { method: 'POST', body: fd });
            recalcularTotales();
        } catch {}
    }

    function recalcularTotales() {
        let subtotal = 0;
        document.querySelectorAll('.ts-cart-item').forEach(item => {
            const qty     = parseInt(item.querySelector('.ts-qty-input')?.value || 0);
            const precio  = parseFloat(item.dataset.precio || 0);
            const sub     = qty * precio;
            subtotal      += sub;
            const subEl   = item.querySelector('[data-subtotal]');
            if (subEl) subEl.textContent = 'Bs. ' + formatNum(sub);
        });

        // Leer valores de envío desde el HTML (vienen de la BD via PHP)
        const envioEl      = document.getElementById('envioGratis');
        const envioGratis  = parseFloat(envioEl?.dataset.desde || 500);
        const costoEnvio   = parseFloat(envioEl?.dataset.costo || 25);
        const envioFinal   = (subtotal >= envioGratis && subtotal > 0) ? 0 : (subtotal > 0 ? costoEnvio : 0);
        const total        = subtotal + envioFinal;

        setEl('summarySubtotal', 'Bs. ' + formatNum(subtotal));
        setEl('summaryEnvio', envioFinal === 0
            ? '<span class="text-success fw-600">¡Gratis!</span>'
            : 'Bs. ' + formatNum(envioFinal));
        setEl('summaryTotal', 'Bs. ' + formatNum(total));

        const envioMsg = document.getElementById('envioMessage');
        if (envioMsg) {
            if (envioFinal === 0 && subtotal > 0) {
                envioMsg.innerHTML = '<i class="bi bi-check-circle text-success me-1"></i>¡Envío gratis aplicado!';
            } else if (subtotal > 0) {
                const faltante = envioGratis - subtotal;
                envioMsg.innerHTML = `<i class="bi bi-truck me-1"></i>Agrega Bs. ${formatNum(faltante)} más para envío gratis`;
            }
        }
    }

    function checkCarritoVacio() {
        const items = document.querySelectorAll('.ts-cart-item');
        if (items.length === 0) {
            const container = document.getElementById('cartContainer');
            if (container) container.innerHTML = `
                <div class="ts-empty py-5">
                    <i class="bi bi-cart-x"></i>
                    <h4>Tu carrito está vacío</h4>
                    <p>¡Explora nuestro catálogo y encuentra productos increíbles!</p>
                    <a href="${BASE_URL}/catalogo" class="btn-ts-primary mt-2">
                        <i class="bi bi-shop me-2"></i>Ir al Catálogo
                    </a>
                </div>`;
        }
    }

    /* ----------------------------------------------------------------
       DETALLE DE PRODUCTO: galería de imágenes
    ---------------------------------------------------------------- */
    function initProductDetail() {
        const mainImg = document.getElementById('mainProductImg');
        if (!mainImg) return;

        document.querySelectorAll('.ts-thumb').forEach(thumb => {
            thumb.addEventListener('click', () => {
                document.querySelectorAll('.ts-thumb').forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
                mainImg.src = thumb.dataset.src;
                mainImg.style.animation = 'none';
                mainImg.offsetHeight; // reflow
                mainImg.style.animation = 'fadeInUp .3s ease';
            });
        });

        // Selector de cantidad
        const qtyInput = document.getElementById('qty');
        const stock    = parseInt(qtyInput?.dataset.stock || 99);

        document.getElementById('qtyInc')?.addEventListener('click', () => {
            if (qtyInput) qtyInput.value = Math.min(parseInt(qtyInput.value) + 1, stock);
        });
        document.getElementById('qtyDec')?.addEventListener('click', () => {
            if (qtyInput) qtyInput.value = Math.max(parseInt(qtyInput.value) - 1, 1);
        });
    }

    /* ----------------------------------------------------------------
       FILTROS DE CATÁLOGO
    ---------------------------------------------------------------- */
    function initFiltros() {
        const form = document.getElementById('filtrosForm');
        if (!form) return;

        // Auto-submit en selects
        form.querySelectorAll('select[data-auto-submit]').forEach(sel => {
            sel.addEventListener('change', () => form.submit());
        });

        // Precio range
        const rangeMin = document.getElementById('rangeMin');
        const rangeMax = document.getElementById('rangeMax');
        const inputMin = document.getElementById('precioMin');
        const inputMax = document.getElementById('precioMax');

        rangeMin?.addEventListener('input', () => {
            if (inputMin) inputMin.value = rangeMin.value;
        });
        rangeMax?.addEventListener('input', () => {
            if (inputMax) inputMax.value = rangeMax.value;
        });

        // Toggle filtros mobile
        document.getElementById('btnFiltros')?.addEventListener('click', () => {
            document.getElementById('filtrosPanel')?.classList.toggle('show');
        });
    }

    /* ----------------------------------------------------------------
       FORMULARIO DE CHECKOUT: validación
    ---------------------------------------------------------------- */
    function initCheckout() {
        const form = document.getElementById('checkoutForm');
        if (!form) return;

        form.addEventListener('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
                // Scroll al primer error
                const first = form.querySelector(':invalid');
                first?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                showToast('Por favor complete todos los campos requeridos', 'warning');
            } else {
                const btn = form.querySelector('[type="submit"]');
                if (btn) {
                    btn.disabled   = true;
                    btn.innerHTML  = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
                }
            }
        });
    }

    /* ----------------------------------------------------------------
       FORMULARIOS: validación en tiempo real
    ---------------------------------------------------------------- */
    function initFormValidation() {
        document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
            field.addEventListener('blur', () => {
                field.classList.toggle('is-invalid', !field.checkValidity());
                field.classList.toggle('is-valid', field.checkValidity());
            });
        });

        // Confirmar password
        const pass1 = document.getElementById('password');
        const pass2 = document.getElementById('password2');
        if (pass1 && pass2) {
            pass2.addEventListener('input', () => {
                const match = pass1.value === pass2.value;
                pass2.classList.toggle('is-invalid', !match);
                pass2.classList.toggle('is-valid', match);
            });
        }

        // Toggle mostrar/ocultar password
        document.querySelectorAll('[data-toggle-password]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.dataset.togglePassword);
                if (!target) return;
                const show = target.type === 'password';
                target.type = show ? 'text' : 'password';
                btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        });
    }

    /* ----------------------------------------------------------------
       ANIMACIONES: Intersection Observer para cards
    ---------------------------------------------------------------- */
    function initAnimations() {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.ts-product-card, .ts-cat-card, .ts-feature-card, .ts-testimonial').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity .4s ease, transform .4s ease';
            observer.observe(el);
        });

        // Versión con clase 'animated'
        const style = document.createElement('style');
        style.textContent = '.animated { opacity: 1 !important; transform: translateY(0) !important; }';
        document.head.appendChild(style);
    }

    /* ----------------------------------------------------------------
       TOAST NOTIFICATIONS
    ---------------------------------------------------------------- */
    function showToast(msg, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const icons = { success: 'check-circle-fill', danger: 'x-circle-fill', warning: 'exclamation-triangle-fill', info: 'info-circle-fill' };
        const colors = { success: '#198754', danger: '#dc3545', warning: '#ffc107', info: '#0dcaf0' };

        const id   = 'toast_' + Date.now();
        const div  = document.createElement('div');
        div.id     = id;
        div.className = 'toast ts-toast show';
        div.setAttribute('role', 'alert');
        div.style.borderLeftColor = colors[type] || colors.success;
        div.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-${icons[type] || icons.success}" style="color:${colors[type]};font-size:1.1rem;"></i>
                <span class="flex-grow-1 fw-500" style="font-size:.9rem;">${escHtml(msg)}</span>
                <button type="button" class="btn-close btn-sm" onclick="document.getElementById('${id}')?.remove()"></button>
            </div>`;

        container.appendChild(div);
        setTimeout(() => div?.remove(), 4000);
    }

    /* ----------------------------------------------------------------
       ADMIN: previsualización de imágenes
    ---------------------------------------------------------------- */
    function initImagePreview() {
        document.querySelectorAll('[data-preview]').forEach(input => {
            input.addEventListener('change', () => {
                const previewId = input.dataset.preview;
                const preview   = document.getElementById(previewId);
                if (!preview || !input.files[0]) return;

                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            });
        });
    }

    /* ----------------------------------------------------------------
       HELPERS
    ---------------------------------------------------------------- */
    function formatNum(n) {
        return parseFloat(n).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function setEl(id, html) {
        const el = document.getElementById(id);
        if (el) el.innerHTML = html;
    }

    /* ----------------------------------------------------------------
       IMÁGENES: marcar como cargadas para quitar skeleton
    ---------------------------------------------------------------- */
    function initImageLoading() {
        document.querySelectorAll('.ts-product-img-wrap img, .ts-detail-img-main img').forEach(img => {
            if (img.complete && img.naturalWidth > 0) {
                img.classList.add('loaded');
            } else {
                img.addEventListener('load',  () => img.classList.add('loaded'));
                img.addEventListener('error', () => img.classList.add('loaded')); // quitar skeleton aunque falle
            }
        });
    }

    /* ----------------------------------------------------------------
       INIT
    ---------------------------------------------------------------- */
    function init() {
        initNavbar();
        initSearchAutocomplete();
        initCarrito();
        initCartPage();
        initProductDetail();
        initFiltros();
        initCheckout();
        initFormValidation();
        initAnimations();
        initImagePreview();
        initImageLoading();
    }

    document.addEventListener('DOMContentLoaded', init);

    // API pública
    return { showToast, actualizarContadorCarrito, recalcularTotales };

})();
