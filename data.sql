CREATE TABLE polis(
polis_id INTEGER PRIMARY KEY AUTOINCREMENT,
num_polis INTEGER NOT NULL,
date_creation INT,
groupe INTEGER NOT NULL,
champ INTEGER NOT NULL,
description LONGTEXT,
first_cin VARCHAR(100));

CREATE TABLE client(
client_id INTEGER PRIMARY KEY AUTOINCREMENT,
cin VARCHAR(100),
nom VARCHAR(255),
prenom VARCHAR(255)
);

PRAGMA foreign_keys = ON;

CREATE TABLE abonnement(
polis_id INTEGER,
client_id INTEGER,
compteur INTEGER,
FOREIGN KEY (polis_id) REFERENCES polis(id) ON DELETE CASCADE,
FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE
);

CREATE TABLE meta(
taille_groupe INT,
taille_champ INT,
first_date INT
);