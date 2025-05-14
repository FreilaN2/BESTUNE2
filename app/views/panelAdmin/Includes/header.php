<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' | ' : '' ?><?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/BESTUNE2/app/views/panelAdmin/index.php"><?= SITE_NAME ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/BESTUNE2/app/views/panelAdmin/usuarios/listar.php">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/BESTUNE2/app/views/panelAdmin/promociones/listar.php">Promociones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/BESTUNE2/app/views/panelAdmin/planes/listar.php">Planes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/BESTUNE2/app/views/panelAdmin/eventos/listar.php">Eventos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/BESTUNE2/app/views/panelAdmin/instagram/listar.php">Instagram</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= getCurrentUser()['nombre'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Mi perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>