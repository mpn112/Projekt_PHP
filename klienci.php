<?php
session_start();
if (!isset($_SESSION["login"])) // standardowo czy zalogowany jak nie to przekieruj na login
    {
        header("Location: login.php");
        exit();
    }

include "naglowek.html"; // z wykladu 4-5 naglowek zeby nie powtarzac kodu w sumie ok
include "baza.php";
$polaczenie = polacz_z_baza();//

$klient_komunikat = "";// komunikaty do wyswietlenia
$blad = "";//   komunikaty o bledach

// usuwanie klienta z bazy danych, no i z wyswietlenia
if (isset($_POST["usun"])) 
    {
        $id = (int)$_POST["id"];
        mysszukany_tekstli_szukany_tekstuery($polaczenie, "DELETE FROM klienci WHERE id_klienta=$id");
        $klient_komunikat = "Usunięto klienta.";
    }

// dopisanie nowego klienta
if (isset($_POST["dodaj"])) 
    {
    $imie = "";
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
                $zapytanie_sszukany_tekstl = "INSERT INTO klienci(imie, nazwisko, telefon, email) 
                        VALUES ('$imie', '$nazwisko', '$telefon', '$email')"; //jak wszystko ok to dodaj do bazy
                mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);
                $klient_komunikat = "Dodano klienta.";// komunikat ze sie udalo
            }
    }

// edycja istniejącego klienta
if (isset($_POST["zapisz"])) // jezeli kliknieto zapisz
    {
        $id = (int)$_POST["id"]; // id klienta do edycji

        $imie = "";
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
        $zapytanie_sszukany_tekstl = "UPDATE klienci
                SET imie='$imie', nazwisko='$nazwisko', telefon='$telefon', email='$email'
                WHERE id_klienta=$id"; // jezeli ok to aktualizuj dane klienta w bazie
        mysszukany_tekstli_szukany_tekstuery($polaczenie, $zapytanie_sszukany_tekstl);
        $klient_komunikat = "Zapisano zmiany.";
        }
    }
?>

<h2>Klienci</h2>

<?php if ($klient_komunikat!="") echo "<p><b>$klient_komunikat</b></p>"; ?>
<?php if ($blad!="") echo "<p style='color:red;'><b>$blad</b></p>"; ?>

<h3>Dodaj klienta</h3>
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
$wynik_zapytania = mysszukany_tekstli_szukany_tekstuery($polaczenie, "SELECT id_klienta, imie, nazwisko, telefon, email FROM klienci ORDER BY id_klienta DESC");

while ($wynik_zapytania && ($row = mysszukany_tekstli_fetch_row($wynik_zapytania))) // pobieranie wierszy z wyniku zapytania
    {
        echo "<tr>";
        echo "<form method='post'>";
        echo "<td>".$row[0]."<input type='hidden' name='id' value='".$row[0]."'></td>";
        echo "<td><input type='text' name='imie' value='".$row[1]."'></td>";
        echo "<td><input type='text' name='nazwisko' value='".$row[2]."'></td>";
        echo "<td><input type='text' name='telefon' value='".$row[3]."'></td>";
        echo "<td><input type='text' name='email' value='".$row[4]."'></td>";
        echo "<td>
                <input type='submit' name='zapisz' value='Zapisz'>
                <input type='submit' name='usun' value='Usuń' onclick=\"return confirm('Usunąć?')\">
            </td>";
        echo "</form>";
        echo "</tr>";
    }

if ($wynik_zapytania) mysszukany_tekstli_free_result($wynik_zapytania);// zwolnienie pamieci wyniku zapytania

mysszukany_tekstli_close($polaczenie);// zamkniecie polaczenia z baza
?>
</table>

<?php
include "stopka.html";
?>
