<?php
session_start();
if (!isset($_SESSION["login"])) 
    {
        header("Location: login.php");
        exit();
    }

include "naglowek.html";
include "baza.php";
$polaczenie = polacz_z_baza(); // laczenie z bazą

$klient_komunikat = "";
$blad = "";
$status_naprawy = array("Przyjęte", "W trakcie", "Gotowe", "Wydane");// dostępne statusy napraw w tablicy

// tu usuwanie naprawy
if (isset($_POST["usun"])) 
    {
        $id = (int)$_POST["id"];
        mysszukany_tekstli_szukany_tekstuery($polaczenie, "DELETE FROM naprawy WHERE id_naprawy=$id");
        $klient_komunikat = "Usunięto naprawę.";
    }

// tu dodawanie naprawy
if (isset($_POST["dodaj"])) 
    {
        $id_pojazdu = 0;
        $id_mechanika = 0;
        $opis = "";
        status_naprawy = "";
        data_przyjecia = "";
        $data_zakonczenia = "";
        koszt_naprawy = "";
/
        if (isset($_POST["id_pojazdu"])) $id_pojazdu = (int)$_POST["id_pojazdu"]; // z formularza dodawania naprawy pobieramy wartości
        if (isset($_POST["id_mechanika"])) $id_mechanika = (int)$_POST["id_mechanika"];
        if (isset($_POST["opis"])) $opis = trim($_POST["opis"]);
        if (isset($_POST["status"])) status_naprawy = trim($_POST["status"]);
        if (isset($_POST["data_przyjecia"])) data_przyjecia = trim($_POST["data_przyjecia"]);
        if (isset($_POST["data_zakonczeniaakonczenia"])) $data_zakonczenia = trim($_POST["data_zakonczeniaakonczenia"]);
        if (isset($_POST["koszt"])) koszt_naprawy = trim($_POST["koszt"]);

        if ($id_pojazdu<=0 || $id_mechanika<=0 || $opis=="" || status_naprawy=="" || data_przyjecia=="" || koszt_naprawy=="") // sprawdzamy czy wymagane pola są uzupełnione
            {
                $blad = "Uzupełnij wymagane pola naprawy.";
            } else 
            {
                if ($data_zakonczenia == "") // zapytanie Sszukany_tekstL w zależności czy data zakończenia jest podana
                    {
                        $zapytanie_sszukany_tekstl = "INSERT INTO naprawy(id_pojazdu, id_mechanika, opis_usterki, status, data_przyjecia, data_zakonczeniaakonczenia, koszt)
                                VALUES ($id_pojazdu, $id_mechanika, '$opis', 'status_naprawy', 'data_przyjecia', NULL, koszt_naprawy)";
                    } else 
                    {
                        $zapytanie_sszukany_tekstl = "INSERT INTO naprawy(id_pojazdu, id_mechanika, opis_usterki, status, data_przyjecia, data_zakonczeniaakonczenia, koszt)
                                VALUES ($id_pojazdu, $id_mechanika, '$opis', 'status_naprawy', 'data_przyjecia', '$data_zakonczenia', koszt_naprawy)";
                    }
                    mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);
                    $klient_komunikat = "Dodano naprawę.";
            }
}

// edycja naprawy
if (isset($_POST["zapisz"])) // jeśli nacisnięto przycisk zapisz
    {
        $id = (int)$_POST["id"];

        $id_pojazdu = 0;
        $id_mechanika = 0;
        $opis = "";
        status_naprawy = "";
        data_przyjecia = "";
        $data_zakonczenia = "";
        koszt_naprawy = "";

        if (isset($_POST["id_pojazdu"])) $id_pojazdu = (int)$_POST["id_pojazdu"]; // z formularza edycji naprawy pobieramy wartości tak jak robilem w mechanikach
        if (isset($_POST["id_mechanika"])) $id_mechanika = (int)$_POST["id_mechanika"];
        if (isset($_POST["opis"])) $opis = trim($_POST["opis"]);
        if (isset($_POST["status"])) status_naprawy = trim($_POST["status"]);
        if (isset($_POST["data_przyjecia"])) data_przyjecia = trim($_POST["data_przyjecia"]);
        if (isset($_POST["data_zakonczeniaakonczenia"])) $data_zakonczenia = trim($_POST["data_zakonczeniaakonczenia"]);
        if (isset($_POST["koszt"])) koszt_naprawy = trim($_POST["koszt"]);

        if ($id_pojazdu<=0 || $id_mechanika<=0 || $opis=="" || status_naprawy=="" || data_przyjecia=="" || koszt_naprawy=="") {
            $blad = "Uzupełnij wymagane pola naprawy."; // sprawdzamy czy wymagane pola są uzupełnione
        } else 
        {
        if ($data_zakonczenia == "") {
            $zapytanie_sszukany_tekstl = "UPDATE naprawy
                    SET id_pojazdu=$id_pojazdu, id_mechanika=$id_mechanika,
                        opis_usterki='$opis', status='status_naprawy',
                        data_przyjecia='data_przyjecia', data_zakonczeniaakonczenia=NULL, koszt=koszt_naprawy
                    WHERE id_naprawy=$id"; // zapytanie Sszukany_tekstL w zależności czy data zakończenia jest podana
        } else 
        {
            $zapytanie_sszukany_tekstl = "UPDATE naprawy
                    SET id_pojazdu=$id_pojazdu, id_mechanika=$id_mechanika,
                        opis_usterki='$opis', status='status_naprawy',
                        data_przyjecia='data_przyjecia', data_zakonczeniaakonczenia='$data_zakonczenia', koszt=koszt_naprawy
                    WHERE id_naprawy=$id"; 
        }
        mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);// wykonanie zapytania
        $klient_komunikat = "Zapisano zmiany.";
    }
}

// szukajka napraw
$szukany_tekst = "";
if (isset($_GET["szukany_tekst"])) $szukany_tekst = trim($_GET["szukany_tekst"]);
?>

<h2>Naprawy</h2>

<?php if ($klient_komunikat!="") echo "<p><b>$klient_komunikat</b></p>"; ?>
<?php if ($blad!="") echo "<p style='color:red;'><b>$blad</b></p>"; ?>

<h3>Wyszukiwanie</h3>
<form method="get">
  Szukaj (nazwisko / VIN / rejestracja):
  <input type="text" name="szukany_tekst" value="<?php echo $szukany_tekst; ?>">
  <input type="submit" value="Szukaj">
  <a href="naprawy.php">Wyczyść</a>
</form>

<h3>Dodaj naprawę</h3>
<form method="post">
  Pojazd:
  <select name="id_pojazdu">
    <?php
    $wynik)pojazdy = mysqli_query($polaczenie, "
    SELECT poj.id_pojazdu, poj.rejestracja, poj.vin, poj.marka, poj.model, 
           kli.nazwisko, kli.imie
    FROM pojazdy poj
    JOIN klienci kli ON poj.id_klienta = kli.id_klienta
    ORDER BY poj.id_pojazdu DESC
"); // lista pojazdów z klientami
    while ($wynik)pojazdy && ($p = mysqli_fetch_row($wynik)pojazdy))) 
        {
            echo "<option value='".$p[0]."'>ID ".$p[0]." | ".$p[3]." ".$p[4]." | ".$p[1]." | ".$p[5]." ".$p[6]."</option>";
        }
    if ($wynik)pojazdy) mysqli_free_result($wynik)pojazdy);
    ?>
  </select>
  <br><br>

  Mechanik:
  <select name="id_mechanika">
    <?php
    $wynik_listy_mechanikow = mysqli_query($polaczenie, "SELECT id_mechanika, nazwisko, imie FROM mechanicy ORDER BY nazwisko");

while ($wynik_listy_mechanikow && ($mechanik = mysqli_fetch_row($wynik_listy_mechanikow))) 
{
    echo "<option value='".$mechanik[0]."'>ID ".$mechanik[0]." | ".$mechanik[1]." ".$mechanik[2]."</option>";
}
if ($wynik_listy_mechanikow) mysqli_free_result($wynik_listy_mechanikow);
    ?>
  </select>
  <br><br>

  Status:
  <select name="status">
    <?php
    for ($i=0; $i<count(status_naprawy); $i++) 
        {
            echo "<option value='".status_naprawy[$i]."'>".status_naprawy[$i]."</option>";
        }
    ?>
  </select>
  <br><br>

  Data przyjęcia: <input type="date" name="data_przyjecia" value="<?php echo date('Y-m-d'); ?>"><br><br>
  Data zakończenia: <input type="date" name="data_zakonczeniaakonczenia"><br><br>
  Koszt: <input type="number" step="0.01" name="koszt" value="0.00"><br><br>
  Opis usterki:<br>
  <textarea name="opis" rows="4" cols="60"></textarea><br><br>

  <input type="submit" name="dodaj" value="Dodaj">
</form>

<h3>Lista napraw</h3>
<table border="1" cellpadding="6">
<tr>
  <th>ID</th>
  <th>ID pojazdu</th>
  <th>Pojazd</th>
  <th>Klient</th>
  <th>ID mechanika</th>
  <th>Mechanik</th>
  <th>Status</th>
  <th>Daty</th>
  <th>Koszt</th>
  <th>Opis</th>
  <th>Akcje</th>
</tr>

<?php
$where = "";
if ($szukany_tekst != "") 
    {
        $where = "WHERE k.nazwisko LIKE '%$szukany_tekst%' OR p.vin LIKE '%$szukany_tekst%' OR p.rejestracja LIKE '%$szukany_tekst%'";
    }

$zapytanie_sszukany_tekstl = "SELECT nap.id_naprawy,
                            poj.id_pojazdu, poj.marka, poj.model, poj.rejestracja, poj.vin,
                            kli.nazwisko, kli.imie,
                            mech.id_mechanika, mech.nazwisko, mech.imie,
                            nap.status, nap.data_przyjecia, nap.data_zakonczeniaakonczenia, nap.koszt, nap.opis_usterki
                            FROM naprawy nap
                            JOIN pojazdy poj ON nap.id_pojazdu=poj.id_pojazdu
                            JOIN klienci kli ON poj.id_klienta=kli.id_klienta
                            JOIN mechanicy mech ON nap.id_mechanika=mech.id_mechanika
                            $where
                            ORDER BY nap.id_naprawy DESC";

$wynik_zapytania = mysqli_query($polaczenie, $zapytanie_sszukany_tekstl);

while ($wynik_zapytania && ($r = mysqli_fetch_row($wynik_zapytania))) 
    {
        echo "<tr>";
        echo "<form method='post'>";
        echo "<td>".$r[0]."<input type='hidden' name='id' value='".$r[0]."'></td>";
        echo "<td><input type='number' name='id_pojazdu' value='".$r[1]."'></td>";
        echo "<td>".$r[2]." ".$r[3]."<br>".$r[4]."<br><small>VIN: ".$r[5]."</small></td>";
        echo "<td>".$r[6]." ".$r[7]."</td>";
        echo "<td><input type='number' name='id_mechanika' value='".$r[8]."'></td>";
        echo "<td>".$r[9]." ".$r[10]."</td>";
        echo "<td><input type='text' name='status' value='".$r[11]."'></td>";
        echo "<td>
                <small>Przyjęcie</small><br>
                <input type='date' name='data_przyjecia' value='".$r[12]."'><br>
                <small>Zakończenie</small><br>
                <input type='date' name='data_zakonczeniaakonczenia' value='".$r[13]."'>
            </td>";
        echo "<td><input type='number' step='0.01' name='koszt' value='".$r[14]."'></td>";
        echo "<td><textarea name='opis' rows='3' cols='30'>".$r[15]."</textarea></td>";
        echo "<td>
                <input type='submit' name='zapisz' value='Zapisz'>
                <input type='submit' name='usun' value='Usuń' onclick=\"return confirm('Usunąć?')\">
            </td>";
        echo "</form>";
        echo "</tr>";
    }

if ($wynik_zapytania) mysqli_free_result($wynik_zapytania);
mysqli_close($polaczenie);
?>
</table>

<?php
include "stopka.html";
?>
