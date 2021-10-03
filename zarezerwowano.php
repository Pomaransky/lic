<?php
	session_start();
	if (!isset($_SESSION['zarezerwowano']))
	{
		header('Location: index.php');
		exit();
    }
    else{
        unset($_SESSION['zarezerwowano']);
	}
	require_once "polaczenie.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else{
    $iduzyt=$_SESSION['id'];
		$wynik=$polaczenie->query("SELECT * FROM rezerwacje WHERE id='$iduzyt'");
		if(!$wynik) throw new Exception($polaczenie->error);
		$wiersz=$wynik->fetch_assoc();
		$od=$wiersz['od'];
		$do=$wiersz['do'];
    $ileosob=$wiersz['ile_osob'];
    $idr=$wiersz['id_r'];
    $oferta=$wiersz['oferta'];
		$wynik=$polaczenie->query("SELECT UNIX_TIMESTAMP('$do')-UNIX_TIMESTAMP('$od') AS roznica FROM rezerwacje WHERE id='$iduzyt'");
		$wiersz=$wynik->fetch_assoc();
    $roznica = $wiersz['roznica'];
    $ileosob = $_SESSION['ile_osob'];
    $polaczenie->query("UPDATE pokoje SET id_r='$idr',zajety_do = '$do' WHERE zajety_do IS NOT NULL AND zajety_do < '$od' AND ilosc_miejsc = '$ileosob' ORDER BY zajety_do ASC LIMIT 1");
    $wynik=$polaczenie->query("SELECT cena_za_noc FROM pokoje WHERE id_r='$idr'");
    $wiersz=$wynik->fetch_assoc();
    $cena_noc = $wiersz['cena_za_noc'];
    $wynik=$polaczenie->query("SELECT dodatkowa_cena FROM oferty WHERE nazwa_oferty='$oferta'");
    $wiersz=$wynik->fetch_assoc();
    $dod_cen=$wiersz['dodatkowa_cena'];
		$cena=($roznica/86400*$ileosob*$cena_noc)+($roznica/86400*$ileosob*$dod_cen);
		$polaczenie->query("UPDATE rezerwacje SET do_zaplaty='$cena' WHERE id='$iduzyt'");
		$wiersz=$wynik->fetch_assoc();
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
  <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="styles/rezerwacja.css?v=<?php echo time(); ?>">
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
  <div class="container color-white text-center font-size-small">
    <div class="content">
      <div class="sukces">Zarezerwowałeś miejsca w pokoju!</div></br>
      <?php 
    echo '<div class="item">Do zapłaty: <b>'.$cena.'</b> PLN. </div>';
    echo '<div class="item">Id rezerwacji: <b>'.$idr.'</b> </div>';
  ?>
      W celu opłacenia miejsc w pokoju prosimy dokonać wpłaty na konto:
      <b>1111 2222 3333 4444 5555 6666</b>
      <div class="item">W tytule przelewu prosimy o wpisanie
        <b>Id rezerwacji</b>
      </div>
      <div class="error"><b>Uwaga!!!</b></div>
      Wpłatę należy wykonać co najmniej 7 dni przed dniem przybycia do hotelu, w innym wypadku <b>rezerwacja zostanie
        anulowana</b> przez pracownika hotelu.</br>
    </div>
  </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>