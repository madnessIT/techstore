<?php
/**
 * TechStore - Vista: Página Principal (Home)
 * Archivo: views/home.php
 */
$titulo   = 'Inicio - Tecnología de Vanguardia';
$metaDesc = 'TechStore Bolivia - La mejor tienda de tecnología con laptops, computadoras, componentes, impresoras y más al mejor precio.';
require BASE_PATH . '/views/partials/header.php';
?>

<!-- ============================================================
     HERO CAROUSEL
============================================================ -->
<section class="ts-hero">
    <div id="heroCarousel" class="carousel slide w-100 ts-carousel" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active">
                <div class="container py-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-6 ts-hero-content">
                            <div class="ts-hero-badge animate-fade-up">
                                <i class="bi bi-lightning-fill"></i> Nuevos Productos 2025
                            </div>
                            <h1 class="ts-hero-title animate-fade-up animate-delay-1">
                                Tecnología de<br><span class="highlight">Vanguardia</span><br>a Tu Alcance
                            </h1>
                            <p class="ts-hero-subtitle animate-fade-up animate-delay-2">
                                Laptops, PCs, componentes y accesorios de las mejores marcas. 
                                Garantía oficial y soporte técnico especializado.
                            </p>
                            <div class="d-flex gap-3 flex-wrap animate-fade-up animate-delay-3">
                                <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-primary btn btn-lg">
                                    <i class="bi bi-shop-window"></i> Ver Catálogo
                                </a>
                                <a href="<?= BASE_URL ?>/catalogo?ofertas=1" class="btn-ts-outline btn btn-lg">
                                    <i class="bi bi-tag-fill"></i> Ver Ofertas
                                </a>
                            </div>
                            <div class="ts-hero-stats animate-fade-up animate-delay-4">
                                <div class="ts-hero-stat"><span class="stat-value">500+</span><span class="stat-label">Productos</span></div>
                                <div class="ts-hero-stat"><span class="stat-value">50+</span><span class="stat-label">Marcas</span></div>
                                <div class="ts-hero-stat"><span class="stat-value">5000+</span><span class="stat-label">Clientes</span></div>
                                <div class="ts-hero-stat"><span class="stat-value">10+</span><span class="stat-label">Años</span></div>
                            </div>
                        </div>
                        <div class="col-lg-6 d-none d-lg-flex justify-content-center animate-fade-up animate-delay-2">
                            <div style="position:relative;">
                                <div style="width:380px;height:320px;background:rgba(13,110,253,.08);border-radius:30px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(13,110,253,.15);">
                                    <i class="bi bi-laptop" style="font-size:10rem;color:rgba(13,110,253,.3);"></i>
                                </div>
                                <div style="position:absolute;top:-15px;right:-15px;background:var(--ts-primary);color:white;border-radius:50%;width:80px;height:80px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                    <span style="font-size:.65rem;font-weight:700;line-height:1;">ENVÍO</span>
                                    <span style="font-size:.65rem;font-weight:700;line-height:1;">GRATIS</span>
                                    <span style="font-size:.6rem;opacity:.8;">+Bs.500</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="carousel-item">
                <div class="container py-5">
                    <div class="row align-items-center">
                        <div class="col-lg-7 ts-hero-content">
                            <div class="ts-hero-badge"><i class="bi bi-controller"></i> Gaming Zone</div>
                            <h1 class="ts-hero-title">
                                Equipos <span class="highlight">Gaming</span><br>para Pros
                            </h1>
                            <p class="ts-hero-subtitle">
                                RTX 4090, monitores 4K 144Hz, teclados mecánicos y todo lo que necesitas para dominar.
                            </p>
                            <a href="<?= BASE_URL ?>/categoria/gaming" class="btn-ts-primary btn btn-lg mt-2">
                                <i class="bi bi-controller"></i> Ver Gaming
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="carousel-item">
                <div class="container py-5">
                    <div class="row align-items-center">
                        <div class="col-lg-7 ts-hero-content">
                            <div class="ts-hero-badge"><i class="bi bi-briefcase-fill"></i> Soluciones Empresariales</div>
                            <h1 class="ts-hero-title">
                                Equipa tu<br><span class="highlight">Empresa</span><br>con lo Mejor
                            </h1>
                            <p class="ts-hero-subtitle">
                                Computadoras, redes, impresoras y servidores con precios corporativos y soporte dedicado.
                            </p>
                            <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-primary btn btn-lg mt-2">
                                <i class="bi bi-building"></i> Soluciones Empresariales
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- ============================================================
     BENEFICIOS / FEATURES
============================================================ -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <div class="row g-3">
            <?php
            $features = [
                ['bi-truck',          'Envío Gratis',       'En compras mayores a Bs. 500'],
                ['bi-shield-check',   'Garantía Oficial',   'Todos los productos con garantía'],
                ['bi-headset',        'Soporte 24/7',       'Asistencia técnica especializada'],
                ['bi-arrow-repeat',   'Devoluciones',       '30 días para cambios sin costo'],
            ];
            foreach ($features as $f): ?>
            <div class="col-6 col-lg-3">
                <div class="ts-feature-card">
                    <div class="ts-feature-icon"><i class="bi <?= $f[0] ?>"></i></div>
                    <div>
                        <p class="ts-feature-title mb-0"><?= $f[1] ?></p>
                        <p class="ts-feature-desc"><?= $f[2] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     CATEGORÍAS PRINCIPALES
============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="ts-section-title mb-1">Categorías</h2>
                <p class="text-muted mb-0">Explora nuestra amplia gama de productos tecnológicos</p>
            </div>
            <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-outline btn d-none d-md-inline-flex">
                Ver todo <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-3">
            <?php foreach (array_slice($categorias, 0, 10) as $i => $cat): ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                <a href="<?= BASE_URL ?>/categoria/<?= e($cat['slug']) ?>" class="ts-cat-card" 
                   style="animation-delay: <?= $i * 0.05 ?>s">
                    <div class="ts-cat-icon">
                        <i class="bi <?= e($cat['icono'] ?? 'bi-tag') ?>"></i>
                    </div>
                    <div class="ts-cat-name"><?= e($cat['nombre']) ?></div>
                    <div class="ts-cat-count"><?= (int)$cat['total_productos'] ?> productos</div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     PRODUCTOS DESTACADOS
============================================================ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="ts-section-title mb-1">Productos Destacados</h2>
                <p class="text-muted mb-0">Los más elegidos por nuestros clientes</p>
            </div>
            <a href="<?= BASE_URL ?>/catalogo" class="btn-ts-outline btn d-none d-md-inline-flex">
                Ver catálogo <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-3">
            <?php if (!empty($destacados)): ?>
                <?php foreach ($destacados as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <?php include BASE_PATH . '/views/partials/product-card.php'; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12 ts-empty">
                <i class="bi bi-inbox"></i>
                <h5>Próximamente nuevos productos</h5>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     BANNER OFERTA ESPECIAL
============================================================ -->
<section class="py-5" style="background:linear-gradient(135deg,#0d1117 0%,#0a1628 100%);">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-danger mb-3 px-3 py-2 fs-6">🔥 Oferta Especial</span>
                <h2 class="text-white fw-800 fs-1 mb-3">
                    Hasta <span style="color:var(--ts-warning)">30% OFF</span><br>en Laptops Seleccionadas
                </h2>
                <p class="text-white-50 fs-5 mb-4">
                    Aprovecha nuestras ofertas de temporada en las mejores marcas. ¡Tiempo limitado!
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= BASE_URL ?>/categoria/laptops" class="btn btn-warning btn-lg fw-700 px-4">
                        <i class="bi bi-lightning-fill me-2"></i>Ver Laptops en Oferta
                    </a>
                    <a href="<?= BASE_URL ?>/catalogo" class="btn btn-outline-light btn-lg px-4">
                        Explorar más <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-flex justify-content-center">
                <div class="text-center">
                    <i class="bi bi-laptop" style="font-size:8rem;color:rgba(255,193,7,.2);"></i>
                    <div class="mt-3 d-flex justify-content-center gap-3">
                        <?php foreach ([['HP','#1a73e8'],['Dell','#007db8'],['ASUS','#003366'],['Lenovo','#e2231a']] as $marca): ?>
                        <span class="badge px-3 py-2 fw-600" style="background:<?= $marca[1] ?>;font-size:.8rem;"><?= $marca[0] ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     OFERTAS
============================================================ -->
<?php if (!empty($ofertas)): ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="ts-section-title mb-1">Ofertas Especiales</h2>
                <p class="text-muted mb-0">Precios increíbles por tiempo limitado</p>
            </div>
        </div>
        <div class="row g-3">
            <?php foreach ($ofertas as $p): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <?php include BASE_PATH . '/views/partials/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     TESTIMONIOS
============================================================ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ts-section-title center mb-2">Lo que dicen nuestros clientes</h2>
            <p class="text-muted">Miles de clientes satisfechos confían en TechStore</p>
        </div>
        <?php
        $db  = Database::getInstance();
        $testimonios = $db->query("SELECT * FROM testimonios WHERE activo=1 ORDER BY id LIMIT 6");
        ?>
        <div class="row g-4">
            <?php foreach ($testimonios as $t): ?>
            <div class="col-md-6 col-lg-4">
                <div class="ts-testimonial">
                    <div class="ts-test-stars">
                        <?php for ($s = 0; $s < 5; $s++): ?>
                            <i class="bi bi-star<?= $s < $t['calificacion'] ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="ts-test-text">"<?= e($t['mensaje']) ?>"</p>
                    <div class="d-flex align-items-center gap-2 mt-auto">
                        <div style="width:44px;height:44px;background:var(--ts-primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.1rem;flex-shrink:0;">
                            <?= strtoupper(substr($t['nombre'], 0, 1)) ?>
                        </div>
                        <div>
                            <p class="ts-test-name mb-0"><?= e($t['nombre']) ?></p>
                            <p class="ts-test-role mb-0"><?= e($t['cargo'] ?? 'Cliente') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     MARCAS / LOGOS
============================================================ -->
<section class="py-5 border-top">
    <div class="container">
        <p class="text-center text-muted small text-uppercase fw-600 letter-spacing mb-4">Marcas Oficiales que Distribuimos</p>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-md-5">
            <?php
            $marcas = ['HP','Dell','ASUS','Lenovo','Samsung','LG','Intel','NVIDIA','Kingston','TP-Link','Epson','Logitech','Sony'];
            foreach ($marcas as $m): ?>
            <span style="font-size:1.1rem;font-weight:800;color:#ccc;letter-spacing:.5px;opacity:.7;transition:opacity .2s;"
                  onmouseover="this.style.opacity='1';this.style.color='#333'"
                  onmouseout="this.style.opacity='.7';this.style.color='#ccc'"><?= $m ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require BASE_PATH . '/views/partials/footer.php'; ?>
