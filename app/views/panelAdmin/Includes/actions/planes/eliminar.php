<?php
require_once '../../../includes/config.php';
require_once '../../../includes/auth.php';
checkAuth();

if (!isAdmin()) {
    $_SESSION['message'] = "No tienes permisos para realizar esta acción";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de plan no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../../planes/listar.php");
    exit();
}

$id_plan = (int)$_GET['id'];

// Obtener información del plan para eliminar la imagen asociada
$stmt = $db->prepare("SELECT imagen_principal FROM planes WHERE id_plan = ?");
$stmt->execute([$id_plan]);
$plan = $stmt->fetch();

try {
    $db->beginTransaction();

    // Eliminar imagen del servidor si existe
    if ($plan && !empty($plan['imagen_principal'])) {
        $ruta_relativa = ltrim($plan['imagen_principal'], '/'); // assets/img/planes/xxx.jpg
        $base_path = dirname(__DIR__, 3); // ← corregido: subir 3 niveles hasta BESTUNE2
        $imagen_path = $base_path . '/public/' . $ruta_relativa;

        if (file_exists($imagen_path)) {
            if (!unlink($imagen_path)) {
                $_SESSION['message'] = "El plan fue eliminado, pero no se pudo eliminar la imagen.";
            }
        }
    }

    // Eliminar plan
    $stmt = $db->prepare("DELETE FROM planes WHERE id_plan = ?");
    $stmt->execute([$id_plan]);

    $db->commit();

    if (!isset($_SESSION['message'])) {
        $_SESSION['message'] = "Plan eliminado correctamente.";
    }
    $_SESSION['message_type'] = "success";

} catch (PDOException $e) {
    $db->rollBack();
    $_SESSION['message'] = "Error al eliminar el plan: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../../../planes/listar.php");
exit();
