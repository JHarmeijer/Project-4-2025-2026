-- Database: PHP1_EindOpdracht

-- Verwijderen van de database als deze al bestaat
DROP DATABASE IF EXISTS webshopProject4;

-- Database aanmaken
CREATE DATABASE webshopProject4;

USE webshopProject4;

CREATE TABLE klant(
    klant_ID INT AUTO_INCREMENT PRIMARY KEY,
    klant_naam VARCHAR(100)  NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    wachtwoord VARCHAR(255) NOT NULL,
    adres VARCHAR(100),
    registratie_datum DATE,
    telefoon_nummer VARCHAR(20)
);

CREATE TABLE producten(
    product_ID INT AUTO_INCREMENT PRIMARY KEY,
    product_naam VARCHAR(100) NOT NULL,
    prijs DECIMAL(10,2) NOT NULL,
    voorraad  INT DEFAULT 0
);


CREATE TABLE coupons (
    coupon_ID INT AUTO_INCREMENT PRIMARY KEY,
    coupon_code VARCHAR(50)  NOT NULL UNIQUE,
    korting_type ENUM('percentage','vast_bedrag') NOT NULL,
    kortings_waarde DECIMAL(10,2) NOT NULL,
    actief TINYINT(1) DEFAULT 1 
);


-- Tabel: bestellingen (als laatste, want verwijst naar de 3 tabellen hierboven)
CREATE TABLE bestellingen (
    bestelling_ID INT AUTO_INCREMENT PRIMARY KEY,
    klant_ID      INT NOT NULL,
    product_ID    INT NOT NULL,
    coupon_ID     INT DEFAULT NULL,
    datum_aankoop DATE,
    FOREIGN KEY (klant_ID)   REFERENCES klant(klant_ID)         ON DELETE CASCADE,
    FOREIGN KEY (product_ID) REFERENCES producten(product_ID)   ON DELETE CASCADE,
    FOREIGN KEY (coupon_ID)  REFERENCES coupons(coupon_ID)      ON DELETE SET NULL
);

CREATE TABLE reviews (
  review_ID INT AUTO_INCREMENT PRIMARY KEY,
  product_ID INT NOT NULL,
  naam VARCHAR(100) NOT NULL,
  beoordeling INT NOT NULL,
  tekst TEXT NOT NULL,
  pluspunt VARCHAR(255),
  minpunt VARCHAR(255),
  datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (product_ID)
    REFERENCES producten(product_ID)
    ON DELETE CASCADE
);

-- Testdata: klant
INSERT INTO klant (klant_naam, email, wachtwoord, adres, registratie_datum, telefoon_nummer) VALUES
    ('Jan de Vries', 'jan@example.com',   'hash1abc', 'Hoofdstraat 1', '2024-01-10', '0612345678'),
    ('Emma Bakker',  'emma@example.com',  'hash2def', 'Kerkweg 22',    '2024-02-14', '0623456789'),
    ('Lucas Smit',   'lucas@example.com', 'hash3ghi', 'Molenpad 5',    '2024-03-01', NULL);
 
-- Testdata: producten
INSERT INTO producten (product_naam, prijs, voorraad) VALUES
    ('Margherita Pizza', 12.50, 50),
    ('Caesar Salade',     8.95, 30),
    ('Tiramisu',          5.50, 20);
 

-- Testdata: coupons
INSERT INTO coupons (coupon_code, korting_type, kortings_waarde, actief) VALUES
    ('PIZZA10',  'percentage',  10.00, 1),
    ('WELKOM5',  'vast_bedrag',  5.00, 1),
    ('DESSERT5', 'percentage',   5.00, 1);
 
-- Testdata: bestellingen
INSERT INTO bestellingen (klant_ID, product_ID, coupon_ID, datum_aankoop) VALUES
    (1, 1, 1,    '2024-03-15'),
    (2, 3, 2,    '2024-03-16'),
    (3, 2, NULL, '2024-03-17');