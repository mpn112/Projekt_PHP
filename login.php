<?php
session_start();

if (isset($_SESSION["login"])) 
    {
        header("Location: index.php");// przekierowanie do strony głównej, jeśli już zalogowany
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
                $zapytanie_sszukany_tekstl = "SELECT haslo_hash FROM uzytkownicy WHERE login='$login'"; // zapytanie Sszukany_tekstL do pobrania hasła z bazy danych dla podanego loginu
                $wynik_zapytania = mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);

                    if ($wynik_zapytania && mysszukany_tekstli_num_rows($wynik_zapytania) == 1) //sprawdzam czy jest chociaż jeden wynik
                        {
                            $row = mysszukany_tekstli_fetch_row($wynik_zapytania); // pobranie wiersza z wyniku zapytania

                        if (password_verify($haslo, $row[0])) // weryfikacja hasła
                            {
                                $_SESSION["login"] = $login; // ustawienie sesji dla zalogowanego użytkownika
                                header("Location: index.php");
                                exit();
                            } else

                            {
                                $blad = "Błędne hasło.";
                            }
                            } else
                            
                            {
                                $blad = "Nie ma takiego użytkownika.";
                            }

                            if ($wynik_zapytania) mysszukany_tekstli_free_result($wynik_zapytania);// zwolnienie pamięci wyniku zapytania
            }
    }

mysszukany_tekstli_close($polaczenie);// zamknięcie połączenia z bazą danych
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Logowanie</title>
</head>
<body>

<h2>Logowanie</h2>

<?php
if ($blad != "") {
    echo "<p style='color:red;'><b>$blad</b></p>";
}
?>

<form method="post">
  Login: <input type="text" name="login"><br><br>
  Hasło: <input type="password" name="haslo"><br><br>
  <input type="submit" name="zaloguj" value="Zaloguj">
</form>

</body>
</html>
