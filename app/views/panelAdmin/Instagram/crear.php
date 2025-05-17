<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Crear Post de Instagram';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion']);
    $url = trim($_POST['url_post']);
    $visible = isset($_POST['visible']) ? 1 : 0;

    $errors = [];

    if (empty($descripcion)) $errors[] = "La descripción es requerida";
    if (empty($url)) $errors[] = "La URL es requerida";
    if (!filter_var($url, FILTER_VALIDATE_URL)) $errors[] = "La URL no es válida";

    $media_url = null;

    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de archivo no permitido. Use JPG, PNG, GIF, MP4 o WEBM.";
        } elseif ($_FILES['media']['size'] > 10 * 1024 * 1024) {
            $errors[] = "El archivo es demasiado grande. Máximo 10MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/instagram/';
            if ($upload_dir === false) {
                $errors[] = "No se pudo acceder a la carpeta de destino.";
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $media_nombre = 'instagram_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destino = $upload_dir . $media_nombre;

                if (move_uploaded_file($_FILES['media']['tmp_name'], $destino)) {
                    $media_url = 'assets/img/instagram/' . $media_nombre;
                } else {
                    $errors[] = "Error al subir el archivo.";
                }
            }
        }
    } else {
        $errors[] = "Debe subir una foto o video.";
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO instagram_posts 
                (descripcion, url_post, visible, url_media, id_usuario_actualizador) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $descripcion,
                $url,
                $visible,
                $media_url,
                $_SESSION['user_id']
            ]);

            $_SESSION['message'] = "Post creado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if ($media_url && file_exists($upload_dir . $media_nombre)) {
                unlink($upload_dir . $media_nombre);
            }
            $errors[] = "Error al guardar el post: " . $e->getMessage();
        }
    }
}

$descripcion_value = $_POST['descripcion'] ?? '';
$url_value = $_POST['url_post'] ?? '';
$visible_checked = isset($_POST['visible']) ? 'checked' : '';
?>

<div class="max-w-3xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-instagram text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Crear Post de Instagram</h2>
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
            <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción *</label>
            <textarea id="descripcion" name="descripcion" required rows="3"
                      class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($descripcion_value) ?></textarea>
        </div>

        <div>
            <label for="url_post" class="block text-sm font-medium text-gray-700">URL *</label>
            <input type="url" id="url_post" name="url_post" required
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($url_value) ?>">
            <p class="text-xs text-gray-500 mt-1">Ejemplo: https://www.instagram.com/p/Cg5hJY5LQ6P/</p>
        </div>

        <div>
            <label for="media" class="block text-sm font-medium text-gray-700">Foto o Video *</label>
            <input type="file" id="media" name="media" accept="image/*,video/*" required
                   class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm">
            <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPG, PNG, GIF, MP4, WEBM. Máx. 10MB.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-6 pt-4 border-t">
            <label class="flex items-center cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="visible" name="visible"
                           class="sr-only peer" <?= $visible_checked ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-green-500 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-all peer-checked:translate-x-full"></div>
                </div>
                <span class="ml-3 text-sm text-gray-700">Visible en el sitio</span>
            </label>
        </div>

        <div class="flex justify-end gap-4 pt-6 border-t">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-md shadow-sm transition">
                <i class="bi bi-check-circle-fill text-base"></i>
                Crear
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