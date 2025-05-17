<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id_evento = (int)$_GET['id'];
$page_title = 'Editar Evento';
require_once '../includes/header.php';

$stmt = $db->prepare("SELECT * FROM eventos WHERE id_evento = ?");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch();

if (!$evento) {
    $_SESSION['message'] = "Evento no encontrado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];

    $errors = [];

    if (empty($titulo)) $errors[] = "El título es requerido";
    if (empty($fecha)) $errors[] = "La fecha es requerida";

    $imagen_actual = $evento['imagen'] ?? '';
    $imagen_url = $imagen_actual;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/eventos/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $ruta_antigua = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($imagen_actual, '/');
            if (!empty($imagen_actual) && file_exists($ruta_antigua)) unlink($ruta_antigua);

            $imagen_nombre = 'evento_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = $upload_dir . $imagen_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen_url = 'assets/img/eventos/' . $imagen_nombre;
            } else {
                $errors[] = "Error al subir la nueva imagen.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE eventos SET titulo = ?, descripcion = ?, fecha = ?, imagen = ? WHERE id_evento = ?");
            $stmt->execute([$titulo, $descripcion, $fecha, $imagen_url, $id_evento]);

            $_SESSION['message'] = "Evento actualizado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if (isset($destino) && file_exists($destino)) unlink($destino);
            $errors[] = "Error al actualizar el evento: " . $e->getMessage();
        }
    }
}
?>

<div class="max-w-3xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-pencil-square text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Editar Evento</h2>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded">
            <ul class="list-disc pl-5 space-y-1">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label for="titulo" class="block text-sm font-medium text-gray-700">Título *</label>
                <input type="text" id="titulo" name="titulo" required
                       class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="<?= htmlspecialchars($evento['titulo']) ?>">

                <label for="descripcion" class="block mt-4 text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"
                          class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($evento['descripcion']) ?></textarea>
            </div>

            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha y Hora *</label>
                <input type="datetime-local" id="fecha" name="fecha" required
                       class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="<?= date('Y-m-d\TH:i', strtotime($evento['fecha'])) ?>">

                <label for="imagen" class="block mt-4 text-sm font-medium text-gray-700">Imagen del Evento</label>
                <input type="file" id="imagen" name="imagen" accept="image/*"
                       class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm">
                <?php if (!empty($evento['imagen'])): ?>
                    <div class="mt-2">
                        <img src="/bestune2/public/<?= htmlspecialchars($evento['imagen']) ?>"
                             alt="Imagen actual" class="h-24 object-contain rounded border">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-6 border-t">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-md shadow-sm transition">
                <i class="bi bi-check-circle-fill text-base"></i>
                Guardar
            </button>
            <a href="listar.php"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium px-6 py-2 rounded-md shadow-sm transition">
                <i class="bi bi-x-lg text-base"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
