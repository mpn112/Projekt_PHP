<?php
session_start();
if (!isset($_SESSION["login"])) // standardowo czy zalogowany jak nie to przekieruj na login
    {
        header("Location: login.php");// przekierowanie do strony logowania
        exit();
    }

include "naglowek.html"; // z wykladu 4-5 naglowek zeby nie powtarzac kodu w sumie ok
include "baza.php"; // plik z funkcja polacz_z_baza()
$polaczenie = polacz_z_baza();

$klient_komunikat = "";// komunikaty do wyswietlenia
$blad = "";//   komunikaty o bledach

// usuwanie klienta z bazy danych, no i z wyswietlenia
if (isset($_POST["usun"])) 
{
    $id = (int)$_POST["id"];

    $wynik_zapytania = mysqli_query($polaczenie, 
    "SELECT COUNT(*) 
    FROM naprawy nap
    JOIN pojazdy poj ON nap.id_pojazdu = poj.id_pojazdu
    WHERE poj.id_klienta = $id");// zapytanie sprawdzajace czy klient ma pojazdy z naprawami
    $ileNapraw = 0;  // zmienna do przechowywania liczby napraw
    if ($wynik_zapytania) 
        {   $wiersz = mysqli_fetch_row($wynik_zapytania); 
            $ileNapraw = (int)$wiersz[0]; mysqli_free_result($wynik_zapytania); 
        }// pobranie wyniku zapytania

    if ($ileNapraw > 0) 
        {
             $blad = "Nie można usunąć klienta: ma pojazd powiązany z naprawami.";
        } else 
        {// jezeli nie ma powiazan czyli nie zakonczonych napraw to usun klienta
            mysqli_query($polaczenie, "DELETE FROM pojazdy WHERE id_klienta=$id");
            mysqli_query($polaczenie, "DELETE FROM klienci WHERE id_klienta=$id");
            $klient_komunikat = "Usunięto klienta."; // komunikat o usunieciu klienta
        }
}

// dopisanie nowego klienta
if (isset($_POST["dodaj"])) // jezeli kliknieto dodaj
    {
    $imie = ""; // zmienne do przechowywania danych klienta
    $nazwisko = "";
    $telefon = "";
    $email = "";

        if (isset($_POST["imie"])) $imie = trim($_POST["imie"]); //jezeli istnieje to przypisz wartosc z formularza
        if (isset($_POST["nazwisko"])) $nazwisko = trim($_POST["nazwisko"]);
        if (isset($_POST["telefon"])) $telefon = trim($_POST["telefon"]);
        if (isset($_POST["email"])) $email = trim($_POST["email"]);

        if ($imie=="" || $nazwisko=="" || $telefon=="" || $email=="") //tu sprawdzam czy wszystkie pola wpisane maja dane
            {
                $blad = "Uzupełnij wszystkie pola klienta.";
            } else 
            {
                $zapytanie_sql = "INSERT INTO klienci(imie, nazwisko, telefon, email) 
                        VALUES ('$imie', '$nazwisko', '$telefon', '$email')"; //jak wszystko ok to dodaj do bazy
                $ok = mysqli_query($polaczenie, $zapytanie_sql);
                    if ($ok == false) 
                        {
                            $blad = "Nie udało się dodać klienta.";
                        } else 
                        {
                            $komunikat = "Dodano klienta.";
                        }
            }
    }

// edycja istniejącego klienta
if (isset($_POST["zapisz"])) // jezeli kliknieto zapisz
    {
        $id = (int)$_POST["id"]; // id klienta do edycji

        $imie = ""; // zmienne do przechowywania danych klienta
        $nazwisko = "";
        $telefon = "";
        $email = "";

        if (isset($_POST["imie"])) $imie = trim($_POST["imie"]); // przypisanie wartosci z formularza
        if (isset($_POST["nazwisko"])) $nazwisko = trim($_POST["nazwisko"]);
        if (isset($_POST["telefon"])) $telefon = trim($_POST["telefon"]);
        if (isset($_POST["email"])) $email = trim($_POST["email"]);

        if ($imie=="" || $nazwisko=="" || $telefon=="" || $email=="")    // sprawdzenie czy wszystkie pola wypelnione
        {
            $blad = "Uzupełnij wszystkie pola klienta.";
        } else 
        {
            $zapytanie_sql = "UPDATE klienci
                SET imie='$imie', nazwisko='$nazwisko', telefon='$telefon', email='$email'
                WHERE id_klienta=$id"; // jezeli ok to aktualizuj dane klienta w bazie
            mysqli_query($polaczenie, $zapytanie_sql);
            $komunikat = "Zapisano zmiany.";
        }
    }
?>

<h2>Klienci</h2>

<?php 
if ($klient_komunikat!="") echo "<p><b>$klient_komunikat</b></p>"; 
if ($blad!="") echo "<p style='color:red;'><b>$blad</b></p>"; 
?>

<h3>Dodaj klienta</h3> <!--formularz do dodawania klienta  --> 
<form method="post">
  Imię: <input type="text" name="imie"><br><br> 
  Nazwisko: <input type="text" name="nazwisko"><br><br>
  Telefon: <input type="text" name="telefon"><br><br>
  Email: <input type="text" name="email"><br><br>
  <input type="submit" name="dodaj" value="Dodaj">
</form>

<h3>Lista klientów</h3>
<table border="1" cellpadding="6">
<tr>
  <th>ID</th>
  <th>Imię</th>
  <th>Nazwisko</th>
  <th>Telefon</th>
  <th>Email</th>
  <th>Akcje</th>
</tr>

<?php
$wynik_zapytania = mysqli_query($polaczenie, "SELECT id_klienta, imie, nazwisko, telefon, email FROM klienci ORDER BY id_klienta DESC"); //pobranie listy klientow z bazy danych

while ($wynik_zapytania && ($numer_wiersza = mysqli_fetch_row($wynik_zapytania))) // wyswietlenie kazdego klienta w formularzu do edycji
    {
        echo "<tr>";
        echo "<form method='post'>";
        echo "<td>".$numer_wiersza[0]."<input type='hidden' name='id' value='".$numer_wiersza[0]."'></td>"; // ukryte pole z id klienta
        echo "<td><input type='text' name='imie' value='".$numer_wiersza[1]."'></td>"; // pola do edycji danych klienta
        echo "<td><input type='text' name='nazwisko' value='".$numer_wiersza[2]."'></td>";
        echo "<td><input type='text' name='telefon' value='".$numer_wiersza[3]."'></td>";
        echo "<td><input type='text' name='email' value='".$numer_wiersza[4]."'></td>";
        echo "<td>
                <input type='submit' name='zapisz' value='Zapisz'> 
                <input type='submit' name='usun' value='Usuń' onclick=\"return confirm('Usunąć?')\">
            </td>";
        echo "</form>";
        echo "</tr>";
    }

if ($wynik_zapytania) mysqli_free_result($wynik_zapytania); // zwolnienie pamięci wyniku zapytania

mysqli_close($polaczenie); // zamknięcie połączenia z bazą
?>
</table>

<?php
include "stopka.html";
?>
