<?php
ob_start();

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_administrativo');

// Detectar dinámicamente el nombre de la carpeta del proyecto (ej: BESTUNE2)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_dir = dirname($_SERVER['SCRIPT_NAME']); // ej: /BESTUNE2/app/views/panelAdmin
$parts = explode('/', trim($script_dir, '/'));
$project = $parts[0] ?? ''; // primer segmento de la ruta

define('BASE_URL', "$protocol://$host/$project/public/");
define('PANEL_PATH', "/$project/"); // ← para redirecciones internas a archivos PHP

// Conexión a la base de datos
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Configuración general
define('SITE_NAME', 'Bestune');
define('SITE_LOGO', BASE_URL . 'assets/img/logo.png');
?>
