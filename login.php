<link rel="stylesheet" href="style.css">   
<?php
session_start();

if (isset($_SESSION["login"])) 
    {
        header("Location: index.php");
        exit();
    }

include "baza.php";
$polaczenie = polacz_z_baza();

$blad = "";

if (isset($_POST["zaloguj"])) 
{
    $login = "";
    $haslo = "";

    if (isset($_POST["login"])) $login = trim($_POST["login"]); 
    if (isset($_POST["haslo"])) $haslo = trim($_POST["haslo"]);

    if ($login == "" || $haslo == "") 
        {
            $blad = "Podaj login i hasło."; 
        } else 
        {
        $login_sql = mysqli_real_escape_string($polaczenie, $login);
        $zapytanie_sql = "SELECT haslo_hash FROM uzytkownicy WHERE login='$login_sql'"; 
        $wynik_zapytania = mysqli_query($polaczenie, $zapytanie_sql);

        if ($wynik_zapytania && mysqli_num_rows($wynik_zapytania) == 1) 
            {
                $numer_wiersza = mysqli_fetch_row($wynik_zapytania); 
                $hash_hasla = $numer_wiersza[0];
                if (password_verify($haslo, $hash_hasla)) 
                    {
                        $_SESSION["login"] = $login; 
                        header("Location: index.php");
                        exit();
                    }
                    else
                    {
                        $blad = "Błędne hasło.";
                    }
            }
                else
                {
                    $blad = "Nie ma takiego użytkownika.";
                }

            if ($wynik_zapytania)
            {
                mysqli_free_result($wynik_zapytania); 
            }
        }
}

mysqli_close($polaczenie); 
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Logowanie</title>
</head>
<body>
<div class="box">
  <div class="card login-card">
    <h2>Logowanie</h2>

<?php
    if ($blad != "") 
        {
            echo "<div class='blad'><b>$blad</b></div>";
        }
?>

    <form method="post" autocomplete="off">
      <div class="form-row">
        <label>Login:</label>
        <input type="text" name="login">
      </div>

      <div class="form-row">
        <label>Hasło:</label>
        <input type="password" name="haslo">
      </div>

      <button class="btn" type="submit" name="zaloguj" value="1">Zaloguj</button>
    </form>
  </div>
</div>
<?php
include "stopka.html";
?>
</body>
