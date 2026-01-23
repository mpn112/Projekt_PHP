<?php
session_start();
if (!isset($_SESSION["login"])) { header("Location: login.php"); exit(); }

// tylko admin
if ($_SESSION["login"] !== "admin") { echo "<b>Brak uprawnień.</b>"; exit(); }

include "naglowek.html";
include "baza.php";
$polaczenie = polacz_z_baza();

$komunikat = "";
$blad = "";

/* USUWANIE */
if (isset($_POST["usun"])) {
    $id = (int)$_POST["id"];

    // nie pozwól usunąć admina
    $q = mysqli_query($polaczenie, "SELECT login FROM uzytkownicy WHERE id_uzytkownika=$id");
    $login = "";
    if ($q && ($r = mysqli_fetch_row($q))) $login = $r[0];
    if ($q) mysqli_free_result($q);

    if ($login === "admin") {
        $blad = "Nie można usunąć konta admin.";
    } else {
        mysqli_query($polaczenie, "DELETE FROM uzytkownicy WHERE id_uzytkownika=$id");
        $komunikat = "Usunięto użytkownika.";
    }
}

/* DODAWANIE */
if (isset($_POST["dodaj"])) {
    $login = isset($_POST["login"]) ? trim($_POST["login"]) : "";
    $haslo = isset($_POST["haslo"]) ? trim($_POST["haslo"]) : "";

    if ($login=="" || $haslo=="") {
        $blad = "Uzupełnij login i hasło.";
    } else if (preg_match("/\s/", $login)) {
        $blad = "Login nie może zawierać spacji.";
    } else {
        $login_sql = mysqli_real_escape_string($polaczenie, $login);
        $hash = password_hash($haslo, PASSWORD_DEFAULT);
        $hash_sql = mysqli_real_escape_string($polaczenie, $hash);

        $ok = mysqli_query($polaczenie,
            "INSERT INTO uzytkownicy(login, haslo_hash) VALUES ('$login_sql', '$hash_sql')"
        );

        if ($ok) $komunikat = "Dodano pracownika (konto użytkownika).";
        else $blad = "Nie udało się dodać (może login już istnieje).";
    }
}

/* RESET HASŁA */
if (isset($_POST["reset_hasla"])) {
    $id = (int)$_POST["id"];
    $nowe = isset($_POST["nowe_haslo"]) ? trim($_POST["nowe_haslo"]) : "";

    if ($nowe=="") {
        $blad = "Podaj nowe hasło.";
    } else {
        $hash = password_hash($nowe, PASSWORD_DEFAULT);
        $hash_sql = mysqli_real_escape_string($polaczenie, $hash);
        mysqli_query($polaczenie, "UPDATE uzytkownicy SET haslo_hash='$hash_sql' WHERE id_uzytkownika=$id");
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
while ($wynik && ($u = mysqli_fetch_row($wynik))) {
    echo "<tr>";
    echo "<td>".$u[0]."</td>";
    echo "<td>".$u[1]."</td>";
    echo "<td>";

    echo "<form method='post' style='display:inline-block; margin-right:10px;'>
            <input type='hidden' name='id' value='".$u[0]."'>
            <button class='btn danger' type='submit' name='usun' value='1' onclick=\"return confirm('Usunąć?')\">Usuń</button>
          </form>";

    echo "<form method='post' style='display:inline-block;'>
            <input type='hidden' name='id' value='".$u[0]."'>
            <input type='password' name='nowe_haslo' placeholder='Nowe hasło' style='max-width:220px; display:inline-block;'>
            <button class='btn' type='submit' name='reset_hasla' value='1'>Reset hasła</button>
          </form>";

    echo "</td>";
    echo "</tr>";
}
if ($wynik) mysqli_free_result($wynik);

mysqli_close($polaczenie);
?>
</table>

<?php include "stopka.html"; ?>
