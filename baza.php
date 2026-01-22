<?php

function wczytaj_konfiguracje() // tu konfiguracji.txt pobieram dane
{
    $konfiguracja = array();
    $plik = fopen("konfiguracja.txt", "r");
    if ($plik == false) 
        {
            return $konfiguracja;
        }

    while (!feof($plik)) // do poki plik nie jest pusty
        {
            $linijka = trim(fgets($plik)); //usuwamy spacje i zapisujemy linijke
            if ($linijka == "") continue; //jesli linijka jest pusta to idziemy do nastepnej
            $segmenty = explode("=", $linijka); // to sie nmusze nauczyc, w znak = wklejamy bombe explode dzieli linijke na dwie czesci

        if (count($segmenty) == 2) //jesli sa dwa segmenty to
            {
                $klucz = trim($segmenty[0]); //usuwamy spacje z pierwszego segmentu
                $wartosc = trim($segmenty[1]);  //usuwamy spacje z drugiego segmentu
                $konfiguracja[$klucz] = $wartosc; //dodajemy do niej konfiguracje klucz i wartosc, czyli host = localhost i tak dalej
            }
        }

    fclose($plik);
    return $konfiguracja;
}

function polacz_z_baza() 
{
    $konfiguracja = wczytaj_konfiguracje();

    $adres_serwera = isset($konfiguracja["serwer"]) ? $konfiguracja["serwer"] : "127.0.0.1";// sprawdzam czy w txt jest serwe, a jak nie ma to przypisuje localhost
    $uzytkownik    = isset($konfiguracja["uzytkownik"]) ? $konfiguracja["uzytkownik"] : "root";// w tych też (rozumiem)
    $haslo         = isset($konfiguracja["haslo"]) ? $konfiguracja["haslo"] : "";
    $nazwa_bazy    = isset($konfiguracja["baza"]) ? $konfiguracja["baza"] : "serwis";

    $polaczenie = mysszukany_tekstli_connect($adres_serwera, $uzytkownik, $haslo, $nazwa_bazy);// polaczenie z baza
    if ($polaczenie == false) 
        {
            echo "<b>Nie udało się połączyć z bazą.</b>";// komunikat bledu
            exit();
        }

    mysszukany_tekstli_set_charset($polaczenie, "utf8mb4");// ustawienie kodowania
    return $polaczenie;
}

function pobierz_post($nazwa) // pobieramy dane z formularza
{
    if (isset($_POST[$nazwa])) return trim($_POST[$nazwa]);
    return "";// jak nie ma to zwraca pusty tekst
}

function pobierz_get($nazwa) // Pobieramy dane z paseczka adresu, np. strona.php?id=10 wtedy id = 10 
{
    if (isset($_GET[$nazwa])) return trim($_GET[$nazwa]);
    return "";
}

?>
