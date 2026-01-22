<?php

function wczytaj_konfiguracje() // z konfiguracja.txt pobieram dane
{
$konfiguracja = array(); // tworze pusta tablice asocjacyjna czyli klucz wartosc z wyklady 4-5
$plik = fopen("konfiguracja.txt", "r");
    if ($plik == false) // jezeli nie ma pliku to zwracam pusta tablice
        {
            return $konfiguracja;
        }

        while (!feof($plik))// dopoki nie koniec pliku 
        {
            $linijka = trim(fgets($plik));
            if ($linijka == "") continue; // jezeli linijka jest pusta to pomijam

            $segmenty = explode("=", $linijka);// w miejsce = wsadzamy bombe i rozbijamy na 2 czesci DO NAUKI

            if (count($segmenty) == 2) 
                {
                    $klucz = trim($segmenty[0]); // klucz to pierwsza czesc
                    $wartosc = trim($segmenty[1]); // wartosc to druga czesc
                    $konfiguracja[$klucz] = $wartosc; // przypisuje do tablicy asocjacyjnej

                }
        }

    fclose($plik);
    return $konfiguracja;
}



function polacz_z_baza()
{
    $konfiguracja = wczytaj_konfiguracje();// pobieram konfiguracje z pliku txt

    // wartości domyślne, gdy nie ma wpisu w txt
    $adres_serwera = isset($konfiguracja["serwer"]) ? $konfiguracja["serwer"] : "127.0.0.1";
    $uzytkownik    = isset($konfiguracja["uzytkownik"]) ? $konfiguracja["uzytkownik"] : "root";
    $haslo         = isset($konfiguracja["haslo"]) ? $konfiguracja["haslo"] : "";
    $nazwa_bazy    = isset($konfiguracja["baza"]) ? $konfiguracja["baza"] : "serwis";

 
    $polaczenie = mysqli_connect($adres_serwera, $uzytkownik, $haslo, $nazwa_bazy);// probuje sie polaczyc z baza


    if ($polaczenie == false)
        {

            echo "<b>Nie udało się połączyć z bazą.</b>";// jezeli nie udalo sie polaczyc to wyswietlam komunikat
            exit();
        }

    mysqli_set_charset($polaczenie, "utf8mb4");

    return $polaczenie;// zwracam polaczenie z baza
}


function pobierz_post($nazwa)
{
    if (isset($_POST[$nazwa])) return trim($_POST[$nazwa]);// jezeli istnieje to zwracam wartosc z formularza

    return "";
}


function pobierz_get($nazwa)
{
    if (isset($_GET[$nazwa])) return trim($_GET[$nazwa]);
    return "";
}
function zapisz_konfiguracje($konfiguracja)
{
    // zapisujemy dokładnie te 4 klucze
    $klucze = array("serwer", "uzytkownik", "haslo", "baza");

    $tekst = "";
    for ($i = 0; $i < count($klucze); $i++)
    {
        $k = $klucze[$i];
        $v = isset($konfiguracja[$k]) ? trim($konfiguracja[$k]) : "";

        // zabezpieczenie: żeby nie dało się wstrzyknąć nowych linii do pliku
        $v = str_replace(array("\r", "\n"), "", $v);

        $tekst .= $k . "=" . $v . "\n";
    }

    $ok = file_put_contents("konfiguracja.txt", $tekst);
    return ($ok !== false);
}



