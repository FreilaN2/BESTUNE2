<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_administrativo');

// Conexión a la base de datos
try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Configuración general
define('SITE_NAME', 'Bestune');
define('SITE_LOGO', '/BESTUNE2/public/assets/img/logo.png');
?>