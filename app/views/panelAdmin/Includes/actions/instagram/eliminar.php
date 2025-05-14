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
    $_SESSION['message'] = "ID de post no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../../instagram/listar.php");
    exit();
}

$id_post = (int)$_GET['id'];

// Obtener información del post para eliminar el archivo asociado
$stmt = $db->prepare("SELECT url_media FROM instagram_posts WHERE id_post = ?");
$stmt->execute([$id_post]);
$post = $stmt->fetch();

try {
    $db->beginTransaction();

    // Eliminar archivo del servidor si existe
    if ($post && !empty($post['url_media'])) {
        $ruta_relativa = ltrim($post['url_media'], '/'); // quitar "/" inicial
        $archivo_path = realpath(__DIR__ . '/../../../../') . '/public/' . $ruta_relativa;

        if (file_exists($archivo_path)) {
            unlink($archivo_path);
        }
    }

    // Eliminar el post de la base de datos
    $stmt = $db->prepare("DELETE FROM instagram_posts WHERE id_post = ?");
    $stmt->execute([$id_post]);

    $db->commit();

    $_SESSION['message'] = "Post de Instagram eliminado correctamente";
    $_SESSION['message_type'] = "success";
} catch (PDOException $e) {
    $db->rollBack();
    $_SESSION['message'] = "Error al eliminar el post: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../../../instagram/listar.php");
exit();
