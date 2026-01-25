<?php
session_start();
if (!isset($_SESSION["login"])) 
    {
        header("Location: login.php");
        exit();
    }

include "naglowek.html";// Nagłówek strony
include "baza.php"; 
$polaczenie = polacz_z_baza();// Połączenie z bazą danych

$klient_komunikat = "";
$blad = "";

// tu usuwamy mechanikow
if (isset($_POST["usun"])) // sprawdzenie czy formularz został wysłany
{
    $id = (int)$_POST["id"];

    $wynik_zapytania = mysqli_query($polaczenie, "SELECT COUNT(*) FROM naprawy WHERE id_mechanika=$id"); // sprawdzamy czy są powiązane naprawy
    $ile = 0;
    if ($wynik_zapytania) // jesli zapytanie się powiodło
        { 
            $wiersz = mysqli_fetch_row($wynik_zapytania); // pobieramy pierwszy wiersz wyniku
            $ile = (int)$wiersz[0]; mysqli_free_result($wynik_zapytania);
        }

    if ($ile > 0) // jeśli są powiązane naprawy, to nie usuwamy
        {
            $blad = "Nie można usunąć mechanika: istnieją powiązane naprawy.";
        } else 
        {
            mysqli_query($polaczenie, "DELETE FROM mechanicy WHERE id_mechanika=$id");
            $klient_komunikat = "Usunięto mechanika."; // komunikat dla klienta
        }
}

// tu dodawanie machanika
if (isset($_POST["dodaj"])) // sprawdzenie czy formularz został wysłany
{
    $imie = "";
    $nazwisko = "";
    $specjalizacja = "";

    if (isset($_POST["imie"])) $imie = trim($_POST["imie"]);
    if (isset($_POST["nazwisko"])) $nazwisko = trim($_POST["nazwisko"]);
    if (isset($_POST["specjalizacja"])) $specjalizacja = trim($_POST["specjalizacja"]);

    if ($imie=="" || $nazwisko=="" || $specjalizacja=="") // sprawdzamy czy pola nie są puste
        {
            $blad = "Uzupełnij wszystkie pola.";
        } else 
        {
            $imie_sql = mysqli_real_escape_string($polaczenie, $imie); // zabezpieczenie żeby nikt nie popsuł zapytania SQL NAUCZYCE SIĘ TEGO!!!
            $nazwisko_sql = mysqli_real_escape_string($polaczenie, $nazwisko);
            $spec_sql = mysqli_real_escape_string($polaczenie, $specjalizacja);
            $zapytanie_sql = "INSERT INTO mechanicy(imie,nazwisko,specjalizacja)
                            VALUES ('$imie_sql','$nazwisko_sql','$spec_sql')"; // tworzymy zapytanie SQL
            mysqli_query($polaczenie, $zapytanie_sql);// wykonujemy zapytanie SQL
            $klient_komunikat = "Dodano mechanika."; 
        }
}

// edycja mechanika
if (isset($_POST["zapisz"])) 
{
    $id = (int)$_POST["id"]; // pobranie id mechanika do edycji

    $imie = "";
    $nazwisko = "";
    $specjalizacja = "";

    if (isset($_POST["imie"])) $imie = trim($_POST["imie"]);
    if (isset($_POST["nazwisko"])) $nazwisko = trim($_POST["nazwisko"]);
    if (isset($_POST["specjalizacja"])) $specjalizacja = trim($_POST["specjalizacja"]);

    if ($imie=="" || $nazwisko=="" || $specjalizacja=="") // sprawdzamy czy pola nie są puste
        {
            $blad = "Uzupełnij wszystkie pola.";
        } else 
        {
            $imie_sql = mysqli_real_escape_string($polaczenie, $imie); // zabezpieczenie żeby nikt nie popsuł zapytania SQL
            $nazwisko_sql = mysqli_real_escape_string($polaczenie, $nazwisko);
            $spec_sql = mysqli_real_escape_string($polaczenie, $specjalizacja);
            $zapytanie_sql = "UPDATE mechanicy
                            SET imie='$imie_sql', nazwisko='$nazwisko_sql', specjalizacja='$spec_sql'
                            WHERE id_mechanika=$id"; // tworzymy zapytanie SQL do aktualizacji danych mechanika

            mysqli_query($polaczenie, $zapytanie_sql);

            $klient_komunikat = "Zapisano zmiany.";

        }
}
?>

<h2>Mechanicy</h2>

<?php 
    if ($klient_komunikat!="") // wyswietlanie komunikatu dla klienta
    {
        echo "<p><b>$klient_komunikat</b></p>";
    }
    if ($blad!="")
    {
        echo "<p style='color:red;'><b>$blad</b></p>"; 
    }
?>

<h3>Dodaj mechanika</h3> <!-- formularz dodawania mechanika -->
<form method="post">
  Imię: <input type="text" name="imie"><br><br>
  Nazwisko: <input type="text" name="nazwisko"><br><br>
  Specjalizacja: <input type="text" name="specjalizacja"><br><br>
  <input type="submit" name="dodaj" value="Dodaj">
</form>

<h3>Lista mechaników</h3>
<table border="1" cellpadding="6">
<tr>
  <th>ID</th>
  <th>Imię</th>
  <th>Nazwisko</th>
  <th>Specjalizacja</th>
  <th>Akcje</th>
</tr>

<?php
$wynik_zapytania = mysqli_query($polaczenie, "SELECT id_mechanika, imie, nazwisko, specjalizacja FROM mechanicy ORDER BY id_mechanika DESC");// pobranie listy mechanikow

    while ($wynik_zapytania && ($numer_wiersza = mysqli_fetch_row($wynik_zapytania))) // pobieranie wierszy z wyniku zapytania
    {
        //wyswietlanie tego formularza do edycji mechanika
        echo "<tr>";
        echo "<form method='post'>";
        echo "<td>".$numer_wiersza[0]."<input type='hidden' name='id' value='".$numer_wiersza[0]."'></td>";
        echo "<td><input type='text' name='imie' value='".$numer_wiersza[1]."'></td>";
        echo "<td><input type='text' name='nazwisko' value='".$numer_wiersza[2]."'></td>";
        echo "<td><input type='text' name='specjalizacja' value='".$numer_wiersza[3]."'></td>";   
        echo "<td>
                <input type='submit' name='zapisz' value='Zapisz'>
                <input type='submit' name='usun' value='Usuń' onclick=\"return confirm('Usunąć?')\">
            </td>";
        echo "</form>";
        echo "</tr>"; 
    }

    if ($wynik_zapytania) mysqli_free_result($wynik_zapytania); // zwolnienie pamięci wyniku
    mysqli_close($polaczenie); // zamknięcie połączenia z bazą    
?>
</table>

<?php
include "stopka.html";