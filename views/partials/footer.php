</main>

<!-- ============================================================
     FOOTER
============================================================ -->
<footer class="ts-footer mt-5">
    <div class="ts-footer-top">
        <div class="container">
            <div class="row g-4">
                <!-- Empresa -->
                <div class="col-lg-4 col-md-6">
                    <div class="ts-brand mb-3">
                        <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
                        <span class="brand-text fs-4">Tech<span class="brand-highlight">Store</span></span>
                    </div>
                    <p class="ts-footer-desc">
                        Tu tienda de tecnología de confianza en Bolivia. 
                        Más de 10 años equipando hogares y empresas con los mejores productos tecnológicos.
                    </p>
                    <div class="ts-social-links mt-3">
                        <a href="#" class="ts-social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="ts-social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="ts-social-link" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="ts-social-link" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="ts-social-link" title="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Categorías -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="ts-footer-title">Categorías</h6>
                    <ul class="ts-footer-links">
                        <li><a href="<?= BASE_URL ?>/categoria/laptops"><i class="bi bi-chevron-right"></i>Laptops</a></li>
                        <li><a href="<?= BASE_URL ?>/categoria/computadoras-escritorio"><i class="bi bi-chevron-right"></i>Escritorio</a></li>
                        <li><a href="<?= BASE_URL ?>/categoria/componentes"><i class="bi bi-chevron-right"></i>Componentes</a></li>
                        <li><a href="<?= BASE_URL ?>/categoria/monitores"><i class="bi bi-chevron-right"></i>Monitores</a></li>
                        <li><a href="<?= BASE_URL ?>/categoria/impresoras"><i class="bi bi-chevron-right"></i>Impresoras</a></li>
                        <li><a href="<?= BASE_URL ?>/categoria/redes"><i class="bi bi-chevron-right"></i>Redes</a></li>
                        <li><a href="<?= BASE_URL ?>/categoria/accesorios"><i class="bi bi-chevron-right"></i>Accesorios</a></li>
                        <li><a href="<?= BASE_URL ?>/categoria/gaming"><i class="bi bi-chevron-right"></i>Gaming</a></li>
                    </ul>
                </div>
                
                <!-- Información -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="ts-footer-title">Información</h6>
                    <ul class="ts-footer-links">
                        <li><a href="#"><i class="bi bi-chevron-right"></i>Sobre Nosotros</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i>Términos y Condiciones</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i>Política de Privacidad</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i>Política de Devoluciones</a></li>
                        <li><a href="#"><i class="bi bi-chevron-right"></i>Garantías</a></li>
                        <li><a href="<?= BASE_URL ?>/admin" target="_blank"><i class="bi bi-chevron-right"></i>Administración</a></li>
                    </ul>
                </div>
                
                <!-- Contacto -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="ts-footer-title">Contacto</h6>
                    <ul class="ts-footer-contact">
                        <li>
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            <span>Av. Tecnológica #123, Zona Central, La Paz, Bolivia</span>
                        </li>
                        <li>
                            <i class="bi bi-telephone-fill text-primary"></i>
                            <span>+591 2 123-4567</span>
                        </li>
                        <li>
                            <i class="bi bi-whatsapp text-success"></i>
                            <span>+591 71 234-567</span>
                        </li>
                        <li>
                            <i class="bi bi-envelope-fill text-primary"></i>
                            <span>info@techstore.bo</span>
                        </li>
                        <li>
                            <i class="bi bi-clock-fill text-primary"></i>
                            <span>Lun-Vie: 8:00-18:00 | Sáb: 9:00-14:00</span>
                        </li>
                    </ul>
                    
                    <!-- Métodos de pago -->
                    <div class="ts-payment-methods mt-3">
                        <small class="text-muted d-block mb-2">Métodos de pago aceptados:</small>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="ts-payment-badge"><i class="bi bi-cash"></i> Efectivo</span>
                            <span class="ts-payment-badge"><i class="bi bi-bank"></i> Transferencia</span>
                            <span class="ts-payment-badge"><i class="bi bi-qr-code"></i> QR</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="ts-footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> TechStore Bolivia. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">Desarrollado con <i class="bi bi-heart-fill text-danger"></i> en Bolivia</small>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to top -->
<button class="ts-back-top" id="backToTop" title="Ir arriba">
    <i class="bi bi-arrow-up"></i>
</button>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= BASE_URL ?>/assets/js/techstore.js"></script>

<?php if (isset($extraJs)) echo $extraJs; ?>

<script>
    // Actualizar contador del carrito al cargar
    TechStore.actualizarContadorCarrito();
</script>
</body>
</html>
