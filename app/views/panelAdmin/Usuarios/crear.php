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
    
    // Validaciones
    $errors = [];
    
    if (empty($nombre)) $errors[] = "El nombre es requerido";
    if (empty($email)) $errors[] = "El email es requerido";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email no es válido";
    if (empty($password)) $errors[] = "La contraseña es requerida";
    if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres";
    
    // Verificar si el email ya existe
    $stmt = $db->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "El email ya está registrado";
    
    if (empty($errors)) {
        // Usar SHA-256 en lugar de password_hash()
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

<div class="card">
    <div class="card-body">
        <h2 class="card-title">Crear Nuevo Usuario</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required 
                       value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required 
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="text-muted">Mínimo 8 caracteres</small>
            </div>
            
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono (Opcional)</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" 
                       value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>">
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="es_administrador" name="es_administrador" <?= isset($_POST['es_administrador']) && $_POST['es_administrador'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="es_administrador">Usuario Administrador</label>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" <?= !isset($_POST['activo']) || $_POST['activo'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">Usuario Activo</label>
            </div>
            
            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>