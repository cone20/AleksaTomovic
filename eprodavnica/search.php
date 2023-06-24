<?php
error_reporting(E_ERROR | E_PARSE);
session_start();

if (isset($_POST['logout'])) {
	unset($_SESSION['user']);
	unset($_SESSION['admin']);
	unset($_SESSION['cart']);
	unset($_SESSION['noOf']);
}

include('./db.php');
$id = $_SESSION['user'];
$novac = 0;
$cartItems = 0;

if (!isset($_SESSION['cart'])) {
	$_SESSION['cart'] = array();
}


$sql = "SELECT stanje FROM korisnici WHERE id = '$id'";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
	$novac = $row['stanje'];
}

$productInfo = array(
	'id' => array(),
	'naziv' => array(),
	'cena' => array(),
	'kolicina' => array(),
	'opis' => array()
);

$sql = "SELECT * FROM artikli ORDER BY naziv";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
	array_push($productInfo['id'], $row['id']);
	array_push($productInfo['naziv'], $row['naziv']);
	array_push($productInfo['cena'], $row['cena']);
	array_push($productInfo['kolicina'], $row['kolicina']);
	array_push($productInfo['opis'], $row['opis']);
}

$requests = array();

foreach ($_POST as $key => $value) {
	if (str_starts_with($key, 'add')) {
		$index = explode('_', $key)[1];

		array_push($_SESSION['cart'], $index);
		$cartItems = sizeof($_SESSION['cart']);

	}
}

if(isset($_GET['search'])) {
	$pojam = '%'.$_GET['searched_item'].'%';
	$sql = "SELECT * FROM artikli WHERE naziv LIKE '$pojam' ORDER BY naziv";

	$productInfo = array(
		'id' => array(),
		'naziv' => array(),
		'cena' => array(),
		'kolicina' => array(),
		'opis' => array()
	);

	$res = $conn->query($sql);

	while ($row = $res->fetch_assoc()) {
		array_push($productInfo['id'], $row['id']);
		array_push($productInfo['naziv'], $row['naziv']);
		array_push($productInfo['cena'], $row['cena']);
		array_push($productInfo['kolicina'], $row['kolicina']);
		array_push($productInfo['opis'], $row['opis']);
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	<title>Е-продавница Претрага</title>
	<!-- Favicon-->
	<link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
	<!-- Bootstrap icons-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<!-- Core theme CSS (includes Bootstrap)-->
	<link href="./styles.css" rel="stylesheet" />
	<link href="./main.css" rel="stylesheet" />
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container px-4 px-lg-5">
			<a class="navbar-brand fw-bolder display-1 text-uppercase" href="http://localhost/eprodavnica/">Е-продавница</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
				aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span
					class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 text-center">
					<li class="nav-item"><a class="nav-link" aria-current="page" href="./index.php">Почетна</a></li>
					<?php if (isset($_SESSION["user"]) || isset($_SESSION['admin'])): ?>
						<li class="nav-item links"><a class="nav-link active" href="./search.php">Претражи</a></li>
					<?php endif; ?>
					<?php if (isset($_SESSION['admin'])): ?>
						<li class="nav-item links admin_link"><a class="nav-link" href="./admin.php">Админ</a></li>
					<?php endif; ?>
				</ul>
				<?php
				if (isset($_SESSION["user"])): ?>
					<a class="links" href="./cart.php">
						<div class="d-flex justify-content-center text-center mb-2">
							Корпа
							<span class="badge bg-dark text-white rounded-pill">
								<?php echo $cartItems; ?>
							</span>
						</div>
					</a>
					<p class="text-center mb-2">Стање:
						<?php echo $novac; ?>
					</p>
					<form method="post" class="d-flex justify-content-center text-center">
						<button type="submit" name="logout" class="btn btn-danger px-2 mx-3">Излогуј се</button>
					</form>
					<?php
				elseif (!isset($_SESSION['user']) && !isset($_SESSION['admin'])):
					?>
					<div class="d-flex flex-column flex-lg-row gap-sm-3 gap-lg-5 text-center">
						<a class="nav-link links" href="./login.php">Пријави се</a>
						<a class="nav-link links" href="./register.php">Направи налог</a>
					</div>
					<?php
				endif;
				?>
				<?php if (isset($_SESSION['admin'])): ?>
					<form method="post" class="d-flex justify-content-center text-center">
						<button type="submit" name="logout" class="btn btn-danger px-2 mx-3">Излогуј се</button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</nav>


	<!-- Header-->
	<header class="bg-dark py-5">
		<div class="container px-4 px-lg-5 my-5 h-100">
			<div class="text-center text-white">
				<h1 class="display-4 fw-bolder">Претражите производе:</h1>
				<p class="lead fw-normal text-white-50 mb-0"></p>
			</div>
		</div>
	</header>
	<!-- Search input -->
	<section class="bg-dark py-5">
		<div class="container px-4 px-lg-5">
			<form method="get">
				<div class="text-center text-white d-flex flex-column flex-md-row justify-content-center  gap-5">
					<input type="text" name="searched_item" class="inputs w-100" placeholder="Унесите кључну реч" value="<?php echo $_GET['searched_item'] ?>" >
					<button type="submit" name="search" class="btn btn-light">Претражи</button>
				</div>
			</form>
		</div>
	</section>

	<section class="py-5">
		<div class="container px-4 px-lg-5 mt-5">
			<div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-3 row-cols-xl-4 justify-content-center">
				<!-- Cards -->
				<?php for ($i = 0; $i < sizeof($productInfo['id']); $i++): ?>

					<div class="col mb-5">
						<div class="card h-100">
							<!-- Product image-->
							<img class="card-img-top" src="https://dummyimage.com/450x300/dee2e6/6c757d.jpg" alt="..." />
							<!-- Product details-->
							<div class="card-body p-4">
								<div class="text-center">
									<!-- Product name-->
									<h5 class="fw-bolder">
										<?php echo $productInfo['naziv'][$i]; ?>
									</h5>
									<p class="text-muted font-italic">
										<?php echo $productInfo['opis'][$i]; ?>
									</p>
									<!-- Product price-->
									<?php echo $productInfo['cena'][$i]; ?> РСД
								</div>
								<div class="text-center">
									Количина:
									<?php echo $productInfo['kolicina'][$i]; ?>
								</div>
							</div>
							<!-- Product actions-->
							<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
								<div class="text-center">
									<?php if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])): ?>
										<a class="btn btn-outline-dark mt-auto" href="./login.php">Пријава</a>
									<?php endif; ?>
									<?php if (isset($_SESSION['user'])): ?>
										<?php if (in_array($productInfo['id'][$i], $_SESSION['cart']) && $productInfo['kolicina'][$i] > 0): ?>
											<input type="submit" value="У корпи!" disabled>
										<?php endif; ?>
										<?php if (!in_array($productInfo['id'][$i], $_SESSION['cart']) && $productInfo['kolicina'][$i] > 0): ?>
											<form method="post">
												<input type="submit" value="Додај у корпу" name="add_<?php echo $productInfo['id'][$i] ?>">
											</form>
										<?php endif; ?>
										<?php if ($productInfo['kolicina'][$i] == 0): ?>
											<form method="post">
												<input type="submit" value="Нема на стању!" disabled>
											</form>
										<?php endif; ?>

									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

				<?php endfor; ?>
			</div>
		</div>
	</section>
	<!-- Footer-->
	<footer id="footer" class="py-5 bg-dark">
		<div class="container">
			<p class="m-0 text-center text-white">Сва права задржана &copy; Алекса Томовић 2023</p>
		</div>
	</footer>

	<!-- Bootstrap -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
		crossorigin="anonymous"></script>
</body>

</html>