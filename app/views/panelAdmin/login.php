<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && hash('sha256', $password) === $user['password_hash']) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['is_admin'] = $user['es_administrador'];
        $_SESSION['message'] = "Bienvenido, " . $user['nombre'];
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } else {
        $error = "Credenciales incorrectas";
        error_log("Intento de login fallido para: $email");
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | <?= SITE_NAME ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/img/favicon.ico">

    <!-- Atlantis Lite CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/css/atlantis.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body {
            background-color: #0A0F1A;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
        }

        .card-login {
            background: rgba(25, 28, 35, 0.95);
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            color: #ffffff;
            animation: softPop 0.5s ease-out;
        }

        .form-control {
            border-radius: 0.5rem;
            background-color: #1e2330;
            border: 1px solid #2f354a;
            color: #fff;
            transition: border-color 0.3s ease;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .form-control:hover {
            border-color: #FFB300;
        }

        .form-control:focus {
            background-color: #1e2330;
            color: #fff;
            border-color: #FFB300;
            box-shadow: 0 0 0 0.2rem rgba(255,179,0,0.25);
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 300px;
        }

        .form-label {
            font-weight: 500;
            color: #ccc;
        }

        .btn-primary {
            background-color: #FFB300;
            border-color: #FFB300;
            color: #0A0F1A;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e09e00;
            border-color: #e09e00;
            transform: scale(1.03);
        }

        .alert {
            background-color: #d9534f;
            color: #fff;
            border: none;
        }

        footer {
            position: absolute;
            bottom: 15px;
            text-align: center;
            width: 100%;
            color: #666;
            font-size: 0.8rem;
        }

        @keyframes softPop {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

    <div class="card-login animate__animated animate__fadeInDown">
        <img src="<?= BASE_URL ?>assets/img/logo.png" alt="Logo" class="logo">

        <?php if (isset($error)): ?>
            <div class="alert text-center" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="correo@dominio.com" required>
            </div>
            <div class="form-group mt-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Ingresar</button>
        </form>
    </div>

    <footer>
        &copy; <?= date('Y') ?> BESTUNE Panel Admin. Todos los derechos reservados.
    </footer>

    <!-- Atlantis Lite JS -->
    <script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/core/jquery.3.2.1.min.js"></script>
    <script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
    <script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/core/popper.min.js"></script>
    <script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/core/bootstrap.min.js"></script>
    <script src="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/js/atlantis.min.js"></script>
</body>
</html>
