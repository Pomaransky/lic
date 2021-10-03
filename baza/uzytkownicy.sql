-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 03 Paź 2021, 19:47
-- Wersja serwera: 10.4.20-MariaDB
-- Wersja PHP: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `uzytkownicy`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `oferty`
--

CREATE TABLE `oferty` (
  `id_oferty` int(20) NOT NULL,
  `nazwa_oferty` varchar(20) NOT NULL,
  `opis_oferty` varchar(255) NOT NULL,
  `dodatkowa_cena` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `oferty`
--

INSERT INTO `oferty` (`id_oferty`, `nazwa_oferty`, `opis_oferty`, `dodatkowa_cena`) VALUES
(1, 'Standard', 'Jakiś standardowy opis oferty', 0),
(12, 'Nowa', 'Jakis opis', 150);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pokoje`
--

CREATE TABLE `pokoje` (
  `nr_pokoju` int(11) NOT NULL,
  `ilosc_miejsc` int(11) NOT NULL,
  `cena_za_noc` int(11) NOT NULL,
  `id_r` int(11) DEFAULT NULL,
  `zajety_do` date DEFAULT NULL,
  `dodatkowe_info` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `pokoje`
--

INSERT INTO `pokoje` (`nr_pokoju`, `ilosc_miejsc`, `cena_za_noc`, `id_r`, `zajety_do`, `dodatkowe_info`) VALUES
(1, 1, 400, 278, '2021-09-30', 'Jakieś dodatkowe info dla przykładu'),
(2, 1, 400, 278, '2021-09-30', NULL),
(3, 2, 202, NULL, NULL, NULL),
(4, 5, 202, NULL, '2021-09-30', 'Bo nie'),
(5, 3, 100, NULL, NULL, NULL),
(6, 4, 100, 279, '2021-10-15', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rezerwacje`
--

CREATE TABLE `rezerwacje` (
  `id_r` int(11) NOT NULL,
  `od` date NOT NULL,
  `do` date NOT NULL,
  `oferta` varchar(20) NOT NULL,
  `ile_osob` int(1) NOT NULL,
  `id` int(11) NOT NULL,
  `do_zaplaty` int(11) DEFAULT NULL,
  `czy_oplacono` tinyint(1) NOT NULL,
  `imie` varchar(12) NOT NULL,
  `nazwisko` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `rezerwacje`
--

INSERT INTO `rezerwacje` (`id_r`, `od`, `do`, `oferta`, `ile_osob`, `id`, `do_zaplaty`, `czy_oplacono`, `imie`, `nazwisko`) VALUES
(278, '2021-09-29', '2021-09-30', 'Standard', 1, 48, 400, 1, 'asd', 'asdasd'),
(279, '2021-10-14', '2021-10-15', 'Standard', 4, 49, 400, 1, 'asdasd', 'asdasdasd');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(3) NOT NULL,
  `uzytkownik` text COLLATE utf8_polish_ci NOT NULL,
  `haslo` text COLLATE utf8_polish_ci DEFAULT NULL,
  `email` text COLLATE utf8_polish_ci NOT NULL,
  `admin` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `uzytkownik`, `haslo`, `email`, `admin`) VALUES
(38, 'newuser', '$2y$10$AVMK6TZ8GQM41Kd019IcOe0Jv9oER/IGAhG6PVtgmsXkhdMhO8dAS', 'newuser@tlen.pl', 0),
(2, 'Marek', '$2y$10$Wo6cgPJ.6fCunaB0HqBKp.CPdXTInBBFrjc.Yz8QTTQwL7p2s4Cne', 'marek@gmail.com', 0),
(16, 'Maciek', '$2y$10$rRB9LjcYoHb3FEzhMyINH.n.fxkJI9SjhccfdX.C6WEZRibk2ETr.', 'maciek@maciek.pl', 0),
(17, 'Konrad', '$2y$10$gy8FtXZqx.mmrNeHi84LY.HTpBrwtU.L3tfQfgkGg5Kh6ocITHJFS', 'konrad@konrad.pl', 0),
(21, 'nowy', '$2y$10$5EGi0oJAo1tWoRE76oLR8e9ZXIV3TbnUi2rVRkMc0Focl6W0TXnya', 'nowy@nowy.pl', 0),
(20, 'mateo', '$2y$10$r7WbAMvwZpys2SX8F48Sj.DoczCRQfkZrqxU.iWiBbpojdpcKBTia', 'mat@mat.pl', 0),
(43, 'test', '$2y$10$RvqYgPzl3E3jpHgGabvyx.fFBDzzj/eJcuS/EXtdLnFNgKwQYYnd.', 'test@test.pl', 0),
(44, 'test2', '$2y$10$9eYir2GKGhwa8eNM/vzTz.1Se6QMCC7BwF0FhZwAULhV68yJ5ja7u', 'test2@test2.pl', 0),
(45, 'test3', '$2y$10$wvqVD/zatZaehKcLN7D9YOfWMuK2byvBMXv7JyZGbVzcYgK2Oieyy', 'test3@test3.pl', 0),
(46, 'test4', '$2y$10$rdRrDEeJoiGOJCbWhLIPROycYdLF26skynBW5zOz.BpeBksurHgN6', 'test4@test4.pl', 0),
(47, 'test5', '$2y$10$To9d0cI8fjohGH/kzxGMfuquXXBve2o/FvxjI/97IJLq0yGAdjZu.', 'test5@test5.pl', 0),
(48, 'test6', '$2y$10$vVGXg00HftIGIETjUU/JNuhJBSogyPiUU1pueCL3mIZzjBdRxQvfa', 't@t.pl', 0),
(49, 'test7', '$2y$10$bWLxM.X1T5l8C8SeJtRzo.Whmsm4DTeVFWT9CZrxKS57kdGUgWmlq', 't7@t.pl', 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `oferty`
--
ALTER TABLE `oferty`
  ADD PRIMARY KEY (`id_oferty`);

--
-- Indeksy dla tabeli `pokoje`
--
ALTER TABLE `pokoje`
  ADD PRIMARY KEY (`nr_pokoju`),
  ADD KEY `id_r` (`id_r`);

--
-- Indeksy dla tabeli `rezerwacje`
--
ALTER TABLE `rezerwacje`
  ADD PRIMARY KEY (`id_r`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `oferty`
--
ALTER TABLE `oferty`
  MODIFY `id_oferty` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `rezerwacje`
--
ALTER TABLE `rezerwacje`
  MODIFY `id_r` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `pokoje`
--
ALTER TABLE `pokoje`
  ADD CONSTRAINT `pokoje_ibfk_1` FOREIGN KEY (`id_r`) REFERENCES `rezerwacje` (`id_r`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
