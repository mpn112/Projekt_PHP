<?php
session_start();

if (!isset($_SESSION["login"])) 
    {
        header("Location: login.php");
        exit();
    }

// tylko admin
if ($_SESSION["login"] !== "admin") 
    {
        echo "<p><b>Brak uprawnień.</b></p>";
        exit();
    }

include "naglowek.html";
include "baza.php";

$komunikat = "";
$blad = "";

// wczytanie aktualnej konfiguracji
$konfiguracja = wczytaj_konfiguracje();

$serwer = isset($konfiguracja["serwer"]) ? $konfiguracja["serwer"] : "127.0.0.1";
$uzytkownik = isset($konfiguracja["uzytkownik"]) ? $konfiguracja["uzytkownik"] : "root";
$haslo = isset($konfiguracja["haslo"]) ? $konfiguracja["haslo"] : "";
$baza = isset($konfiguracja["baza"]) ? $konfiguracja["baza"] : "serwis";

if (isset($_POST["zapisz_cfg"]))
{
    $serwer = "";
    $uzytkownik = "";
    $haslo = "";
    $baza = "";

    if (isset($_POST["serwer"])) $serwer = trim($_POST["serwer"]);
    if (isset($_POST["uzytkownik"])) $uzytkownik = trim($_POST["uzytkownik"]);
    if (isset($_POST["haslo"])) $haslo = trim($_POST["haslo"]);
    if (isset($_POST["baza"])) $baza = trim($_POST["baza"]);

    if ($serwer=="" || $uzytkownik=="" || $baza=="")
      {
          $blad = "Uzupełnij: serwer, użytkownik i nazwa bazy.";
      }
      else
      {
          $nowa_konfiguracja = array();
          $nowa_konfiguracja["serwer"] = $serwer;
          $nowa_konfiguracja["uzytkownik"] = $uzytkownik;
          $nowa_konfiguracja["haslo"] = $haslo;
          $nowa_konfiguracja["baza"] = $baza;

          if (zapisz_konfiguracje($nowa_konfiguracja))
          {
              $komunikat = "Zapisano konfigurację do konfiguracja.txt";
          }
          else
          {
              $blad = "Nie udało się zapisać konfiguracja.txt (sprawdź uprawnienia).";
          }
      }
}
?>

<h2>Ustawienia bazy (tylko admin)</h2>

<?php
if ($komunikat != "") echo "<div class='msg'><b>$komunikat</b></div>";
if ($blad != "") echo "<div class='err'><b>$blad</b></div>";
?>

<form method="post" autocomplete="off">
  <div class="grid2">
    <div>
      <label>Serwer</label>
      <input type="text" name="serwer" value="<?php echo htmlspecialchars($serwer); ?>">
    </div>

    <div>
      <label>Użytkownik</label>
      <input type="text" name="uzytkownik" value="<?php echo htmlspecialchars($uzytkownik); ?>">
    </div>
  </div>

  <br>

  <div class="grid2">
    <div>
      <label>Hasło</label>
      <input type="text" name="haslo" value="<?php echo htmlspecialchars($haslo); ?>">
    </div>

    <div>
      <label>Nazwa bazy</label>
      <input type="text" name="baza" value="<?php echo htmlspecialchars($baza); ?>">
    </div>
  </div>

  <br>

  <button class="btn" type="submit" name="zapisz_cfg" value="1">Zapisz</button>
</form>

<?php include "stopka.html"; ?>
