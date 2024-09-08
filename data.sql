CREATE TABLE polis (
    polis_id INT AUTO_INCREMENT PRIMARY KEY,
    num_polis INT NOT NULL,
    date_creation INT,
    groupe INT NOT NULL,
    champ INT NOT NULL,
    description LONGTEXT,
    first_cin VARCHAR(100)
);

CREATE TABLE client (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    cin VARCHAR(100),
    nom VARCHAR(255),
    prenom VARCHAR(255)
);

CREATE TABLE abonnement (
    polis_id INT,
    client_id INT,
    compteur INT,
    FOREIGN KEY (polis_id) REFERENCES polis(polis_id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES client(client_id) ON DELETE CASCADE
);

CREATE TABLE meta (
    taille_groupe INT,
    taille_champ INT,
    first_date INT
);

CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    motdepasse VARCHAR(255)
);
