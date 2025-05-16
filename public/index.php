<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../config/database.php';
include '../app/views/layout/header.php';

$view = $_GET['view'] ?? 'home';

switch ($view) {
	case 'garantia':
		include '../app/views/garantia.php';
		break;

	case 'contacto':
		include '../app/views/contacto.php';
		break;

	case 'planes_venta':
		include '../app/views/planes_venta.php';
		break;

	case 'bestune-t55':
		include '../app/views/vehiculos/bestune-t55.php';
		break;

	case 'bestune-t77':
		include '../app/views/vehiculos/bestune-t77.php';
		break;
	
	case 'bestune-t99':
		include '../app/views/vehiculos/bestune-t99.php';
		break;

	case 'bestune-b70':
		include '../app/views/vehiculos/bestune-b70.php';
		break;
	
	case 'bestune-r7':
		include '../app/views/vehiculos/bestune-r7.php';
		break;

	case 'toyota-corolla':
		include '../app/views/vehiculos/toyota-corolla.php';	
		break;
	
	case 'toyota-levin':
		include '../app/views/vehiculos/toyota-levin.php';	
		break;

	case 'toyota-levin-sports':
		include '../app/views/vehiculos/toyota-levin-sports.php';	
		break;

	case 'toyota-corollacross':
		include '../app/views/vehiculos/toyota-corollacross.php';	
		break;

	case 'toyota-corollacross-elite':
		include '../app/views/vehiculos/toyota-corollacross-elite.php';	
		break;

	case 'toyota-rav4':
		include '../app/views/vehiculos/toyota-rav4.php';	
		break;

	case 'toyota-rav4-plus':
		include '../app/views/vehiculos/toyota-rav4-plus.php';	
		break;

	case 'toyota-highlander':
		include '../app/views/vehiculos/toyota-highlander.php';	
		break;

	case 'toyota-highlander-extreme':
		include '../app/views/vehiculos/toyota-highlander-extreme.php';	
		break;

	case 'home':
	default:
		include '../app/views/home.php';
		break;
}

include '../app/views/layout/footer.php';
