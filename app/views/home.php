<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="bestune venezuela" name="keywords" />
	<meta content="bestune venezuela,BESTUNE,FAW" name="description" />
	<title>BESTUNE VENEZUELA</title>

	<div class="homepage-slider owl-carousel">
<?php
$slides = [
    [
        'bg' => 'assets/img/intro-bg.webp',
        'subtitle' => 'BESTUNE, la marca más reciente de',
        'title' => 'FAW Group',
        'align' => 'col-lg-7 offset-lg-1 offset-xl-0'
    ],
    [
        'bg' => 'assets/img/new-bg-tecnologia-t55.webp',
        'subtitle' => 'Máxima tecnología',
        'title' => 'Calidad, tecnología y trayectoria',
        'align' => 'col-lg-10 offset-lg-1 text-center'
    ],
    [
        'bg' => 'assets/img/t99heaswe.webp',
        'subtitle' => 'Lo mejor',
        'title' => 'Un diseño para encantar',
        'align' => 'col-lg-10 offset-lg-1 text-right'
    ]
];

foreach ($slides as $slide) {
    $imagePath = file_exists($slide['bg']) ? $slide['bg'] : 'img/default-slide.webp';
?>
    <div class="single-homepage-slider" style="background-image: url('<?= $imagePath ?>')">
        <div class="container">
            <div class="row">
                <div class="col-md-12 <?= $slide['align'] ?>">
                    <div class="hero-text">
                        <div class="hero-text-tablecell">
                            <p class="subtitle"><?= $slide['subtitle'] ?></p>
                            <h1><?= $slide['title'] ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div>


<div class="list-section align-items-center text-center" style="background-color: #fca311;padding-top: 20px;padding-bottom: 20px;">
	<div class="container">
		<div class="row">
			<div class="col-lg-4">
				<div class="list-box d-flex align-items-center">
					<img src="assets/img/vzla.webp" class="text-center" style="width: 80%;">
				</div>
			</div>
			<div class="col-lg-8">
				<div class="list-box d-flex align-items-center mt-4">
					<div class="content mt-4">
						<h2 class="text-white">Ya disponibles en <span class="orange-text" style="color:#14213d;">VENEZUELA</span></h2>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="product-section mt-100 mb-100">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2 text-center">
				<div class="section-title">
					<h3><span class="orange-text">VEHÍCULOS</span></h3>
					<p>Descubre lo nuevo de BESTUNE.</p>
				</div>
			</div>

			<?php
			$vehiculos = [
				['nombre' => 'T99', 'img' => 't99/t99-12.webp', 'url' => 'bestune-t99.php'],
				['nombre' => 'T77', 'img' => 't77/t77bg.webp', 'url' => 'bestune-t77.php' , 'class' => 'product-image3'],
				['nombre' => 'T55', 'img' => 't55/T55-7.webp', 'url' => 'bestune-t55.php'],
				['nombre' => 'B70', 'img' => 'b70/B70.webp', 'url' => 'bestune-b70.php'],
				['nombre' => 'R7', 'img' => 'r7/R7.webp', 'url' => 'bestune-R7.php', 'class' => 'product-image2'],
			];
			foreach ($vehiculos as $vehiculo):
			?>
				<div class="col-lg-4 col-md-6 text-center">
					<div class="single-product-item">
						<div class="<?= $vehiculo['class'] ?? 'product-image' ?>">
							<a href="<?= $vehiculo['url'] ?>"><img src="assets/img/<?= $vehiculo['img'] ?>" alt=""></a>
						</div>
						<p class="product-price"><?= $vehiculo['nombre'] ?></p>
						<a href="<?= $vehiculo['url'] ?>" class="cart-btn mb-2"> Ver más</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div class="list-section pt-80 pb-80">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
				<div class="list-box d-flex align-items-center">
					<div class="list-icon">
						<i class="fas fa-car"></i>
					</div>
					<div class="content">
						<h3>Vehículos</h3>
						<p>Haga su solicitud con algunos de nuestros agentes.</p>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
				<div class="list-box d-flex align-items-center">
					<div class="list-icon">
						<i class="fas fa-map"></i>
					</div>
					<div class="content">
						<h3>Nuestros Concesionarios</h3>
						<p>Nuestros aliados están dispuestos a ayudarte.</p>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-md-6">
				<div class="list-box d-flex justify-content-start align-items-center">
					<div class="list-icon">
						<i class="fas fa-mobile"></i>
					</div>
					<div class="content">
						<h3>Solicita tu Cotización</h3>
						<p>Atención personalizada.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="video-container mb-4">
	<video src="assets/img/t77.mp4" autoplay loop playsinline muted></video>
</div>

<div class="abt-section mb-4" style="padding-top: 30px;">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-md-12">
				<div class="abt-bg">
					<a href="assets/img/BESTUNE_T77.mp4" class="video-play-btn popup-youtube"><i class="fas fa-play"></i></a>
				</div>
			</div>
			<div class="col-lg-6 col-md-12">
				<div class="abt-text">
					<p class="top-sub">Prueba de manejo</p>
					<h2>Reserve una <span class="orange-text">prueba de manejo.</span></h2>
					<p>Tendrás la oportunidad de probar cualquiera de nuestros modelos disponibles a la venta, y así podrás darte cuenta de la calidad de cada uno de nuestros vehículos.</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="list-section pt-80 pb-80">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
				<div class="list-box d-flex align-items-center">
					<div class="list-icon">
						<i class="fas fa-car" style="color: #1136fc;border: 2px #1136fc dotted;"></i>
					</div>
					<div class="content">
						<h3>Compra Nuevo</h3>
						<p>Con 3 Años de Garantía o 100 Mil Km.</p>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
				<div class="list-box d-flex align-items-center">
					<div class="list-icon">
						<i class="fas fa-car"></i>
					</div>
					<div class="content">
						<h3>Compra Usado</h3>
						<p>Con Garantía Extendida.</p>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-md-6">
				<div class="list-box d-flex justify-content-start align-items-center">
					<div class="list-icon">
						<i class="fas fa-car" style="color: #0dc818;border: 2px #0dc818 dotted;"></i>
					</div>
					<div class="content">
						<h3>Vende tu BESTUNE usado</h3>
						<p>En nuestros concesionarios.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="testimonail-section mt-100 mb-100">
	<div class="container">
		<div class="row">
			<div class="col-lg-10 offset-lg-1 text-center">
				<div class="single-testimonial-slider">
					<div class="client-avater">
						<a href="contacto.php">
							<img src="assets/img/sucursal1.webp" alt="">
						</a>
					</div>
					<div class="client-meta">
						<h3>EVENTO <span>Exhibición de vehículos</span></h3>
						<p class="testimonial-body">
							Gran exhibición en Guatire, con nuestros aliados comerciales 1947 RUTA. Sábado 04 de Febrero
						</p>
						<div class="last-icon">
							<i class="fas fa-car"></i>
							<p><i class="fab fa-instagram"></i> @1947rutaCars</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="video-container">
	<video src="assets/img/t99.mp4" autoplay loop playsinline muted></video>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const slides = document.querySelector('.instagram-slides');
	const slideItems = document.querySelectorAll('.instagram-slide');
	const dots = document.querySelectorAll('.slider-dot');
	const prevBtn = document.querySelector('.prev');
	const nextBtn = document.querySelector('.next');

	let currentIndex = 0;
	const slidesToShow = 2;

	function updateSlider() {
		const slideWidth = slideItems[0].offsetWidth + 20;
		slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`;

		dots.forEach((dot, index) => {
			dot.classList.toggle('active', index === Math.floor(currentIndex / slidesToShow));
		});
	}

	nextBtn.addEventListener('click', () => {
		if (currentIndex < slideItems.length - slidesToShow) {
			currentIndex += slidesToShow;
			updateSlider();
		}
	});

	prevBtn.addEventListener('click', () => {
		if (currentIndex > 0) {
			currentIndex -= slidesToShow;
			updateSlider();
		}
	});

	setInterval(() => {
		if (currentIndex < slideItems.length - slidesToShow) {
			currentIndex += slidesToShow;
		} else {
			currentIndex = 0;
		}
		updateSlider();
	}, 5000);
});
</script>

</body>
</html>