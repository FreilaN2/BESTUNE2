<?php
require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/auth.php';
checkAuth();

if (!isAdmin()) {
    $_SESSION['message'] = "No tienes permisos para realizar esta acción";
    $_SESSION['message_type'] = "danger";
    header("Location: " . PANEL_PATH . "app/views/panelAdmin/index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID de usuario no proporcionado o inválido";
    $_SESSION['message_type'] = "danger";
    header("Location: " . PANEL_PATH . "app/views/panelAdmin/usuarios/listar.php");
    exit();
}

$id_usuario = (int)$_GET['id'];

// Prevenir que el usuario se elimine a sí mismo
if ($id_usuario === (int)$_SESSION['user_id']) {
    $_SESSION['message'] = "No puedes eliminar tu propia cuenta";
    $_SESSION['message_type'] = "warning";
    header("Location: " . PANEL_PATH . "app/views/panelAdmin/usuarios/listar.php");
    exit();
}

// Verificar si existe el usuario
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    $_SESSION['message'] = "El usuario no existe";
    $_SESSION['message_type'] = "danger";
    header("Location: " . PANEL_PATH . "app/views/panelAdmin/usuarios/listar.php");
    exit();
}

try {
    $db->beginTransaction();

    $stmt = $db->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);

    $db->commit();

    $_SESSION['message'] = "Usuario eliminado correctamente";
    $_SESSION['message_type'] = "success";
} catch (PDOException $e) {
    $db->rollBack();
    $_SESSION['message'] = "Error al eliminar el usuario: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: " . PANEL_PATH . "app/views/panelAdmin/usuarios/listar.php");
exit();
