<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

session_destroy();
header("Location: " . PANEL_PATH . "app/views/panelAdmin/login.php");
exit();
