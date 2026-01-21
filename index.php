<?php
require_once "auth.php";
wymagaj_logowania();
require_once "db.php";
include "header.php";
?>
<div class="card">
  <h2>Menu</h2>
  <ul>
    <li><a href="klienci.php">Klienci</a></li>
    <li><a href="pojazdy.php">Pojazdy</a></li>
    <li><a href="mechanicy.php">Mechanicy</a></li>
    <li><a href="naprawy.php">Naprawy + wyszukiwanie</a></li>
  </ul>
</div>
<?php include "footer.php"; ?>
