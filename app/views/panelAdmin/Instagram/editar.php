<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

$page_title = 'Editar Post de Instagram';
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de post no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

$id = (int)$_GET['id'];

$stmt = $db->prepare("SELECT * FROM instagram_posts WHERE id_post = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    $_SESSION['message'] = "Post no encontrado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion']);
    $url = trim($_POST['url_post']);
    $visible = isset($_POST['visible']) ? 1 : 0;

    $errors = [];

    if (empty($descripcion)) $errors[] = "La descripción es requerida";
    if (empty($url)) $errors[] = "La URL es requerida";
    if (!filter_var($url, FILTER_VALIDATE_URL)) $errors[] = "La URL no es válida";

    $media_actual = $post['url_media'];
    $media_url = $media_actual;

    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'];
        $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de archivo no permitido. Use JPG, PNG, GIF, MP4 o WEBM.";
        } elseif ($_FILES['media']['size'] > 10 * 1024 * 1024) {
            $errors[] = "El archivo es demasiado grande. Máximo 10MB permitidos.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/instagram/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (!empty($media_actual)) {
                $archivo_anterior = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($media_actual, '/');
                if (file_exists($archivo_anterior)) {
                    unlink($archivo_anterior);
                }
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

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE instagram_posts 
                SET descripcion = ?, url_post = ?, visible = ?, url_media = ?, 
                    id_usuario_actualizador = ?, fecha_actualizacion = NOW() 
                WHERE id_post = ?");
            $stmt->execute([$descripcion, $url, $visible, $media_url, $_SESSION['user_id'], $id]);

            $_SESSION['message'] = "Post actualizado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error al guardar el post: " . $e->getMessage();
        }
    }
}

$descripcion_value = $_POST['descripcion'] ?? $post['descripcion'];
$url_value = $_POST['url_post'] ?? $post['url_post'];
?>

<div class="max-w-3xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-pencil-square text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Editar Post de Instagram</h2>
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
        </div>

        <div>
            <label for="media" class="block text-sm font-medium text-gray-700">Media</label>
            <input type="file" id="media" name="media" accept="image/*,video/*"
                class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm">
            <small class="text-gray-500">Formatos: JPG, PNG, GIF, MP4, WEBM. Máx. 10MB</small>

            <?php if (!empty($post['url_media'])): ?>
                <div class="mt-2">
                    <?php if (preg_match('/\.(mp4|webm)$/i', $post['url_media'])): ?>
                        <video src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>" controls class="rounded shadow h-48"></video>
                    <?php else: ?>
                        <img src="/BESTUNE2/public/<?= htmlspecialchars($post['url_media']) ?>" alt="Media actual" class="h-48 rounded shadow object-contain">
                    <?php endif; ?>
                    <p class="text-gray-500 text-sm mt-1">Media actual</p>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Visibilidad</h3>
            <label class="flex items-center cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="visible" name="visible"
                        class="sr-only peer"
                        <?= $post['visible'] ? 'checked' : '' ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-green-500 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-all peer-checked:translate-x-full"></div>
                </div>
                <span class="ml-3 text-sm text-gray-700">Visible en el sitio</span>
            </label>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t">
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
