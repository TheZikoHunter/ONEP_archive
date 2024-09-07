<?php
session_start();
// Path to your SQLite database file

$databasePath = 'archive.db';

try {
    // Create a new PDO instance and connect to the SQLite database
    $pdo = new PDO("sqlite:" . $databasePath);

    // Set error mode to exception to handle errors more gracefully
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    // Handle any connection errors
   
}

// Now we define the meta data to work with

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
