<?php
		session_start();

	if (!isset($_SESSION['zalogowany']) && (!isset($_SESSION['access_token'])))
	{
		header('Location: index.php');
		exit();
	}
	$tokengoogle=true;
	if(!isset($_SESSION['logowanie_google'])){
		$tokengoogle=false;
	}
		if($tokengoogle){
			require_once "polaczenie.php";
	
			$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
		
			if ($polaczenie->connect_errno!=0)
			{
			echo "Error: ".$polaczenie->connect_errno;
			}
			else
			{
				if($tokengoogle){
					$dodaj=true;
								$uzyt=$_SESSION['id_g'];
								$em=$_SESSION['email'];
								$wynik=$polaczenie->query("SELECT * FROM uzytkownicy WHERE email='$em'");
								$ile_takich=$wynik->num_rows;
								if($ile_takich>0){
									$dodaj=false;
								}
								else{
								$polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL,'$uzyt',NULL,'$em')");
								}
							}
							$em=$_SESSION['email'];
							$wynik=$polaczenie->query("SELECT * FROM uzytkownicy WHERE email='$em'");
							$wiersz=$wynik->fetch_assoc();
							$_SESSION['id']=$wiersz['id'];
				}
			}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel</title>
  <link rel="shortcut icon" href="images/hotel.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Dela+Gothic+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="styles/zalogowano.css?v=<?php echo time(); ?>">
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
      <div class="if-logged">
        <?php
	      if(isset($_SESSION['logowanie_google'])){
		      echo 'Witaj, '.$_SESSION['givenName'].'!</br></br> <div class="d-flex"><a href="profil.php"><button class="btn-green">Profil!</button></a>  <a href="wyloguj.php"><button class="btn-red ms-2">Wyloguj się!</button></a></div>';
	      }
	      else{
		      if(isset($_SESSION['uzytkownik'])){
		        echo 'Witaj, '.$_SESSION['uzytkownik'].'!</br></br> <div class="d-flex"><a href="profil.php"><button class="btn-green">Profil!</button></a>  <a href="wyloguj.php"><button class="btn-red ms-2">Wyloguj się!</button></a></div>';
	        }
	      }
    ?>
      </div>
    </div>
  </nav>
  <div class="container color-white font-size-small">
    <div class="content">
      <div class="title">Witamy w hotelu AKM!</div>
      <div class="image"></div>
      <p>Morandówka znajduje się na Starym Mieście w Zamościu, 100 metrów od katedry, 160 metrów od ratusza
      i 1 km od synagogi. Obiekt mieści się w budynku z XVI wieku i oferuje wspólny salon oraz bar.
      Wszystkie pokoje dysponują łazienką i są wyposażone w biurko oraz telewizor z płaskim ekranem.
      Z wybranych pokoi roztacza się widok na miasto.
      Codziennie rano w obiekcie oferowane jest śniadanie włoskie
      i w formie bufetu. Tutejsza restauracja zaprasza na potrawy kuchni włoskiej.
      W wolnej chwili można pograć w rzutki.
      Morandówka jest zlokalizowany zaledwie kilka kroków od Galerii Zamojskiej
      i 300 metrów od pałacu Zamoyskich. Odległość od lotniska Lublin-Świdnik wynosi 88 km.

      Mówimy w Twoim języku!</br></p>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>