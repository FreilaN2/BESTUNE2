<?php
require_once '../../../includes/config.php';
require_once '../../../includes/auth.php';
checkAuth();

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de evento no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../../eventos/listar.php");
    exit();
}

$id_evento = (int)$_GET['id'];

// Obtener información del evento para eliminar la imagen asociada
$stmt = $db->prepare("SELECT imagen FROM eventos WHERE id_evento = ?");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch();

try {
    $db->beginTransaction();

    // Eliminar el evento
    $stmt = $db->prepare("DELETE FROM eventos WHERE id_evento = ?");
    $stmt->execute([$id_evento]);

    // Eliminar registros vinculados
    $stmt = $db->prepare("DELETE FROM registros_eventos WHERE id_evento = ?");
    $stmt->execute([$id_evento]);

    // Eliminar la imagen del sistema de archivos
    if ($evento && !empty($evento['imagen'])) {
        $ruta_relativa = ltrim($evento['imagen'], '/');
        $base_path = realpath(__DIR__ . '/../../../../'); // Llega hasta /BESTUNE2
        $imagen_path = $base_path . '/public/' . $ruta_relativa;

        if (file_exists($imagen_path)) {
            unlink($imagen_path);
        }
    }

    $db->commit();

    $_SESSION['message'] = "Evento eliminado correctamente";
    $_SESSION['message_type'] = "success";
} catch (PDOException $e) {
    $db->rollBack();
    $_SESSION['message'] = "Error al eliminar el evento: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../../../eventos/listar.php");
exit();
