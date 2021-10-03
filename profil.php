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
    require_once "polaczenie.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else{
    if(!$tokengoogle){
    $iduzyt=$_SESSION['id'];
    $wynik=$polaczenie->query("SELECT * FROM uzytkownicy WHERE id='$iduzyt'");
    if(!$wynik) throw new Exception($polaczenie->error);
    $wiersz=$wynik->fetch_assoc();
    $nazwa=$wiersz['uzytkownik'];
    $email=$wiersz['email'];
    $admin=$wiersz['admin'];
    strval($admin);
    if($admin == 0){
      $admin = "NIE";
    } else {
      $admin = "TAK";
    }
    if(isset($_POST['haslo'])){
    $jestOK=true;
    $haslo=$_POST['haslo'];
    $haslo1=$_POST['haslo1'];
    $haslo2=$_POST['haslo2'];
    if(password_verify($haslo,$wiersz['haslo'])){
    if(strlen($haslo1)<8||strlen($haslo1)>20){
        $jestOK=false;
        $_SESSION['error_haslo1']="Hasło musi mieć od 8 do 20 znaków!";
    }
    if($haslo1!=$haslo2){
        $jestOK=false;
        $_SESSION['error_haslo2']="Hasła muszą być takie same!";
    }
    $haslo_hash=password_hash($haslo1,PASSWORD_DEFAULT);
    }else{
    $jestOK=false;
    $_SESSION['error_haslo']="Hasło niepoprawne!";
    }
    if($jestOK){
        $polaczenie->query("UPDATE uzytkownicy SET haslo='$haslo_hash' WHERE id='$iduzyt'");
        $zmieniono=true;
    }
}
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
  <link rel="stylesheet" href="styles/profil.css?v=<?php echo time(); ?>">
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
      <?php
if(!$tokengoogle){
echo '<div class="title"><b>DANE KONTA: </b></div>';
echo '<div class="item"><b>Nazwa użytkownika: </b>'.$nazwa.'</div>';
echo '<div class="item"><b>E-mail: </b>'.$email.'</div>';
echo '<div class="item"><b>Admin: </b>'.$admin.'</div>';
echo '<form method="POST">';
echo '<div class="item">Stare hasło:</div><input type="password" name="haslo"/></br>';

if(isset($_SESSION['error_haslo'])){
    echo '<div class="error">'.$_SESSION['error_haslo'].'</div>';
    unset($_SESSION['error_haslo']);
}

echo '<div class="item">Nowe hasło:</div><input type="password" name="haslo1"/></br>';

if(isset($_SESSION['error_haslo1'])){
    echo '<div class="error">'.$_SESSION['error_haslo1'].'</div>';
    unset($_SESSION['error_haslo1']);
}

echo '<div class="item">Powtórz nowe hasło:</div><input type="password" name="haslo2"/></br></br>';

if(isset($_SESSION['error_haslo2'])){
    echo '<div class="error">'.$_SESSION['error_haslo2'].'</div>';
    unset($_SESSION['error_haslo2']);
}

echo'<button type="submit" class="btn-green">Zmień hasło!</button></br></br>';
echo '</form>';

if(isset($zmieniono)&&$zmieniono==true){
    echo '<div class="sukces">Hasło zmienione!!!</div>'.'</br>';
}
}
if($tokengoogle){
    echo '<b>Dane konta</b></br>';
    echo '<b>Imię:</b>'.$_SESSION['givenName'].'</br>';
    echo '<b>Nazwisko:</b>'.$_SESSION['familyName'].'</br>';
    echo '<b>E-mail:</b>'.$_SESSION['email'].'</br>';
}
?>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>