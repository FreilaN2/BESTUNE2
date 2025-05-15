<?php
	// Obtener el parámetro 'view' de la URL (o usar 'inicio' como valor por defecto)
	$current_view = isset($_GET['view']) ? $_GET['view'] : '';
	$current_aliado = isset($_GET['aliados']) ? $_GET['aliados'] : '';

if (!function_exists('isActive')) {
	function isActive($view_param, $aliado_param = '') {
		global $current_view, $current_aliado;

		if ($view_param === '' && $current_view === '') {
			return true;
		}

		if ($view_param === $current_view) {
			if ($aliado_param === '' || $aliado_param == $current_aliado) {
				return true;
			}
		}

		return false;
	}
}
?>

	<link rel="shortcut icon" type="image/png" href="assets/img/favicon.ico">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/all.min.css">
	<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.css">
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<link rel="stylesheet" href="assets/css/animate.css">
	<link rel="stylesheet" href="assets/css/meanmenu.min.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/responsive.css">

<style>
	
.instagram-slider {
    position: relative;
    max-width: 900px; 
    margin: 0 auto;
    overflow: hidden;
}

.instagram-slides {
    display: flex;
    transition: transform 0.5s ease;
    gap: 8px; 
    padding: 0 5px; 
}

.instagram-slide {
    min-width: calc(50% - 4px); /* 50% - mitad del gap (8px/2) */
    box-sizing: border-box;
    transform: scale(0.7); /* Tamaño al 70% */
    transform-origin: center;
    margin: 0; /* Elimina márgenes adicionales */
}

/* Asegura que Instagram no añada espacios extra */
.instagram-slide blockquote.instagram-media {
    margin: 0 !important; 
    padding: 0 !important;
}
        
.slider-nav {
	text-align: center;
	margin-top: 10px;
}
        
.slider-dot {
	display: inline-block;
	width: 10px;
	height: 10px;
	background: #ccc;
	border-radius: 50%;
	margin: 0 5px;
	cursor: pointer;
}
        
.slider-dot.active {
	background: #3897f0;
}
     
.slider-arrow {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	background: rgba(0,0,0,0.5);
	color: white;
	border: none;
	padding: 10px;
	cursor: pointer;
	z-index: 10;
}
        
.prev {
	left: 10px;
}
        
.next {
	right: 10px;
}
   
.product-image2 img {
	width: 77%;
	border-radius: 5px;
	margin-bottom: 20px;
}

.product-image3 img {
	width: 100%;
	border-radius: 5px;
	margin-bottom: 20px;
}

.main-menu ul li a.active {
    color: #fca311; 
    font-weight: bold;
}

.main-menu ul li.dropdown.active > a.dropdown-toggle {
  
    color: #fca311;
    font-weight: bold;
}


.main-menu ul li a.dropdown-item.active {
    color: #fca311;
}

</style>
</head>

<body>
	<!-- HEADER -->
<div class="loader">
	<div class="loader-inner">
		<div class="circle"></div>
	</div>
</div>

<div class="top-header-area" id="sticker">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-sm-12 text-center">
				<div class="main-menu-wrap">
					<div class="site-logo">
						<a href="index.php">
							<img src="assets/img/logo.png" alt="">
						</a>
					</div>
					<nav class="main-menu">
						<ul>
							<li><a href="index.php" class="<?php echo isActive('') ? 'active' : ''; ?>">INICIO</a></li>
							
							<li class="dropdown <?php echo (strpos($current_view, 'contacto') !== false) ? 'active' : ''; ?>">
								<a class="dropdown-toggle" href="#" data-bs-toggle="dropdown">ALIADOS COMERCIALES</a>
								<ul class="sub-menu">
									<li><a class="dropdown-item" href="#">Región Central <i class="fas fa-angle-right"></i></a>
										<ul class="submenu dropdown-menu text-center">
											<li><a class=" <?php echo isActive('contacto', '1') ? 'active' : ''; ?>" href="index.php?view=contacto&aliados=1">Chacao</a></li>
											<li><a class=" <?php echo isActive('contacto', '9') ? 'active' : ''; ?>" href="index.php?view=contacto&aliados=9">Las Mercedes</a></li>
											<li><a class=" <?php echo isActive('contacto', '3') ? 'active' : ''; ?>" href="index.php?view=contacto&aliados=3">Altamira</a></li>
											<li><a class=" <?php echo isActive('contacto', '4') ? 'active' : ''; ?>" href="index.php?view=contacto&aliados=4">Valencia</a></li>
										</ul>
									</li>
									<li><a class="dropdown-item" href="#">Región Oriental <i class="fas fa-angle-right"></i> </a>
										<ul class="submenu dropdown-menu">
											<li><a class=" <?php echo isActive('contacto', '6') ? 'active' : ''; ?>" href="index.php?view=contacto&aliados=6">Lechería</a></li>
										</ul>
									</li>
									<li><a class="dropdown-item" href="#">Región Los Llanos <i class="fas fa-angle-right"></i></a>
										<ul class="submenu dropdown-menu">
											<li><a class=" <?php echo isActive('contacto', '8') ? 'active' : ''; ?>" href="index.php?view=contacto&aliados=8">Barinas</a></li>
										</ul>
									</li>
									<li><a class="dropdown-item" href="#">Región Guárico <i class="fas fa-angle-right"></i></a>
										<ul class="submenu dropdown-menu">
											<li><a class=" <?php echo isActive('contacto', '10') ? 'active' : ''; ?>" href="index.php?view=contacto&aliados=10">Valle de la Pascua</a></li>
										</ul>
									</li>
								</ul>
							</li>
							
							<li><a href="index.php?view=garantia" class="<?php echo isActive('garantia') ? 'active' : ''; ?>">GARANTIA</a></li>
							
							<li class="dropdown <?php echo (strpos($current_view, 'bestune') !== false || strpos($current_view, 'toyota') !== false) ? 'active' : ''; ?>">
								<a class="dropdown-toggle" href="#" data-bs-toggle="dropdown">VEHICULOS</a>
								<ul class="sub-menu">
									<li><a class="dropdown-item" href="#">Faw Bestune <i class="fas fa-angle-right"></i></a>
										<ul class="submenu dropdown-menu text-center">
											<li><a class="<?php echo isActive('bestune-t55') ? 'active' : ''; ?>" href="index.php?view=bestune-t55">T55</a></li>
											<li><a class="<?php echo isActive('bestune-t77') ? 'active' : ''; ?>" href="index.php?view=bestune-t77">T77</a></li>
											<li><a class="<?php echo isActive('bestune-t99') ? 'active' : ''; ?>" href="index.php?view=bestune-t99">T99</a></li>
											<li><a class="<?php echo isActive('bestune-b70') ? 'active' : ''; ?>" href="index.php?view=bestune-b70">B70</a></li>
											<li><a class="<?php echo isActive('bestune-r7') ? 'active' : ''; ?>" href="index.php?view=bestune-r7">R7</a></li>
										</ul>
									</li>
									<li><a class="dropdown-item" href="#">Faw Toyota <i class="fas fa-angle-right"></i></a>
										<ul class="submenu dropdown-menu text-center">
											<li><a class="dropdown-item <?php echo isActive('toyota-corolla') ? 'active' : ''; ?>" href="index.php?view=toyota-corolla">Corolla</a></li>
											<li><a class="dropdown-item <?php echo isActive('toyota-levin') ? 'active' : ''; ?>" href="index.php?view=toyota-levin">Levin</a></li>
											<li><a class="dropdown-item <?php echo isActive('toyota-corollacross') ? 'active' : ''; ?>" href="index.php?view=toyota-corollacross">Corolla Cross</a></li>
										</ul>
									</li>
								</ul>
							</li>


							<li><a href="index.php?view=planes_venta" class="<?php echo isActive('planes_venta') ? 'active' : ''; ?>">PLANES DE VENTA</a></li>
							
							<li><a href="https://bestune-venezuela.com/quienessomos.php" class="cart-btn" style="background:#fca311; color:white;">SOMOS INTERNACIONAL</a></li>
						</ul>
					</nav>
					<div class="mobile-menu"></div>
				</div>
			</div>
		</div>
	</div>
</div>
