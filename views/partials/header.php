<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc ?? 'TechStore Bolivia - Tu tienda de tecnología de confianza') ?>">
    <title><?= e($titulo ?? 'TechStore Bolivia') ?> | TechStore</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/assets/css/techstore.css" rel="stylesheet">
    
    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>
<body>

<!-- ============================================================
     NAVBAR PRINCIPAL
============================================================ -->
<nav class="navbar navbar-expand-lg navbar-dark ts-navbar sticky-top" id="mainNavbar">
    <div class="container">
        
        <!-- Logo -->
        <a class="navbar-brand ts-brand" href="<?= BASE_URL ?>/">
            <span class="brand-icon"><i class="bi bi-cpu-fill"></i></span>
            <span class="brand-text">Tech<span class="brand-highlight">Store</span></span>
        </a>
        
        <!-- Barra de búsqueda (desktop) -->
        <form class="d-none d-lg-flex ts-search-form mx-3 flex-grow-1" 
              action="<?= BASE_URL ?>/buscar" method="GET">
            <div class="input-group">
                <input type="search" name="q" class="form-control ts-search-input" 
                       placeholder="Buscar laptops, componentes, accesorios..." 
                       value="<?= e($_GET['q'] ?? '') ?>"
                       autocomplete="off" id="searchInputDesktop">
                <button class="btn ts-search-btn" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <div class="ts-autocomplete" id="autocompleteDesktop"></div>
        </form>
        
        <!-- Iconos derecha -->
        <div class="d-flex align-items-center gap-2 ms-auto me-2">
            <!-- Mi cuenta -->
            <?php if (clienteLogueado()): ?>
            <div class="dropdown">
                <a href="#" class="ts-nav-icon dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <span class="d-none d-md-inline ms-1"><?= e($_SESSION['cliente_nombre']) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end ts-dropdown">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/mi-cuenta"><i class="bi bi-person me-2"></i>Mi Cuenta</a></li>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/mi-cuenta#pedidos"><i class="bi bi-bag me-2"></i>Mis Pedidos</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
            <?php else: ?>
            <a href="<?= BASE_URL ?>/login" class="ts-nav-icon">
                <i class="bi bi-person"></i>
                <span class="d-none d-md-inline ms-1">Ingresar</span>
            </a>
            <?php endif; ?>
            
            <!-- Carrito -->
            <a href="<?= BASE_URL ?>/carrito" class="ts-nav-icon position-relative">
                <i class="bi bi-cart3"></i>
                <span class="ts-cart-badge" id="cartBadge">0</span>
            </a>
        </div>
        
        <!-- Toggle mobile -->
        <button class="navbar-toggler ts-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link ts-nav-link" href="<?= BASE_URL ?>/">
                        <i class="bi bi-house me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link ts-nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-grid me-1"></i>Categorías
                    </a>
                    <ul class="dropdown-menu ts-dropdown ts-mega-menu">
                        <?php 
                        $cats = (new CategoriaModel())->obtenerTodas();
                        foreach ($cats as $cat): ?>
                        <li>
                            <a class="dropdown-item" href="<?= BASE_URL ?>/categoria/<?= e($cat['slug']) ?>">
                                <i class="bi <?= e($cat['icono'] ?? 'bi-tag') ?> me-2 text-primary"></i>
                                <?= e($cat['nombre']) ?>
                                <span class="badge bg-secondary ms-auto"><?= $cat['total_productos'] ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link ts-nav-link" href="<?= BASE_URL ?>/catalogo">
                        <i class="bi bi-shop me-1"></i>Catálogo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link ts-nav-link" href="<?= BASE_URL ?>/catalogo?ofertas=1">
                        <i class="bi bi-tag me-1"></i>Ofertas
                    </a>
                </li>
                
                <!-- Búsqueda mobile -->
                <li class="nav-item d-lg-none mt-2">
                    <form action="<?= BASE_URL ?>/buscar" method="GET">
                        <div class="input-group">
                            <input type="search" name="q" class="form-control" placeholder="Buscar...">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php $flash = getFlash(); if ($flash): ?>
<div class="container mt-3">
    <div class="alert alert-<?= e($flash['tipo']) ?> alert-dismissible fade show ts-alert" role="alert">
        <i class="bi bi-<?= $flash['tipo'] === 'success' ? 'check-circle' : ($flash['tipo'] === 'danger' ? 'x-circle' : 'info-circle') ?> me-2"></i>
        <?= e($flash['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100" id="toastContainer"></div>

<main>
