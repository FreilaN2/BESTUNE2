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

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de promoción no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: " . PANEL_PATH . "app/views/panelAdmin/promociones/listar.php");
    exit();
}

$id_promocion = (int)$_GET['id'];

// Obtener información de la promoción para eliminar la imagen asociada
$stmt = $db->prepare("SELECT imagen_url FROM promociones WHERE id_promocion = ?");
$stmt->execute([$id_promocion]);
$promocion = $stmt->fetch();

try {
    $db->beginTransaction();

    if ($promocion && !empty($promocion['imagen_url'])) {
        $ruta_relativa = ltrim($promocion['imagen_url'], '/');
        $base_path = realpath(__DIR__ . '/../../../../'); // llega hasta /BESTUNE2
        $imagen_path = $base_path . '/public/' . $ruta_relativa;

        if (file_exists($imagen_path)) {
            unlink($imagen_path);
        }
    }

    // Eliminar la promoción de la base de datos
    $stmt = $db->prepare("DELETE FROM promociones WHERE id_promocion = ?");
    $stmt->execute([$id_promocion]);

    $db->commit();

    $_SESSION['message'] = "Promoción eliminada correctamente";
    $_SESSION['message_type'] = "success";

} catch (PDOException $e) {
    $db->rollBack();
    $_SESSION['message'] = "Error al eliminar la promoción: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: " . PANEL_PATH . "app/views/panelAdmin/promociones/listar.php");
exit();
