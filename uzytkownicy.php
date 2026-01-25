<?php
session_start();
if (!isset($_SESSION["login"])) 
    { header("Location: login.php");
        exit();
    }

// tylko admin
if ($_SESSION["login"] !== "admin") // sprawdz czy admin
     { echo "<b>Brak uprawnień.</b>";
        exit(); 
    }

include "naglowek.html";
include "baza.php";
$polaczenie = polacz_z_baza(); // funkcja z pliku baza.php

$komunikat = "";
$blad = "";

// Usuwanie mechnika
if (isset($_POST["usun"]))
{
    $id = (int)$_POST["id"];
    $wynik_sprawdzenia_loginu = mysqli_query// sprawdzamy login użytkownika, żeby nie pozwolić usunąć admina
    (
        $polaczenie, "SELECT login FROM uzytkownicy WHERE id_uzytkownika=$id" // pobierz login użytkownika o danym id
    );

    $login = "";
    if ($wynik_sprawdzenia_loginu && ($wiersz = mysqli_fetch_row($wynik_sprawdzenia_loginu))) //jeśli zapytanie się powiodło i jest wynik
    {
        $login = $wiersz[0]; // pobierz login z wyniku zapytania
    }

    if ($wynik_sprawdzenia_loginu) //jezeli wsio ok w zapytani
    {
        mysqli_free_result($wynik_sprawdzenia_loginu); // to czyscimy pamiec
    }

    if ($login === "admin") //jezeli login to admin to nie pozwalamy usunąć
    {
        $blad = "Nie można usunąć konta admin.";
    }
    else
    {
        mysqli_query($polaczenie, "DELETE FROM uzytkownicy WHERE id_uzytkownika=$id");
        $komunikat = "Usunięto użytkownika.";
    }
}

//dodawanie uzytkownika
if (isset($_POST["dodaj"]))
{
    $login = "";
    $haslo = "";

    if (isset($_POST["login"])) $login = trim($_POST["login"]); 
    if (isset($_POST["haslo"])) $haslo = trim($_POST["haslo"]);

    if ($login=="" || $haslo=="") //jezeli login lub haslo puste
        {
            $blad = "Uzupełnij login i hasło.";
        }
        else if (strpos($login, " ") !== false) //jak nie to sprawdzamy czy login nie zawiera spacji
        {
            $blad = "Login nie może zawierać spacji.";
        }
        else
        {
            $login_sql = mysqli_real_escape_string($polaczenie, $login); // zabezpieczenie przed popsuciem zapytania SQL
            $hash = password_hash($haslo, PASSWORD_DEFAULT);
            $hash_sql = mysqli_real_escape_string($polaczenie, $hash);

            $ok = mysqli_query($polaczenie,
                "INSERT INTO uzytkownicy(login, haslo_hash) VALUES ('$login_sql', '$hash_sql')" // dodajemy użytkownika do bazy
            );

            if ($ok)
            {
                $komunikat = "Dodano pracownika (konto użytkownika).";
            }
            else
            {
                $blad = "Nie udało się dodać (może login już istnieje).";
            }
        }
}

//reset hasla
if (isset($_POST["reset_hasla"])) 
{
    $id = (int)$_POST["id"];
    $nowe = isset($_POST["nowe_haslo"]) ? trim($_POST["nowe_haslo"]) : "";

    if ($nowe=="") //jezeli nowe haslo puste
        {
            $blad = "Podaj nowe hasło."; // ustaw blad
        } else 
        {
            $hash = password_hash($nowe, PASSWORD_DEFAULT);//  generuj hash nowego hasla
            $hash_sql = mysqli_real_escape_string($polaczenie, $hash); 
            mysqli_query($polaczenie, "UPDATE uzytkownicy SET haslo_hash='$hash_sql' WHERE id_uzytkownika=$id");// aktualizuj haslo w bazie
            $komunikat = "Zmieniono hasło.";
        }
}
?>

<h2>Pracownicy (użytkownicy systemu)</h2>

<?php
if ($komunikat!="") echo "<div class='msg'><b>$komunikat</b></div>";
if ($blad!="") echo "<div class='err'><b>$blad</b></div>";
?>

<h3>Dodaj pracownika</h3>
<form method="post" autocomplete="off">
  Login: <input type="text" name="login"><br><br>
  Hasło: <input type="password" name="haslo"><br><br>
  <button class="btn" type="submit" name="dodaj" value="1">Dodaj</button>
</form>

<h3>Lista użytkowników</h3>
<table border="1" cellpadding="6">
<tr>
  <th>ID</th>
  <th>Login</th>
  <th>Akcje</th>
</tr>

<?php
$wynik = mysqli_query($polaczenie, "SELECT id_uzytkownika, login FROM uzytkownicy ORDER BY id_uzytkownika DESC");

while ($wynik && ($u = mysqli_fetch_row($wynik)))
{
    $id = $u[0];
    $login = $u[1];
?>
<tr>
  <td><?php echo $id; ?></td>
  <td><?php echo $login; ?></td>

  <td>
    <form method="post" style="display:inline-block; margin-right:10px;"> <!-- to jest formularz do usuwania użytkownika -->
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <button class="btn danger" type="submit" name="usun" value="1"
        onclick="return confirm('Usunąć?')">Usuń</button>
    </form>
    <form method="post" style="display:inline-block;">  <!-- to jest formularz do resetowania hasła -->
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="password" name="nowe_haslo" placeholder="Nowe hasło"
            style="max-width:220px; display:inline-block;">
        <button class="btn" type="submit" name="reset_hasla" value="1">Reset hasła</button>
    </form>
  </td>
</tr>
<?php
}
if ($wynik) mysqli_free_result($wynik);

mysqli_close($polaczenie);
?>
</table>

<?php include "stopka.html"; ?>
