<?php
// funciones.php - Funciones de ayuda general

/**
 * Formatea un número como moneda (USD)
 */
function formatear_moneda($monto) {
    return '$ ' . number_format($monto, 2, '.', ',');
}

/**
 * Escapa HTML para prevenir XSS
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Genera un número de factura aleatorio / secuencial simple para demostración
 * En un sistema real esto vendría de una secuencia en DB
 */
function generar_numero_factura() {
    return 'FAC-' . date('Ymd') . '-' . rand(1000, 9999);
}
