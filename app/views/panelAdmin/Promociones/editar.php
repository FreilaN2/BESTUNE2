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

if (!isset($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id_promocion = (int)$_GET['id'];
$page_title = 'Editar Promoción';
require_once '../includes/header.php';

$stmt = $db->prepare("SELECT * FROM promociones WHERE id_promocion = ?");
$stmt->execute([$id_promocion]);
$promocion = $stmt->fetch();

if (!$promocion) {
    $_SESSION['message'] = "Promoción no encontrada";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

$usuarios = $db->query("SELECT id_usuario, nombre, email FROM usuarios ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $descuento = (float)$_POST['descuento'];
    $codigo = trim($_POST['codigo_promocion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $activa = isset($_POST['activa']) ? 1 : 0;
    $id_usuario = (int)$_POST['id_usuario_creador'];

    $errors = [];
    if (empty($titulo)) $errors[] = "El título es requerido";
    if (empty($descuento)) $errors[] = "El descuento es requerido";
    if ($descuento <= 0 || $descuento > 100) $errors[] = "El descuento debe ser entre 0.01 y 100";
    if (empty($codigo)) $errors[] = "El código de promoción es requerido";
    if (empty($fecha_inicio)) $errors[] = "La fecha de inicio es requerida";
    if (empty($fecha_fin)) $errors[] = "La fecha de fin es requerida";
    if ($fecha_fin < $fecha_inicio) $errors[] = "La fecha de fin no puede ser anterior a la fecha de inicio";

    $imagen_actual = $promocion['imagen_url'] ?? '';
    $imagen_url = $imagen_actual;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido.";
        } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB.";
        } else {
            $upload_dir = realpath(__DIR__ . '/../../../../') . '/public/assets/img/promociones/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            if (!empty($imagen_actual)) {
                $ruta_antigua = realpath(__DIR__ . '/../../../../') . '/public/' . ltrim($imagen_actual, '/');
                if (file_exists($ruta_antigua)) unlink($ruta_antigua);
            }

            $imagen_nombre = 'promo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = $upload_dir . $imagen_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                $imagen_url = 'assets/img/promociones/' . $imagen_nombre;
            } else {
                $errors[] = "Error al subir la nueva imagen.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE promociones SET titulo = ?, descripcion = ?, descuento = ?, codigo_promocion = ?, fecha_inicio = ?, fecha_fin = ?, imagen_url = ?, activa = ?, id_usuario_creador = ? WHERE id_promocion = ?");
            $stmt->execute([$titulo, $descripcion, $descuento, $codigo, $fecha_inicio, $fecha_fin, $imagen_url, $activa, $id_usuario, $id_promocion]);

            $_SESSION['message'] = "Promoción actualizada exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            if (isset($destino) && file_exists($destino)) unlink($destino);
            $errors[] = "Error al actualizar la promoción: " . $e->getMessage();
        }
    }
}
?>

<div class="max-w-4xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-pencil-square text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Editar Promoción</h2>
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
                <input type="text" id="titulo" name="titulo" required class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="<?= htmlspecialchars($promocion['titulo']) ?>">

                <label for="descripcion" class="block mt-4 text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($promocion['descripcion']) ?></textarea>

                <label for="descuento" class="block mt-4 text-sm font-medium text-gray-700">Descuento (%) *</label>
                <input type="number" step="0.01" min="0.01" max="100" id="descuento" name="descuento" required class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="<?= htmlspecialchars($promocion['descuento']) ?>">

                <label for="codigo_promocion" class="block mt-4 text-sm font-medium text-gray-700">Código de Promoción *</label>
                <input type="text" id="codigo_promocion" name="codigo_promocion" required class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="<?= htmlspecialchars($promocion['codigo_promocion']) ?>">
            </div>

            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio *</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="<?= htmlspecialchars($promocion['fecha_inicio']) ?>">

                <label for="fecha_fin" class="block mt-4 text-sm font-medium text-gray-700">Fecha de Fin *</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="<?= htmlspecialchars($promocion['fecha_fin']) ?>">

                <label for="imagen" class="block mt-4 text-sm font-medium text-gray-700">Imagen Promocional</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm">
                <?php if (!empty($promocion['imagen_url'])): ?>
                    <img src="/bestune2/public/<?= htmlspecialchars($promocion['imagen_url']) ?>" alt="Imagen actual" class="mt-2 h-24 object-contain rounded border">
                <?php endif; ?>

                <label for="id_usuario_creador" class="block mt-4 text-sm font-medium text-gray-700">Responsable *</label>
                <select id="id_usuario_creador" name="id_usuario_creador" class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id_usuario'] ?>" <?= $promocion['id_usuario_creador'] == $usuario['id_usuario'] ? 'selected' : '' ?>><?= htmlspecialchars($usuario['nombre']) ?> (<?= htmlspecialchars($usuario['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>

 <div class="flex items-center mt-4">
    <label for="activa" class="flex items-center cursor-pointer">
        <div class="relative">
            <input type="checkbox" id="activa" name="activa" class="sr-only peer" <?= $promocion['activa'] ? 'checked' : '' ?>>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-green-500 transition-all"></div>
            <div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-all peer-checked:translate-x-full"></div>
        </div>
        <span class="ml-3 text-sm text-gray-700">Promoción Activa</span>
    </label>
</div>

        <div class="flex justify-end gap-4 pt-6 border-t">
            <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-md shadow-sm transition">
                <i class="bi bi-check-circle-fill text-base"></i>
                Guardar
            </button>
            <a href="listar.php" class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium px-6 py-2 rounded-md shadow-sm transition">
                <i class="bi bi-x-lg text-base"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
