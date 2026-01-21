<?php
function wczytaj_konfig() {
    $cfg = array();
    $wskaznikDoPliku = fopen("config.txt", "r");
    if ($wskaznikDoPliku == false) {
        return $cfg;
    }

    while (!feof($wskaznikDoPliku)) {
        // $linia = "klucz = wartość" 
        $linia = trim(fgets($wskaznikDoPliku));
        if ($linia == "") continue;

        // $segmenty = ["klucz ", " wartość"]
        $segmenty = explode("=", $linia);
        if (count($segmenty) == 2) {
            $cfg[trim($segmenty[0])] = trim($segmenty[1]);
        }
    }
    fclose($wskaznikDoPliku);
    return $cfg;
}

function db_polacz() {
    $cfg = wczytaj_konfig();
    $host = isset($cfg["host"]) ? $cfg["host"] : "127.0.0.1";
    $user = isset($cfg["user"]) ? $cfg["user"] : "root";
    $pass = isset($cfg["pass"]) ? $cfg["pass"] : "";
    $db   = isset($cfg["db"])   ? $cfg["db"]   : "serwis";

    $pol = mysqli_connect($host, $user, $pass, $db);
    if ($pol == false) {
        die("Błąd połączenia z bazą!");
    }
    mysqli_set_charset($pol, "utf8mb4");
    return $pol;
}
function h($t) {
    return htmlspecialchars($t, ENT_QUOTES, "UTF-8");
}


function pobierz_post($nazwa) {
    if (isset($_POST[$nazwa])) return trim($_POST[$nazwa]);
    return "";
}

function pobierz_get($nazwa) {
    if (isset($_GET[$nazwa])) return trim($_GET[$nazwa]);
    return "";
}
?>
