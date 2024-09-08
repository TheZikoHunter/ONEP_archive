<?php
session_start();

$servername = "localhost";
$username = "root";  // Nom d'utilisateur MySQL
$password = "";      // Mot de passe MySQL
$dbname = "archive_onep";

// Créer la connexion
$conn = new mysqli($servername, $username, $password);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si la base de données existe déjà
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    echo "Erreur lors de la création de la base de données: " . $conn->error;
}

// Fermer la connexion
$conn->close();

try {
    
    $dsn = 'mysql:dbname=archive_onep;host=127.0.0.1';
	$user = 'root';
	$password = '';
	
	

	$pdo = new PDO($dsn, $user, $password);

    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    // Handle any connection errors
   echo $e -> getMessage();
}

$start = $pdo -> prepare("CREATE TABLE IF NOT EXISTS polis (
    polis_id INT AUTO_INCREMENT PRIMARY KEY,
    num_polis INT NOT NULL,
    date_creation INT,
    groupe INT NOT NULL,
    champ INT NOT NULL,
    description LONGTEXT,
    first_cin VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS client (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    cin VARCHAR(100),
    nom VARCHAR(255),
    prenom VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS abonnement (
    polis_id INT,
    client_id INT,
    compteur INT,
    FOREIGN KEY (polis_id) REFERENCES polis(polis_id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES client(client_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS meta(
    taille_groupe INT,
    taille_champ INT,
    first_date INT
);

CREATE TABLE IF NOT EXISTS user(
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    motdepasse VARCHAR(255)
);
");

$start -> execute();
$start -> closeCursor();
$meta = $pdo -> query("SELECT * FROM meta") -> fetchAll();

if(!empty($meta)){
    $taille_groupe = $meta[0]['taille_groupe'];
    $taille_champ = $meta[0]['taille_champ'];
    $first_date = $meta[0]['first_date'];
}else{
    $taille_groupe = 10;
    $taille_champ = 50;
}

if(isset($_POST['input-champ']) && isset($_POST['input-groupe']) && isset($_POST['first-date']) && empty($enchain)){
    $query = $pdo -> prepare('INSERT INTO meta (taille_groupe, taille_champ, first_date) VALUES (:groupe, :champ, :date)');
    $query -> execute([
        ':groupe' => $_POST['input-groupe'],
        ':champ' => $_POST['input-champ'],
        ':date' => $_POST['first-date']
    ]);
}
$enchain = $pdo -> query("SELECT * FROM meta ") -> fetch();



if(!isset($_SESSION['loged'])){
    require_once 'auto.php';
    exit;
}
elseif(isset($_POST['logout'])){
    unset($_SESSION['loged']);
    session_destroy();
    require_once 'auto.php';
    exit;
}
?>
