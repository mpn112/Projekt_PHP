<link rel="stylesheet" href="style.css">   
<?php
session_start();

if (isset($_SESSION["login"])) 
    {
        header("Location: index.php");//przekierowanie do strony głównej jeśli użytkownik jest już zalogowany
        exit();
    }

include "baza.php";
$polaczenie = polacz_z_baza();// tworzenie połaczenia z baza danych

$blad = "";

if (isset($_POST["zaloguj"])) // sprawdzenie czy formularz został wysłany
{
    $login = "";
    $haslo = "";

    if (isset($_POST["login"])) $login = trim($_POST["login"]); //  usuwanie białych znaków z początku i końca wyklad 3-4
    if (isset($_POST["haslo"])) $haslo = trim($_POST["haslo"]);

    if ($login == "" || $haslo == "") // sprawdzenie czy pola nie są puste
        {
            $blad = "Podaj login i hasło."; //jezeli puste, ustawienie komunikatu o błędzie
        } else 
        {
        $login_sql = mysqli_real_escape_string($polaczenie, $login);
        $zapytanie_sql = "SELECT haslo_hash FROM uzytkownicy WHERE login='$login_sql'"; // zapytanie SQL do pobrania hasła z bazy danych dla podanego loginu
        $wynik_zapytania = mysqli_query($polaczenie, $zapytanie_sql);

        if ($wynik_zapytania && mysqli_num_rows($wynik_zapytania) == 1) // sprawdzam czy jest dokładnie jeden wynik
            {
                $numer_wiersza = mysqli_fetch_row($wynik_zapytania); // pobranie wiersza z wyniku zapytania

                if (password_verify($haslo, $numer_wiersza[0])) // weryfikacja hasła
                    {
                        $_SESSION["login"] = $login; // ustawienie sesji dla zalogowanego uzytkownika
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
                        mysqli_free_result($wynik_zapytania); // zwolnienie pamięci wyniku zapytania
                    }
                }
}

mysqli_close($polaczenie); // zamknięcie połączenia z bazą danych
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
    if ($blad != "") // jak jest blad to wyswietl
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
