<?php
// session_start() pokrece sesiju, ustv sesija jesu promenljive koje se mogu koristiti na celom sajtu
// na svakoj stranici gde ima session_start() mogu se koristiti promenljive $_SESSION 
session_start();

// proveravamo da li je ulogovan user ili admin
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
	// ako jeste vracamo ih na pocetnu
	header('Location: ./index.php');
	exit();
}

// ukljucujemo vezu sa bazom
include('./db.php');

// ukoliko je kliknuto dugme sa name="login" ovo ce se pokrenuti
if (isset($_POST['login'])) {
	// uzimamo da je $user ono sto je uneto u 'email' polje
	// a da je $lozinka ono sto je uneto u name="password" polje
	$user = $_POST['email'];
	$lozinka = md5($_POST['password']);

	// na pocetku $success (uspeh) nije tacan i nemamo poruku o gresci ($errorMsg)
	$success = false;
	$errorMsg = '';

	// IZABERI id IZ administratori GDE JE korisnicko_ime = $user I GDE JE lozinka = $lozinka
	$sql = "SELECT id FROM administratori WHERE korisnicko_ime = '$user' AND lozinka = '$lozinka'";

	// izvrsavamo nas query
	$res = $conn->query($sql);

	// ukoliko imamo num_rows 1 znaci da se poklapaju podaci 
	// tkd podesicemo sesiju admina da je jednaka njegovom ID-u iz baze
	if ($res->num_rows === 1) {
		$id = 0;

		while ($row = $res->fetch_assoc()) {
			$id = $row['id'];
		}

		$_SESSION['admin'] = $id;
		$success = true;

		header("Refresh: 2 ; URL=./index.php");

	} else { // ukoliko nisu tacni podaci ovo ce da se ispise na dnu stranice
		$errorMsg = 'Погрешно унешено корисничко име и/или лозинка!';
	}
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Uloguj se!</title>
	<link rel="stylesheet" href="./styles.css">
	<link rel="stylesheet" href="./main.css">

</head>

<body>
	<section class="bg-dark py-5">
		<div class="container px-4 px-lg-5 my-5">
			<div class="text-center text-white">
				<section id="register" class="w-100 d-flex justify-content-center">
					<div class="container h-auto w-100">
						<div class="row d-flex justify-content-center align-items-center h-100">
							<div class="col-12 col-md-8 col-lg-6 col-xl-5">
								<d class="" style="border-radius: 1rem;">
								<h3 class="display-6 mb-5 fw-bolder">Пријави се</h3>
									<form method="post" class="d-flex flex-column gap-3">
										<input type="text" name="email" class="inputs" placeholder="Унесите ваше корисничко име" required />
										<input type="password" name="password" class="inputs" placeholder="Шифра" required />
										<button class="btn btn-light btn-lg btn-block" type="submit" name="login">Пошаљи захтев</button>
									</form>
									<p class="mt-4">Корисник? <a href="./login.php">Пријави се!</a></p>
									<?php if ($errorMessage): ?>
										<p class="error">
											<?php echo $errorMessage; ?>
										</p>
									<?php endif; ?>
									<?php if ($success): ?>
										<p class="success">Успешна пријава!</p>
									<?php endif; ?>
							</div>
						</div>
					</div>
			</div>
	</section>
	</div>
	</div>
	</section>
</body>

</html>