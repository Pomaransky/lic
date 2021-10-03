<?php
		session_start();

        if (!isset($_SESSION['admin']))
        {
            header('Location: index.php');
            exit();
        }
        if(isset($_POST['nazwa_oferty'])){
          $jestOK=true;
          $nazwa_oferty=$_POST['nazwa_oferty'];
          if(strlen($nazwa_oferty)<3||strlen($nazwa_oferty)>20){
              $jestOK=false;
              $_SESSION['error_nazwa_oferty']="Nazwa oferty musi mieć od 3 do 20 znaków!";
          }
          $cena_oferty=$_POST['cena_oferty'];
          if(is_numeric($cena_oferty)==0){
            $jestOK=false;
            $_SESSION['error_cena_oferty']="Cena oferty może zawierać cyfry.";
          }
          $opis_oferty=$_POST['opis_oferty'];
          if(strlen($opis_oferty)<3||strlen($opis_oferty)>255){
            $jestOK=false;
            $_SESSION['error_opis_oferty']="Opis oferty musi mieć od 3 do 255 znaków!";
          }
          require_once "polaczenie.php";
          mysqli_report(MYSQLI_REPORT_STRICT);
          try{
            $polaczenie = new mysqli($host, $db_user,$db_password, $db_name);
            if ($polaczenie->connect_errno!=0) {
              throw new Exception(mysqli_connect_errno());
          }else{
            $wynik=$polaczenie->query("SELECT * FROM oferty WHERE nazwa_oferty='$nazwa_oferty'");
            if(!$wynik) throw new Exception($polaczenie->error);
            $ile_takich_samych_nazw=$wynik->num_rows;
            if($ile_takich_samych_nazw>0){
              $jestOK=false;
              $_SESSION['error_nazwa_oferty']="Podana nazwa oferty już istnieje!";
            }
            if($jestOK==true){
              if($polaczenie->query("INSERT INTO oferty VALUES (NULL, '$nazwa_oferty', '$opis_oferty', '$cena_oferty')")){
                echo "<script type='text/javascript'>alert('Oferta została dodana');</script>";
              }else throw new Exception($polaczenie->error);
          }
          $polaczenie->close();
          }
        }catch(Exception $error){
          echo "Błąd serwera!";
          echo "ERROR:".$error;
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
  <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="styles/dodaj_oferte.css?v=<?php echo time(); ?>">
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
      <form method="POST">
        <div class="item">Nazwa oferty:</div>
        <input type="text" name="nazwa_oferty" />
        <?php
          if(isset($_SESSION['error_nazwa_oferty'])){
            echo '<div class="error">'.$_SESSION['error_nazwa_oferty'].'</div>';
            unset($_SESSION['error_nazwa_oferty']);
          }
        ?>
        <div class="item">Cena oferty:</div>
        <input type="text" name="cena_oferty" />
        <?php
          if(isset($_SESSION['error_cena_oferty'])){
            echo '<div class="error">'.$_SESSION['error_cena_oferty'].'</div>';
            unset($_SESSION['error_cena_oferty']);
          }
        ?>
        <div class="item">Opis oferty:</div>
        <input type="text" name="opis_oferty" />
        <?php
          if(isset($_SESSION['error_opis_oferty'])){
            echo '<div class="error">'.$_SESSION['error_opis_oferty'].'</div>';
            unset($_SESSION['error_opis_oferty']);
          }
        ?>
        <div class="item"><button type="submit" class="btn-green">Dodaj ofertę</button></div>
      </form>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>