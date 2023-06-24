<?php
// pokrecemo sesiju
session_start();

// ako je pritisnuto dugme logout onda ce se unistiti sve sesije
if(isset($_POST['logout'])) {
	unset($_SESSION['user']);
	unset($_SESSION['admin']);
	unset($_SESSION['cart']);
}

// ukoliko ne postoji sesija admin korisnik ce biti vracen na pocetnu
if (!isset($_SESSION['admin'])) {
	header('Location: ./index.php');
	exit();
}

include('./db.php');


// ukoliko je dugme sbm stisnuto
if(isset($_POST['sbm'])) {
	$success = false;
	$errorMessage = '';
	
	// ovde uzimamo podatke o proizvodu
	$naziv = $_POST['naziv'];
	$kolicina = $_POST['kolicina'];
	$cena = $_POST['cena'];
	$opis = $_POST['opis'];

	// IZABERI SVE IZ artikli GDE JE NAZIV $naziv
	$sql = "SELECT * FROM artikli WHERE naziv = '$naziv'";

	$res = $conn->query($sql);

	// ukoliko postoji proizvod sa unetim nazivom izbacice se greska
	if($res->num_rows == 1) {
		$errorMessage = 'Већ постоји наведени производ, унесите га у доњу форму!';
	}else {
		// ukoliko ne postoji, tj. ako je novi proizvod
		// UBACI U artikli (...) VREDNOSTI (...)
		$sql = "INSERT INTO artikli (naziv, kolicina, cena, opis) VALUES ('$naziv', '$kolicina', '$cena', '$opis')";
		$conn->query($sql);
		$success = true;
		header('Refresh: 1');
	}


}

// ukoliko je pritisnuto drugo dugme (za azuriranje proizvoda)
if(isset($_POST['sbm1'])) {
	$success1 = false;
	$errorMessage1 = '';

	$naziv = $_POST['naziv'];
	$kolicina = $_POST['kolicina'];
	$trenutnaKolicina = 0;

	// IZABERI kolicina IZ artikli GDE JE naziv = $naziv
	$sql = "SELECT kolicina FROM artikli WHERE naziv = '$naziv'";
	$res = $conn->query($sql);

	// Ukoliko ne postoji proizvod sa datim nazivom izbacice se greska (jer ovde azuriramo vec postojece)
	if($res->num_rows === 0) {
		$errorMessage1 = 'Не постоји производ са називом "'. $naziv . '"!';
	} else {
		// uzimamo trenutnu kolicinu koja je na stanju
		while($row = $res->fetch_assoc()) {
			$trenutnaKolicina = $row['kolicina'];
		}

		// zatim, dodajemo joj nasu unetu kolicinu, npr ako imamo 5 na stanju, a uneli smo 10, dobicemo 5 + 10 = 15 
		// koje cemo azurirati u bazi 
		$trenutnaKolicina += $kolicina;
		// AZURIRAJ artikli POSTAVI DA JE kolicna = $trenutaKolicina GDE JE naziv = $naziv
		$sql = "UPDATE artikli SET kolicina = '$trenutnaKolicina' WHERE naziv = '$naziv'";
		$conn->query($sql);

		$success1 = true;
	}
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin</title>
	<link rel="stylesheet" href="./styles.css">
	<link rel="stylesheet" href="./main.css">

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
					<li class="nav-item links admin_link"><a class="nav-link active" href="./admin.php">Админ</a></li>
				</ul>
				<form method="post" class="d-flex justify-content-center text-center">
					<button type="submit" name="logout" class="btn btn-danger px-2 mx-3">Излогуј се</button>
				</form>
			</div>
		</div>
	</nav>

	<section class="bg-dark py-5">
		<div class="container px-4 px-lg-5 my-5">
			<div class="text-center text-white">
				<section id="register" class="w-100 d-flex justify-content-center">
					<div class="container h-auto w-100">
						<div class="row d-flex justify-content-center align-items-center h-100">
							<div class="col-12 col-md-8 col-lg-6 col-xl-5">
								<d class="" style="border-radius: 1rem;">
									<h3 class="display-6 mb-5 fw-bolder">Додај нови производ </h3>
									<form method="post" class="d-flex flex-column gap-3">
										<input type="text" name="naziv" class="inputs" placeholder="Унесите назив производа" required />
										<input type="float" step="0.01" min="0.01" name="cena" class="inputs"
											placeholder="Унесите цену производа" required />
										<input type="number" min="1" name="kolicina" class="inputs" placeholder="Унесите количину"
											required />
										<input type="text" name="opis" class="inputs" placeholder="Унесите опис" required />
										<button class="btn btn-light btn-lg btn-block" type="submit" name="sbm">Пошаљи захтев</button>
									</form>
									<?php if ($errorMessage): ?>
										<p class="error mt-4">
											<?php echo $errorMessage; ?>
										</p>
									<?php endif; ?>
									<?php if ($success): ?>
										<p class="success mt-4">Успешнo додат производ!</p>
									<?php endif; ?>
							</div>
						</div>
					</div>
			</div>
	</section>

	<section class="bg-dark py-5">
		<div class="container px-4 px-lg-5 my-5">
			<div class="text-center text-white">
				<section id="register" class="w-100 d-flex justify-content-center">
					<div class="container h-auto w-100">
						<div class="row d-flex justify-content-center align-items-center h-100">
							<div class="col-12 col-md-8 col-lg-6 col-xl-5">
								<d class="" style="border-radius: 1rem;">
									<h3 class="display-6 mb-5 fw-bolder">Додај већ постојећи производ </h3>
									<form method="post" class="d-flex flex-column gap-3">
										<input type="text" name="naziv" class="inputs" placeholder="Унесите тачан назив производа"
											required />
										<input type="number" min="1" name="kolicina" class="inputs"
											placeholder="Унесите додатак већ постојећој количини" required />
										<button class="btn btn-light btn-lg btn-block" type="submit" name="sbm1">Пошаљи захтев</button>
									</form>

									<?php if ($errorMessage1): ?>
										<p class="error mt-4">
											<?php echo $errorMessage1; ?>
										</p>
									<?php endif; ?>
									<?php if ($success1): ?>
										<p class="success mt-4">Успешнo ажуриран производ!</p>
									<?php endif; ?>
							</div>
						</div>
					</div>
			</div>
	</section>
</body>

</html>