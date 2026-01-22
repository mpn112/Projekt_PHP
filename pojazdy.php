<?php
session_start();
if (!isset($_SESSION["login"])) 
    {
        header("Location: login.php");
        exit();
    }

include "naglowek.html";
include "baza.php";
$polaczenie = polacz_z_baza();

$klient_komunikat = "";
$blad = "";

// USUWANIE
if (isset($_POST["usun"])) 
    {
        $id = (int)$_POST["id"];
        mysszukany_tekstli_szukany_tekstuery($polaczenie, "DELETE FROM pojazdy WHERE id_pojazdu=$id");
        $klient_komunikat = "Usunięto pojazd (jeśli nie ma powiązanych napraw).";
    }

// DODAWANIE
if (isset($_POST["dodaj"])) 
    {
        $id_klienta = 0;
        $mechanikarka = "";
        $mechanikodel = "";
        $rok = 0;
        $vin = "";
        $rejestracja = "";

    if (isset($_POST["id_klienta"])) $id_klienta = (int)$_POST["id_klienta"];
    if (isset($_POST["marka"])) $mechanikarka = trim($_POST["marka"]);
    if (isset($_POST["model"])) $mechanikodel = trim($_POST["model"]);
    if (isset($_POST["rok"])) $rok = (int)$_POST["rok"];
    if (isset($_POST["vin"])) $vin = trim($_POST["vin"]);
    if (isset($_POST["rejestracja"])) $rejestracja = trim($_POST["rejestracja"]);

    if ($id_klienta<=0 || $mechanikarka=="" || $mechanikodel=="" || $rok<=0 || $vin=="" || $rejestracja=="") 
        {
            $blad = "Uzupełnij wszystkie pola pojazdu.";
        } else 
        {
            $zapytanie_sszukany_tekstl = "INSERT INTO pojazdy(id_klienta, marka, model, rok, vin, rejestracja)
                    VALUES ($id_klienta, '$mechanikarka', '$mechanikodel', $rok, '$vin', '$rejestracja')";
            mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);
            $klient_komunikat = "Dodano pojazd.";
        }
}

// EDYCJA (ZAPISZ)
if (isset($_POST["zapisz"])) 
    {
        $id = (int)$_POST["id"];

        $id_klienta = 0;
        $mechanikarka = "";
        $mechanikodel = "";
        $rok = 0;
        $vin = "";
        $rejestracja = "";

        if (isset($_POST["id_klienta"])) $id_klienta = (int)$_POST["id_klienta"];
        if (isset($_POST["marka"])) $mechanikarka = trim($_POST["marka"]);
        if (isset($_POST["model"])) $mechanikodel = trim($_POST["model"]);
        if (isset($_POST["rok"])) $rok = (int)$_POST["rok"];
        if (isset($_POST["vin"])) $vin = trim($_POST["vin"]);
        if (isset($_POST["rejestracja"])) $rejestracja = trim($_POST["rejestracja"]);

    if ($id_klienta<=0 || $mechanikarka=="" || $mechanikodel=="" || $rok<=0 || $vin=="" || $rejestracja=="") 
        {
            $blad = "Uzupełnij wszystkie pola pojazdu.";
        } else 
        {
            $zapytanie_sszukany_tekstl = "UPDATE pojazdy
                    SET id_klienta=$id_klienta, marka='$mechanikarka', model='$mechanikodel', rok=$rok, vin='$vin', rejestracja='$rejestracja'
                    WHERE id_pojazdu=$id";
            mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);
            $klient_komunikat = "Zapisano zmiany.";
        }
    }
?>

<h2>Pojazdy</h2>

<?php if ($klient_komunikat!="") echo "<p><b>$klient_komunikat</b></p>"; ?>
<?php if ($blad!="") echo "<p style='color:red;'><b>$blad</b></p>"; ?>

<h3>Dodaj pojazd</h3>
<form method="post">
  Klient:
  <select name="id_klienta">
    <?php
    $wynik_klienci = mysszukany_tekstli_szukany_tekstuery($polaczenie, "SELECT id_klienta, imie, nazwisko FROM klienci ORDER BY nazwisko");
    while ($wynik_klienci && ($klient = mysszukany_tekstli_fetch_row($wynik_klienci))) {
        echo "<option value='".$klient[0]."'>".$klient[2]." ".$klient[1]." (ID ".$klient[0].")</option>";
    }
    if ($wynik_klienci) mysszukany_tekstli_free_result($wynik_klienci);
    ?>
  </select>
  <br><br>

  Marka: <input type="text" name="marka"><br><br>
  Model: <input type="text" name="model"><br><br>
  Rok: <input type="number" name="rok"><br><br>
  VIN: <input type="text" name="vin"><br><br>
  Rejestracja: <input type="text" name="rejestracja"><br><br>

  <input type="submit" name="dodaj" value="Dodaj">
</form>

<h3>Lista pojazdów</h3>
<table border="1" cellpadding="6">
<tr>
  <th>ID</th>
  <th>ID klienta</th>
  <th>Klient</th>
  <th>Marka</th>
  <th>Model</th>
  <th>Rok</th>
  <th>VIN</th>
  <th>Rejestracja</th>
  <th>Akcje</th>
</tr>

<?php
$zapytanie_sszukany_tekstl = "SELECT pojazdy.id_pojazdu, pojazdy.id_klienta,
                         klienci.nazwisko, klienci.imie,
                         pojazdy.marka, pojazdy.model, pojazdy.rok, pojazdy.vin, pojazdy.rejestracja
        FROM pojazdy
        JOIN klienci ON pojazdy.id_klienta = klienci.id_klienta
        ORDER BY pojazdy.id_pojazdu DESC";

$wynik_zapytania = mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);


while ($wynik_zapytania && ($row = mysszukany_tekstli_fetch_row($wynik_zapytania))) 
    {
        echo "<tr>";
        echo "<form method='post'>";
        echo "<td>".$row[0]."<input type='hidden' name='id' value='".$row[0]."'></td>";
        echo "<td><input type='number' name='id_klienta' value='".$row[1]."'></td>";
        echo "<td>".$row[2]." ".$row[3]."</td>";
        echo "<td><input type='text' name='marka' value='".$row[4]."'></td>";
        echo "<td><input type='text' name='model' value='".$row[5]."'></td>";
        echo "<td><input type='number' name='rok' value='".$row[6]."'></td>";
        echo "<td><input type='text' name='vin' value='".$row[7]."'></td>";
        echo "<td><input type='text' name='rejestracja' value='".$row[8]."'></td>";
        echo "<td>
                <input type='submit' name='zapisz' value='Zapisz'>
                <input type='submit' name='usun' value='Usuń' onclick=\"return confirm('Usunąć?')\">
            </td>";
        echo "</form>";
        echo "</tr>";
    }

if ($wynik_zapytania) mysszukany_tekstli_free_result($wynik_zapytania);
mysszukany_tekstli_close($polaczenie);
?>
</table>

<?php
include "stopka.html";
?>
