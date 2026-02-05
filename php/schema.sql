CREATE DATABASE IF NOT EXISTS sistema_facturas;
USE sistema_facturas;

CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(20) NOT NULL,
    cliente_nombre VARCHAR(100) NOT NULL,
    cliente_ruc VARCHAR(20) NOT NULL,
    cliente_direccion VARCHAR(255),
    cliente_email VARCHAR(100),
    fecha_emision DATE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    porcentaje_iva DECIMAL(5, 2) NOT NULL DEFAULT 12.00,
    monto_iva DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    propina DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    es_negociable BOOLEAN NOT NULL DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS detalles_factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_nombre VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    unidad_medida VARCHAR(20) DEFAULT 'unidades',
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE
);
