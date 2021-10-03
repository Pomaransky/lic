<?php
		session_start();
    
    require_once "polaczenie.php";
    mysqli_report(MYSQLI_REPORT_STRICT);

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($polaczenie->connect_errno!=0) {
		echo "Error: ".$polaczenie->connect_errno;
	} else{
    if(isset($_SESSION['zalogowany'])){
      $iduzyt=$_SESSION['id'];
    $wynik=$polaczenie->query("SELECT admin FROM uzytkownicy WHERE id='$iduzyt'");
    if(!$wynik) throw new Exception($polaczenie->error);
    $wiersz=$wynik->fetch_assoc();
    $admin=$wiersz['admin'];
    if($admin == 1){
      $_SESSION['admin'] = true;
    }
    }
    }
    $wynik = $polaczenie->query("SELECT * FROM pokoje");
    if(!$wynik) throw new Exception($polaczenie->error);
    $i=0;
    $niedostepne = [];
    while($wiersz = $wynik->fetch_assoc()){
        $tablica[$i] = $wiersz['ilosc_miejsc'];
        if($wiersz['id_r'] != NULL){
          array_push($niedostepne,$wiersz['ilosc_miejsc']);
        }
        $i++;
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
  <link rel="stylesheet" href="styles/oferta.css?v=<?php echo time(); ?>">
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
      if(isset($_SESSION['admin'])){
        echo '<a href="dodaj_oferte.php"><button class="btn-blue edit">Dodaj ofertę</button></a>';
      }
      ?>
      <div class="header">Nasze pokoje:</div>
      <div class="pokoje">
        <?php
      $unique = array_count_values($tablica);
      foreach($unique as $ilosc_osob => $ilosc){
        $wynik = $polaczenie->query("SELECT cena_za_noc FROM pokoje WHERE ilosc_miejsc = $ilosc_osob");
        if(!$wynik) throw new Exception($polaczenie->error);
        $wiersz=$wynik->fetch_assoc();
        $cena = $wiersz['cena_za_noc'];
        foreach($niedostepne as $dousuniecia){
          if($ilosc_osob == $dousuniecia){
            $ilosc -= 1;
          }
        }
        if($ilosc <= 0){
          echo '<div class="pokoj-niedostepny">';
        } else {
          echo '<div class="pokoj">';
        }
        echo '<div class="title">Pokój '.$ilosc_osob.' osobowy</div>
              <div class="cena" id="edytuj_cene'.$ilosc_osob.'">Cena: '.$cena.' zł</div>
              <div class="dostepnosc"> Dostępnych w tym momencie: '.$ilosc.'</div>';
              if($ilosc <= 0 ){
                $wynik = $polaczenie->query("SELECT zajety_do FROM pokoje WHERE ilosc_miejsc='$ilosc_osob' ORDER BY zajety_do ASC LIMIT 1");
                if(!$wynik) throw new Exception($polaczenie->error);
                $wiersz = $wynik->fetch_assoc();
                $zajety_do = $wiersz['zajety_do'];
                echo '<div class="error">
                      Dostępny od: '.date('Y-m-d',strtotime($zajety_do.'+ 1 day')).'</div>';
              } else {
                echo '<div class="sukces">
                      Dostępny od zaraz
                      </div>';
              }
              if(isset($_SESSION['admin'])){
                echo '<form method="POST">
                      <div>Nowa cena:</div>
                      <input type="text" name="nowa_cena" />
                      <button type="submit" name="edytuj_cene'.$ilosc_osob.'" class="btn-green edit-price">Edytuj cenę pokoju</button>
                      </form>';
                }
                if(isset($_POST['edytuj_cene'.$ilosc_osob])){
                  $nowaCena=$_POST['nowa_cena'];
                  $polaczenie->query("UPDATE pokoje SET cena_za_noc='$nowaCena' WHERE ilosc_miejsc='$ilosc_osob'");
                  echo "<script type='text/javascript'>
                          document.getElementById('edytuj_cene".$ilosc_osob."').innerText = 'Cena: ".$nowaCena." zł';
                        </script>";
                }
        echo  '</div>';
      }
      ?>
      </div>
      <div class="header">Nasze oferty:</div>
      <div class="oferty">
        <?php
          $wynik = $polaczenie->query("SELECT * FROM oferty");
          if(!$wynik) throw new Exception($polaczenie->error);
          while($wiersz=$wynik->fetch_assoc()){
            echo '<div class="oferta" id="'.$wiersz['id_oferty'].'">
                  <div class="title">'.$wiersz['nazwa_oferty'].'</div>
                  <div class="cena">Dodatkowa opłata: '.$wiersz['dodatkowa_cena'].' zł</div>
                  <div class="opis"> Opis: <div class="tresc-opisu">'.$wiersz['opis_oferty'].'</div></div>';
                  if(isset($_SESSION['admin'])){
                    echo '<form method="POST">
                          <button type="submit" name="usun'.$wiersz['id_oferty'].'" class="btn-red delete-offer">Usuń</button>
                          </form>';
                          $id_oferty = $wiersz['id_oferty'];
                          if(isset($_POST['usun'.$wiersz['id_oferty']])){
                            $polaczenie->query("DELETE FROM oferty WHERE id_oferty='$id_oferty'");
                            echo "<script type='text/javascript'>
                                    document.getElementById('".$id_oferty."').remove();
                                  </script>";
                          }
                  }
                  echo '</div>';
          }
      ?>
      </div>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>