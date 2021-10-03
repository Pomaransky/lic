<?php
		session_start();
	require_once "config_g.php";
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true) || isset(($_SESSION['access_token'])))
	{
		header('Location: zalogowano.php');
		exit();
	}
	$permissions = ['email'];
	$loginURLG = $gClient->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel</title>
  <link rel="shortcut icon" href="images/hotel.png">
  <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="styles/index.css?v=<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
</head>

<body>
  <nav>
    <button class="hamburger">
      <span class="hamburger__box">
        <span class="hamburger__inner"></span>
      </span>
    </button>
    <div class="navigation">
      <ul class="navigation__list">
        <li class="navigation__item"><a href="index.php">Strona główna</a></li>
        <li class="navigation__item"><a href="oferta.php">Oferta</a></li>
        <li class="navigation__item"><a href="galeria.php">Galeria</a></li>
        <li class="navigation__item"><a href="rezerwacja.php">Zarezerwuj pokój</a></li>
        <?php 
        if(isset($_SESSION['admin'])){
          echo '<li class="navigation__item"><a href="panel.php">Panel administracyjny</a></li>';
        }
        ?>
      </ul>
    </div>
  </nav>
  <div class="container color-white text-center font-size-small">
    <div class="login-section">
      <div class="image"></div>
      <form action="logowanie.php" method="POST">
        Login:<input type="text" name="login" /><br /><br />
        Hasło:<input type="password" name="haslo" /><br /><br />
        <button type="submit" class="btn-green">Zaloguj!</button><br />
      </form>
      <?php
	      if(isset($_SESSION['blad']))	echo $_SESSION['blad'].'</br>';
	      unset($_SESSION['blad']);
      ?>
      <div class="rejestracja">
        Nie masz konta?
        <button class="btn-blue"><a href="rejestracja.php">Zarejestruj się!</a></button>
      </div>
      <button type="button" onclick="window.location = '<?php echo $loginURLG ?>';" class="btn-red">Zaloguj za pomocą
        Google</button>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>