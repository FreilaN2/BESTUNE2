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

$page_title = 'Crear Usuario';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $telefono = trim($_POST['telefono']);
    $es_administrador = isset($_POST['es_administrador']) ? 1 : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;

    $errors = [];

    if (empty($nombre)) $errors[] = "El nombre es requerido";
    if (empty($email)) $errors[] = "El email es requerido";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email no es válido";
    if (empty($password)) $errors[] = "La contraseña es requerida";
    if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres";

    $stmt = $db->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "El email ya está registrado";

    if (empty($errors)) {
        $password_hash = hash('sha256', $password);
        try {
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password_hash, telefono, es_administrador, activo) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $email, $password_hash, $telefono, $es_administrador, $activo]);
            $_SESSION['message'] = "Usuario creado exitosamente";
            $_SESSION['message_type'] = "success";
            header("Location: listar.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error al crear el usuario: " . $e->getMessage();
        }
    }
}
?>

<div class="max-w-3xl mx-auto bg-white border border-blue-100 p-8 rounded-xl shadow-lg mt-10">
    <div class="flex items-center gap-3 mb-6 border-b pb-3 border-blue-500">
        <i class="bi bi-person-plus-fill text-3xl text-blue-600"></i>
        <h2 class="text-2xl font-bold text-blue-800">Crear Nuevo Usuario</h2>
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

    <form method="POST" class="space-y-6">
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
            <input type="text" id="nombre" name="nombre" required
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
            <input type="email" id="email" name="email" required
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña *</label>
            <input type="password" id="password" name="password" required
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
        </div>

        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input type="tel" id="telefono" name="telefono"
                   class="mt-1 w-full border border-gray-300 bg-gray-50 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>">
        </div>

        <div class="flex flex-col sm:flex-row gap-6 pt-4 border-t">
            <!-- Toggle Admin -->
            <label class="flex items-center cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="es_administrador" name="es_administrador"
                           class="sr-only peer"
                           <?= isset($_POST['es_administrador']) && $_POST['es_administrador'] ? 'checked' : '' ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-all peer-checked:translate-x-full"></div>
                </div>
                <span class="ml-3 text-sm text-gray-700">Usuario Administrador</span>
            </label>

            <!-- Toggle Activo -->
            <label class="flex items-center cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="activo" name="activo"
                           class="sr-only peer"
                           <?= !isset($_POST['activo']) || $_POST['activo'] ? 'checked' : '' ?>>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-green-500 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-all peer-checked:translate-x-full"></div>
                </div>
                <span class="ml-3 text-sm text-gray-700">Usuario Activo</span>
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
