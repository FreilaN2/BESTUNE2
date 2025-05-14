<?php
session_start();
require_once 'config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function checkAuth() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function getCurrentUser() {
    global $db;
    
    if (!isLoggedIn()) return null;
    
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Nueva función para verificar si es admin
function isAdmin() {
    $user = getCurrentUser();
    return ($user && $user['es_administrador'] == 1);
}
?>