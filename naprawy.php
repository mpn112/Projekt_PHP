<?php
session_start();
if (!isset($_SESSION["login"])) // jeśli nie zalogowano
    {
        header("Location: login.php");// przekierowanie do strony logowania
        exit();
    }

include "naglowek.html";
include "baza.php";
$polaczenie = polacz_z_baza(); // laczenie z baza

$klient_komunikat = "";
$blad = "";
$statusy_naprawy = array("Przyjęte", "W trakcie", "Gotowe", "Wydane");// dostępne statusy napraw w tablicy

// tu usuwanie naprawy
if (isset($_POST["usun"])) 
    {
        $id = (int)$_POST["id"];
        mysqli_query($polaczenie, "DELETE FROM naprawy WHERE id_naprawy=$id");
        $klient_komunikat = "Usunięto naprawę.";
    }

// tu dodawanie naprawy
if (isset($_POST["dodaj"])) 
{
    $id_pojazdu = 0;
    $id_mechanika = 0;
    $opis = "";
    $status_naprawy = "";
    $data_przyjecia = "";
    $data_zakonczenia = "";
    $koszt_naprawy = "";

    if (isset($_POST["id_pojazdu"])) $id_pojazdu = (int)$_POST["id_pojazdu"];
    if (isset($_POST["id_mechanika"])) $id_mechanika = (int)$_POST["id_mechanika"];
    if (isset($_POST["opis"])) $opis = trim($_POST["opis"]);
    if (isset($_POST["status"])) $status_naprawy = trim($_POST["status"]);
    if (isset($_POST["data_przyjecia"])) $data_przyjecia = trim($_POST["data_przyjecia"]);
    if (isset($_POST["data_zakonczenia"])) $data_zakonczenia = trim($_POST["data_zakonczenia"]);
    if (isset($_POST["koszt"])) $koszt_naprawy = trim($_POST["koszt"]);

    if ($id_pojazdu<=0 || $id_mechanika<=0 || $opis=="" || $status_naprawy=="" || $data_przyjecia=="" || $koszt_naprawy=="") 
    {
        $blad = "Uzupełnij wymagane pola naprawy.";
    } 
    else 
    {
        
        $opis_sql = mysqli_real_escape_string($polaczenie, $opis);// zabezpieczenie żeby nikt nie popsuł zapytania SQL
        $status_sql = mysqli_real_escape_string($polaczenie, $status_naprawy);
        $data_p_sql = mysqli_real_escape_string($polaczenie, $data_przyjecia);

        if ($data_zakonczenia == "") // jeśli data zakonczenia naprawy nie jest podana
        {
            $zapytanie_sql = "INSERT INTO naprawy(id_pojazdu, id_mechanika, opis_usterki, status, data_przyjecia, data_zakonczenia, koszt)
                              VALUES ($id_pojazdu, $id_mechanika, '$opis_sql', '$status_sql', '$data_p_sql', NULL, ".(float)$koszt_naprawy.")"; // wstawienie NULL do bazy
        } 
        else 
        {
            $data_z_sql = mysqli_real_escape_string($polaczenie, $data_zakonczenia);
            $zapytanie_sql = "INSERT INTO naprawy(id_pojazdu, id_mechanika, opis_usterki, status, data_przyjecia, data_zakonczenia, koszt)
                              VALUES ($id_pojazdu, $id_mechanika, '$opis_sql', '$status_sql', '$data_p_sql', '$data_z_sql', ".(float)$koszt_naprawy.")";// wstawienie daty do bazy
        }

        mysqli_query($polaczenie, $zapytanie_sql);
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
    $status_naprawy = "";
    $data_przyjecia = "";
    $data_zakonczenia = "";
    $koszt_naprawy = "";

    if (isset($_POST["id_pojazdu"])) $id_pojazdu = (int)$_POST["id_pojazdu"];// pobranie wartości z formularza
    if (isset($_POST["id_mechanika"])) $id_mechanika = (int)$_POST["id_mechanika"];
    if (isset($_POST["opis"])) $opis = trim($_POST["opis"]);
    if (isset($_POST["status"])) $status_naprawy = trim($_POST["status"]);
    if (isset($_POST["data_przyjecia"])) $data_przyjecia = trim($_POST["data_przyjecia"]);
    if (isset($_POST["data_zakonczenia"])) $data_zakonczenia = trim($_POST["data_zakonczenia"]);
    if (isset($_POST["koszt"])) $koszt_naprawy = trim($_POST["koszt"]);

    if ($id_pojazdu<=0 || $id_mechanika<=0 || $opis=="" || $status_naprawy=="" || $data_przyjecia=="" || $koszt_naprawy=="")// sprawdzenie czy wymagane pola są uzupełnione
        {
            $blad = "Uzupełnij wymagane pola naprawy.";
        }
        else    
        {
            $opis_sql = mysqli_real_escape_string($polaczenie, $opis); // zabezpieczenie tekstów przed zepsuciem zapytania SQL)
            $status_sql = mysqli_real_escape_string($polaczenie, $status_naprawy);
            $data_p_sql = mysqli_real_escape_string($polaczenie, $data_przyjecia);

        if ($data_zakonczenia == "") // jeśli data zakonczenia naprawy nie jest podana
        {
            $zapytanie_sql = "UPDATE naprawy SET id_pojazdu=$id_pojazdu, id_mechanika=$id_mechanika,
                            opis_usterki='$opis_sql', status='$status_sql',
                            data_przyjecia='$data_p_sql', data_zakonczenia=NULL, koszt=".(float)$koszt_naprawy."
                            WHERE id_naprawy=$id";
        }
        else
        {
            $data_z_sql = mysqli_real_escape_string($polaczenie, $data_zakonczenia);
            
            $zapytanie_sql = "UPDATE naprawy SET id_pojazdu=$id_pojazdu, id_mechanika=$id_mechanika,
                            opis_usterki='$opis_sql', status='$status_sql',
                            data_przyjecia='$data_p_sql', data_zakonczenia='$data_z_sql', koszt=".(float)$koszt_naprawy." // aktualizacja naprawy
                            WHERE id_naprawy=$id";
        }

        mysqli_query($polaczenie, $zapytanie_sql);
        $klient_komunikat = "Zapisano zmiany.";
    }
}

// szukajka napraw
$szukany_tekst = "";// domyslnie pusty tekst do wyszukania
if (isset($_GET["szukany_tekst"])) $szukany_tekst = trim($_GET["szukany_tekst"]); // pobranie tekstu do wyszukania
?>

<h2>Naprawy</h2>

<?php if ($klient_komunikat!="") 
    {
        echo "<p><b>$klient_komunikat</b></p>";
    }
      if ($blad!="") 
      {
        echo "<p style='color:red;'><b>$blad</b></p>";
    } 
?>
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
    
$wynik_pojazdy = mysqli_query
    ($polaczenie, "SELECT poj.id_pojazdu, poj.rejestracja, poj.vin, poj.marka, poj.model, 
                kli.nazwisko, kli.imie
                FROM pojazdy poj
                JOIN klienci kli ON poj.id_klienta = kli.id_klienta
                ORDER BY poj.id_pojazdu DESC" /* pobranie listy pojazdów z klientami */
    );
    
    while ($wynik_pojazdy && ($pojazd = mysqli_fetch_row($wynik_pojazdy))) 
    {
        echo "<option value='".$pojazd[0]."'>".$pojazd[3]." ".$pojazd[4]." | ".$pojazd[1]." | ".$pojazd[5]." ".$pojazd[6]."</option>";
    }
    
    if ($wynik_pojazdy) mysqli_free_result($wynik_pojazdy);
?>
</select>
<br><br>

  Mechanik:
  <select name="id_mechanika">
<?php
    $wynik_listy_mechanikow = mysqli_query($polaczenie, "SELECT id_mechanika, nazwisko, imie FROM mechanicy ORDER BY nazwisko");// pobranie listy mechaników


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
for ($i=0; $i<count($statusy_naprawy); $i++) // generowanie opcji statusow z tablicy
    {
        echo "<option value=".$statusy_naprawy[$i]."'>".$statusy_naprawy[$i]."</option>";
    }
?>
</select>
<br><br>

Data przyjęcia:
<input type="date" name="data_przyjecia" value="<?php echo date('Y-m-d'); ?>">
<br><br>

Data zakończenia:
<input type="date" name="data_zakonczenia">
<br><br>

Koszt:
<input type="number" step="0.01" name="koszt" value="0.00">
<br><br>

Opis usterki:<br>
<textarea name="opis" rows="4" cols="60"></textarea>
<br><br>

<button type="submit" name="dodaj" value="1">Dodaj</button>
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
$warunek_wyszukiwania = "";// domyślnie brak warunku wyszukiwania
if ($szukany_tekst != "") 
    {
        $q = mysqli_real_escape_string($polaczenie, $szukany_tekst);
        $warunek_wyszukiwania = "WHERE kli.nazwisko LIKE '%$q%' 
                                 poj.vin LIKE '%$q%' 
                                 OR poj.rejestracja LIKE '%$q%'";
    }

$zapytanie_sql = "SELECT nap.id_naprawy,
                         poj.id_pojazdu, poj.marka, poj.model, poj.rejestracja, poj.vin,
                         kli.nazwisko, kli.imie,
                         mech.id_mechanika, mech.nazwisko, mech.imie,
                         nap.status, nap.data_przyjecia, nap.data_zakonczenia, nap.koszt, nap.opis_usterki
                  FROM naprawy nap
                  JOIN pojazdy poj ON nap.id_pojazdu = poj.id_pojazdu
                  JOIN klienci kli ON poj.id_klienta = kli.id_klienta
                  JOIN mechanicy mech ON nap.id_mechanika = mech.id_mechanika
                  $warunek_wyszukiwania
                  ORDER BY nap.id_naprawy DESC";

$wynik_zapytania = mysqli_query($polaczenie, $zapytanie_sql);

while ($wynik_zapytania && ($numer_wiersza = mysqli_fetch_row($wynik_zapytania))) 
{
    echo "<tr>";
    echo "<form method='post'>";

    echo "<td>".$numer_wiersza[0]."<input type='hidden' name='id' value='".$numer_wiersza[0]."'></td>";
    echo "<td><input type='number' name='id_pojazdu' value='".$numer_wiersza[1]."'></td>";

    echo "<td>".$numer_wiersza[2]." ".$numer_wiersza[3]."<br>".$numer_wiersza[4]."<br><small>VIN: ".$numer_wiersza[5]."</small></td>";

    echo "<td>".$numer_wiersza[6]." ".$numer_wiersza[7]."</td>";

    echo "<td><input type='number' name='id_mechanika' value='".$numer_wiersza[8]."'></td>";

    echo "<td>".$numer_wiersza[9]." ".$numer_wiersza[10]."</td>";

    echo "<td><input type='text' name='status' value='".$numer_wiersza[11]."'></td>";

    echo "<td>
            <small>Przyjęcie</small><br>
            <input type='date' name='data_przyjecia' value='".$numer_wiersza[12]."'><br>
            <small>Zakończenie</small><br>
            <input type='date' name='data_zakonczenia' value='".$numer_wiersza[13]."'>
          </td>";

    echo "<td><input type='number' step='0.01' name='koszt' value='".$numer_wiersza[14]."'></td>";

    echo "<td><textarea name='opis' rows='3' cols='30'>".$numer_wiersza[15]."</textarea></td>";

    echo "<td>
            <button type='submit' name='zapisz' value='1'>Zapisz</button>
            <button type='submit' name='usun' value='1' onclick=\"return confirm('Usunąć?')\">Usuń</button>
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








