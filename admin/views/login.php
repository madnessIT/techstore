<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo | TechStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Sora',sans-serif; background:linear-gradient(135deg,#0d1117 0%,#0a1628 100%); min-height:100vh; display:flex; align-items:center; }
        .login-card { background:white; border-radius:20px; padding:2.5rem; box-shadow:0 30px 80px rgba(0,0,0,.4); max-width:420px; width:100%; }
        .brand-icon { width:50px;height:50px;background:#0d6efd;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:white; }
        .form-control { border-radius:10px; border:1.5px solid #dee2e6; padding:.65rem 1rem; transition:all .2s; }
        .form-control:focus { border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,.12); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-card">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="brand-icon"><i class="bi bi-cpu-fill"></i></div>
                    <div>
                        <h1 class="h5 fw-800 mb-0">TechStore <span style="color:#0d6efd;">Admin</span></h1>
                        <p class="text-muted small mb-0">Panel de Administración</p>
                    </div>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger py-2 d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-x-circle-fill"></i><span class="small"><?= e($error) ?></span>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/admin/login">
                    <input type="hidden" name="_csrf" value="<?= adminCsrfGen() ?>">
                    <div class="mb-3">
                        <label class="fw-600 small mb-1">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0"
                                   value="<?= e($_POST['email'] ?? '') ?>"
                                   placeholder="admin@techstore.bo" required autocomplete="email">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="fw-600 small mb-1">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 border-end-0"
                                   id="adminPass" placeholder="Tu contraseña" required autocomplete="current-password">
                            <button class="input-group-text bg-light" type="button"
                                    onclick="const i=document.getElementById('adminPass');i.type=i.type==='password'?'text':'password'">
                                <i class="bi bi-eye text-muted"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-700 fs-5">
                        <i class="bi bi-shield-lock me-2"></i>Acceder al Panel
                    </button>
                </form>

                <hr class="my-3">
                <div class="text-center">
                    <a href="<?= BASE_URL ?>/" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Volver a la tienda
                    </a>
                </div>

                <!-- Credenciales demo -->
                <div class="mt-3 p-3 bg-light rounded-3" style="font-size:.78rem;">
                    <strong class="d-block text-muted mb-1"><i class="bi bi-info-circle me-1"></i>Credenciales de prueba:</strong>
                    <p class="mb-0 font-monospace">admin@techstore.bo / password</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
