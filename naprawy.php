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
if (isset($_POST["usun"])) // jeśli naciśnięto przycisk usuń
    {
        $id = (int)$_POST["id"];
        mysqli_query($polaczenie, "DELETE FROM naprawy WHERE id_naprawy=$id"); // usunięcie naprawy z bazy
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

    if (isset($_POST["id_pojazdu"])) $id_pojazdu = (int)$_POST["id_pojazdu"]; // pobranie wartości z formularza
    if (isset($_POST["id_mechanika"])) $id_mechanika = (int)$_POST["id_mechanika"]; 
    if (isset($_POST["opis"])) $opis = trim($_POST["opis"]);
    if (isset($_POST["status"])) $status_naprawy = trim($_POST["status"]);
    if (isset($_POST["data_przyjecia"])) $data_przyjecia = trim($_POST["data_przyjecia"]);
    if (isset($_POST["data_zakonczenia"])) $data_zakonczenia = trim($_POST["data_zakonczenia"]);
    if (isset($_POST["koszt"])) $koszt_naprawy = trim($_POST["koszt"]);

    if ($id_pojazdu<=0 || $id_mechanika<=0 || $opis=="" || $status_naprawy=="" || $data_przyjecia=="" || $koszt_naprawy=="") // sprawdzenie czy wymagane pola są uzupełnione
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
        echo "<p><b>$klient_komunikat</b></p>"; // wyświetlenie komunikatu
    }
      if ($blad!="") 
      {
        echo "<p style='color:red;'><b>$blad</b></p>"; // wyświetlenie błędu
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
    // dopóki mamy wynik zapytania ($wynik_pojazdy) i da sie pobrac kolejny wiersz z bazy
    while ($wynik_pojazdy && ($pojazd = mysqli_fetch_row($wynik_pojazdy)))
        {
            $id = $pojazd[0];

            $marka_model = $pojazd[3] . " " . $pojazd[4];
            $rejestracja = $pojazd[1];
            $wlasciciel  = $pojazd[5] . " " . $pojazd[6];

            echo "<option value='$id'>$marka_model | $rejestracja | $wlasciciel</option>";
        }

    if ($wynik_pojazdy) 
        {
            mysqli_free_result($wynik_pojazdy);
        }
?>
</select>
<br><br>

  Mechanik:
  <select name="id_mechanika">
<?php
    $wynik_listy_mechanikow = mysqli_query($polaczenie,
     "SELECT id_mechanika, nazwisko, imie FROM mechanicy ORDER BY nazwisko");// pobranie listy mechaników

        while ($wynik_listy_mechanikow && ($mechanik = mysqli_fetch_row($wynik_listy_mechanikow)))
            {
                $id_mechanika = $mechanik[0];
                $nazwisko = $mechanik[1];
                $imie = $mechanik[2];

                echo "<option value='$id_mechanika'>ID $id_mechanika | $nazwisko $imie</option>";
            }

    if ($wynik_listy_mechanikow)
        {
            mysqli_free_result($wynik_listy_mechanikow);
        }
?>

</select>
<br><br>
 
Status:
<select name="status">
<?php
for ($i=0; $i<count($statusy_naprawy); $i++) // tworzymy liste rozwijana statusuw kazdy status z tablicy jako osobna opcja
    {
        echo "<option value='".$statusy_naprawy[$i]."'>".$statusy_naprawy[$i]."</option>";
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
    $szukany_tekst_sql = mysqli_real_escape_string($polaczenie, $szukany_tekst); // zabezpieczamy tekst wpisany przez użytkownika przed SQL Injection
    
    $warunek_wyszukiwania = "WHERE (kli.nazwisko LIKE '%$szukany_tekst_sql%'
                            OR poj.vin LIKE '%$szukany_tekst_sql%'
                            OR poj.rejestracja LIKE '%$szukany_tekst_sql%')"; // tworzymy fragment zapytania SQL do wyszukiwania po nazwisku, VIN albo rejestracji
    }

$zapytanie_sql = "SELECT naprawy.id_naprawy,
                         pojazdy.id_pojazdu, pojazdy.marka, pojazdy.model, pojazdy.rejestracja, pojazdy.vin,
                         klienci.nazwisko, klienci.imie,
                         mechanicy.id_mechanika, mechanicy.nazwisko, mechanicy.imie,
                         naprawy.status, naprawy.data_przyjecia, naprawy.data_zakonczenia, naprawy.koszt, naprawy.opis_usterki
                  FROM naprawy
                  JOIN pojazdy ON naprawy.id_pojazdu = pojazdy.id_pojazdu
                  JOIN klienci ON pojazdy.id_klienta = klienci.id_klienta
                  JOIN mechanicy ON naprawy.id_mechanika = mechanicy.id_mechanika
                  $warunek_wyszukiwania
                  ORDER BY naprawy.id_naprawy DESC"; // Jeśli użytkownik wpisał tekst do wyszukiwania, dokładamy warunek WHERE (po nazwisku/VIN/rejestracji)


$wynik_zapytania = mysqli_query($polaczenie, $zapytanie_sql); // wykonanie zapytania SQL i pobranie wyników z bazy

while ($wynik_zapytania && ($numer_wiersza = mysqli_fetch_row($wynik_zapytania)))
{
    $id = $numer_wiersza[0];
    $id_pojazdu = $numer_wiersza[1];

    $marka = $numer_wiersza[2];
    $model = $numer_wiersza[3];
    $rejestracja = $numer_wiersza[4];
    $vin = $numer_wiersza[5];

    $nazwisko_klienta = $numer_wiersza[6];
    $imie_klienta = $numer_wiersza[7];

    $id_mechanika = $numer_wiersza[8];
    $nazwisko_mechanika = $numer_wiersza[9];
    $imie_mechanika = $numer_wiersza[10];

    $status = $numer_wiersza[11];
    $data_przyjecia = $numer_wiersza[12];
    $data_zakonczenia = $numer_wiersza[13];

    $koszt = $numer_wiersza[14];
    $opis = $numer_wiersza[15];
?>
<tr>
  <form method="post">
    <td>
      <?php echo $id; ?>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
    </td>
    <td>
      <input type="number" name="id_pojazdu" value="<?php echo $id_pojazdu; ?>">
    </td>
    <td>
      <?php echo $marka . " " . $model; ?><br>
      <?php echo $rejestracja; ?><br>
      <small>VIN: <?php echo $vin; ?></small>
    </td>
    <td>
      <?php echo $nazwisko_klienta . " " . $imie_klienta; ?>
    </td>
    <td>
      <input type="number" name="id_mechanika" value="<?php echo $id_mechanika; ?>">
    </td>
    <td>
      <?php echo $nazwisko_mechanika . " " . $imie_mechanika; ?>
    </td>
    <td>
      <input type="text" name="status" value="<?php echo $status; ?>">
    </td>
    <td>
      <small>Przyjęcie</small><br>
      <input type="date" name="data_przyjecia" value="<?php echo $data_przyjecia; ?>"><br>
    <small>Zakończenie</small><br>
      <input type="date" name="data_zakonczenia" value="<?php echo $data_zakonczenia; ?>">
    </td>
    <td>
      <input type="number" step="0.01" name="koszt" value="<?php echo $koszt; ?>">
    </td>
    <td>
      <textarea name="opis" rows="3" cols="30"><?php echo $opis; ?></textarea>
    </td>
    <td>
      <button type="submit" name="zapisz" value="1">Zapisz</button>
      <button type="submit" name="usun" value="1" onclick="return confirm('Usunąć?')">Usuń</button>
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








