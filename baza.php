<?php

function wczytaj_konfiguracje() // z konfiguracja.txt pobieram dane
{
$konfiguracja = array(); // tworze pusta tablice asocjacyjna czyli klucz wartosc z wyklady 4-5
$plik = fopen("konfiguracja.txt", "r");// otwieram plik do odczytu
    if ($plik == false) // jezeli nie ma pliku to zwracam pusta tablice
        {
            return $konfiguracja;// zwracam pusta tablice
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

    fclose($plik);// zamykam plik
    return $konfiguracja; // zwracam tablice asocjacyjna
}



function polacz_z_baza()
{
    $konfiguracja = wczytaj_konfiguracje();// pobieram konfiguracje z pliku txt

    // wartości domyślne, gdy nie ma wpisu w txt
    if (isset($konfiguracja["serwer"])) // jezeli istnieje to przypisuje wartosc z pliku txt
        {
            $adres_serwera = $konfiguracja["serwer"];
        } else 
        {
            $adres_serwera = "127.0.0.1";       
        }

    if (isset($konfiguracja["uzytkownik"])) // to samo tylko dla uzytkownika
        {
            $uzytkownik = $konfiguracja["uzytkownik"];
        } else 
        {
            $uzytkownik = "root";
        }

    if (isset($konfiguracja["haslo"])) // to samo tylko dla hasla
        {
            $haslo = $konfiguracja["haslo"];
        } else 
        {
            $haslo = "";
        }

    if (isset($konfiguracja["baza"])) //j.w.
        {
            $nazwa_bazy = $konfiguracja["baza"];
        } else 
        {
            $nazwa_bazy = "serwis";
        }

 
    $polaczenie = mysqli_connect($adres_serwera, $uzytkownik, $haslo, $nazwa_bazy);// probuje sie polaczyc z baza


    if ($polaczenie == false)
        {

            echo "<b>Nie udało się połączyć z bazą.</b>";// jezeli nie udalo sie polaczyc to wyswietlam komunikat
            exit();
        }

    mysqli_set_charset($polaczenie, "utf8mb4");

    return $polaczenie;// zwracam polaczenie z baza
}


function pobierz_formularza($nazwa)
{
    if (isset($_POST[$nazwa])) return trim($_POST[$nazwa]);// jezeli istnieje to zwracam wartosc z formularza

    return "";
}


function wez_z_zdresu($nazwa)
{
    if (isset($_GET[$nazwa])) return trim($_GET[$nazwa]);
    return "";
}
function zapisz_konfiguracje($konfiguracja)
{
    // zapisujemy dokładnie te 4 klucze
    $klucze = array("serwer", "uzytkownik", "haslo", "baza");

    $zawartosc_txt = "";
    for ($i = 0; $i < count($klucze); $i++)
    {
        $klucz = $klucze[$i];
        $wartosc = isset($konfiguracja[$klucz]) ? trim($konfiguracja[$klucz]) : "";

        // zabezpieczenie: żeby nie dało się wstrzyknąć nowych linii do pliku
        $wartosc = str_replace(array("\r", "\n"), "", $wartosc);

        $zawartosc_txt .= $klucz . "=" . $wartosc . "\n";
    }

    $czy_zapisano = file_put_contents("konfiguracja.txt", $zawartosc_txt);
    return ($czy_zapisano !== false);
}



