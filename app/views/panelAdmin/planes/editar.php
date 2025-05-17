<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de plan no proporcionado";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

$id_plan = (int)$_GET['id'];

$stmt = $db->prepare("SELECT * FROM planes WHERE id_plan = ?");
$stmt->execute([$id_plan]);
$plan = $stmt->fetch();

if (!$plan) {
    $_SESSION['message'] = "El plan no existe";
    $_SESSION['message_type'] = "danger";
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_plan = trim($_POST['nombre_plan']);
    $imagen_actual = $plan['imagen_principal'];
    $imagen_url = $imagen_actual;

    $errors = [];

    if (empty($nombre_plan)) {
        $errors[] = "El nombre del plan es requerido";
    }

    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.";
        } elseif ($_FILES['imagen_principal']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen es demasiado grande. Máximo 5MB permitidos.";
        } else {
            $upload_dir = dirname(__DIR__, 2) . '/public/assets/img/planes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (!empty($imagen_actual)) {
                $ruta_anterior = dirname(__DIR__, 2) . '/public/' . ltrim($imagen_actual, '/');
                if (file_exists($ruta_anterior)) {
                    unlink($ruta_anterior);
                }
            }

            $imagen_nombre = 'plan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destino = $upload_dir . $imagen_nombre;

            if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $destino)) {
                $imagen_url = 'assets/img/planes/' . $imagen_nombre;
            } else {
                $errors[] = "Error al subir la imagen.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE planes SET nombre_plan = ?, imagen_principal = ? WHERE id_plan = ?");
            $stmt->execute([$nombre_plan, $imagen_url, $id_plan]);

            $_SESSION['message'] = "Plan actualizado correctamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error al actualizar el plan: " . $e->getMessage();
        }
    }
}

require_once '../includes/header.php';
?>

<div class="max-w-3xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-pencil-square text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Editar Plan</h2>
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
            <label for="nombre_plan" class="block text-sm font-medium text-gray-700">Nombre del Plan</label>
            <input type="text" id="nombre_plan" name="nombre_plan" required
                class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                value="<?= htmlspecialchars($plan['nombre_plan']) ?>">
        </div>

        <div>
            <label for="imagen_principal" class="block text-sm font-medium text-gray-700">Imagen Principal</label>
            <input type="file" id="imagen_principal" name="imagen_principal" accept="image/*"
                class="mt-1 w-full border border-gray-300 bg-white rounded-md shadow-sm">
            <?php if (!empty($plan['imagen_principal'])): ?>
                <img src="/bestune2/public/<?= htmlspecialchars($plan['imagen_principal']) ?>" alt="Imagen actual"
                    class="mt-2 h-24 object-contain rounded border">
            <?php endif; ?>
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
