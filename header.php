<?php
require_once "auth.php";
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Serwis samochodowy</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="top">
  <div><b>Serwis samochodowy</b></div>
  <div>
    <?php if (czy_zalogowany()) { ?>
      Zalogowany: <b><?php echo h($_SESSION["login"]); ?></b> |
      <a href="index.php">Menu</a> |
      <a href="logout.php">Wyloguj</a>
    <?php } else { ?>
      <a href="login.php">Zaloguj</a>
    <?php } ?>
  </div>
</div>
<div class="box">