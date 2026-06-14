-- ============================================================
--  TechStore - Script de Base de Datos MySQL
--  Versión: 1.0
--  Fecha: 2025
--  Descripción: Schema completo con datos de prueba
-- ============================================================

CREATE DATABASE IF NOT EXISTS techstore 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE techstore;

-- ============================================================
-- TABLA: configuracion_empresa
-- ============================================================
CREATE TABLE IF NOT EXISTS configuracion_empresa (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL DEFAULT 'TechStore',
  slogan VARCHAR(255) DEFAULT NULL,
  descripcion TEXT DEFAULT NULL,
  email VARCHAR(150) NOT NULL,
  telefono VARCHAR(30) DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL,
  ciudad VARCHAR(100) DEFAULT NULL,
  pais VARCHAR(100) DEFAULT 'Bolivia',
  redes_sociales JSON DEFAULT NULL,
  logo VARCHAR(255) DEFAULT NULL,
  favicon VARCHAR(255) DEFAULT NULL,
  moneda VARCHAR(10) DEFAULT 'Bs.',
  iva_porcentaje DECIMAL(5,2) DEFAULT 13.00,
  envio_gratis_desde DECIMAL(10,2) DEFAULT 500.00,
  costo_envio DECIMAL(10,2) DEFAULT 25.00,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: usuarios (administradores)
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('superadmin','admin','vendedor') NOT NULL DEFAULT 'vendedor',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  avatar VARCHAR(255) DEFAULT NULL,
  ultimo_acceso DATETIME DEFAULT NULL,
  token_reset VARCHAR(100) DEFAULT NULL,
  token_expira DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_email (email),
  INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: clientes
-- ============================================================
CREATE TABLE IF NOT EXISTS clientes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  telefono VARCHAR(30) DEFAULT NULL,
  fecha_nacimiento DATE DEFAULT NULL,
  genero ENUM('M','F','otro') DEFAULT NULL,
  direccion VARCHAR(255) DEFAULT NULL,
  ciudad VARCHAR(100) DEFAULT NULL,
  departamento VARCHAR(100) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  verificado TINYINT(1) NOT NULL DEFAULT 0,
  token_verificacion VARCHAR(100) DEFAULT NULL,
  token_reset VARCHAR(100) DEFAULT NULL,
  token_expira DATETIME DEFAULT NULL,
  ultimo_acceso DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_email (email),
  INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: categorias
-- ============================================================
CREATE TABLE IF NOT EXISTS categorias (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  descripcion TEXT DEFAULT NULL,
  icono VARCHAR(100) DEFAULT NULL,
  imagen VARCHAR(255) DEFAULT NULL,
  categoria_padre INT UNSIGNED DEFAULT NULL,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  orden INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_slug (slug),
  INDEX idx_activa (activa),
  INDEX idx_padre (categoria_padre),
  CONSTRAINT fk_cat_padre FOREIGN KEY (categoria_padre) REFERENCES categorias(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: productos
-- ============================================================
CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  categoria_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  sku VARCHAR(50) NOT NULL UNIQUE,
  descripcion_corta VARCHAR(500) DEFAULT NULL,
  descripcion TEXT DEFAULT NULL,
  especificaciones JSON DEFAULT NULL,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  precio_oferta DECIMAL(10,2) DEFAULT NULL,
  stock INT NOT NULL DEFAULT 0,
  stock_minimo INT NOT NULL DEFAULT 5,
  imagen_principal VARCHAR(255) DEFAULT NULL,
  marca VARCHAR(100) DEFAULT NULL,
  modelo VARCHAR(150) DEFAULT NULL,
  garantia VARCHAR(100) DEFAULT NULL,
  destacado TINYINT(1) NOT NULL DEFAULT 0,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  visitas INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_categoria (categoria_id),
  INDEX idx_slug (slug),
  INDEX idx_sku (sku),
  INDEX idx_precio (precio),
  INDEX idx_destacado (destacado),
  INDEX idx_activo (activo),
  FULLTEXT INDEX idx_busqueda (nombre, descripcion_corta),
  CONSTRAINT fk_prod_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: imagenes_productos
-- ============================================================
CREATE TABLE IF NOT EXISTS imagenes_productos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  producto_id INT UNSIGNED NOT NULL,
  imagen VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) DEFAULT NULL,
  orden INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_producto (producto_id),
  CONSTRAINT fk_img_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: carrito
-- ============================================================
CREATE TABLE IF NOT EXISTS carrito (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_id INT UNSIGNED DEFAULT NULL,
  session_id VARCHAR(100) DEFAULT NULL,
  producto_id INT UNSIGNED NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_cliente (cliente_id),
  INDEX idx_session (session_id),
  INDEX idx_producto (producto_id),
  CONSTRAINT fk_car_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_car_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: pedidos
-- ============================================================
CREATE TABLE IF NOT EXISTS pedidos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  numero_orden VARCHAR(20) NOT NULL UNIQUE,
  cliente_id INT UNSIGNED NOT NULL,
  estado ENUM('pendiente','confirmado','procesando','enviado','entregado','cancelado','reembolsado') NOT NULL DEFAULT 'pendiente',
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  iva DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  costo_envio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  nombre_envio VARCHAR(200) NOT NULL,
  email_envio VARCHAR(150) NOT NULL,
  telefono_envio VARCHAR(30) DEFAULT NULL,
  direccion_envio VARCHAR(255) NOT NULL,
  ciudad_envio VARCHAR(100) NOT NULL,
  departamento_envio VARCHAR(100) DEFAULT NULL,
  notas TEXT DEFAULT NULL,
  metodo_pago VARCHAR(50) DEFAULT 'efectivo',
  ip_cliente VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_numero_orden (numero_orden),
  INDEX idx_cliente (cliente_id),
  INDEX idx_estado (estado),
  CONSTRAINT fk_ped_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: detalle_pedidos
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_pedidos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pedido_id INT UNSIGNED NOT NULL,
  producto_id INT UNSIGNED NOT NULL,
  nombre_producto VARCHAR(255) NOT NULL,
  sku_producto VARCHAR(50) DEFAULT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_pedido (pedido_id),
  INDEX idx_producto (producto_id),
  CONSTRAINT fk_det_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_det_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: pagos
-- ============================================================
CREATE TABLE IF NOT EXISTS pagos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pedido_id INT UNSIGNED NOT NULL,
  metodo ENUM('efectivo','transferencia','tarjeta','qr','otro') NOT NULL DEFAULT 'efectivo',
  estado ENUM('pendiente','completado','fallido','reembolsado') NOT NULL DEFAULT 'pendiente',
  monto DECIMAL(10,2) NOT NULL,
  referencia VARCHAR(100) DEFAULT NULL,
  comprobante VARCHAR(255) DEFAULT NULL,
  notas TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_pedido (pedido_id),
  INDEX idx_estado (estado),
  CONSTRAINT fk_pago_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: testimonios
-- ============================================================
CREATE TABLE IF NOT EXISTS testimonios (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_id INT UNSIGNED DEFAULT NULL,
  nombre VARCHAR(150) NOT NULL,
  cargo VARCHAR(100) DEFAULT NULL,
  mensaje TEXT NOT NULL,
  calificacion TINYINT NOT NULL DEFAULT 5,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_test_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: banners
-- ============================================================
CREATE TABLE IF NOT EXISTS banners (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(200) NOT NULL,
  subtitulo VARCHAR(300) DEFAULT NULL,
  imagen VARCHAR(255) NOT NULL,
  url_destino VARCHAR(500) DEFAULT NULL,
  texto_boton VARCHAR(80) DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  orden INT UNSIGNED DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS DE PRUEBA
-- ============================================================

-- Configuración empresa
INSERT INTO configuracion_empresa (nombre, slogan, descripcion, email, telefono, direccion, ciudad, pais, redes_sociales, moneda, iva_porcentaje, envio_gratis_desde, costo_envio) VALUES
('TechStore Bolivia', 'Tu Tienda de Tecnología de Confianza', 'Somos una empresa líder en la venta de equipos tecnológicos, con más de 10 años de experiencia ofreciendo los mejores productos al mejor precio.', 'info@techstore.bo', '+591 2 123-4567', 'Av. Tecnológica #123, Zona Central', 'La Paz', 'Bolivia',
'{"facebook":"https://facebook.com/techstorebo","instagram":"https://instagram.com/techstorebo","twitter":"https://twitter.com/techstorebo","whatsapp":"https://wa.me/59171234567"}',
'Bs.', 13.00, 500.00, 25.00);

-- Usuario administrador (password: Admin123!)
INSERT INTO usuarios (nombre, apellido, email, password, rol, activo) VALUES
('Admin', 'TechStore', 'admin@techstore.bo', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uYutlQda2', 'superadmin', 1),
('Carlos', 'Mendoza', 'carlos@techstore.bo', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uYutlQda2', 'admin', 1),
('Maria', 'López', 'maria@techstore.bo', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uYutlQda2', 'vendedor', 1);

-- Clientes de prueba (password: Cliente123!)
INSERT INTO clientes (nombre, apellido, email, password, telefono, ciudad, departamento, activo, verificado) VALUES
('Juan', 'Pérez', 'juan@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uYutlQda2', '71234567', 'La Paz', 'La Paz', 1, 1),
('Ana', 'García', 'ana@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uYutlQda2', '76543210', 'Cochabamba', 'Cochabamba', 1, 1),
('Pedro', 'Quispe', 'pedro@email.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uYutlQda2', '78901234', 'Santa Cruz', 'Santa Cruz', 1, 1);

-- Categorías
INSERT INTO categorias (nombre, slug, descripcion, icono, activa, orden) VALUES
('Laptops', 'laptops', 'Portátiles para trabajo, estudio y gaming de las mejores marcas', 'bi-laptop', 1, 1),
('Computadoras de Escritorio', 'computadoras-escritorio', 'PCs de alto rendimiento para oficina y hogar', 'bi-pc-display', 1, 2),
('Componentes', 'componentes', 'Procesadores, RAM, discos duros, tarjetas gráficas y más', 'bi-cpu', 1, 3),
('Monitores', 'monitores', 'Pantallas Full HD, 4K y curvas para máximo rendimiento visual', 'bi-display', 1, 4),
('Impresoras', 'impresoras', 'Impresoras de tinta, láser, multifuncionales y de gran formato', 'bi-printer', 1, 5),
('Redes', 'redes', 'Routers, switches, access points y cableado de red', 'bi-wifi', 1, 6),
('Accesorios', 'accesorios', 'Teclados, mouse, auriculares, webcams y más accesorios', 'bi-keyboard', 1, 7),
('Almacenamiento', 'almacenamiento', 'Discos duros externos, SSD, memorias USB y tarjetas SD', 'bi-hdd', 1, 8),
('Teléfonos y Tablets', 'telefonos-tablets', 'Smartphones, tablets y accesorios móviles', 'bi-phone', 1, 9),
('Gaming', 'gaming', 'Equipos y accesorios para gamers profesionales', 'bi-controller', 1, 10);

-- Productos - Laptops
INSERT INTO productos (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, especificaciones, precio, precio_oferta, stock, stock_minimo, imagen_principal, marca, modelo, garantia, destacado, activo) VALUES
(1, 'Laptop HP Pavilion 15 Intel Core i7', 'laptop-hp-pavilion-15-i7', 'LP-HP-001', 'Laptop potente con procesador Intel Core i7 de 11va generación, ideal para trabajo y entretenimiento', 
'La HP Pavilion 15 combina rendimiento y estilo en un diseño delgado y elegante. Con el procesador Intel Core i7 de 11va generación, 16GB de RAM y SSD de 512GB, esta laptop está lista para cualquier tarea. La pantalla Full HD de 15.6 pulgadas ofrece colores vibrantes y ángulos de visión amplios.',
'{"Procesador":"Intel Core i7-1165G7 2.8GHz","RAM":"16GB DDR4 3200MHz","Almacenamiento":"512GB SSD NVMe","Pantalla":"15.6 Full HD IPS","Gráficos":"Intel Iris Xe Graphics","Sistema Operativo":"Windows 11 Home","Batería":"41Wh hasta 8 horas","Peso":"1.75 kg","Puertos":"2x USB-A, 1x USB-C, HDMI, SD Card, 3.5mm Audio","Wireless":"Wi-Fi 6, Bluetooth 5.0","Color":"Plata Natural"}',
4599.00, 3999.00, 15, 3, 'hp-pavilion-15.jpg', 'HP', 'Pavilion 15-eh2001la', '1 año garantía HP', 1, 1),

(1, 'Laptop Dell Inspiron 14 AMD Ryzen 5', 'laptop-dell-inspiron-14-ryzen5', 'LP-DL-001', 'Dell Inspiron 14 con AMD Ryzen 5, perfecta para estudiantes y profesionales móviles', 
'La Dell Inspiron 14 es la compañera ideal para estudiantes y profesionales que necesitan potencia y portabilidad. Equipada con AMD Ryzen 5 5500U, 8GB RAM y SSD de 256GB, ofrece un rendimiento excepcional en un formato compacto.',
'{"Procesador":"AMD Ryzen 5 5500U 2.1GHz","RAM":"8GB DDR4","Almacenamiento":"256GB SSD","Pantalla":"14 pulgadas Full HD","Gráficos":"AMD Radeon Graphics integrada","Sistema Operativo":"Windows 11 Home","Batería":"54Wh hasta 10 horas","Peso":"1.53 kg","Puertos":"2x USB-A 3.0, 1x USB-C, HDMI, 3.5mm Audio","Wireless":"Wi-Fi 5, Bluetooth 5.0","Color":"Gris Titanio"}',
2899.00, NULL, 20, 5, 'dell-inspiron-14.jpg', 'Dell', 'Inspiron 14-5415', '1 año garantía Dell', 1, 1),

(1, 'Laptop ASUS VivoBook 15 Intel Core i5', 'laptop-asus-vivobook-15-i5', 'LP-AS-001', 'ASUS VivoBook 15 con procesador i5 de última generación, diseño delgado y batería de larga duración',
'El ASUS VivoBook 15 redefine la productividad con su diseño ultradelgado y rendimiento sobresaliente. Con Intel Core i5-1135G7, 8GB RAM y SSD de 512GB, es perfecto para multitarea. Su pantalla NanoEdge de 15.6" ofrece una experiencia visual inmersiva.',
'{"Procesador":"Intel Core i5-1135G7 2.4GHz","RAM":"8GB DDR4","Almacenamiento":"512GB SSD PCIe","Pantalla":"15.6 Full HD NanoEdge","Gráficos":"Intel Iris Xe","Sistema Operativo":"Windows 11 Home","Batería":"50Wh hasta 9 horas","Peso":"1.8 kg","Puertos":"1x USB-C, 2x USB-A, HDMI, SD, Audio","Wireless":"Wi-Fi 6, Bluetooth 5.0","Color":"Plata Transparente"}',
3199.00, 2799.00, 18, 4, 'asus-vivobook-15.jpg', 'ASUS', 'VivoBook 15 X513EA', '1 año garantía ASUS', 0, 1),

(1, 'Laptop Lenovo ThinkPad E14 Business', 'laptop-lenovo-thinkpad-e14', 'LP-LN-001', 'ThinkPad E14 empresarial con teclado ergonómico, seguridad avanzada y máximo rendimiento',
'El Lenovo ThinkPad E14 es el estándar de referencia para laptops empresariales. Con procesador AMD Ryzen 7, 16GB RAM y SSD de 512GB, combinado con el legendario teclado ThinkPad y características de seguridad avanzadas como lector de huella dactilar y cámara con tapa de privacidad.',
'{"Procesador":"AMD Ryzen 7 5700U 1.8GHz","RAM":"16GB DDR4","Almacenamiento":"512GB SSD NVMe","Pantalla":"14 Full HD IPS Anti-glare","Gráficos":"AMD Radeon RX Vega 8","Sistema Operativo":"Windows 11 Pro","Batería":"45Wh hasta 11 horas","Peso":"1.69 kg","Seguridad":"Lector huella, TPM 2.0, Tapa webcam","Wireless":"Wi-Fi 6, Bluetooth 5.1"}',
4999.00, NULL, 10, 2, 'lenovo-thinkpad-e14.jpg', 'Lenovo', 'ThinkPad E14 Gen3', '1 año garantía Lenovo', 1, 1),

(1, 'Laptop Gaming ASUS ROG Strix G15', 'laptop-gaming-asus-rog-strix-g15', 'LP-ROG-001', 'Laptop gaming de alto rendimiento con RTX 3060 y pantalla 144Hz para gaming profesional',
'El ASUS ROG Strix G15 está diseñado para gamers que exigen lo mejor. Con AMD Ryzen 9 5900HX, NVIDIA GeForce RTX 3060 6GB, 16GB RAM DDR4 y pantalla Full HD 144Hz, este equipo maneja cualquier juego a máxima configuración.',
'{"Procesador":"AMD Ryzen 9 5900HX 3.3GHz","RAM":"16GB DDR4 3200MHz","Almacenamiento":"512GB SSD NVMe + 1TB HDD","Pantalla":"15.6 Full HD IPS 144Hz","Gráficos":"NVIDIA GeForce RTX 3060 6GB","Sistema Operativo":"Windows 11 Home","Batería":"90Wh","Peso":"2.3 kg","Refrigeración":"Triple ventilador ROG","Iluminación":"RGB Aura Sync","Puertos":"USB-C, 3x USB-A, HDMI 2.0b, RJ-45, Audio"}',
8499.00, 7999.00, 8, 2, 'asus-rog-strix-g15.jpg', 'ASUS', 'ROG Strix G15 G513', '1 año garantía ASUS', 1, 1);

-- Productos - Computadoras
INSERT INTO productos (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, especificaciones, precio, precio_oferta, stock, stock_minimo, imagen_principal, marca, modelo, garantia, destacado, activo) VALUES
(2, 'PC de Escritorio HP Pavilion Desktop i7', 'pc-hp-pavilion-desktop-i7', 'PC-HP-001', 'PC de alto rendimiento con Core i7, ideal para hogar y oficina con Windows 11',
'La HP Pavilion Desktop ofrece el poder que necesitas para trabajo, entretenimiento y más. Con Intel Core i7-11700, 16GB RAM y SSD de 512GB + HDD de 1TB, más gráficos NVIDIA dedicados, este equipo maneja todo con facilidad.',
'{"Procesador":"Intel Core i7-11700 2.5GHz 8 núcleos","RAM":"16GB DDR4 3200MHz (expandible a 64GB)","Almacenamiento":"512GB SSD + 1TB HDD","Gráficos":"NVIDIA GeForce GTX 1650 4GB","Sistema Operativo":"Windows 11 Home","Puertos":"USB 3.1, USB-C, HDMI, DisplayPort, RJ-45","Óptico":"Lector DVD","Dimensiones":"15.5 x 36.4 x 29.4 cm"}',
5499.00, NULL, 12, 3, 'hp-pavilion-desktop.jpg', 'HP', 'Pavilion TP01-2xxx', '1 año garantía HP', 1, 1),

(2, 'All-in-One Dell Inspiron 24 Touch', 'all-in-one-dell-inspiron-24-touch', 'PC-DL-001', 'All-in-One con pantalla táctil 24 pulgadas Full HD, diseño compacto y elegante',
'El Dell Inspiron 24 AIO combina potencia y elegancia en un diseño que elimina el desorden. Con Intel Core i5, 8GB RAM y SSD 512GB, más una hermosa pantalla táctil de 24", es perfecto para familias y profesionales que valoran el espacio.',
'{"Procesador":"Intel Core i5-1135G7","RAM":"8GB DDR4","Almacenamiento":"512GB SSD","Pantalla":"23.8 Full HD Táctil","Gráficos":"Intel Iris Xe","Sistema Operativo":"Windows 11 Home","Webcam":"2MP Full HD","Audio":"Altavoces Waves MaxxAudio Pro","Wireless":"Wi-Fi 6, Bluetooth 5.0"}',
4999.00, 4499.00, 7, 2, 'dell-aio-24.jpg', 'Dell', 'Inspiron 24 5420 AIO', '1 año garantía Dell', 0, 1);

-- Productos - Componentes
INSERT INTO productos (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, especificaciones, precio, precio_oferta, stock, stock_minimo, imagen_principal, marca, modelo, garantia, destacado, activo) VALUES
(3, 'Procesador Intel Core i9-12900K', 'procesador-intel-core-i9-12900k', 'CP-IN-001', 'El procesador Intel de 12va generación para máximo rendimiento en workstations y gaming',
'El Intel Core i9-12900K representa la cúspide del rendimiento de consumo con arquitectura híbrida de 16 núcleos (8P+8E). Ideal para creadores de contenido, ingenieros y gamers que necesitan el máximo poder de procesamiento.',
'{"Núcleos":"16 (8P + 8E)","Hilos":"24","Frecuencia Base":"3.2GHz (P-core)","Frecuencia Turbo":"5.2GHz","Caché":"30MB Intel Smart Cache","TDP":"125W (PBP)","Socket":"LGA 1700","Memoria":"DDR4/DDR5 hasta 128GB","PCIe":"PCIe 5.0 y PCIe 4.0","Proceso":"Intel 7 (10nm)"}',
2499.00, 2199.00, 25, 5, 'intel-i9-12900k.jpg', 'Intel', 'Core i9-12900K', '3 años garantía Intel', 1, 1),

(3, 'Tarjeta Gráfica NVIDIA GeForce RTX 3080', 'tarjeta-grafica-nvidia-rtx-3080', 'CP-NV-001', 'RTX 3080 10GB - La GPU definitiva para gaming 4K y creación de contenido profesional',
'La NVIDIA GeForce RTX 3080 establece un nuevo estándar en el rendimiento de gaming con 8704 CUDA cores, 10GB GDDR6X y soporte para ray tracing y DLSS de segunda generación. Experimenta el gaming 4K como nunca antes.',
'{"CUDA Cores":"8704","Memoria":"10GB GDDR6X","Bus de memoria":"320-bit","Velocidad memoria":"19 Gbps","TDP":"320W","Conector":"PCIe 4.0 x16","Salidas":"3x DisplayPort 1.4a, 1x HDMI 2.1","Resolución máx":"7680x4320","DirectX":"12 Ultimate","NVENC/NVDEC":"Sí"}',
5999.00, NULL, 8, 2, 'nvidia-rtx-3080.jpg', 'NVIDIA', 'GeForce RTX 3080', '3 años garantía NVIDIA', 1, 1),

(3, 'RAM Kingston Fury Beast 32GB DDR4 3200MHz', 'ram-kingston-fury-beast-32gb-ddr4', 'CP-KG-001', 'Kit de 2x16GB DDR4 de alta velocidad con disipador de calor para gaming y workstations',
'La Kingston Fury Beast DDR4 ofrece velocidades de hasta 3200MHz con latencias optimizadas para gaming y productividad. Con su diseño de disipador agresivo y compatibilidad con XMP 2.0, esta RAM te da la ventaja que necesitas.',
'{"Capacidad":"32GB (2x16GB)","Tipo":"DDR4","Velocidad":"3200MHz","Latencia":"CL16","Voltaje":"1.35V","Form Factor":"DIMM 288-pin","XMP":"2.0","Color":"Negro con disipador"}',
599.00, 549.00, 40, 10, 'kingston-fury-32gb.jpg', 'Kingston', 'KF432C16BBK2/32', '2 años garantía Kingston', 0, 1),

(3, 'SSD Samsung 970 EVO Plus 1TB NVMe', 'ssd-samsung-970-evo-plus-1tb', 'CP-SS-001', 'SSD NVMe de alta velocidad para el máximo rendimiento en lectura y escritura',
'El Samsung 970 EVO Plus ofrece velocidades de lectura secuencial de hasta 3,500 MB/s y escritura de 3,300 MB/s, gracias a la tecnología V-NAND y el controlador MJX. La solución de almacenamiento definitiva para profesionales.',
'{"Capacidad":"1TB","Interfaz":"NVMe M.2 PCIe 3.0 x4","Factor de forma":"M.2 2280","Velocidad lectura":"3,500 MB/s","Velocidad escritura":"3,300 MB/s","IOPS lectura":"600,000","IOPS escritura":"550,000","Garantía":"5 años","Resistencia":"600 TBW","Cifrado":"AES 256-bit"}',
799.00, 699.00, 30, 8, 'samsung-970-evo-1tb.jpg', 'Samsung', '970 EVO Plus MZ-V7S1T0', '5 años garantía Samsung', 1, 1);

-- Productos - Monitores
INSERT INTO productos (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, especificaciones, precio, precio_oferta, stock, stock_minimo, imagen_principal, marca, modelo, garantia, destacado, activo) VALUES
(4, 'Monitor LG 27UK850 4K IPS 27"', 'monitor-lg-27uk850-4k-27', 'MN-LG-001', 'Monitor 4K Ultra HD con panel IPS, HDR400 y USB-C para máxima fidelidad de color',
'El LG 27UK850 ofrece una experiencia visual extraordinaria con resolución 4K UHD, panel IPS de amplio espectro de color y compatibilidad con HDR400. Con conectividad USB-C para laptops, es el monitor ideal para diseñadores y profesionales.',
'{"Tamaño":"27 pulgadas","Resolución":"3840x2160 (4K UHD)","Panel":"IPS","Refresh Rate":"60Hz","Tiempo Respuesta":"5ms","HDR":"HDR400","Brillo":"350 cd/m²","Contraste":"1000:1","Color":"99% sRGB, 95% DCI-P3","Conectividad":"2x HDMI 2.0, DisplayPort 1.4, 2x USB-A, 1x USB-C 60W","VESA":"100x100mm","Ajuste":"Altura, Pivote, Inclinación"}',
2199.00, 1899.00, 10, 3, 'lg-27uk850.jpg', 'LG', '27UK850-W', '1 año garantía LG', 1, 1),

(4, 'Monitor Samsung Odyssey G5 32" Curvo 144Hz', 'monitor-samsung-odyssey-g5-32-curvo', 'MN-SS-001', 'Monitor gaming curvo WQHD 32" con 144Hz y 1ms para gaming ultra inmersivo',
'El Samsung Odyssey G5 redefine el gaming con su panel curvo 1000R que envuelve tu visión y resolución WQHD de 2560x1440. Con 144Hz y 1ms de respuesta, cada frame se muestra con perfecta fluidez y nitidez.',
'{"Tamaño":"32 pulgadas","Resolución":"2560x1440 (WQHD)","Panel":"VA Curvo 1000R","Refresh Rate":"144Hz","Tiempo Respuesta":"1ms MPRT","HDR":"HDR10","Brillo":"300 cd/m²","Contraste":"2500:1 (típico)","FreeSync":"Premium","Conectividad":"2x HDMI 1.4, 1x DisplayPort 1.2","VESA":"75x75mm"}',
2499.00, NULL, 12, 3, 'samsung-odyssey-g5.jpg', 'Samsung', 'Odyssey G5 LC32G55TQWLXZL', '1 año garantía Samsung', 0, 1);

-- Productos - Impresoras
INSERT INTO productos (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, especificaciones, precio, precio_oferta, stock, stock_minimo, imagen_principal, marca, modelo, garantia, destacado, activo) VALUES
(5, 'Impresora Epson EcoTank L3250 Multifunción WiFi', 'impresora-epson-ecotank-l3250', 'IM-EP-001', 'Multifuncional con sistema de tanque de tinta recargable, WiFi y app Epson Smart Panel',
'La Epson EcoTank L3250 elimina el costoso ciclo de los cartuchos con su sistema de tanques de tinta recargables. Con conectividad WiFi integrada y la app Epson Smart Panel, imprime, copia y escanea con facilidad desde tu smartphone.',
'{"Función":"Imprime, Copia, Escanea","Tecnología":"Inyección de tinta Micro Piezo","Resolución impresión":"5760x1440 dpi","Velocidad B/N":"33 ppm","Velocidad Color":"15 ppm","Conectividad":"USB, WiFi, WiFi Direct","App":"Epson Smart Panel","Rendimiento tinta negra":"4500 páginas","Rendimiento tinta color":"7500 páginas","Peso":"4 kg"}',
899.00, 849.00, 20, 5, 'epson-l3250.jpg', 'Epson', 'EcoTank L3250', '1 año garantía Epson', 1, 1),

(5, 'Impresora HP LaserJet Pro M404dn', 'impresora-hp-laserjet-pro-m404dn', 'IM-HP-001', 'Impresora láser monocromo de alta velocidad con impresión automática a doble cara',
'La HP LaserJet Pro M404dn es ideal para pequeñas empresas que necesitan impresión rápida y de alta calidad. Con hasta 38 ppm, dúplex automático y conectividad por red, aumenta la productividad de tu equipo.',
'{"Función":"Impresión","Tecnología":"Láser monocromo","Resolución":"1200x1200 dpi","Velocidad":"38 ppm","Dúplex":"Automático","Conectividad":"USB 2.0, Gigabit Ethernet","Capacidad bandeja":"100 hojas entrada manual + 250 hojas principal","Ciclo mensual":"80,000 páginas","Rendimiento cartucho":"9,200 páginas","Peso":"9.5 kg"}',
1599.00, NULL, 15, 4, 'hp-laserjet-m404dn.jpg', 'HP', 'LaserJet Pro M404dn', '1 año garantía HP', 0, 1);

-- Productos - Redes
INSERT INTO productos (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, especificaciones, precio, precio_oferta, stock, stock_minimo, imagen_principal, marca, modelo, garantia, destacado, activo) VALUES
(6, 'Router WiFi 6 TP-Link Archer AX73', 'router-wifi6-tp-link-archer-ax73', 'RD-TP-001', 'Router WiFi 6 AX5400 con cobertura superior y velocidades ultrarrápidas para el hogar',
'El TP-Link Archer AX73 lleva el WiFi de tu hogar al siguiente nivel con WiFi 6 (802.11ax), velocidades combinadas de 5400Mbps y 6 antenas de alta ganancia para cobertura total. Con OFDMA y MU-MIMO, conecta más dispositivos sin perder velocidad.',
'{"Estándar":"WiFi 6 (802.11ax)","Velocidad":"AX5400 (4804+574 Mbps)","Bandas":"Dual Band (2.4GHz + 5GHz)","Antenas":"6 externas de alta ganancia","MU-MIMO":"8x8","Procesador":"1.5GHz Tri-core","RAM":"512MB","Flash":"128MB","Puertos":"1x WAN Gigabit, 4x LAN Gigabit, 1x USB 3.0","Seguridad":"WPA3, HomeCare"}',
799.00, 699.00, 18, 5, 'tp-link-ax73.jpg', 'TP-Link', 'Archer AX73', '2 años garantía TP-Link', 1, 1),

(6, 'Switch TP-Link 24 Puertos Gigabit TL-SG1024D', 'switch-tp-link-24-puertos-gigabit', 'RD-TP-002', 'Switch no administrable de 24 puertos Gigabit para redes empresariales y PYMES',
'El TP-Link TL-SG1024D ofrece 24 puertos Gigabit en un chasis de rack 1U, perfecto para expandir redes en oficinas y PYMES. Con capacidad de switching de 48Gbps y Auto MDI/MDIX en todos los puertos.',
'{"Puertos":"24x RJ-45 10/100/1000Mbps","Capacidad Switching":"48Gbps","Tabla MAC":"8K","Buffer":"4Mb","Forma":"Rack 1U 19 pulgadas","Dimensiones":"17.3 x 7.1 x 1.0 in","Alimentación":"100-240V","Consumo":"11.6W máx","Temperatura operación":"0-40°C"}',
499.00, NULL, 14, 4, 'tp-link-sg1024d.jpg', 'TP-Link', 'TL-SG1024D', '2 años garantía TP-Link', 0, 1);

-- Productos - Accesorios
INSERT INTO productos (categoria_id, nombre, slug, sku, descripcion_corta, descripcion, especificaciones, precio, precio_oferta, stock, stock_minimo, imagen_principal, marca, modelo, garantia, destacado, activo) VALUES
(7, 'Teclado Mecánico Logitech G Pro X TKL', 'teclado-mecanico-logitech-g-pro-x', 'AC-LG-001', 'Teclado mecánico gaming TKL con switches intercambiables GX y retroiluminación RGB',
'El Logitech G Pro X TKL es el teclado elegido por los pro-gamers más competitivos. Con tecnología de switches intercambiables en caliente, retroiluminación RGB LIGHTSYNC y diseño TKL compacto, domina cualquier competencia.',
'{"Tipo":"Mecánico TKL","Switches":"Logitech GX Blue (Clicky) - intercambiables","Retroiluminación":"RGB por tecla LIGHTSYNC","Cable":"Desmontable USB","Anti-ghosting":"Full","Teclas multimedia":"Sí","Dimensiones":"361.4 x 152.4 x 34.2 mm","Peso":"980g"}',
799.00, 699.00, 22, 5, 'logitech-g-pro-x.jpg', 'Logitech', 'G Pro X TKL', '2 años garantía Logitech', 0, 1),

(7, 'Mouse Inalámbrico Logitech MX Master 3', 'mouse-inalambrico-logitech-mx-master-3', 'AC-LG-002', 'El mejor mouse para productividad con scroll MagSpeed, botones programmables y multi-dispositivo',
'El Logitech MX Master 3 es el mouse definitivo para profesionales. Con rueda de desplazamiento MagSpeed electromagnética de 1000 líneas/segundo, 8 botones programables y la capacidad de controlar hasta 3 computadoras simultáneamente con Flow.',
'{"Tipo":"Inalámbrico","Sensores":"Darkfield High Precision","DPI":"200-4000 DPI (ajustable)","Botones":"8 programables","Conectividad":"Bluetooth, Unificador USB Logitech","Multi-dispositivo":"Hasta 3 con Easy Switch","Batería":"Recargable USB-C, 70 días","Peso":"141g","Compatibilidad":"Windows, macOS, Linux"}',
549.00, NULL, 30, 8, 'logitech-mx-master-3.jpg', 'Logitech', 'MX Master 3', '2 años garantía Logitech', 1, 1),

(7, 'Auriculares Sony WH-1000XM5 Noise Cancelling', 'auriculares-sony-wh-1000xm5', 'AC-SN-001', 'Los mejores auriculares inalámbricos con cancelación de ruido líder en la industria',
'Los Sony WH-1000XM5 establecen el estándar de oro en cancelación de ruido. Con procesamiento QN1 y 8 micrófonos, eliminan prácticamente todo el ruido ambiental. La batería de 30 horas y la carga rápida los hacen perfectos para viajes y trabajo.',
'{"Tipo":"Over-ear inalámbrico","Cancelación de ruido":"Inteligente adaptativa","Procesador":"HD Noise Cancelling QN1","Micrófonos":"8 (4 para ANC, 4 para llamadas)","Driver":"30mm","Respuesta frecuencia":"4Hz-40,000Hz","Batería":"30 horas con ANC","Carga rápida":"3 min = 3 horas","Conectividad":"Bluetooth 5.2, NFC, 3.5mm","Peso":"250g"}',
1299.00, 1099.00, 15, 4, 'sony-wh1000xm5.jpg', 'Sony', 'WH-1000XM5', '1 año garantía Sony', 1, 1);

-- Testimonios
INSERT INTO testimonios (nombre, cargo, mensaje, calificacion, activo) VALUES
('Carlos Rodríguez', 'Ingeniero de Sistemas', 'Excelente servicio y productos de calidad. Mi laptop llegó en perfectas condiciones y el soporte técnico fue muy profesional. Definitivamente volvería a comprar.', 5, 1),
('María Fernández', 'Diseñadora Gráfica', 'Compré una tarjeta gráfica y el proceso fue muy sencillo. Los precios son competitivos y la entrega fue rápida. Muy satisfecha con mi compra.', 5, 1),
('Roberto Quispe', 'Emprendedor', 'La mejor tienda de tecnología de La Paz. Encontré todo lo que necesitaba para equipar mi oficina. El asesoramiento del equipo fue clave para tomar la mejor decisión.', 4, 1),
('Ana Vargas', 'Estudiante Universitaria', 'Compré mi primera laptop aquí y la experiencia fue increíble. Me ayudaron a elegir el modelo correcto según mi presupuesto y necesidades. ¡Totalmente recomendado!', 5, 1),
('Diego Mamani', 'Gerente de IT', 'Proveemos nuestros equipos corporativos con TechStore Bolivia. La atención empresarial es excelente, los precios justos y el servicio post-venta es de primer nivel.', 5, 1);

-- Banners
INSERT INTO banners (titulo, subtitulo, imagen, url_destino, texto_boton, activo, orden) VALUES
('Tecnología de Vanguardia', 'Los mejores equipos para profesionales y gamers. Envío gratis en compras mayores a Bs. 500', 'banner-tech.jpg', '/catalogo', 'Ver Catálogo', 1, 1),
('Laptops desde Bs. 2,899', 'La mejor selección de portátiles para trabajo, estudio y gaming con garantía oficial', 'banner-laptops.jpg', '/catalogo?categoria=laptops', 'Ver Laptops', 1, 2),
('Componentes de Alto Rendimiento', 'Arma tu PC dream build con los mejores procesadores, GPUs y memorias RAM', 'banner-componentes.jpg', '/catalogo?categoria=componentes', 'Ver Componentes', 1, 3);

-- Pedido de ejemplo
INSERT INTO pedidos (numero_orden, cliente_id, estado, subtotal, iva, costo_envio, total, nombre_envio, email_envio, telefono_envio, direccion_envio, ciudad_envio, departamento_envio, metodo_pago, ip_cliente) VALUES
('TS-2025-000001', 1, 'entregado', 3999.00, 519.87, 0.00, 4518.87, 'Juan Pérez', 'juan@email.com', '71234567', 'Av. Los Rosales #456, Zona Sur', 'La Paz', 'La Paz', 'efectivo', '127.0.0.1'),
('TS-2025-000002', 2, 'procesando', 2199.00, 285.87, 25.00, 2509.87, 'Ana García', 'ana@email.com', '76543210', 'Calle Principal #123', 'Cochabamba', 'Cochabamba', 'transferencia', '127.0.0.1');

INSERT INTO detalle_pedidos (pedido_id, producto_id, nombre_producto, sku_producto, cantidad, precio_unitario, subtotal) VALUES
(1, 2, 'Laptop Dell Inspiron 14 AMD Ryzen 5', 'LP-DL-001', 1, 3999.00, 3999.00),
(2, 8, 'Monitor LG 27UK850 4K IPS 27"', 'MN-LG-001', 1, 1899.00, 1899.00),
(2, 13, 'Mouse Inalámbrico Logitech MX Master 3', 'AC-LG-002', 1, 300.00, 300.00);

INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia) VALUES
(1, 'efectivo', 'completado', 4518.87, NULL),
(2, 'transferencia', 'pendiente', 2509.87, 'TRF-20250601-001');
