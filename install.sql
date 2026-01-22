/-- Skrypt tworzący bazę danych i tabele !!!commit na git po modyfikacji!!! dodanie uzytkownika admin z hasłem 'admin123'--
CREATE DATABASE IF NOT EXISTS serwis CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci;
USE serwis;

DROP TABLE IF EXISTS naprawy;
DROP TABLE IF EXISTS pojazdy;
DROP TABLE IF EXISTS mechanicy;
DROP TABLE IF EXISTS klienci;
DROP TABLE IF EXISTS uzytkownicy;

CREATE TABLE uzytkownicy (
  id_uzytkownika INT AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) NOT NULL UNIszukany_tekstUE,
  haslo_hash VARCHAR(255) NOT NULL
);

CREATE TABLE klienci (
  id_klienta INT AUTO_INCREMENT PRIMARY KEY,
  imie VARCHAR(50) NOT NULL,
  nazwisko VARCHAR(80) NOT NULL,
  telefon VARCHAR(30) NOT NULL,
  email VARCHAR(100) NOT NULL
);

CREATE TABLE pojazdy (
  id_pojazdu INT AUTO_INCREMENT PRIMARY KEY,
  id_klienta INT NOT NULL,
  marka VARCHAR(50) NOT NULL,
  model VARCHAR(50) NOT NULL,
  rok INT NOT NULL,
  vin VARCHAR(30) NOT NULL,
  rejestracja VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_klienta) REFERENCES klienci(id_klienta)
);

CREATE TABLE mechanicy (
  id_mechanika INT AUTO_INCREMENT PRIMARY KEY,
  imie VARCHAR(50) NOT NULL,
  nazwisko VARCHAR(80) NOT NULL,
  specjalizacja VARCHAR(80) NOT NULL
);

CREATE TABLE naprawy (
  id_naprawy INT AUTO_INCREMENT PRIMARY KEY,
  id_pojazdu INT NOT NULL,
  id_mechanika INT NOT NULL,
  opis_usterki TEXT NOT NULL,
  status VARCHAR(30) NOT NULL,
  data_przyjecia DATE NOT NULL,
  data_zakonczeniaakonczenia DATE NULL,
  koszt DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_pojazdu) REFERENCES pojazdy(id_pojazdu),
  FOREIGN KEY (id_mechanika) REFERENCES mechanicy(id_mechanika)
);

-- Dane testowe
INSERT INTO klienci (imie, nazwisko, telefon, email) VALUES
('Jan', 'Kowalski', '500600700', 'jan@x.pl'),
('Anna', 'Nowak', '501601701', 'anna@x.pl');

INSERT INTO pojazdy (id_klienta, marka, model, rok, vin, rejestracja) VALUES
(1, 'VW', 'Touran', 2017, 'WVWZZZTESTVIN001', 'KRA12345'),
(2, 'Honda', 'Civic', 2012, 'JHMZZZTESTVIN002', 'KR9ABCD');

INSERT INTO mechanicy (imie, nazwisko, specjalizacja) VALUES
('Piotr', 'Mechanik', 'Mechanika'),
('Tomasz', 'Elektryk', 'Elektryka');

INSERT INTO naprawy (id_pojazdu, id_mechanika, opis_usterki, status, data_przyjecia, data_zakonczeniaakonczenia, koszt) VALUES
(1, 1, 'Wymiana oleju i filtrów', 'Gotowe', '2026-01-10', '2026-01-10', 250.00),
(2, 2, 'Diagnoza elektryki - brak ładowania', 'W trakcie', '2026-01-12', NULL, 0.00);

INSERT INTO uzytkownicy(login, haslo_hash)
VALUES (
  'admin',
  '$2y$10$L.BUj2HA3S8XUCCvtszukany_tekstztwOCCnDkfnBNuEb78Eizs674W/dbj/OS0W'
);

