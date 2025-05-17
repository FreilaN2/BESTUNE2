<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Crear Evento';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha = $_POST['fecha'];

    $errors = [];

    if (empty($titulo)) $errors[] = "El título es requerido";
    if (empty($fecha)) $errors[] = "La fecha es requerida";

    $imagen_nombre = null;
    $imagen_url = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/eventos/';
            if ($upload_dir === false) {
                $errors[] = "No se pudo acceder a la carpeta de destino.";
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $imagen_nombre = 'evento_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destino = $upload_dir . $imagen_nombre;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                    $imagen_url = 'assets/img/eventos/' . $imagen_nombre;
                } else {
                    $errors[] = "Error al subir la imagen";
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO eventos 
                (titulo, descripcion, fecha, imagen, id_usuario_creador) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $titulo,
                $descripcion,
                $fecha,
                $imagen_url,
                $_SESSION['user_id']
            ]);

            $_SESSION['message'] = "Evento creado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if ($imagen_nombre && file_exists($destino)) {
                unlink($destino);
            }
            $errors[] = "Error al crear el evento: " . $e->getMessage();
        }
    }
}
?>

<div class="max-w-3xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-calendar-event text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Crear Nuevo Evento</h2>
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
        <div>
            <label for="titulo" class="block text-sm font-medium text-gray-700">Título *</label>
            <input type="text" id="titulo" name="titulo" required
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : '' ?>">
        </div>

        <div>
            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"
                      class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
        </div>

        <div>
            <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha y Hora *</label>
            <input type="datetime-local" id="fecha" name="fecha" required
                   class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : '' ?>">
        </div>

        <div>
            <label for="imagen" class="block text-sm font-medium text-gray-700">Imagen del Evento</label>
            <input type="file" id="imagen" name="imagen" accept="image/*"
                   class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm">
            <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, GIF, WEBP. Máx. 5MB.</p>
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