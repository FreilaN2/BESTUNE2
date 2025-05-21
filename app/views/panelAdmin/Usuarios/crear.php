<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkAuth();

if (!isAdmin()) {
    $_SESSION['message'] = "No tienes permisos para acceder a esta sección";
    $_SESSION['message_type'] = "danger";
    header("Location: " . PANEL_PATH . "app/views/panelAdmin/index.php");
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
            header("Location: " . PANEL_PATH . "app/views/panelAdmin/usuarios/listar.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error al crear el usuario: " . $e->getMessage();
        }
    }
}
?>

<div class="page-inner" style="background-color: #f8f9fa;">
    <div class="page-header">
        <h4 class="page-title">Crear Usuario</h4>
        <a href="<?= PANEL_PATH ?>app/views/panelAdmin/usuarios/listar.php" class="btn btn-secondary btn-round ml-auto">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border rounded">
                <div class="card-header">
                    <div class="card-title"><i class="fa fa-user-plus"></i> Nuevo Usuario</div>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required
                                   value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control" required
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Contraseña *</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <small class="form-text text-muted">Debe tener al menos 8 caracteres.</small>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono" class="form-control"
                                   value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>">
                        </div>

                        <div class="form-check">
                            <label>
                                <input type="checkbox" name="es_administrador"
                                    <?= isset($_POST['es_administrador']) && $_POST['es_administrador'] ? 'checked' : '' ?>>
                                <span class="form-check-sign">Usuario Administrador</span>
                            </label>
                        </div>

                        <div class="form-check">
                            <label>
                                <input type="checkbox" name="activo"
                                    <?= !isset($_POST['activo']) || $_POST['activo'] ? 'checked' : '' ?>>
                                <span class="form-check-sign">Usuario Activo</span>
                            </label>
                        </div>

                        <div class="card-action text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                            <a href="<?= PANEL_PATH ?>app/views/panelAdmin/usuarios/listar.php" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</main>
</div> <!-- Cierra wrapper -->
<?php require_once '../includes/footer.php'; ?>
