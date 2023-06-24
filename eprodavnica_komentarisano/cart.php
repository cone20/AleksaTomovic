<?php
session_start();

// ukoliko korisnik nije ulogovan bice vracen na pocetnu
if (!isset($_SESSION['user'])) {
	header('Location: ./index.php');
	exit();
}

// ukoliko je stisnuto dugme za logout
if (isset($_POST['logout'])) {
	unset($_SESSION['user']);
	unset($_SESSION['admin']);
	unset($_SESSION['cart']);
	unset($_SESSION['noOf']);
}

include('./db.php');

// sve sto radimo u cart.php (korpi) mogu samo korisnici da rade, tkd
// sva funkcionalnost je omogucena samo ulogovanim korisnicima
if (isset($_SESSION['user'])) {
	// uzimamo $id korisnika koji je ulogovan, postavljamo da je novac = 0 
	$id = $_SESSION['user'];
	$novac = 0;

	// ukoliko nije postavljena sesija cart (tj. ako smo usli na cart bez prethodnog dodavanja icega u korpu)
	if (!isset($_SESSION['cart'])) {
		// podesicemo da je cart prazan niz, a da je noOf (number of) takodje prazan niz
		$_SESSION['cart'] = array();	
		$_SESSION['noOf'] = array();
	}

	// ukoliko nije postavljen noOf sesija ovde cemo je postaviti i zavisno od broja elemenata u korpi ispisacemo
	// odredjeni broj 1, noOf sluzi da mi odredimo koliko kolicinski artikala korisnik zeli, tkd ako imamo u $_SESSION['cart'] id-eve {1, 2, 3}
	// u noOf imacemo {1, 1, 1} na pocetku
	if(!isset($_SESSION['noOf'])) {
		$_SESSION['noOf'] = array();
		
		for($i = 0; $i < sizeof($_SESSION['cart']); $i++) {
			array_push($_SESSION['noOf'], 1);
		}
		
	}

	// IZABERI stanje IZ korisnici GDE JE id = $id
	$sql = "SELECT stanje FROM korisnici WHERE id = '$id'";
	$res = $conn->query($sql);
	while ($row = $res->fetch_assoc()) {
		// azuriramo stanje koje korisnik ima na nalogu
		$novac = $row['stanje'];
	}

	// na pocetku ukupna cena proizvoda je 0
	$cenaUkupno = 0;

	// pravimo asocijativni niz, koji ima nizove u sebi, ovo se koristi da bismo odredili po indeksu sta cemu odgovara
	// npr ako nam je id 28 na indeksu 0, onda ce cena na indeksu 0 da odgovara tom id-u
	$productInfo = array(
		'id' => array(),
		'naziv' => array(),
		'cena' => array(),
		'kolicina' => array(),
	);

	// ovde popunjavamo nas asocijativni niz
	for ($i = 0; $i < sizeof($_SESSION['cart']); $i++) {
		$id = $_SESSION['cart'][$i];

		$sql = "SELECT * FROM artikli WHERE id = '$id'";
		$res = $conn->query($sql);

		while ($row = $res->fetch_assoc()) {
			array_push($productInfo['id'], $row['id']);
			array_push($productInfo['naziv'], $row['naziv']);
			array_push($productInfo['cena'], $row['cena']);
			array_push($productInfo['kolicina'], $row['kolicina']);
			$cenaUkupno += $row['cena'];
		}
	}

	// ovo je komplikovanije, ali u principu ukoliko name="add_..." pocinje sa "add" onda ce broj iza "_" da bude
	// id koji je kliknut, zatim odredice se cena na osnovu kolicine artikla koja je izabrana
	foreach($_POST as $key => $value) {
		if(str_starts_with($key, 'add')) {
			$index = explode('_', $key)[1];
			$_SESSION['noOf'][$index] = $_POST[$key];
			$cenaUkupno = 0;
			for($i = 0; $i < sizeof($_SESSION['noOf']); $i++) {
				$cenaUkupno += $productInfo['cena'][$i] * $_SESSION['noOf'][$i]; // ovde se vidi kako se racuna cena
			}
		}
	}

	// ukoliko je kliknuto dugme za kupovinu
	if(isset($_POST['buy'])) {
		$success = false;
		$errorMessage = '';

		// proveravamo da li je korisnik uneo vece vrednosti od onih koje imamo u bazi
		// i da li je uneo negativne brojeve
		for($i = 0; $i < sizeof($_SESSION['noOf']); $i++) {
			if($_SESSION['noOf'][$i] > $productInfo['kolicina'][$i]) {
				$_SESSION['noOf'][$i] = $productInfo['kolicina'][$i];
			} else if ($_SESSION['noOf'][$i] <= 0) {
				$_SESSION['noOf'][$i] = 1;
			}

			// na kraju racunamo ukupnu cenu koja ce biti oduzeta sa naloga korisnika
			$cenaUkupno += $productInfo['cena'][$i] * $_SESSION['noOf'][$i];
		}

		// ukoliko je cena veca od stanja na racunu izbacice se greska
		if($cenaUkupno > $novac) {
			$errorMessage = 'Немате довољно новца на рачуну! Уплатите у најближој пошти или се обратите администратору!';
		} 
		// ukoliko je prazna korpa izbacice se greska
		else if (isset($_SESSION['cart']) && sizeof($_SESSION['cart']) === 0) {
			$errorMessage = 'Нисте убацили ништа у корпу!';
		} else {
			// ovde racunamo i azuriramo kolicinu svakog artikla koji smo odabrali
			for($i = 0; $i < sizeof($_SESSION['cart']); $i++) {
				$id = $_SESSION['cart'][$i];
				$kol = $productInfo['kolicina'][$i] - $_SESSION['noOf'][$i];
				$sql = "UPDATE artikli SET kolicina = '$kol' WHERE id = '$id'";
				$conn->query($sql);
			}

			$userId = $_SESSION['user'];
			$novoStanje = $novac - $cenaUkupno;
			// a ovde azuriramo stanje na racunu korisnika
			$sql = "UPDATE korisnici SET stanje = '$novoStanje' WHERE id = '$userId'";
			$conn->query($sql);
			
			$success = true;

			// na kraju praznimo sesije cart i noOf
			$_SESSION['cart'] = array();
			$_SESSION['noOf'] = array();
			header('Refresh: 2');
		}
	}

	// ukoliko je kliknuto dugme za brisanje ispraznice se sesijski nizovi
	if(isset($_POST['remove'])) {
		$_SESSION['noOf'] = array();
		$_SESSION['cart'] = array();
		header('Refresh: 0');
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
	<title>Е-продавница Корпа</title>
	<!-- Favicon-->
	<link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
	<!-- Bootstrap icons-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
	<!-- Core theme CSS (includes Bootstrap)-->
	<link href="./styles.css" rel="stylesheet" />
	<link href="./main.css" rel="stylesheet" />
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container px-4 px-lg-5">
			<a class="navbar-brand fw-bolder display-1 text-uppercase" href="http://localhost/e-commerce/">Е-продавница</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
				aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span
					class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 text-center">
					<li class="nav-item"><a class="nav-link" aria-current="page" href="./index.php">Почетна</a></li>
					<?php if (isset($_SESSION["user"]) || isset($_SESSION['admin'])): ?>
						<li class="nav-item links"><a class="nav-link" href="./search.php">Претражи</a></li>
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
								<?php echo sizeof($_SESSION['cart']); ?>
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
		<div class="container px-4 px-lg-5 my-5">
			<div class="text-center text-white">
				<h1 class="display-4 fw-bolder">Ваша корпа</h1>
				<p class="lead fw-normal text-white-50 mb-0">Помоћ доступна 24/7</p>
				<p class="lead fw-normal text-white-50 mb-0">e-prodavnica@gmail.com</p>
			</div>
		</div>
	</header>
	
	<?php if(isset($_SESSION['user']) && sizeof($_SESSION['cart']) > 0): ?>
		<section class="py-5 ">
			<div class="container-fluid ">
				<div class="row d-flex justify-content-center">
					<div class="col-sm-12 col-md-10 col-lg-9">
						<div class="row d-flex justify-content-end">
							<div class="col-sm-12 col-md-6 col-lg-2">
								<form method="post">
									<button type="submit" class="w-100 btn btn-outline-danger" name="remove"><i class="text-end bi bi-trash"></i>Обриши</button>

								</form>
							</div>
						</div>
						<table class="table table-sm">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Назив</th>
									<th scope="col">Цена</th>
									<th scope="col">Количина</th>
									<th scope="col">Доступно</th>
									<th scope="col">Укупна цена</th>
									<th scope="col">&Sigma;</th>
								</tr>
							</thead>
							<tbody>
								<?php for ($i = 0; $i < sizeof($productInfo['id']); $i++): ?>
									<tr>
										<th scope="row">
											<?php echo $productInfo['id'][$i]; ?>
										</th>
										<td>
											<?php echo $productInfo['naziv'][$i]; ?>
										</td>
										<td>
											<?php echo $productInfo['cena'][$i]; ?>
										</td>
										<td>
											<form method="post">
												<input class="w-50" type="number" onchange="this.form.submit()" name="add_<?php echo $i ?>" min="1" max="<?php echo $productInfo['kolicina'][$i]; ?>" value="<?php echo $_SESSION['noOf'][$i]; ?>" placeholder="1">
											</form>
										</td>
										<td>
											<?php echo $productInfo['kolicina'][$i]; ?>
										</td>
										<td>
											<?php echo ($productInfo['cena'][$i] * $_SESSION['noOf'][$i] == 0) ? $productInfo['cena'][$i] : $productInfo['cena'][$i] * $_SESSION['noOf'][$i];?>
										</td>
										<td></td>
									</tr>
								<?php endfor; ?>
								<?php if (sizeof($productInfo['id'])): ?>
									<tr>
										<th scope="row"></th>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td class="font-weight-bold">
											&Sigma;
											<?php echo $cenaUkupno; ?>
										</td>
									</tr>
								<?php endif; ?>

							</tbody>
						</table>


					</div>
				</div>
			</div>
			<div class="container-fluid mt-5">
				<div class="row d-flex justify-content-center">
					<div class="col-sm-10 col-md-6 col-lg-4 d-flext justify-content-center ">
						<form method="post">
							<input type="submit" class='w-100 mb-4' name="buy" value='Купи!'>
						</form>
						<?php if($errorMessage): ?>
							<p class="error text-center"><?php echo $errorMessage; ?></p>
						<?php endif; ?>
						<?php if($success): ?>
							<p class="success text-center">Успешно сте купили производе, ускоро Вам стижу на адресу!</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
  	<?php else: ?>
		<div class="container-fluid">
			<h2 class="text-center">Немате ништа у корпи... :(</h2>
		</div>
	<?php endif; ?>



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