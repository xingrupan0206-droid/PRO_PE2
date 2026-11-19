
 
-- =====================================================================
-- Database: Tandarts  (MySQL / MariaDB, bv. via phpMyAdmin of XAMPP)
-- Rollen: admin, tandarts, mondhygienist, assistent, patient
-- =====================================================================
 
CREATE DATABASE IF NOT EXISTS Tandarts
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
 
USE Tandarts;
 
-- ---------------------------------------------------------------------
-- Oude tabellen verwijderen (volgorde: eerst tabellen met foreign keys)
-- ---------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
 
DROP VIEW  IF EXISTS v_dashboard_afspraken_per_maand;
DROP VIEW  IF EXISTS v_dashboard_omzet_per_maand;
DROP VIEW  IF EXISTS v_dashboard_populaire_behandelingen;
 
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS feedback;
DROP TABLE IF EXISTS mededelingen;
DROP TABLE IF EXISTS meldingen;
DROP TABLE IF EXISTS betalingen;
DROP TABLE IF EXISTS factuurregels;
DROP TABLE IF EXISTS facturen;
DROP TABLE IF EXISTS afspraken;
DROP TABLE IF EXISTS beschikbaarheid;
DROP TABLE IF EXISTS behandelingen;
DROP TABLE IF EXISTS medisch_dossier;
DROP TABLE IF EXISTS patienten;
DROP TABLE IF EXISTS medewerkers;
DROP TABLE IF EXISTS gebruikers;
DROP TABLE IF EXISTS rollen;
 
SET FOREIGN_KEY_CHECKS = 1;
 
-- ---------------------------------------------------------------------
-- 5. Beveiliging: rollen en gebruikers (inloggen)
-- ---------------------------------------------------------------------
CREATE TABLE rollen (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    naam        VARCHAR(30) NOT NULL UNIQUE,
    omschrijving VARCHAR(255)
) ENGINE=InnoDB;
 
CREATE TABLE gebruikers (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    rol_id          INT NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    wachtwoord_hash VARCHAR(255) NOT NULL,          -- nooit platte tekst opslaan (bcrypt/argon2)
    voornaam        VARCHAR(50)  NOT NULL,
    tussenvoegsel   VARCHAR(20),
    achternaam      VARCHAR(80)  NOT NULL,
    telefoon        VARCHAR(20),
    actief          BOOLEAN NOT NULL DEFAULT TRUE,
    laatste_login   DATETIME NULL,
    aangemaakt_op   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES rollen(id)
) ENGINE=InnoDB;
 
-- ---------------------------------------------------------------------
-- 1. Patiëntregistratie en -beheer
-- ---------------------------------------------------------------------
CREATE TABLE patienten (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    gebruiker_id    INT NOT NULL UNIQUE,
    geboortedatum   DATE NOT NULL,
    straat          VARCHAR(100),
    huisnummer      VARCHAR(10),
    postcode        VARCHAR(10),
    woonplaats      VARCHAR(80),
    verzekeraar     VARCHAR(80),
    polisnummer     VARCHAR(30),
    avg_toestemming BOOLEAN NOT NULL DEFAULT FALSE, -- AVG: toestemming verwerking gegevens
    avg_datum       DATETIME NULL,
    FOREIGN KEY (gebruiker_id) REFERENCES gebruikers(id) ON DELETE CASCADE
) ENGINE=InnoDB;
 
CREATE TABLE medisch_dossier (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT NOT NULL UNIQUE,
    allergieen      TEXT,
    medicatie       TEXT,
    aandoeningen    TEXT,
    notities        TEXT,
    bijgewerkt_door INT NULL,                       -- gebruiker (tandarts/assistent)
    bijgewerkt_op   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)      REFERENCES patienten(id)  ON DELETE CASCADE,
    FOREIGN KEY (bijgewerkt_door) REFERENCES gebruikers(id) ON DELETE SET NULL
) ENGINE=InnoDB;
 
-- Medewerkers = tandarts, mondhygienist, assistent
CREATE TABLE medewerkers (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    gebruiker_id    INT NOT NULL UNIQUE,
    functie         ENUM('tandarts','mondhygienist','assistent') NOT NULL,
    big_nummer      VARCHAR(20) NULL,
    specialisatie   VARCHAR(100),
    FOREIGN KEY (gebruiker_id) REFERENCES gebruikers(id) ON DELETE CASCADE
) ENGINE=InnoDB;
 
-- ---------------------------------------------------------------------
-- 2. Afsprakenbeheer
-- ---------------------------------------------------------------------
CREATE TABLE behandelingen (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    naam            VARCHAR(100) NOT NULL,
    omschrijving    VARCHAR(255),
    duur_minuten    INT NOT NULL DEFAULT 30,
    prijs           DECIMAL(8,2) NOT NULL,
    actief          BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;
 
CREATE TABLE beschikbaarheid (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    medewerker_id   INT NOT NULL,
    dag_van_week    TINYINT NOT NULL,               -- 1 = maandag ... 7 = zondag
    begintijd       TIME NOT NULL,
    eindtijd        TIME NOT NULL,
    FOREIGN KEY (medewerker_id) REFERENCES medewerkers(id) ON DELETE CASCADE
) ENGINE=InnoDB;
 
CREATE TABLE afspraken (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT NOT NULL,
    medewerker_id   INT NOT NULL,
    behandeling_id  INT NOT NULL,
    start_tijd      DATETIME NOT NULL,
    eind_tijd       DATETIME NOT NULL,
    status          ENUM('aangevraagd','bevestigd','verzet','geannuleerd','afgerond') NOT NULL DEFAULT 'aangevraagd',
    opmerking       VARCHAR(255),
    aangemaakt_op   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id)     REFERENCES patienten(id),
    FOREIGN KEY (medewerker_id)  REFERENCES medewerkers(id),
    FOREIGN KEY (behandeling_id) REFERENCES behandelingen(id)
) ENGINE=InnoDB;
 
-- ---------------------------------------------------------------------
-- 3. Facturatie
-- ---------------------------------------------------------------------
CREATE TABLE facturen (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    factuurnummer   VARCHAR(20) NOT NULL UNIQUE,
    patient_id      INT NOT NULL,
    afspraak_id     INT NULL,
    factuurdatum    DATE NOT NULL,
    vervaldatum     DATE NOT NULL,
    totaalbedrag    DECIMAL(10,2) NOT NULL DEFAULT 0,
    status          ENUM('concept','openstaand','betaald','vervallen') NOT NULL DEFAULT 'concept',
    FOREIGN KEY (patient_id)  REFERENCES patienten(id),
    FOREIGN KEY (afspraak_id) REFERENCES afspraken(id) ON DELETE SET NULL
) ENGINE=InnoDB;
 
CREATE TABLE factuurregels (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    factuur_id      INT NOT NULL,
    behandeling_id  INT NOT NULL,
    aantal          INT NOT NULL DEFAULT 1,
    prijs_per_stuk  DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (factuur_id)     REFERENCES facturen(id) ON DELETE CASCADE,
    FOREIGN KEY (behandeling_id) REFERENCES behandelingen(id)
) ENGINE=InnoDB;
 
-- Optioneel: betalingsgateway (bv. iDEAL/Mollie)
CREATE TABLE betalingen (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    factuur_id      INT NOT NULL,
    bedrag          DECIMAL(10,2) NOT NULL,
    methode         ENUM('ideal','pin','contant','overboeking') NOT NULL,
    transactie_ref  VARCHAR(100),
    betaald_op      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (factuur_id) REFERENCES facturen(id) ON DELETE CASCADE
) ENGINE=InnoDB;
 
-- ---------------------------------------------------------------------
-- 4. Communicatie
-- ---------------------------------------------------------------------
CREATE TABLE meldingen (                             -- automatische e-mail / sms
    id              INT AUTO_INCREMENT PRIMARY KEY,
    gebruiker_id    INT NOT NULL,
    afspraak_id     INT NULL,
    kanaal          ENUM('email','sms') NOT NULL,
    onderwerp       VARCHAR(150),
    bericht         TEXT NOT NULL,
    status          ENUM('wachtrij','verzonden','mislukt') NOT NULL DEFAULT 'wachtrij',
    verzonden_op    DATETIME NULL,
    FOREIGN KEY (gebruiker_id) REFERENCES gebruikers(id) ON DELETE CASCADE,
    FOREIGN KEY (afspraak_id)  REFERENCES afspraken(id)  ON DELETE SET NULL
) ENGINE=InnoDB;
 
CREATE TABLE mededelingen (                          -- tandarts -> patiënten
    id              INT AUTO_INCREMENT PRIMARY KEY,
    medewerker_id   INT NOT NULL,
    type            ENUM('aankomende_afspraak','nieuwe_behandeling','tandheelkundige_tip') NOT NULL,
    titel           VARCHAR(150) NOT NULL,
    inhoud          TEXT NOT NULL,
    gepubliceerd_op DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medewerker_id) REFERENCES medewerkers(id)
) ENGINE=InnoDB;
 
CREATE TABLE feedback (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    afspraak_id     INT NOT NULL UNIQUE,
    patient_id      INT NOT NULL,
    beoordeling     TINYINT NOT NULL,
    opmerking       TEXT,
    aangemaakt_op   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_beoordeling CHECK (beoordeling BETWEEN 1 AND 5),
    FOREIGN KEY (afspraak_id) REFERENCES afspraken(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id)  REFERENCES patienten(id) ON DELETE CASCADE
) ENGINE=InnoDB;
 
-- ---------------------------------------------------------------------
-- 5. Beveiliging en privacy: wie heeft welke gegevens ingezien/gewijzigd
-- ---------------------------------------------------------------------
CREATE TABLE audit_log (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    gebruiker_id    INT NULL,
    actie           VARCHAR(50)  NOT NULL,           -- bv. 'inzien', 'wijzigen', 'verwijderen'
    tabel_naam      VARCHAR(50)  NOT NULL,
    record_id       INT NULL,
    tijdstip        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_adres        VARCHAR(45),
    FOREIGN KEY (gebruiker_id) REFERENCES gebruikers(id) ON DELETE SET NULL
) ENGINE=InnoDB;
 
-- =====================================================================
-- TESTDATA
-- (alle accounts hebben het wachtwoord: wachtwoord)
-- (hash is bcrypt, zoals Laravel Hash::make() dat ook maakt)
-- =====================================================================
 
INSERT INTO rollen (id, naam, omschrijving) VALUES
(1, 'admin',         'Praktijkmanagement en systeembeheer'),
(2, 'tandarts',      'Behandelaar, beheert agenda en dossiers'),
(3, 'mondhygienist', 'Gebitsreiniging en preventie'),
(4, 'assistent',     'Ondersteunt balie, dossiers en afspraken'),
(5, 'patient',       'Patiënt van de praktijk');
 
INSERT INTO gebruikers (id, rol_id, email, wachtwoord_hash, voornaam, tussenvoegsel, achternaam, telefoon) VALUES
(1,  1, 'admin@tandarts.nl',        '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC',     'Sanne',  'de',  'Vries',   '0612345601'),
(2,  2, 'p.jansen@tandarts.nl',     '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC', 'Pieter', NULL,  'Jansen',  '0612345602'),
(3,  2, 'f.elamrani@tandarts.nl',   '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC', 'Fatima', 'el',  'Amrani',  '0612345603'),
(4,  3, 'l.bakker@tandarts.nl',     '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC',   'Lotte',  NULL,  'Bakker',  '0612345604'),
(5,  4, 'm.visser@tandarts.nl',     '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC', 'Mark',   NULL,  'Visser',  '0612345605'),
(6,  5, 'jan.smit@mail.nl',         '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC',  'Jan',    NULL,  'Smit',    '0623456701'),
(7,  5, 'emma.mulder@mail.nl',      '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC',  'Emma',   NULL,  'Mulder',  '0623456702'),
(8,  5, 'ahmed.yilmaz@mail.nl',     '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC',  'Ahmed',  NULL,  'Yilmaz',  '0623456703'),
(9,  5, 'sophie.vandijk@mail.nl',   '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC',  'Sophie', 'van', 'Dijk',   '0623456704'),
(10, 5, 'daan.meijer@mail.nl',      '$2y$10$NLDOw4Zduv7Tnc2txOM4xO1pyYTw8SnYfS83ZS4ecrQ6pLwiwP5iC',  'Daan',   NULL,  'Meijer',  '0623456705');
 
INSERT INTO medewerkers (id, gebruiker_id, functie, big_nummer, specialisatie) VALUES
(1, 2, 'tandarts',      '19012345601', 'Algemene tandheelkunde'),
(2, 3, 'tandarts',      '19012345602', 'Endodontologie'),
(3, 4, 'mondhygienist', '29012345603', 'Preventie en parodontologie'),
(4, 5, 'assistent',     NULL,          'Baliewerk en stoelassistentie');
 
INSERT INTO patienten (id, gebruiker_id, geboortedatum, straat, huisnummer, postcode, woonplaats, verzekeraar, polisnummer, avg_toestemming, avg_datum) VALUES
(1, 6,  '1985-03-12', 'Kerkstraat',   '12',  '3511 AB', 'Utrecht',   'Zilveren Kruis', 'ZK1234567', TRUE, '2026-01-10 10:00:00'),
(2, 7,  '1992-07-25', 'Lindelaan',    '4A',  '3581 CD', 'Utrecht',   'CZ',             'CZ7654321', TRUE, '2026-02-03 14:30:00'),
(3, 8,  '1978-11-02', 'Maanstraat',   '88',  '3531 EF', 'Utrecht',   'VGZ',            'VG1122334', TRUE, '2026-03-15 09:15:00'),
(4, 9,  '2001-05-30', 'Dorpsweg',     '7',   '3454 GH', 'De Meern',  'Menzis',         'MZ5566778', TRUE, '2026-04-21 16:45:00'),
(5, 10, '1969-09-18', 'Stationsplein','101', '3511 JK', 'Utrecht',   'Zilveren Kruis', 'ZK9988776', TRUE, '2026-05-02 11:00:00');
 
INSERT INTO medisch_dossier (patient_id, allergieen, medicatie, aandoeningen, notities, bijgewerkt_door) VALUES
(1, 'Penicilline',      NULL,               NULL,               'Gevoelig gebit bij koude dranken.',        2),
(2, NULL,               NULL,               NULL,               'Regelmatige controle, geen bijzonderheden.', 5),
(3, 'Latex',            'Bloedverdunner',   'Diabetes type 2',  'Voorzichtig bij extracties.',              3),
(4, NULL,               NULL,               NULL,               'Draagt beugel (vast).',                    5),
(5, 'Lidocaïne',        'Bloeddrukverlager','Hoge bloeddruk',   'Tandvleesontsteking in behandeling.',      2);
 
INSERT INTO behandelingen (id, naam, omschrijving, duur_minuten, prijs) VALUES
(1, 'Periodieke controle',         'Controle van gebit en tandvlees',          20,  32.50),
(2, 'Gebitsreiniging',             'Verwijderen van tandsteen en aanslag',     30,  55.00),
(3, 'Vulling',                     'Behandelen van een gaatje',                45,  95.00),
(4, 'Wortelkanaalbehandeling',     'Behandeling van een ontstoken zenuw',      90, 320.00),
(5, 'Röntgenfoto',                 'Intra-orale röntgenfoto',                  15,  25.00),
(6, 'Tanden bleken',               'Professionele bleekbehandeling',           60, 250.00),
(7, 'Extractie',                   'Verwijderen van een tand of kies',         45, 110.00);
 
INSERT INTO beschikbaarheid (medewerker_id, dag_van_week, begintijd, eindtijd) VALUES
(1, 1, '08:30', '12:30'), (1, 1, '13:30', '17:00'),
(1, 2, '08:30', '12:30'), (1, 3, '08:30', '12:30'), (1, 3, '13:30', '17:00'),
(1, 4, '08:30', '17:00'), (1, 5, '08:30', '12:30'),
(2, 2, '09:00', '17:00'), (2, 3, '09:00', '17:00'), (2, 4, '09:00', '13:00'),
(3, 1, '09:00', '17:00'), (3, 3, '09:00', '17:00'), (3, 5, '09:00', '13:00');
 
INSERT INTO afspraken (id, patient_id, medewerker_id, behandeling_id, start_tijd, eind_tijd, status, opmerking) VALUES
(1, 1, 1, 1, '2026-11-10 09:00:00', '2026-11-10 09:20:00', 'afgerond',    'Halfjaarlijkse controle'),
(2, 2, 3, 2, '2026-11-11 10:00:00', '2026-11-11 10:30:00', 'afgerond',    NULL),
(3, 3, 2, 3, '2026-11-12 14:00:00', '2026-11-12 14:45:00', 'afgerond',    'Vulling linksonder'),
(4, 4, 1, 1, '2026-11-24 09:30:00', '2026-11-24 09:50:00', 'bevestigd',   NULL),
(5, 5, 3, 2, '2026-11-25 11:00:00', '2026-11-25 11:30:00', 'aangevraagd', 'Graag een ochtendafspraak'),
(6, 1, 2, 5, '2026-11-30 13:30:00', '2026-11-30 13:45:00', 'geannuleerd', 'Geannuleerd door patiënt'),
(7, 2, 1, 4, '2026-12-02 10:00:00', '2026-12-02 11:30:00', 'bevestigd',   'Wortelkanaalbehandeling');
 
INSERT INTO facturen (id, factuurnummer, patient_id, afspraak_id, factuurdatum, vervaldatum, totaalbedrag, status) VALUES
(1, 'F-2026-0001', 1, 1, '2026-11-10', '2026-12-10',  32.50, 'betaald'),
(2, 'F-2026-0002', 2, 2, '2026-11-11', '2026-12-11',  55.00, 'openstaand'),
(3, 'F-2026-0003', 3, 3, '2026-11-12', '2026-12-12', 120.00, 'betaald');
 
INSERT INTO factuurregels (factuur_id, behandeling_id, aantal, prijs_per_stuk) VALUES
(1, 1, 1,  32.50),
(2, 2, 1,  55.00),
(3, 3, 1,  95.00),
(3, 5, 1,  25.00);
 
INSERT INTO betalingen (factuur_id, bedrag, methode, transactie_ref, betaald_op) VALUES
(1,  32.50, 'ideal', 'TR-100001', '2026-11-10 09:30:00'),
(3, 120.00, 'pin',   'TR-100002', '2026-11-12 14:55:00');
 
INSERT INTO meldingen (gebruiker_id, afspraak_id, kanaal, onderwerp, bericht, status, verzonden_op) VALUES
(9,  4, 'email', 'Bevestiging afspraak', 'Uw afspraak op 24-11-2026 om 09:30 is bevestigd.',        'verzonden', '2026-11-18 08:00:00'),
(9,  4, 'sms',   NULL,                   'Herinnering: afspraak op 24-11 om 09:30 bij Tandarts.',  'wachtrij',  NULL),
(7,  7, 'email', 'Bevestiging afspraak', 'Uw afspraak op 02-12-2026 om 10:00 is bevestigd.',        'verzonden', '2026-11-18 08:05:00'),
(10, 5, 'email', 'Aanvraag ontvangen',   'Wij hebben uw afspraakaanvraag ontvangen.',               'verzonden', '2026-11-17 16:20:00');
 
INSERT INTO mededelingen (medewerker_id, type, titel, inhoud) VALUES
(1, 'tandheelkundige_tip',  'Poets 2x per dag',          'Poets twee keer per dag twee minuten en gebruik ragers of floss.'),
(3, 'tandheelkundige_tip',  'Minder tussendoortjes',     'Vermijd veel suikerhoudende tussendoortjes om gaatjes te voorkomen.'),
(1, 'nieuwe_behandeling',   'Tanden bleken nu mogelijk', 'Vanaf deze maand bieden wij professionele bleekbehandelingen aan.');
 
INSERT INTO feedback (afspraak_id, patient_id, beoordeling, opmerking) VALUES
(1, 1, 5, 'Snelle en vriendelijke service.'),
(2, 2, 4, 'Prettige behandeling, wel even moeten wachten.'),
(3, 3, 5, 'Heel zorgvuldig uitgelegd wat er gebeurde.');
 
INSERT INTO audit_log (gebruiker_id, actie, tabel_naam, record_id, ip_adres) VALUES
(2, 'inzien',   'medisch_dossier', 1, '192.168.1.10'),
(5, 'wijzigen', 'medisch_dossier', 2, '192.168.1.11'),
(3, 'wijzigen', 'medisch_dossier', 3, '192.168.1.12');
 
-- =====================================================================
-- 6. Management dashboard (views)
-- =====================================================================
CREATE VIEW v_dashboard_afspraken_per_maand AS
SELECT DATE_FORMAT(start_tijd, '%Y-%m') AS maand,
       COUNT(*)                         AS aantal_afspraken,
       SUM(status = 'geannuleerd')      AS geannuleerd
FROM afspraken
GROUP BY DATE_FORMAT(start_tijd, '%Y-%m');
 
CREATE VIEW v_dashboard_omzet_per_maand AS
SELECT DATE_FORMAT(factuurdatum, '%Y-%m') AS maand,
       SUM(totaalbedrag)                  AS omzet
FROM facturen
WHERE status IN ('openstaand','betaald')
GROUP BY DATE_FORMAT(factuurdatum, '%Y-%m');
 
CREATE VIEW v_dashboard_populaire_behandelingen AS
SELECT b.naam,
       COUNT(a.id) AS aantal
FROM behandelingen b
LEFT JOIN afspraken a ON a.behandeling_id = b.id AND a.status <> 'geannuleerd'
GROUP BY b.id, b.naam
ORDER BY aantal DESC;
 
-- Voorbeeldqueries:
-- SELECT * FROM v_dashboard_afspraken_per_maand;
-- SELECT * FROM v_dashboard_omzet_per_maand;
-- SELECT * FROM v_dashboard_populaire_behandelingen;