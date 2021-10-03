<?php
        session_start();
        if (!isset($_SESSION['zalogowany']) && (!isset($_SESSION['access_token'])))
        {
            header('Location: index.php');
            exit();
        }
        $aktywna_rezerwacja=false;
        if(isset($_POST['rezerwuj'])){
          $jestOKK=true;
            $imie=$_POST['imie'];
            if(strlen($imie)>12 || strlen($imie)<2){
                $jestOKK=false;
                echo '<script>
                console.log('.(strlen($imie) > 2).');</script>';
                $_SESSION['error_imie']="Wprowadź prawidłowe imię!";
            }
            $nazwisko=$_POST['nazwisko'];
            if(strlen($nazwisko)>12 || strlen($nazwisko)<2){
              $jestOKK=false;
              $_SESSION['error_nazwisko']="Wprowadź prawidłowe nazwisko!";
            }
            $stdate = $_POST['stdate'];
            $enddate = $_POST['enddate'];
            
            $ileosob=$_POST['ileosob'];       
            $oferta=$_POST['oferta'];
            $iduzyt=$_SESSION['id'];
        require_once "polaczenie.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $polaczenie = new mysqli($host, $db_user,$db_password, $db_name);
            if ($polaczenie->connect_errno!=0)
            {
                throw new Exception(mysqli_connect_errno());
            }
            else{
                    $wynik=$polaczenie->query("SELECT * FROM pokoje WHERE ilosc_miejsc='$ileosob' AND id_r IS NULL");
                    if(!$wynik) throw new Exception($polaczenie->error);
                    $ilu2=$wynik->num_rows;
                    if($ilu2<=0){
                      $i=0;
                      $do_tab=[];
                      $wynik=$polaczenie->query("SELECT do FROM rezerwacje WHERE ile_osob='$ileosob'");
                      if(!$wynik) throw new Exception($polaczenie->error);
                      while($wiersz = $wynik->fetch_assoc()){
                        $do_tab[$i] = $wiersz['do'];
                        $i++;
                      }
                      sort($do_tab);
                      if($do_tab[0] >= $od){
                        $jestOKK=false;
                        $_SESSION['error_brakwolnychpokoi']="Przepraszamy w hotelu nie ma wolnych pokoi z podaną liczbą miejsc w podanych datach";
                      }
                    }
                    $wynik=$polaczenie->query("SELECT * FROM rezerwacje WHERE id='$iduzyt'");
                    if(!$wynik) throw new Exception($polaczenie->error);
                    $ilu=$wynik->num_rows;
                    if($ilu>0){
                        $jestOKK=false;
                        $_SESSION['error_aktywnarezerwacja']="Przepraszamy masz już aktywną rezerwację.";
                    }
                    if($jestOKK==true){
                    if($polaczenie->query("INSERT INTO rezerwacje VALUES (NULL,'$stdate' , '$enddate','$oferta','$ileosob','$iduzyt',NULL,false,'$imie','$nazwisko')")){
                        $_SESSION['ile_osob'] = $ileosob;
                        $wynik=$polaczenie->query("SELECT * FROM rezerwacje WHERE id='$iduzyt'");
                        if(!$wynik) throw new Exception($polaczenie->error);
                        $wiersz=$wynik->fetch_assoc();
                        $idr=$wiersz['id_r'];
                    if($polaczenie->query("UPDATE pokoje SET id_r='$idr',zajety_do='$enddate' WHERE ilosc_miejsc='$ileosob' AND id_r IS NULL LIMIT 1")){
                    $_SESSION['zarezerwowano']=true;
                    header('Location:zarezerwowano.php');
                    }
                    else throw new Exception($polaczenie->error);
                    }
                    else throw new Exception($polaczenie->error);
                    $polaczenie->close();
                    }
                }
            }
            catch(Exception $error){
            echo "Błąd serwera!";
            echo "ERROR:".$error;
            }
        }
        $iduzyt=$_SESSION['id'];
        require_once "polaczenie.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $polaczenie = new mysqli($host, $db_user,$db_password, $db_name);
            if ($polaczenie->connect_errno!=0)
            {
                throw new Exception(mysqli_connect_errno());
            }
            else{
                    $wynik=$polaczenie->query("SELECT * FROM rezerwacje WHERE id='$iduzyt'");
                    if(!$wynik) throw new Exception($polaczenie->error);
                    $ilu=$wynik->num_rows;
                    if($ilu>0){
                        $aktywna_rezerwacja=true;
                        $wynik=$polaczenie->query("SELECT * FROM rezerwacje WHERE id='$iduzyt'");
                        $wiersz=$wynik->fetch_assoc();
                        $idr=$wiersz['id_r'];
                        $imie=$wiersz['imie'];
                        $nazwisko=$wiersz['nazwisko'];
                        $od=$wiersz['od'];
                        $do=$wiersz['do'];
                        $cena=$wiersz['do_zaplaty'];
                        $ileosob=$wiersz['ile_osob'];
                        $oferta=$wiersz['oferta'];
                        $czy_oplacono = $wiersz['czy_oplacono'];
                        if(isset($_POST['usun'])){
                          $wynik=$polaczenie->query("SELECT * FROM rezerwacje WHERE ile_osob='$ileosob'");
                          if(!$wynik) throw new Exception($polaczenie->error);
                          $ilewynikow=$wynik -> num_rows;
                          echo '<script> console.log('.$ilewynikow.');</script>';
                          if($ilewynikow > 2){
                            $wynik= $polaczenie->query("SELECT do,id_r FROM rezerwacje ORDER BY do ASC LIMIT 1");
                            $wiersz = $wynik -> fetch_assoc();
                            $nowedo = $wiersz['do'];
                            $noweidr = $wiersz['id_r'];
                            $polaczenie->query("UPDATE pokoje SET zajety_do='$nowedo',id_r='$noweidr' WHERE zajety_do = '$do' AND zajety_do IS NOT NULL LIMIT 1" );
                          } else{
                            $polaczenie->query("UPDATE pokoje SET zajety_do=NULL WHERE id_r='$idr'" );
                          }
                          $polaczenie->query("DELETE FROM rezerwacje WHERE id_r='$idr'");
                            $aktywna_rezerwacja=false;
                    }
                }
            }
        }
            catch(Exception $error){
                echo "Błąd serwera!";
                echo "ERROR:".$error;
                }
                // $ileosobb = $_POST['ileosob'];
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
  <link rel="stylesheet" href="styles/kalendarz.css?v=<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
            }

            #my-calendar {
                max-width: 600px;
            }
        </style>
       
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
      Imię:</br> <input type="text" name="imie" placeholder="Imię"></br></br>
        <?php
if(isset($_SESSION['error_imie'])){
    echo '<div class="error">'.$_SESSION['error_imie'].'</div>';
    unset($_SESSION['error_imie']);
}
?>
        Nazwisko:</br> <input type="text" name="nazwisko" placeholder="Nazwisko" ></br></br>
        <?php
if(isset($_SESSION['error_nazwisko'])){
    echo '<div class="error">'.$_SESSION['error_nazwisko'].'</div>';
    unset($_SESSION['error_nazwisko']);
}
?>
      Pokój
        <select name="ileosob" id="formIleOs">
          <?php
              $miejsca = [];
              $wynik = $polaczenie->query("SELECT ilosc_miejsc FROM pokoje");
              if(!$wynik) throw new Exception($polaczenie->error);
              while($wiersz=$wynik->fetch_assoc()){
                if(!in_array($wiersz['ilosc_miejsc'],$miejsca)){
                  array_push($miejsca,$wiersz['ilosc_miejsc']);
                }
              }
              for($i=0; $i < count($miejsca); $i++){
                echo '<option value="'.$miejsca[$i].'">'.$miejsca[$i].'</option>';
              }
          ?>
        </select>-osobowy</br></br>
        
          
        
        <button type="submit" name="checkDates" class="btn-blue" id="checkDates">Sprawdź dostępne daty!</button></br></br>
        <select name="oferta">
          <?php
              $wynik = $polaczenie->query("SELECT nazwa_oferty FROM oferty");
              if(!$wynik) throw new Exception($polaczenie->error);
              while($wiersz=$wynik->fetch_assoc()){
                echo '<option>'.$wiersz['nazwa_oferty'].'</option>';
              }
          ?>
        </select></br></br>
        <?php
if(isset($_SESSION['error_brakwolnychpokoi'])){
    echo '<div class="error">'.$_SESSION['error_brakwolnychpokoi'].'</div>';
    unset($_SESSION['error_brakwolnychpokoi']);
}
if(isset($_SESSION['error_aktywnarezerwacja'])){
     echo '<div class="error">'.$_SESSION['error_aktywnarezerwacja'].'</div>';
     unset($_SESSION['error_aktywnarezerwacja']);
}
?>

    <div id="my-calendar"></div>
    <script src="module/moment.js" ></script>
    <script src="js/kalendarz.js"></script>
    <script>
        const formIleOs = document.querySelector("#formIleOs");
        let ileOs = parseInt(formIleOs.value);
        formIleOs.addEventListener("click",()=>{
          ileOs = parseInt(formIleOs.value);
          formIleOs.setAttribute('value',ileOs);
        });
</script>
    <?php 
      if(isset($_POST['checkDates'])){
        $ileosob = $_POST['ileosob'];
        $do_z_pokoi=$polaczenie->query("SELECT zajety_do FROM pokoje WHERE ilosc_miejsc='$ileosob' ORDER BY zajety_do LIMIT 1");
        $wiersz = $do_z_pokoi->fetch_assoc();
        $zajety_do = $wiersz['zajety_do'];
        echo '<script>
        for(let i=0;i<formIleOs.children.length;i++){
                if(formIleOs.children[i].getAttribute("value") == '.$ileosob.'){
                  formIleOs.setAttribute("value",'.$ileosob.');
                  formIleOs.children[i].setAttribute("selected","true");
                }
              }
        </script>';
        $wynikololo=$polaczenie->query("SELECT * FROM rezerwacje WHERE ile_osob='$ileosob'");
        $tab1 = array();
        $tab2 = array();
            while($wiersz = $wynikololo->fetch_assoc()){
                $od=$wiersz['od'];
                // $do=$wiersz['do'];
                while($od<=$zajety_do){
                    $tab2[]=$od;
                    $od=date( 'Y-m-d', strtotime( $od.' +1 day' ) );
                }
            }

            for ($i = 0; $i < count($tab2) ; $i++){
                $suma = 0;
                for ($j = 0; $j < count($tab2) ; $j++){
                if ($tab2[$i] == $tab2[$j]) $suma++;
                }
                if($suma>1){
                    $tab1[]=$tab2[$i];
                }
                
            }
            $blacklist = array_unique($tab1);
            $datyDoSkreslenia = "";
            for($i=0;$i < count($blacklist);$i++){
                $datyDoSkreslenia .= '"'.strval($blacklist[$i]).'",';
            }
            
       ?>
     
       <?php
         echo '<script>
         const my_calendar = new TavoCalendar("#my-calendar", {
             date:["'.date("Y-m-d").'"],
             blacklist:['.$datyDoSkreslenia.'],
             format: "YYYY-MM-DD",
             locale: "pl",
             range_select: true
         });
         </script>';
      } else {
        $ileosob = 1;
        echo '<script>
        for(let i=0;i<formIleOs.children.length;i++){
                if(formIleOs.children[i].getAttribute("value") == '.$ileosob.'){
                  formIleOs.setAttribute("value",'.$ileosob.');
                  formIleOs.children[i].setAttribute("selected","true");
                }
              }
        </script>';
        $do_z_pokoi=$polaczenie->query("SELECT zajety_do FROM pokoje WHERE ilosc_miejsc='$ileosob' ORDER BY zajety_do LIMIT 1");
        $wiersz = $do_z_pokoi->fetch_assoc();
        $zajety_do = $wiersz['zajety_do'];
        $wynikololo=$polaczenie->query("SELECT * FROM rezerwacje WHERE ile_osob='$ileosob'");
        $tab1 = array();
        $tab2 = array();

            while($wiersz = $wynikololo->fetch_assoc()){
                $od=$wiersz['od'];
                // $do=$wiersz['do'];
                while($od<=$zajety_do){
                    $tab2[]=$od;
                    $od=date( 'Y-m-d', strtotime( $od.' +1 day' ) );
                }
            }

            for ($i = 0; $i < count($tab2) ; $i++){
                $suma = 0;
                for ($j = 0; $j < count($tab2) ; $j++){
                if ($tab2[$i] == $tab2[$j]) $suma++;
                }
                if($suma>1){
                    $tab1[]=$tab2[$i];
                }
                
            }
            $blacklist = array_unique($tab1);
            $datyDoSkreslenia = "";
            for($i=0;$i < count($blacklist);$i++){
                $datyDoSkreslenia .= '"'.strval($blacklist[$i]).'",';
            }
            
       ?>
     
       <?php
         echo '<script>
         const my_calendar = new TavoCalendar("#my-calendar", {
             date:["'.date("Y-m-d").'"],
             blacklist:['.$datyDoSkreslenia.'],
             format: "YYYY-MM-DD",
             locale: "pl",
             range_select: true
         });
         </script>';
      }

 
?>
<script>
                      const kal = document.querySelector(".tavo-calendar__days");
                      kal.addEventListener("click",()=>{
                        let stdate = my_calendar.getStartDate();
                        document.getElementById("stdate").setAttribute("value",stdate);
                         const kalL = document.querySelector(".tavo-calendar__days");
                         kalL.addEventListener("click",()=>{
                           let enddate = my_calendar.getEndDate();
                           document.getElementById("enddate").setAttribute("value",enddate);   
                          });
                        });
</script>

<input type="date" name="stdate" id="stdate" style="opacity:0;">
<input type="date" name="enddate" id="enddate" style="opacity:0;"><br>
        <input type="submit" name="rezerwuj" value="Rezerwuj!" class="btn-green"></br></br></br>
      </form>
      <b>Moja rezerwacja:</b></br>
      <?php

if($aktywna_rezerwacja){
    echo '<div class="table row">';
    echo '<div class="col-6 col-xxl-12 row g-0">';
    echo '<div class="col-12 col-xxl-1 bg-green bg-xxl-green d-flex justify-content-center align-items-center">Id rez.</div>';
    echo '<div class="col-12 col-xxl-1 bg-blue bg-xxl-green d-flex justify-content-center align-items-center">Imię</div>';
    echo '<div class="col-12 col-xxl-2 bg-green bg-xxl-green d-flex justify-content-center align-items-center">Nazwisko</div>';
    echo '<div class="col-12 col-xxl-1 bg-blue bg-xxl-green d-flex justify-content-center align-items-center">Pokój</div>';
    echo '<div class="col-12 col-xxl-2 bg-green bg-xxl-green d-flex justify-content-center align-items-center">Od</div>';
    echo '<div class="col-12 col-xxl-2 bg-blue bg-xxl-green d-flex justify-content-center align-items-center">Do</div>';
    echo '<div class="col-12 col-xxl-1 bg-green bg-xxl-green d-flex justify-content-center align-items-center">Oferta</div>';
    echo '<div class="col-12 col-xxl-2 bg-blue bg-xxl-green d-flex justify-content-center align-items-center">Do zapłaty</div>';
    echo '</div>';
    echo '<div class="col-6 col-xxl-12 row g-0">';
    echo '<div class="col-12 col-xxl-1 bg-green bg-xxl-blue d-flex justify-content-center align-items-center">'.$idr.'</div>';
    echo '<div class="col-12 col-xxl-1 bg-blue d-flex justify-content-center align-items-center">'.$imie.'</div>';
    echo '<div class="col-12 col-xxl-2 bg-green bg-xxl-blue d-flex justify-content-center align-items-center">'.$nazwisko.'</div>';
    echo '<div class="col-12 col-xxl-1 bg-blue d-flex justify-content-center align-items-center">'.$ileosob.' osobowy</div>';
    echo '<div class="col-12 col-xxl-2 bg-green bg-xxl-blue d-flex justify-content-center align-items-center">'.$od.'</div>';
    echo '<div class="col-12 col-xxl-2 bg-blue d-flex justify-content-center align-items-center">'.$do.'</div>';
    echo '<div class="col-12 col-xxl-1 bg-green bg-xxl-blue d-flex justify-content-center align-items-center">'.$oferta.'</div>';
    if($czy_oplacono == 1){
        echo '<div class="col-12 col-xxl-2 bg-blue d-flex justify-content-center align-items-center">Opłacono</div>';
    } else {
        echo '<div class="col-12 col-xxl-2 bg-blue d-flex justify-content-center align-items-center">'.$cena.' złotych.</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '<form method="POST">';
    echo '<button type="submit" name="usun" class="btn-red">Usuń rezerwację!</button></br>';
    echo '</form>';
}
else{
    echo 'Brak aktywnej rezerwacji';
}
?>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>