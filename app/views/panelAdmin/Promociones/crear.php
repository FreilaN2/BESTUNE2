<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isAdmin()) {
    $_SESSION['message'] = "No tienes permisos para acceder a esta sección";
    $_SESSION['message_type'] = "danger";
    header("Location: ../index.php");
    exit();
}

$page_title = 'Crear Promoción';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $descuento = (float)$_POST['descuento'];
    $codigo = trim($_POST['codigo_promocion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $activa = isset($_POST['activa']) ? 1 : 0;

    $errors = [];

    if (empty($titulo)) $errors[] = "El título es requerido";
    if ($descuento <= 0 || $descuento > 100) $errors[] = "El descuento debe estar entre 0.01% y 100%";
    if (empty($fecha_inicio)) $errors[] = "La fecha de inicio es requerida";
    if (empty($fecha_fin)) $errors[] = "La fecha de fin es requerida";
    if ($fecha_inicio > $fecha_fin) $errors[] = "La fecha de inicio no puede ser mayor a la fecha de fin";

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
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/promociones/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $imagen_nombre = 'promo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = $upload_dir . $imagen_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen_url = 'assets/img/promociones/' . $imagen_nombre;
            } else {
                $errors[] = "Error al subir la imagen.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO promociones 
                (titulo, descripcion, descuento, codigo_promocion, fecha_inicio, fecha_fin, imagen_url, activa, id_usuario_creador)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $titulo, $descripcion, $descuento, $codigo, $fecha_inicio, $fecha_fin, $imagen_url, $activa, $_SESSION['user_id']
            ]);

            $_SESSION['message'] = "Promoción creada exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if ($imagen_url && file_exists($upload_dir . $imagen_nombre)) {
                unlink($upload_dir . $imagen_nombre);
            }
            $errors[] = "Error al crear la promoción: " . $e->getMessage();
        }
    }
}

$activa_checked = (!isset($_POST['activa']) || $_POST['activa']) ? 'checked' : '';
?>

<div class="max-w-3xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-gift text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Crear Nueva Promoción</h2>
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
            <textarea id="descripcion" name="descripcion" rows="3"
                      class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
        </div>

        <div>
            <label for="descuento" class="block text-sm font-medium text-gray-700">Descuento (%) *</label>
            <input type="number" id="descuento" name="descuento" min="0.01" max="100" step="0.01" required
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= isset($_POST['descuento']) ? htmlspecialchars($_POST['descuento']) : '' ?>">
            <p class="text-xs text-gray-500 mt-1">Ejemplo: 15.5 = 15.5% de descuento</p>
        </div>

        <div>
            <label for="codigo_promocion" class="block text-sm font-medium text-gray-700">Código de Promoción *</label>
            <input type="text" id="codigo_promocion" name="codigo_promocion" required
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= isset($_POST['codigo_promocion']) ? htmlspecialchars($_POST['codigo_promocion']) : '' ?>">
            <p class="text-xs text-gray-500 mt-1">Código que los clientes usarán para aplicar el descuento</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio *</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required
                       class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="<?= isset($_POST['fecha_inicio']) ? htmlspecialchars($_POST['fecha_inicio']) : '' ?>">
            </div>

            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">Fecha de Fin *</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required
                       class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       value="<?= isset($_POST['fecha_fin']) ? htmlspecialchars($_POST['fecha_fin']) : '' ?>">
            </div>
        </div>

        <div>
            <label for="imagen" class="block text-sm font-medium text-gray-700">Imagen Promocional</label>
            <input type="file" id="imagen" name="imagen" accept="image/*"
                   class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm">
            <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WEBP. Máx. 5MB.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-6 pt-4 border-t">
            <label class="flex items-center cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="activa" name="activa" class="sr-only peer" <?= $activa_checked ?> >
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-green-500 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-all peer-checked:translate-x-full"></div>
                </div>
                <span class="ml-3 text-sm text-gray-700">Promoción Activa</span>
            </label>
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
