<?php
		session_start();
    if (!isset($_SESSION['admin']))
    {
      header('Location: index.php');
      exit();
    }
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
  <link rel="stylesheet" href="styles/main.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="styles/panel.css?v=<?php echo time(); ?>">
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
    <table>
    <tr class="naglowek">
    <td>Id rezerwacji</td>
    <td>Imię</td>
    <td>Nazwisko</td>
    <td>Do zapłaty</td>
    <td>Opłacono</td>
    <td>Akcje</td>
    </tr>
    <?php 
      $wynik = $polaczenie->query("SELECT * FROM rezerwacje");
      if(!$wynik) throw new Exception($polaczenie->error);
      while($wiersz=$wynik->fetch_assoc()){
        $idr = $wiersz['id_r'];
        $do_zaplaty = $wiersz['do_zaplaty'];
        if($wiersz['czy_oplacono']){
          $czy_oplacono = "Tak";
        } else {
          $czy_oplacono = "Nie";
        }
        $imie = $wiersz['imie'];
        $nazwisko = $wiersz['nazwisko'];
        echo '<tr>';
        echo '<td>'.$idr.'</td>';
        echo '<td>'.$imie.'</td>';
        echo '<td>'.$nazwisko.'</td>';
        echo '<td>'.$do_zaplaty.' PLN</td>';
        echo '<td id="'.$wiersz['id_r'].'">'.$czy_oplacono.'</td>';
        echo '<td>
              <form method="POST">
              <button type="submit" name="oplac'.$wiersz['id_r'].'" class="btn-green">Wniesiono opłatę</button>
              </form>
              </td>';
              $id_rez = $wiersz['id_r'];
              $tak = "Tak";
              if(isset($_POST['oplac'.$wiersz['id_r']])){
                $polaczenie->query("UPDATE rezerwacje SET czy_oplacono=true WHERE id_r='$id_rez'");
                echo "<script type='text/javascript'>
                        document.getElementById('".$idr."').innerText = '".$tak."';
                      </script>";
              }
        echo '</tr>';
      }
    ?>
    </table>
    <table>
    <tr class="naglowek">
    <td>Numer pokoju</td>
    <td>Ilość miejsc</td>
    <td>Cena za noc</td>
    <td>Id rezerwacji</td>
    <td>Zajęty do</td>
    <td>Dodatkowe informacje</td>
    </tr>
    <?php 
      $wynik = $polaczenie->query("SELECT * FROM pokoje");
      if(!$wynik) throw new Exception($polaczenie->error);
      while($wiersz=$wynik->fetch_assoc()){
        $nr_pokoju = $wiersz['nr_pokoju'];
        $ilosc_miejsc = $wiersz['ilosc_miejsc'];
        $cena_za_noc = $wiersz['cena_za_noc'];
        $id_r = $wiersz['id_r'];
        if($id_r == NULL){
          $id_r = "---";
        }
        $zajety_do = $wiersz['zajety_do'];
        if($zajety_do == NULL){
          $zajety_do = "---";
        }
        $dodatkowe_info = $wiersz['dodatkowe_info'];
        if($dodatkowe_info == NULL){
          $dodatkowe_info = "---";
        }
        echo '<tr class="tr-with-borders">';
        echo '<td id="'.$nr_pokoju.'">'.$nr_pokoju.'</td>';
        echo '<form method="POST">';
        echo '<td><input name="nowa_ilosc_miejsc'.$nr_pokoju.'" id="input_nowa_ilosc_miejsc'.$nr_pokoju.'" value="'.$ilosc_miejsc.'"/></td>';
        echo '<td>'.$cena_za_noc.' PLN</td>';
        echo '<td>'.$id_r.'</td>';
        echo '<td><input type="date" name="nowa_data'.$nr_pokoju.'" id="input_nowa_data'.$nr_pokoju.'" value="'.$zajety_do.'"/></td>';
        echo '<td class="add-info"><textarea name="info_do_bazy'.$nr_pokoju.'" id="input_info'.$nr_pokoju.'" value="'.$dodatkowe_info.'">'.$dodatkowe_info.'</textarea>';
        echo '<button class="btn-green" type="submit" name="info'.$nr_pokoju.'">Zapisz</button></td>';
        echo '</form>';
        if(isset($_POST['info'.$nr_pokoju])){
          $info_do_bazy = $_POST['info_do_bazy'.$nr_pokoju];
          $nowa_ilosc_miejsc = $_POST['nowa_ilosc_miejsc'.$nr_pokoju];
          $nowa_data = $_POST['nowa_data'.$nr_pokoju];
          $polaczenie->query("UPDATE pokoje SET dodatkowe_info='$info_do_bazy',ilosc_miejsc='$nowa_ilosc_miejsc',zajety_do='$nowa_data' WHERE nr_pokoju='$nr_pokoju'");
          echo "<script type='text/javascript'>
                  document.getElementById('input_info".$nr_pokoju."').innerText= '".$info_do_bazy."';
                  document.getElementById('input_nowa_ilosc_miejsc".$nr_pokoju."').setAttribute('value','".$nowa_ilosc_miejsc."');
                  document.getElementById('input_nowa_data".$nr_pokoju."').setAttribute('value','".$nowa_data."');
                </script>";
                
        }
        echo '</tr>';
      }
    ?>
    </table>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>