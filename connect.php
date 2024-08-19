<?php
// Path to your SQLite database file
$databasePath = '../archive.db';

try {
    // Create a new PDO instance and connect to the SQLite database
    $pdo = new PDO("sqlite:" . $databasePath);

    // Set error mode to exception to handle errors more gracefully
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    echo "Connected to the SQLite database successfully!";

    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS polis(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            num_polis INTEGER NOT NULL,
            date_creation DATE,
            groupe INTEGER NOT NULL,
            champ INTEGER NOT NULL);
    ";

    // Execute the create table query
    $pdo->exec($createTableQuery);

    echo "Table 'polis' is ready to use.";
} catch (PDOException $e) {
    // Handle any connection errors
    echo "Failed to connect to the SQLite database: " . $e->getMessage();
}
?>
