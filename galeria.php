<?php
	session_start();
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
  <link rel="stylesheet" href="styles/galeria.css?v=<?php echo time(); ?>">
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
    <div class="containerr">
      <span onclick="this.parentElement.style.display='none'" class="closebtn">&times;</span>
      <img id="expandedImg">
      <div id="imgtext"></div>
    </div>
    <div class="content">
      <div class="column">
        <img src="images/1osobowy1.jpg" alt="First room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/1osobowy2.jpg" alt="First room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/1osobowy3.jpg" alt="First room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/1osobowy4.jpg" alt="First room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/2osobowy1.jpg" alt="Second room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/2osobowy2.jpg" alt="Second room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/2osobowy3.jpg" alt="Second room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/2osobowy4.jpg" alt="Second room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/2osobowy5.jpg" alt="Second room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/3osobowy1.jpg" alt="Third room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/3osobowy2.jpg" alt="Third room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/3osobowy3.jpg" alt="Third room" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h1.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h2.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h3.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h4.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h5.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h6.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h7.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h8.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/h9.jpg" alt="Hotel" onclick="myFunction(this);">
      </div>
      <div class="column">
        <img src="images/lazienka.jpg" alt="Łazienka" onclick="myFunction(this);">
      </div>
    </div>
  </div>
  <script src="js/main.js"></script>
  <script src="js/galeria.js"></script>
</body>

</html>