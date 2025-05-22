<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? $page_title . ' | ' : '' ?><?= SITE_NAME ?></title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/img/favicon.ico">

  <!-- Atlantis Lite CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/css/atlantis.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/Atlantis-Lite-master/assets/css/fonts.min.css">
</head>
<body>
  <div class="wrapper">

    <!-- HEADER Atlantis -->
    <div class="main-header">
      <!-- Logo Header -->
      <div class="logo-header d-flex align-items-center justify-content-center" data-background-color="blue">
        <a href="/<?= explode('/', trim(dirname($_SERVER['SCRIPT_NAME']), '/'))[0] ?>/app/views/panelAdmin/index.php" class="logo">
          <img src="<?= SITE_LOGO ?>" alt="<?= SITE_NAME ?>" class="navbar-brand" style="height: 40px;">
        </a>
        <button class="navbar-toggler sidenav-toggler ml-auto" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"><i class="icon-menu"></i></span>
        </button>
        <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
      </div>

      <!-- Navbar sin buscador -->
      <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
        <div class="container-fluid d-flex align-items-center">

          <!-- ENLACES DE SECCIÓN REPARTIDOS -->
          <ul class="navbar-nav d-flex flex-row flex-fill justify-content-around">
            <li class="nav-item">
              <a class="nav-link text-white" href="/<?= explode('/', trim(dirname($_SERVER['SCRIPT_NAME']), '/'))[0] ?>/app/views/panelAdmin/usuarios/listar.php">
                <i class="fas fa-users"></i> Usuarios
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="/<?= explode('/', trim(dirname($_SERVER['SCRIPT_NAME']), '/'))[0] ?>/app/views/panelAdmin/promociones/listar.php">
                <i class="fas fa-tags"></i> Promociones
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="/<?= explode('/', trim(dirname($_SERVER['SCRIPT_NAME']), '/'))[0] ?>/app/views/panelAdmin/planes/listar.php">
                <i class="fas fa-cube"></i> Planes
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="/<?= explode('/', trim(dirname($_SERVER['SCRIPT_NAME']), '/'))[0] ?>/app/views/panelAdmin/eventos/listar.php">
                <i class="fas fa-calendar"></i> Eventos
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="/<?= explode('/', trim(dirname($_SERVER['SCRIPT_NAME']), '/'))[0] ?>/app/views/panelAdmin/instagram/listar.php">
                <i class="fab fa-instagram"></i> Instagram
              </a>
            </li>
          </ul>

          <!-- ÍCONOS DERECHA -->
          <ul class="navbar-nav topbar-nav align-items-center">
            <li class="nav-item dropdown hidden-caret">
              <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                <li><div class="dropdown-title">No hay notificaciones</div></li>
              </ul>
            </li>
            <li class="nav-item hidden-caret">
              <!-- Botón con modal de confirmación -->
              <button class="nav-link text-white btn btn-link p-0" data-toggle="modal" data-target="#logoutModal" title="Cerrar sesión">
                <i class="fas fa-sign-out-alt"></i>
              </button>
            </li>
          </ul>

        </div>
      </nav>
    </div>

    <!-- Modal de Confirmación de Cierre de Sesión -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-center font-weight-bold w-100" id="logoutModalLabel">Cerrar Sesión</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center font-weight-bold">
            ¿Estás seguro de cerrar sesión?
          </div>
          <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <a href="/<?= explode('/', trim(dirname($_SERVER['SCRIPT_NAME']), '/'))[0] ?>/app/views/panelAdmin/logout.php" class="btn btn-danger">Cerrar sesión</a>
          </div>
        </div>
      </div>
    </div>