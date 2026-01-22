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
</head>
<body>

<h2>Menu</h2>
<p>Zalogowany jako: <b><?php echo $_SESSION["login"]; ?></b></p> <!-- wyswietlamy kto jest zalogowany -->
<ul>
  <li><a href="mechanicy.php">Mechanicy</a></li>
  <li><a href="klienci.php">Klienci</a></li>
  <li><a href="pojazdy.php">Pojazdy</a></li>
  <li><a href="naprawy.php">Naprawy</a></li>
</ul>

<p><a href="logout.php">Wyloguj</a></p>

</body>
</html>
