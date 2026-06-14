<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloAdmin ?? 'Panel Admin') ?> | TechStore Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --admin-sidebar: 260px; --admin-nav: 60px; --primary:#0D6EFD; --dark:#212529; }
        body { font-family:'Sora',sans-serif; background:#f0f2f5; }

        /* Sidebar */
        .admin-sidebar {
            width: var(--admin-sidebar);
            min-height: 100vh;
            background: #0d1117;
            position: fixed; top:0; left:0; z-index:1030;
            display: flex; flex-direction: column;
            transition: transform .25s ease;
        }
        .admin-sidebar .brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap:10px;
        }
        .admin-sidebar .brand-icon {
            width:34px;height:34px;background:var(--primary);border-radius:8px;
            display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;
        }
        .admin-sidebar .brand-name { color:white;font-weight:800;font-size:1.1rem; }
        .admin-sidebar .brand-name span { color:var(--primary); }

        .admin-nav { padding: 1rem 0; flex:1; overflow-y:auto; }
        .admin-nav .nav-section {
            font-size:.65rem;text-transform:uppercase;letter-spacing:.8px;
            color:rgba(255,255,255,.3);padding:.5rem 1.5rem .3rem;font-weight:700;
        }
        .admin-nav .nav-link {
            color:rgba(255,255,255,.65);
            padding:.6rem 1.5rem;
            display:flex;align-items:center;gap:.75rem;
            font-size:.875rem;font-weight:500;
            border-radius:0;
            transition:all .2s;
        }
        .admin-nav .nav-link i { font-size:1rem;width:20px;text-align:center; }
        .admin-nav .nav-link:hover { color:white;background:rgba(255,255,255,.06); }
        .admin-nav .nav-link.active {
            color:white;background:rgba(13,110,253,.2);
            border-right:3px solid var(--primary);
        }

        /* Main */
        .admin-main {
            margin-left: var(--admin-sidebar);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* Top nav */
        .admin-topbar {
            background: white;
            height: var(--admin-nav);
            border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            position: sticky; top:0; z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }

        .admin-content { padding: 1.75rem; flex:1; }

        /* Cards */
        .admin-card {
            background:white;border-radius:12px;
            border:1px solid rgba(0,0,0,.06);
            box-shadow:0 2px 12px rgba(0,0,0,.04);
        }
        .admin-card .card-header {
            background:white;border-bottom:1px solid #f0f2f5;
            padding:1rem 1.5rem;border-radius:12px 12px 0 0 !important;
            font-weight:700;
        }
        .admin-card .card-body { padding:1.5rem; }

        /* Stat cards */
        .stat-card {
            background:white;border-radius:12px;padding:1.5rem;
            border:1px solid rgba(0,0,0,.06);
            display:flex;align-items:center;gap:1rem;
            box-shadow:0 2px 12px rgba(0,0,0,.04);
            transition:transform .2s;
        }
        .stat-card:hover { transform:translateY(-3px); }
        .stat-icon {
            width:54px;height:54px;border-radius:14px;
            display:flex;align-items:center;justify-content:center;
            font-size:1.4rem;flex-shrink:0;
        }
        .stat-value { font-size:1.6rem;font-weight:800;line-height:1.1; }
        .stat-label { font-size:.8rem;color:#6c757d; }

        /* Tablas */
        .admin-table th {
            font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;
            color:#6c757d;font-weight:700;background:#fafbfc;
            border-bottom:2px solid #f0f2f5;
        }
        .admin-table td { vertical-align:middle;border-color:#f5f5f5; }
        .admin-table tbody tr:hover td { background:#fafbff; }

        /* Estado badges */
        .estado-pendiente   { background:#fff3cd;color:#856404; }
        .estado-confirmado  { background:#d1ecf1;color:#0c5460; }
        .estado-procesando  { background:#cce5ff;color:#004085; }
        .estado-enviado     { background:#d4edda;color:#155724; }
        .estado-entregado   { background:#d4edda;color:#155724; }
        .estado-cancelado   { background:#f8d7da;color:#721c24; }
        .estado-reembolsado { background:#e2e3e5;color:#383d41; }

        /* Sidebar toggle mobile */
        .sidebar-overlay {
            display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1029;
        }
        @media(max-width:991px) {
            .admin-sidebar { transform:translateX(-100%); }
            .admin-sidebar.show { transform:translateX(0); }
            .admin-main { margin-left:0; }
            .sidebar-overlay.show { display:block; }
        }

        /* Forms */
        .admin-form-label { font-weight:600;font-size:.875rem;margin-bottom:.35rem; }
        .admin-form-control {
            border-radius:8px;border:1.5px solid #dee2e6;
            padding:.6rem .9rem;font-size:.9rem;
            transition:border-color .2s,box-shadow .2s;
        }
        .admin-form-control:focus {
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(13,110,253,.1);
        }

        /* Toast */
        .admin-flash {
            border-radius:10px;border:none;font-weight:500;
        }
    </style>
    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ============ SIDEBAR ============ -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-cpu-fill"></i></div>
        <div class="brand-name">Tech<span>Store</span> <small class="opacity-50" style="font-size:.6rem;">ADMIN</small></div>
    </div>

    <nav class="admin-nav">
        <span class="nav-section">Principal</span>
        <a href="<?= BASE_URL ?>/admin" class="nav-link <?= ($modulo ?? '') === '' || ($modulo ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <span class="nav-section mt-2">Catálogo</span>
        <a href="<?= BASE_URL ?>/admin/productos" class="nav-link <?= ($modulo ?? '') === 'productos' ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Productos
        </a>
        <a href="<?= BASE_URL ?>/admin/categorias" class="nav-link <?= ($modulo ?? '') === 'categorias' ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap"></i> Categorías
        </a>

        <span class="nav-section mt-2">Ventas</span>
        <a href="<?= BASE_URL ?>/admin/pedidos" class="nav-link <?= ($modulo ?? '') === 'pedidos' ? 'active' : '' ?>">
            <i class="bi bi-bag-check"></i> Pedidos
            <?php
            $db = Database::getInstance();
            $pendientes = (int)$db->scalar("SELECT COUNT(*) FROM pedidos WHERE estado='pendiente'");
            if ($pendientes > 0): ?>
            <span class="badge bg-danger ms-auto"><?= $pendientes ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/admin/reportes" class="nav-link <?= ($modulo ?? '') === 'reportes' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line"></i> Reportes
        </a>

        <span class="nav-section mt-2">Usuarios</span>
        <a href="<?= BASE_URL ?>/admin/clientes" class="nav-link <?= ($modulo ?? '') === 'clientes' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Clientes
        </a>
        <?php if (adminCan('admin')): ?>
        <a href="<?= BASE_URL ?>/admin/usuarios" class="nav-link <?= ($modulo ?? '') === 'usuarios' ? 'active' : '' ?>">
            <i class="bi bi-shield-person"></i> Administradores
        </a>
        <?php endif; ?>

        <span class="nav-section mt-2">Sistema</span>
        <a href="<?= BASE_URL ?>/admin/configuracion" class="nav-link <?= ($modulo ?? '') === 'configuracion' ? 'active' : '' ?>">
            <i class="bi bi-gear-fill"></i> IVA y Envío
        </a>
        <a href="<?= BASE_URL ?>/" target="_blank" class="nav-link">
            <i class="bi bi-box-arrow-up-right"></i> Ver Tienda
        </a>
        <a href="<?= BASE_URL ?>/admin/logout" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </nav>

    <!-- Admin info -->
    <div class="p-3 border-top" style="border-color:rgba(255,255,255,.07) !important;">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
            </div>
            <div style="overflow:hidden;">
                <p class="mb-0 text-white fw-600 small text-truncate"><?= e($_SESSION['admin_nombre'] ?? '') ?></p>
                <p class="mb-0 small text-truncate" style="color:rgba(255,255,255,.4);font-size:.72rem;">
                    <?= ucfirst($_SESSION['admin_rol'] ?? '') ?>
                </p>
            </div>
        </div>
    </div>
</aside>

<!-- ============ MAIN ============ -->
<div class="admin-main">
    <!-- Topbar -->
    <div class="admin-topbar">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="openSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="flex-grow-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small">
                        <a href="<?= BASE_URL ?>/admin" class="text-primary text-decoration-none">Dashboard</a>
                    </li>
                    <?php if (isset($breadcrumb)): foreach ($breadcrumb as $b): ?>
                    <li class="breadcrumb-item small <?= isset($b['url']) ? '' : 'active' ?>">
                        <?= isset($b['url']) ? '<a href="' . e($b['url']) . '" class="text-primary text-decoration-none">' . e($b['label']) . '</a>' : e($b['label']) ?>
                    </li>
                    <?php endforeach; endif; ?>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= BASE_URL ?>/admin/pedidos?estado=pendiente" class="btn btn-sm btn-outline-warning position-relative">
                <i class="bi bi-bell"></i>
                <?php if (isset($pendientes) && $pendientes > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;"><?= $pendientes ?></span>
                <?php endif; ?>
            </a>
            <span class="text-muted small d-none d-sm-inline"><?= date('d/m/Y') ?></span>
        </div>
    </div>

    <!-- Flash message -->
    <?php $f = adminGetFlash(); if ($f): ?>
    <div class="mx-3 mt-3">
        <div class="alert admin-flash alert-<?= e($f['tipo']) ?> alert-dismissible fade show d-flex gap-2 align-items-center" role="alert">
            <i class="bi bi-<?= $f['tipo'] === 'success' ? 'check-circle-fill' : ($f['tipo'] === 'danger' ? 'x-circle-fill' : 'exclamation-circle-fill') ?>"></i>
            <span><?= e($f['mensaje']) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <div class="admin-content">
