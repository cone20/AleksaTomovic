<?php
session_start();

// ako je korisnik ulogovan onda ne sme pristupiti stranici za registraciju
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
	header('Location: ./index.php');
	exit();
}

include('./db.php');

if (isset($_POST['register'])) {
	//ime, prezime, email, password
	$email = $_POST['email'];
	$ime = $_POST['name'];
	$prezime = $_POST['lastname'];
	$password = $_POST['password'];

	$done = false;
	$errors = false;
	$errorMessage = '';

	// IZABERI SVE IZ korisnici GDE JE email = $email
	$sql = "SELECT * FROM korisnici WHERE email = '$email'";

	$res = $conn->query($sql);

	// ukoliko postoji email onda korisnik ne sme da se registruje, zato se izbacuje greska
	if ($res->num_rows == 1) {
		$errorMessage = "Е-маил већ постоји <br />!";
		$errors = true;
	}
	
	// ukoliko je sifra kraca od 3 karaktera izbacuje se greska
	if (strlen($password) < 3) {
		$errorMessage = "Лозинка не сме имати мање од 3 карактера!";
		$errors = true;
	}

	$password = md5($password);

	// ukoliko nema $errors, onda moze da se nastavi registracija
	if (!$errors) {
		// UBACU U korisnici(...) VREDNOSTI (...)
		$sql = "INSERT INTO korisnici(email, lozinka, ime, prezime) 
		VALUES ('$email', '$password', '$ime', '$prezime');";

		// ukoliko je query izvrsen, ispisi poruku (pogledaj <?php if... dole) 
		if ($conn->query($sql) === TRUE) {
			$done = true;
			header("Refresh: 2 ; URL=./login.php");
		}
	}

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./styles.css">
	<link rel="stylesheet" href="./main.css">
	<title>Регистрација</title>
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
									<h3 class="display-6 mb-5 fw-bolder">Направи свој налог</h3>
									<form method="post" class="d-flex flex-column gap-3">
										<input type="email" name="email" class="inputs" placeholder="Е-адреса" required />
										<input type="password" name="password" class="inputs" placeholder="Шифра" required />
										<input type="text" name="name" class="inputs" placeholder="Име" required />
										<input type="text" name="lastname" class="inputs" placeholder="Презиме" required />
										<button class="btn btn-light btn-lg btn-block" type="submit" name="register">Пошаљи захтев</button>
									</form>
									<p class="mt-4">Имаш налог? <a href="./login.php">Улогуј се!</a></p>
									<?php if ($errors): ?>
										<p class="error">
											<?php echo $errorMessage; ?>
										</p>
									<?php endif; ?>
									<?php if ($done): ?>
										<p class="success">Успешна регистрација!</p>
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