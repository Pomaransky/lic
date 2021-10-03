<?php
    session_start();
	
	if ((!isset($_POST['login'])) || (!isset($_POST['haslo'])))
	{
		header('Location: index.php');
		exit();
	}

	require_once "polaczenie.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];
		
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
	
		if ($wynik = @$polaczenie->query(sprintf("SELECT * FROM uzytkownicy WHERE uzytkownik='%s'",mysqli_real_escape_string($polaczenie,$login))))
		{
			$ilu = $wynik->num_rows;
			if($ilu>0)
			{
					$wiersz = $wynik->fetch_assoc();
					if(password_verify($haslo,$wiersz['haslo']))
					{
					$_SESSION['zalogowany'] = true;
					$_SESSION['id'] = $wiersz['id'];
					$_SESSION['uzytkownik'] = $wiersz['uzytkownik'];
					$_SESSION['email'] = $wiersz['email'];
					if($wiersz['admin'] == 1){
                        $_SESSION['admin'] = true;
                    }
					unset($_SESSION['blad']);
					$wynik->free_result();
					header('Location: zalogowano.php');
					}
					else 
					{
					$_SESSION['blad'] = '<div class="error">Nieprawidłowy login lub hasło!</div>';
					header('Location: index.php');
					}
			} else 
			{
				$_SESSION['blad'] = '<div class="error">Nieprawidłowy login lub hasło!</div>';
				header('Location: index.php');
			}
			
		}
		$polaczenie->close();
	}
	
?>