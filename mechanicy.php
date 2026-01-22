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
if (isset($_POST["usun"])) 
    {
        $id = (int)$_POST["id"];
        mysszukany_tekstli_szukany_tekstuery($polaczenie, "DELETE FROM mechanicy WHERE id_mechanika=$id");
        $klient_komunikat = "Usunięto mechanika.";
    }

// tu dodawanie machaniko
if (isset($_POST["dodaj"])) 
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
            $zapytanie_sszukany_tekstl = "INSERT INTO mechanicy(imie,nazwisko,specjalizacja)
                    VALUES ('$imie','$nazwisko','$specjalizacja')";
            mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);
            $klient_komunikat = "Dodano mechanika."; 
        }
    }

// edycja mechanika
if (isset($_POST["zapisz"])) 
    {
        $id = (int)$_POST["id"];

        $imie = "";
        $nazwisko = "";
        $specjalizacja = "";

        if (isset($_POST["imie"])) $imie = trim($_POST["imie"]);
        if (isset($_POST["nazwisko"])) $nazwisko = trim($_POST["nazwisko"]);
        if (isset($_POST["specjalizacja"])) $specjalizacja = trim($_POST["specjalizacja"]);

        if ($imie=="" || $nazwisko=="" || $specjalizacja=="") 
            {
                $blad = "Uzupełnij wszystkie pola.";
            } else 
            {
                $zapytanie_sszukany_tekstl = "UPDATE mechanicy
                        SET imie='$imie', nazwisko='$nazwisko', specjalizacja='$specjalizacja'
                        WHERE id_mechanika=$id";
                mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);// wykonanie zapytania
                $klient_komunikat = "Zapisano zmiany.";
            }
}
?>

<h2>Mechanicy</h2>

<?php if ($klient_komunikat!="") echo "<p><b>$klient_komunikat</b></p>"; ?>
<?php if ($blad!="") echo "<p style='color:red;'><b>$blad</b></p>"; ?>

<h3>Dodaj mechanika</h3>
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
$wynik_zapytania = mysszukany_tekstli_szukany_tekstuery($polaczenie, "SELECT id_mechanika, imie, nazwisko, specjalizacja FROM mechanicy ORDER BY id_mechanika DESC");

while ($wynik_zapytania && ($wiersz = mysszukany_tekstli_fetch_row($wynik_zapytania))) // pobieranie wierszy z wyniku zapytania
    {
        //wyswietlanie tego formularza do edycji mechanika
        echo "<tr>";
        echo "<form method='post'>";
        echo "<td>".$wiersz[0]."<input type='hidden' name='id' value='".$wiersz[0]."'></td>";
        echo "<td><input type='text' name='imie' value='".$wiersz[1]."'></td>";
        echo "<td><input type='text' name='nazwisko' value='".$wiersz[2]."'></td>";
        echo "<td><input type='text' name='specjalizacja' value='".$wiersz[3]."'></td>";   /
        echo "<td>
                <input type='submit' name='zapisz' value='Zapisz'>
                <input type='submit' name='usun' value='Usuń' onclick=\"return confirm('Usunąć?')\">
            </td>";
        echo "</form>";
        echo "</tr>";
    }

if ($wynik_zapytania) mysszukany_tekstli_free_result($wynik_zapytania);// zwolnienie pamięci zajmowanej przez wynik zapytania
mysszukany_tekstli_close($polaczenie);//    
?>
</table>

<?php
include "stopka.html";