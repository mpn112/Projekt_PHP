<?php
session_start();

function czy_zalogowany() {
    return isset($_SESSION["login"]);
}

function wymagaj_logowania() {
    if (!czy_zalogowany()) {
        header("Location: login.php");
        exit();
    }
}
?>
