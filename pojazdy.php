<?php
session_start();
if (!isset($_SESSION["login"])) // jeśli nie zalogowano
    {
        header("Location: login.php"); // przekieruj do strony logowania
        exit();
    }

include "naglowek.html";
include "baza.php";
$polaczenie = polacz_z_baza(); // nawiązanie połączenia z bazą

$klient_komunikat = "";
$blad = "";

// usuwanie pojazdu
if (isset($_POST["usun"])) 
{
    $id = (int)$_POST["id"];

    $ile = 0;
    $wynik_zapytania = mysqli_query($polaczenie, "SELECT COUNT(*) FROM naprawy WHERE id_pojazdu=$id"); // sprawdzenie powiązanych napraw
    if ($wynik_zapytania) 
    {
        $numer_wiersza = mysqli_fetch_row($wynik_zapytania);// pobranie liczby powiązanych napraw
        $ile = (int)$numer_wiersza[0];
        mysqli_free_result($wynik_zapytania);// zwolnienie pamięci
    }

    if ($ile > 0) 
    {
        $blad = "Nie można usunąć pojazdu: istnieją powiązane naprawy.";
    } 
    else 
    {
        mysqli_query($polaczenie, "DELETE FROM pojazdy WHERE id_pojazdu=$id");
        $klient_komunikat = "Usunięto pojazd.";
    }
}

// dodawanie pojazdow
if (isset($_POST["dodaj"])) 
{
    $id_klienta = 0;
    $marka = "";
    $model = "";
    $rok = 0;
    $vin = "";
    $rejestracja = "";

    if (isset($_POST["id_klienta"])) $id_klienta = (int)$_POST["id_klienta"]; // pobranie danych z formularza
    if (isset($_POST["marka"])) $marka = trim($_POST["marka"]);
    if (isset($_POST["model"])) $model = trim($_POST["model"]);
    if (isset($_POST["rok"])) $rok = (int)$_POST["rok"];
    if (isset($_POST["vin"])) $vin = trim($_POST["vin"]);
    if (isset($_POST["rejestracja"])) $rejestracja = trim($_POST["rejestracja"]);

    if ($id_klienta <= 0 || $marka=="" || $model=="" || $rok <= 0 || $vin=="" || $rejestracja=="") // czy wszystkie pola są wypełnione
    {
        $blad = "Uzupełnij wszystkie pola pojazdu.";
    } 
    else 
    {
        $marka_sql = mysqli_real_escape_string($polaczenie, $marka); 
        $model_sql = mysqli_real_escape_string($polaczenie, $model);
        $vin_sql = mysqli_real_escape_string($polaczenie, $vin);
        $rejestracja_sql = mysqli_real_escape_string($polaczenie, $rejestracja);

        $zapytanie_sql = "INSERT INTO pojazdy(id_klienta, marka, model, rok, vin, rejestracja)
                          VALUES ($id_klienta, '$marka_sql', '$model_sql', $rok, '$vin_sql', '$rejestracja_sql')"; // zapytanie dodające pojazd

        mysqli_query($polaczenie, $zapytanie_sql);// dodanie pojazdu do bazy
        $klient_komunikat = "Dodano pojazd.";
    }
}  


// edycja dodanego pojazdu
if (isset($_POST["zapisz"])) 
{
    $id = (int)$_POST["id"]; // pobranie ID pojazdu

    $id_klienta = 0;
    $marka = "";
    $model = "";
    $rok = 0;
    $vin = "";
    $rejestracja = "";

    if (isset($_POST["id_klienta"])) $id_klienta = (int)$_POST["id_klienta"]; // pobranie danych z formularza
    if (isset($_POST["marka"])) $marka = trim($_POST["marka"]);
    if (isset($_POST["model"])) $model = trim($_POST["model"]); 
    if (isset($_POST["rok"])) $rok = (int)$_POST["rok"];
    if (isset($_POST["vin"])) $vin = trim($_POST["vin"]);
    if (isset($_POST["rejestracja"])) $rejestracja = trim($_POST["rejestracja"]);

    if ($id_klienta<=0 || $marka=="" || $model=="" || $rok<=0 || $vin=="" || $rejestracja=="")// czy wszystkie pola są wypełnione 
    {
        $blad = "Uzupełnij wszystkie pola pojazdu.";
    } 
    else 
    {
        $marka_sql = mysqli_real_escape_string($polaczenie, $marka);
        $model_sql = mysqli_real_escape_string($polaczenie, $model);
        $vin_sql = mysqli_real_escape_string($polaczenie, $vin);
        $rejestracja_sql = mysqli_real_escape_string($polaczenie, $rejestracja);

        $zapytanie_sql = "UPDATE pojazdy
                          SET id_klienta=$id_klienta, marka='$marka_sql', model='$model_sql',
                              rok=$rok, vin='$vin_sql', rejestracja='$rejestracja_sql'
                          WHERE id_pojazdu=$id"; // aktualizacja danych pojazdu

        mysqli_query($polaczenie, $zapytanie_sql);// wykonanie zapytania
        $klient_komunikat = "Zapisano zmiany.";
    }
}
?>

<h2>Pojazdy</h2>

<?php
if ($klient_komunikat != "") 
    {
        echo "<p><b>$klient_komunikat</b></p>";
    }

if ($blad != "") 
    {
        echo "<p style='color:red;'><b>$blad</b></p>";
    }
?>

<h3>Dodaj pojazd</h3>
<form method="post">
  
  Klient:
  <select name="id_klienta">
<?php
     $wynik_klienci = mysqli_query($polaczenie,
                    "SELECT id_klienta, imie, nazwisko FROM klienci ORDER BY nazwisko"
);

while ($wynik_klienci && ($klient = mysqli_fetch_row($wynik_klienci)))
    {
        $id = $klient[0];
        $imie = $klient[1];
        $nazwisko = $klient[2];

        echo "<option value='$id'>$nazwisko $imie (ID $id)</option>";
    }

    if ($wynik_klienci)
    {
        mysqli_free_result($wynik_klienci);
    }
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
$zapytanie_sql = "SELECT pojazdy.id_pojazdu, pojazdy.id_klienta,
                klienci.nazwisko, klienci.imie,
                pojazdy.marka, pojazdy.model, pojazdy.rok, pojazdy.vin, pojazdy.rejestracja
                FROM pojazdy
                JOIN klienci ON pojazdy.id_klienta = klienci.id_klienta
                ORDER BY pojazdy.id_pojazdu DESC"; // pobranie listy pojazdów wraz z danymi klientow

$wynik_zapytania = mysqli_query($polaczenie, $zapytanie_sql);

while ($wynik_zapytania && ($numer_wiersza = mysqli_fetch_row($wynik_zapytania))) // wyświetlenie pojazdów w pętli
{
    $id = $numer_wiersza[0]; // id pojazdu
    $id_klienta = $numer_wiersza[1];  // id klienta

    $nazwisko = $numer_wiersza[2]; // nazwisko klienta 
    $imie = $numer_wiersza[3];

    $marka = $numer_wiersza[4];
    $model = $numer_wiersza[5];
    $rok = $numer_wiersza[6];
    $vin = $numer_wiersza[7];
    $rejestracja = $numer_wiersza[8];
?>
<tr>
  <form method="post">
    <td>
      <?php echo $id; ?>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
    </td>

    <td><input type="number" name="id_klienta" value="<?php echo $id_klienta; ?>"></td>

    <td><?php echo $nazwisko . " " . $imie; ?></td>

    <td><input type="text" name="marka" value="<?php echo $marka; ?>"></td>
    <td><input type="text" name="model" value="<?php echo $model; ?>"></td>

    <td><input type="number" name="rok" value="<?php echo $rok; ?>"></td>

    <td><input type="text" name="vin" value="<?php echo $vin; ?>"></td>

    <td><input type="text" name="rejestracja" value="<?php echo $rejestracja; ?>"></td>

    <td>
      <input type="submit" name="zapisz" value="Zapisz">
      <input type="submit" name="usun" value="Usuń" onclick="return confirm('Usunąć?')">
    </td>
  </form>
</tr>
<?php
}

if ($wynik_zapytania) mysqli_free_result($wynik_zapytania);
mysqli_close($polaczenie);
?>
</table>

<?php
include "stopka.html";
?>
