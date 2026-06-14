-- ============================================================
--  TechStore - Migración: IVA por producto + Config Envío
--  Archivo: sql/migracion_iva_envio.sql
--  Ejecutar en phpMyAdmin sobre la BD techstore
-- ============================================================

USE techstore;

-- 1) Agregar campo tiene_iva a productos
ALTER TABLE productos 
    ADD COLUMN tiene_iva TINYINT(1) NOT NULL DEFAULT 1 
    COMMENT '1=aplica IVA, 0=exento de IVA'
    AFTER precio_oferta;

-- 2) Agregar campo porcentaje_iva por producto (hereda global si es NULL)
ALTER TABLE productos
    ADD COLUMN porcentaje_iva DECIMAL(5,2) DEFAULT NULL
    COMMENT 'NULL = usar el IVA global de configuracion_empresa'
    AFTER tiene_iva;

-- 3) Ampliar tabla configuracion_empresa con zonas de envío
ALTER TABLE configuracion_empresa
    ADD COLUMN envio_activo TINYINT(1) NOT NULL DEFAULT 1
        COMMENT '1=ofrece envío, 0=solo retiro en tienda'
        AFTER costo_envio,
    ADD COLUMN envio_gratis_activo TINYINT(1) NOT NULL DEFAULT 1
        COMMENT '1=aplicar envío gratis por monto mínimo'
        AFTER envio_activo,
    ADD COLUMN envio_zonas JSON DEFAULT NULL
        COMMENT 'Zonas de envío con costos diferenciados'
        AFTER envio_gratis_activo,
    ADD COLUMN envio_tiempo_estimado VARCHAR(100) DEFAULT '1-3 días hábiles'
        AFTER envio_zonas,
    ADD COLUMN envio_nota TEXT DEFAULT NULL
        COMMENT 'Mensaje informativo sobre el envío'
        AFTER envio_tiempo_estimado;

-- 4) Crear tabla de zonas de envío (para gestión avanzada)
CREATE TABLE IF NOT EXISTS zonas_envio (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) DEFAULT NULL,
    ciudades JSON DEFAULT NULL     COMMENT 'Lista de ciudades incluidas',
    costo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    costo_express DECIMAL(10,2) DEFAULT NULL,
    tiempo_estimado VARCHAR(100) DEFAULT '1-3 días hábiles',
    activa TINYINT(1) NOT NULL DEFAULT 1,
    orden INT UNSIGNED DEFAULT 0,
    PRIMARY KEY (id),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5) Insertar zonas de envío de prueba (Bolivia)
INSERT INTO zonas_envio (nombre, descripcion, ciudades, costo, costo_express, tiempo_estimado, activa, orden) VALUES
('La Paz / El Alto', 'Entrega en La Paz y El Alto', 
 '["La Paz","El Alto"]', 
 25.00, 50.00, 'Mismo día o siguiente día hábil', 1, 1),

('Cochabamba', 'Entrega en Cochabamba', 
 '["Cochabamba","Quillacollo","Sacaba"]', 
 35.00, 70.00, '1-2 días hábiles', 1, 2),

('Santa Cruz', 'Entrega en Santa Cruz de la Sierra', 
 '["Santa Cruz","Montero","Warnes"]', 
 35.00, 70.00, '1-2 días hábiles', 1, 3),

('Oruro / Potosí', 'Entrega en Oruro y Potosí', 
 '["Oruro","Potosí"]', 
 40.00, 80.00, '2-3 días hábiles', 1, 4),

('Sucre', 'Entrega en Sucre',
 '["Sucre"]',
 40.00, 80.00, '2-3 días hábiles', 1, 5),

('Beni / Pando / Tarija', 'Entrega en ciudades del oriente y sur',
 '["Trinidad","Cobija","Tarija","Yacuiba"]',
 50.00, 100.00, '3-5 días hábiles', 1, 6),

('Todo Bolivia (cobertura nacional)', 'Cualquier ciudad no listada',
 null,
 55.00, null, '3-7 días hábiles', 1, 7);

-- 6) Actualizar configuración empresa con datos de envío
UPDATE configuracion_empresa SET
    envio_tiempo_estimado = '1-3 días hábiles según zona',
    envio_nota = 'El costo de envío varía según tu ciudad. Envío gratis en compras mayores a Bs. 500 dentro de La Paz y El Alto.',
    envio_gratis_activo = 1
WHERE id = 1;

-- 7) Marcar algunos productos como exentos de IVA (ejemplo: accesorios de bajo costo)
-- Por defecto todos tienen IVA = 1. Ajustar según necesidad.
-- Ejemplo: memorias USB y cables podrían estar exentos
-- UPDATE productos SET tiene_iva = 0 WHERE precio < 50;

-- Verificar cambios
SELECT 'productos' AS tabla, COUNT(*) AS total, SUM(tiene_iva) AS con_iva, SUM(1-tiene_iva) AS sin_iva FROM productos
UNION ALL
SELECT 'zonas_envio', COUNT(*), SUM(activa), SUM(1-activa) FROM zonas_envio;
