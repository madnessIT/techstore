# TechStore Bolivia — Manual de Instalación

> **Versión:** 1.0 · **Entorno:** XAMPP / AppServ (Apache + PHP 8 + MySQL)

---

## Requisitos del Sistema

| Componente | Mínimo requerido |
|---|---|
| PHP | 8.0 o superior |
| MySQL | 5.7 / MariaDB 10.3+ |
| Apache | 2.4 con mod_rewrite |
| XAMPP/AppServ | Cualquier versión reciente |
| Navegador | Chrome, Firefox, Edge (moderno) |

---

## Paso 1 — Copiar los archivos

1. Descarga o clona el proyecto.
2. Copia la carpeta `techstore/` dentro de:
   - **XAMPP:** `C:\xampp\htdocs\techstore\`
   - **AppServ:** `C:\AppServ\www\techstore\`
   - **Linux:** `/var/www/html/techstore/`

---

## Paso 2 — Crear la base de datos

1. Abre **phpMyAdmin** en `http://localhost/phpmyadmin`
2. Crea una base de datos llamada **`techstore`**
   - Cotejamiento: `utf8mb4_unicode_ci`
3. Selecciona la base de datos → pestaña **Importar**
4. Selecciona el archivo `sql/techstore.sql`
5. Haz clic en **Importar** → espera que finalice

---

## Paso 3 — Configurar la conexión

Edita `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'techstore');
define('DB_USER', 'root');       // Tu usuario MySQL
define('DB_PASS', '');           // Tu contraseña MySQL

define('BASE_URL', 'http://localhost/techstore');
```

---

## Paso 4 — Habilitar mod_rewrite

### En XAMPP:
1. Abre `C:\xampp\apache\conf\httpd.conf`
2. Descomenta la línea: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Busca `AllowOverride None` y cámbialo a `AllowOverride All`
4. Reinicia Apache

### En AppServ:
1. El mod_rewrite ya suele estar activo
2. Verifica en `httpd.conf` que `AllowOverride All` esté configurado

---

## Paso 5 — Crear carpetas de uploads

Crea las siguientes carpetas con permisos de escritura:
```
techstore/assets/images/products/
techstore/assets/uploads/
techstore/logs/
```

En Linux:
```bash
chmod 755 techstore/assets/images/products/
chmod 755 techstore/assets/uploads/
chmod 755 techstore/logs/
```

---

## Paso 6 — Acceder al sistema

| URL | Descripción |
|---|---|
| `http://localhost/techstore/` | Tienda online |
| `http://localhost/techstore/admin` | Panel administrativo |

---

## Credenciales de Acceso

### Panel Administrativo
| Campo | Valor |
|---|---|
| Email | `admin@techstore.bo` |
| Contraseña | `password` |
| Rol | Super Admin |

### Clientes de Prueba
| Email | Contraseña |
|---|---|
| `juan@email.com` | `password` |
| `ana@email.com` | `password` |

> ⚠️ **IMPORTANTE:** Cambia todas las contraseñas antes de poner en producción.

---

## Estructura de Carpetas

```
techstore/
├── admin/                    # Panel administrativo
│   ├── controllers/          # Controladores admin
│   ├── views/                # Vistas del admin
│   │   ├── partials/         # Header/Footer admin
│   │   ├── products/         # CRUD productos
│   │   ├── categories/       # CRUD categorías
│   │   ├── orders/           # Gestión pedidos
│   │   ├── clients/          # Gestión clientes
│   │   ├── users/            # Gestión usuarios
│   │   └── reports/          # Reportes
│   ├── AdminHelpers.php      # Funciones helpers admin
│   └── index.php             # Router del admin
├── assets/
│   ├── css/
│   │   └── techstore.css     # Estilos principales
│   ├── js/
│   │   └── techstore.js      # JavaScript principal
│   ├── images/
│   │   └── products/         # Imágenes de productos
│   └── uploads/              # Archivos subidos
├── config/
│   ├── config.php            # Configuración global
│   └── Database.php          # Clase PDO (Singleton)
├── controllers/
│   ├── AuthController.php    # Login/Registro/Cuenta
│   └── CatalogoController.php # Catálogo/Carrito/Checkout
├── models/
│   ├── ProductoModel.php     # Modelo productos
│   └── Models.php            # Otros modelos
├── sql/
│   └── techstore.sql         # Script base de datos
├── views/
│   ├── partials/
│   │   ├── header.php        # Navbar y head HTML
│   │   ├── footer.php        # Footer y scripts
│   │   └── product-card.php  # Tarjeta producto
│   ├── auth/                 # Login/Registro/Cuenta
│   ├── cart/                 # Carrito
│   ├── catalog/              # Catálogo y detalle
│   ├── checkout/             # Proceso de pago
│   ├── home.php              # Página principal
│   └── 404.php               # Página error
├── docs/
│   └── README.md             # Este manual
├── .htaccess                 # Reescritura de URLs
└── index.php                 # Front Controller
```

---

## Solución de Problemas

### Error: "No encontrado" / 404 en todas las páginas
- Verifica que `mod_rewrite` esté activo en Apache
- Revisa que `AllowOverride All` esté configurado
- Confirma que el `.htaccess` existe en la raíz

### Error de conexión a BD
- Verifica las credenciales en `config/config.php`
- Confirma que MySQL está corriendo
- Asegúrate que la base de datos `techstore` existe

### Imágenes no cargan
- Verifica que `BASE_URL` en `config.php` sea correcto
- Asegúrate que las carpetas de imágenes existen
- Revisa permisos de las carpetas en Linux

### Error en subida de imágenes
- Verifica en `php.ini`: `file_uploads = On`
- Aumenta `upload_max_filesize = 10M`
- Aumenta `post_max_size = 10M`

---

## Configuración para Producción

1. Cambiar en `config.php`:
   ```php
   define('ENTORNO', 'produccion');
   define('BASE_URL', 'https://tudominio.com');
   ```
2. Cambiar todas las contraseñas
3. Habilitar HTTPS (descomentar en `.htaccess`)
4. Configurar el correo SMTP real

---

*TechStore Bolivia v1.0 — Desarrollado con PHP 8, MySQL, Bootstrap 5*
