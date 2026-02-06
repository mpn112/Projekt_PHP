<?php

function wczytaj_konfiguracje() 
{
    $konfiguracja = array(); 
    $plik = fopen("konfiguracja.txt", "r");// otwieram plik do odczytu
    if ($plik == false) 
    {
        return $konfiguracja;// zwracam pusta tablice
    }

    while (!feof($plik))
    {
        $linijka = trim(fgets($plik));
        if ($linijka == "") continue; 

        $segmenty = explode("=", $linijka);

        if (count($segmenty) == 2) 
        {
            $klucz = trim($segmenty[0]); 
            $wartosc = trim($segmenty[1]); 
            $konfiguracja[$klucz] = $wartosc; 
        }
    }

    fclose($plik);
    return $konfiguracja; // zwracam tablice asocjacyjna
}



function polacz_z_baza()
{
    $konfiguracja = wczytaj_konfiguracje();

    
    if (isset($konfiguracja["serwer"])) 
        {
            $adres_serwera = $konfiguracja["serwer"];
        } else 
        {
            $adres_serwera = "127.0.0.1";       
        }

    if (isset($konfiguracja["uzytkownik"])) 
        {
            $uzytkownik = $konfiguracja["uzytkownik"];
        } else 
        {
            $uzytkownik = "root";
        }

    if (isset($konfiguracja["haslo"])) 
        {
            $haslo = $konfiguracja["haslo"];
        } else 
        {
            $haslo = "";
        }

    if (isset($konfiguracja["baza"]))
        {
            $nazwa_bazy = $konfiguracja["baza"];
        } else 
        {
            $nazwa_bazy = "serwis";
        }

 
    $polaczenie = mysqli_connect($adres_serwera, $uzytkownik, $haslo, $nazwa_bazy);


    if ($polaczenie == false)
        {

            echo "<b>Nie udało się połączyć z bazą.</b>";
            exit();
        }

    mysqli_set_charset($polaczenie, "utf8mb4");

    return $polaczenie;
}


function zapisz_konfiguracje($konfiguracja)
{
    $klucze = array("serwer", "uzytkownik", "haslo", "baza");// zapisujemy dokładnie te 4 klucze

    $zawartosc_txt = "";
    for ($i = 0; $i < count($klucze); $i++)
    {
        $klucz = $klucze[$i];
        $wartosc = "";
        if (isset($konfiguracja[$klucz]) )
            {
                $wartosc = trim($konfiguracja[$klucz]);
            }
       $zawartosc_txt = $zawartosc_txt . $klucz . "=" . $wartosc . "\n";
    }
    // zapisuje do pliku txt i sprawdzam czy sie udalo
    $czy_zapisano = file_put_contents("konfiguracja.txt", $zawartosc_txt);
    return ($czy_zapisano !== false); // zwracam true jezeli sie udalo
}



