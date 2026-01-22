<?php
session_start();// start sesji

if (!isset($_SESSION["login"])) // sprawdzamy czy jestesmy zalogowani
{
    header("Location: login.php");// jeżeli nie to wracamy do login.php tam jest stronka logowania
    exit();
}
?>
<!doctype html>       <!-- PAMIETAJ W HTML TAKI KOMENTARZ BO ZNOWU GODZINA STRATY -->
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Serwis - menu</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="box">
  <div class="card menu-card">

    <div class="app-title">Serwis Samochodowy Olejnik S.A</div>
    <div class="app-subtitle">Panel zarządzania serwisem</div>
    <hr class="sep">

    <h2>Menu</h2>

    <p class="muted">Zalogowany jako: <b><?php echo $_SESSION["login"]; ?></b></p>

    <div class="menu-grid">
      <a class="menu-item" href="mechanicy.php">👨‍🔧 Mechanicy</a>
      <a class="menu-item" href="klienci.php">👤 Klienci</a>
      <a class="menu-item" href="pojazdy.php">🚗 Pojazdy</a>
      <a class="menu-item" href="naprawy.php">🛠️ Naprawy</a>
    </div>

    <p style="margin-top:15px;">
      <a class="btn danger" href="wyloguj.php">Wyloguj</a>
    </p>

    <?php
    include "stopka.html";
    ?>

  </div>
</div>

</body>
</html>
