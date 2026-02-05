<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'sistema_facturas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Habilitar excepciones para errores
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Configurar fetch mode por defecto
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage() . "<br>Por favor asegúrese de importar schema.sql en su gestor de base de datos.");
}
