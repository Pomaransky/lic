<?php
        session_start();
    if(isset($_POST['nazwa'])){
        $jestOK=true;
        $nazwa=$_POST['nazwa'];
        if(strlen($nazwa)<3||strlen($nazwa)>12){
            $jestOK=false;
            $_SESSION['error_nazwa']="Nazwa musi mieć od 3 do 12 znaków!";
        }
        if(ctype_alnum($nazwa)==false){
            $jestOK=false;
            $_SESSION['error_nazwa']="Nazwa nie może zawierać znaków specjalnych! Nazwa może zawierać tylko litery i cyfry.";
        }
        $email=$_POST['email'];
        $emailB=filter_var($email,FILTER_SANITIZE_EMAIL);
        if(filter_var($emailB,FILTER_VALIDATE_EMAIL)==false || ($emailB!=$email)){
            $jestOK=false;
            $_SESSION['error_email']="Niepoprawny adres email!";
        }
        $haslo1=$_POST['haslo1'];
        $haslo2=$_POST['haslo2'];
        if(strlen($haslo1)<8||strlen($haslo1)>20){
            $jestOK=false;
            $_SESSION['error_haslo1']="Hasło musi mieć od 8 do 20 znaków!";
        }
        if($haslo1!=$haslo2){
            $jestOK=false;
            $_SESSION['error_haslo2']="Hasła muszą być takie same!";
        }
        $haslo_hash=password_hash($haslo1,PASSWORD_DEFAULT);
        if(!isset($_POST['regulamin'])){
            $jestOK=false;
            $_SESSION['error_regulamin']="Musisz akceptować regulamin!";
        }
        $skey="6Ld4FdEUAAAAAKjU9lEF5nwUomEI8e0zSv_zZp3t";
        $sprawdz=file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$skey.'&response='.$_POST['g-recaptcha-response']);
        $odpowiedz=json_decode($sprawdz);
        if($odpowiedz->success==false){
            $jestOK=false;
            $_SESSION['error_recaptcha']="Musisz potwierdzić że nie jesteś botem!";
        }
        require_once "polaczenie.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $polaczenie = new mysqli($host, $db_user,$db_password, $db_name);
            if ($polaczenie->connect_errno!=0)
            {
                throw new Exception(mysqli_connect_errno());
            }
            else{
                $wynik=$polaczenie->query("SELECT * FROM uzytkownicy WHERE email='$email'");
                if(!$wynik) throw new Exception($polaczenie->error);
                $ile_takich_samych_maili=$wynik->num_rows;
                if($ile_takich_samych_maili>0){
                    $jestOK=false;
                    $_SESSION['error_email']="Podany email jest już w użyciu!";
                }
                $wynik=$polaczenie->query("SELECT * FROM uzytkownicy WHERE uzytkownik='$nazwa'");
                if(!$wynik) throw new Exception($polaczenie->error);

                $ile_takich_samych_nazw=$wynik->num_rows;
                if($ile_takich_samych_nazw>0){
                    $jestOK=false;
                    $_SESSION['error_nazwa']="Podany nazwa użytkownika jest już zajęta!";
                }
                if($jestOK==true){
                    if($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL,'$nazwa','$haslo_hash','$email',0)")){
                        $_SESSION['zarejestrowano']=true;
                        header('Location:zarejestrowano.php');
                    }else throw new Exception($polaczenie->error);
                }
                $polaczenie->close();
            }
        }
        catch(Exception $error){
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
  <link rel="stylesheet" href="styles/rejestracja.css?v=<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
        <div class="item">Nazwa użytkownika:</div>
        <input type="text" name="nazwa" />
        <?php
        if(isset($_SESSION['error_nazwa'])){
          echo '<div class="error">'.$_SESSION['error_nazwa'].'</div>';
          unset($_SESSION['error_nazwa']);
        } 
      ?>
        <div class="item">E-mail:</div>
        <input type="text" name="email" />
        <?php
        if(isset($_SESSION['error_email'])){
          echo '<div class="error">'.$_SESSION['error_email'].'</div>';
          unset($_SESSION['error_email']);
        }
      ?>
        <div class="item">Hasło:</div>
        <input type="password" name="haslo1" />
        <?php
        if(isset($_SESSION['error_haslo1'])){
          echo '<div class="error">'.$_SESSION['error_haslo1'].'</div>';
          unset($_SESSION['error_haslo1']);
        }
      ?>
        <div class="item">Powtórz hasło:</div>
        <input type="password" name="haslo2" />
        <?php
        if(isset($_SESSION['error_haslo2'])){
          echo '<div class="error">'.$_SESSION['error_haslo2'].'</div>';
          unset($_SESSION['error_haslo2']);
        }
      ?>
        <div class="item"><input type="checkbox" name="regulamin" /> Akceptuję <a href="regulamin.php"
            class="regulamin">regulamin.</a></div>
        <?php
        if(isset($_SESSION['error_regulamin'])){
          echo '<div class="error">'.$_SESSION['error_regulamin'].'</div>';
          unset($_SESSION['error_regulamin']);
        }
      ?>
        <div class="g-recaptcha" data-sitekey="6Ld4FdEUAAAAAFNy3BTIwHi3k8rfckm1Fsw1zNuK"></div>
        <?php
        if(isset($_SESSION['error_recaptcha'])){
          echo '<div class="error">'.$_SESSION['error_recaptcha'].'</div>';
          unset($_SESSION['error_recaptcha']);
        }
      ?>
        <button type="submit" class="btn-blue">Zarejestruj!</button>
      </form>
      <div class="item">Masz już konto?</div>
      <div class="item"><button class="btn-green"><a href="index.php"> Zaloguj się!</a></button></div>
    </div>
  </div>
  </div>
  <script src="js/main.js"></script>
</body>

</html>