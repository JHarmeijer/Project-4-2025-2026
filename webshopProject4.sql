-- ============================================================
-- Gecombineerde SQL Dump - webshopproject4
-- Samengevoegd uit twee versies op 12 jun 2026
-- Bevat: klant (met rol), producten (met img),
--        bestellingen (met groep_ID), coupons, reviews
-- Server: MariaDB 10.4+ | Charset: utf8mb4
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Database aanmaken
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `webshopproject4`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `webshopproject4`;

-- --------------------------------------------------------
-- Tabel: klant
-- Toegevoegd t.o.v. v2: rol ENUM('klant','admin','medewerker')
-- registratie_datum als DATETIME (v1) i.p.v. DATE (v2)
-- --------------------------------------------------------

CREATE TABLE `klant` (
  `klant_ID`           int(11)      NOT NULL AUTO_INCREMENT,
  `klant_naam`         varchar(100) NOT NULL,
  `email`              varchar(100) NOT NULL,
  `wachtwoord`         varchar(255) NOT NULL,
  `adres`              varchar(100) DEFAULT NULL,
  `registratie_datum`  datetime     DEFAULT current_timestamp(),
  `telefoon_nummer`    varchar(20)  DEFAULT NULL,
  `rol`                enum('klant','admin','medewerker') NOT NULL DEFAULT 'klant',
  PRIMARY KEY (`klant_ID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `klant` (`klant_ID`, `klant_naam`, `email`, `wachtwoord`, `adres`, `registratie_datum`, `telefoon_nummer`, `rol`) VALUES
(1, 'Jan de Vries',  'jan@example.com',          'hash1abc',                                                             'Hoofdstraat 1', '2024-01-10 00:00:00', '0612345678', 'klant'),
(2, 'Emma Bakker',   'emma@example.com',          'hash2def',                                                             'Kerkweg 22',    '2024-02-14 00:00:00', '0623456789', 'klant'),
(3, 'Lucas Smit',    'lucas@example.com',         'hash3ghi',                                                             'Molenpad 5',    '2024-03-01 00:00:00', NULL,          'klant'),
(4, 'TestNaam123',   'TestNaam123@gmail.com',     '$2y$10$5NtuHn2OIncfhYzWPVWRO.8KPuThqUFlLxX1.LWDbjVvgAc4wV6Xa',       NULL,            NULL,                  NULL,          'admin'),
(5, 'J',             'J@gmail.com',               '$2y$10$1PX90amwVJpecJOZCu4aMu5KqeZLcYFhVFH5JtMHF1egoZRzVyAne',       NULL,            '2026-06-05 09:36:19', NULL,          'klant'),
(6, 'test',          'itsmyjulian@gmail.com',     '$2y$10$FKZPGgVxS4Y1sEAs.Gs3H.kDoKzn2Y0b6MI4OHC5xQMGhSxMobQj2',       NULL,            NULL,                  NULL,          'klant');

-- --------------------------------------------------------
-- Tabel: producten
-- Toegevoegd t.o.v. v2: img VARCHAR(255)
-- v2 had Caesar Salade (product_ID 2) — opgenomen zonder img
-- --------------------------------------------------------

CREATE TABLE `producten` (
  `product_ID`   int(11)        NOT NULL AUTO_INCREMENT,
  `product_naam` varchar(100)   NOT NULL,
  `prijs`        decimal(10,2)  NOT NULL,
  `voorraad`     int(11)        DEFAULT 0,
  `img`          varchar(255)   DEFAULT NULL,
  PRIMARY KEY (`product_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `producten` (`product_ID`, `product_naam`, `prijs`, `voorraad`, `img`) VALUES
(1, 'Margherita Pizza', 12.50, 50,  'https://uk.ooni.com/cdn/shop/articles/20220211142645-margherita-9920_e41233d5-dcec-461c-b07e-03245f031dfe.jpg?v=1737105431'),
(2, 'Caesar Salade',    8.95,  30,  NULL),
(3, 'Tiramisu',         5.50,  51,  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRYYEJ7Wl7bG8P535PfYj7UDdn3vBpF7ECCzw&s'),
(4, 'chefs mes',        19.95, 55,  'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRfn86tGZh9zAFKJkq6skCVd6fwLvvXhHF6pQ&s');

-- --------------------------------------------------------
-- Tabel: coupons
-- Identiek in beide versies
-- --------------------------------------------------------

CREATE TABLE `coupons` (
  `coupon_ID`        int(11)                            NOT NULL AUTO_INCREMENT,
  `coupon_code`      varchar(50)                        NOT NULL,
  `korting_type`     enum('percentage','vast_bedrag')   NOT NULL,
  `kortings_waarde`  decimal(10,2)                      NOT NULL,
  `actief`           tinyint(1)                         DEFAULT 1,
  PRIMARY KEY (`coupon_ID`),
  UNIQUE KEY `coupon_code` (`coupon_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `coupons` (`coupon_ID`, `coupon_code`, `korting_type`, `kortings_waarde`, `actief`) VALUES
(1, 'PIZZA10',   'percentage',  10.00, 1),
(2, 'WELKOM5',   'vast_bedrag',  5.00, 1),
(3, 'DESSERT5',  'percentage',   5.00, 1);

-- --------------------------------------------------------
-- Tabel: bestellingen
-- Toegevoegd t.o.v. v2: groep_ID INT(11) (voor winkelmandje/groepering)
-- Alle bestellingen uit beide versies samengevoegd (deduplicatie op inhoud)
-- --------------------------------------------------------

CREATE TABLE `bestellingen` (
  `bestelling_ID`  int(11) NOT NULL AUTO_INCREMENT,
  `klant_ID`       int(11) NOT NULL,
  `product_ID`     int(11) NOT NULL,
  `coupon_ID`      int(11) DEFAULT NULL,
  `datum_aankoop`  date    DEFAULT NULL,
  `groep_ID`       int(11) DEFAULT NULL,
  PRIMARY KEY (`bestelling_ID`),
  KEY `klant_ID`  (`klant_ID`),
  KEY `product_ID` (`product_ID`),
  KEY `coupon_ID`  (`coupon_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bestellingen` (`bestelling_ID`, `klant_ID`, `product_ID`, `coupon_ID`, `datum_aankoop`, `groep_ID`) VALUES
-- Bestellingen uit versie 2 (historische testdata)
(1,  1, 1, 1,    '2024-03-15', NULL),
(2,  2, 3, 2,    '2024-03-16', NULL),
(3,  3, 2, NULL, '2024-03-17', NULL),
-- Bestellingen uit versie 1 (recente data met groep_ID)
(12, 4, 1, NULL, '2026-06-12', 1781255831),
(14, 4, 3, NULL, '2026-06-12', 1781255831);

-- --------------------------------------------------------
-- Tabel: reviews
-- Alleen aanwezig in versie 2 — volledig overgenomen
-- --------------------------------------------------------

CREATE TABLE `reviews` (
  `review_ID`   int(11)      NOT NULL AUTO_INCREMENT,
  `product_ID`  int(11)      NOT NULL,
  `naam`        varchar(100) NOT NULL,
  `beoordeling` int(11)      NOT NULL,
  `tekst`       text         NOT NULL,
  `pluspunt`    varchar(255) DEFAULT NULL,
  `minpunt`     varchar(255) DEFAULT NULL,
  `datum`       timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`review_ID`),
  KEY `product_ID` (`product_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reviews` (`review_ID`, `product_ID`, `naam`, `beoordeling`, `tekst`, `pluspunt`, `minpunt`, `datum`) VALUES
(3,  1, 'Jan de Vries', 5, 'Heerlijke pizza, de bodem was precies goed knapperig en de tomatensaus smaakte vers. Zeker een aanrader!', 'Knapperige bodem',    'Iets kleine portie',          '2026-06-12 08:03:46'),
(4,  1, 'Emma Bakker',  4, 'Goede margherita, lekker simpel maar smaakvol. De mozzarella was goed gesmolten. Zou hem zeker weer bestellen.',  'Verse ingrediënten',  'Bezorging duurde wat lang',   '2026-06-12 08:03:46'),
(5,  2, 'Lucas Smit',   5, 'Beste Caesar salade die ik ooit heb gegeten! De dressing was perfect en de croutons waren lekker krokant.',     'Geweldige dressing',  'Geen minpunten',              '2026-06-12 08:03:46'),
(6,  2, 'Jan de Vries', 3, 'Salade was redelijk maar de portie viel wat tegen voor de prijs. De smaak was wel goed.',                       'Verse sla',           'Kleine portie voor de prijs', '2026-06-12 08:03:46'),
(7,  3, 'Emma Bakker',  5, 'De tiramisu was hemels! Precies de juiste balans tussen koffie en mascarpone. Echt een topdesssert.',            'Perfecte balans',     'Had wel een grotere portie gewild', '2026-06-12 08:03:46'),
(8,  3, 'Lucas Smit',   4, 'Heerlijk dessert, lekker romig en niet te zoet. De espresso smaak kwam goed naar voren.',                      'Romige textuur',      'Iets aan de prijzige kant',   '2026-06-12 08:03:46'),
(9,  1, 'admin',        4, 'test aaaaaaaaaaaaaaaaaaaaaaaaaaaa',                                                                             'test',                'test',                        '2026-06-12 08:13:58');

-- --------------------------------------------------------
-- Foreign key constraints
-- --------------------------------------------------------

ALTER TABLE `bestellingen`
  ADD CONSTRAINT `bestellingen_ibfk_1` FOREIGN KEY (`klant_ID`)  REFERENCES `klant`    (`klant_ID`)  ON DELETE CASCADE,
  ADD CONSTRAINT `bestellingen_ibfk_2` FOREIGN KEY (`product_ID`) REFERENCES `producten` (`product_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bestellingen_ibfk_3` FOREIGN KEY (`coupon_ID`)  REFERENCES `coupons`  (`coupon_ID`)  ON DELETE SET NULL;

ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_ID`) REFERENCES `producten` (`product_ID`) ON DELETE CASCADE;

-- --------------------------------------------------------
-- AUTO_INCREMENT waarden
-- --------------------------------------------------------

ALTER TABLE `klant`        MODIFY `klant_ID`       int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `producten`    MODIFY `product_ID`      int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `coupons`      MODIFY `coupon_ID`       int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `bestellingen` MODIFY `bestelling_ID`   int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `reviews`      MODIFY `review_ID`       int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
