<?php
require_once '../connect.php';
$groupe_taille = 10;
$champ_taille = 500;
if(isset($_POST['article']) && !empty($_POST['article'])){
    try{
       

        $group = intdiv(($_POST['article']) + 1, 10) + 1;
        $champ = intdiv(($_POST['article']) + 1, 500) + 1;

        $current_date = date('Y-m-d');
        $query = $pdo -> prepare('INSERT INTO polis (num_polis, date_creation, groupe, champ) VALUES (:num, :date, :groupe, :champ)');
        $query -> execute([
            ':num' => $_POST['article'],
            ':date' =>  $current_date,
            ':groupe' => $group,
            ':champ' => $champ
        ]);
        echo "New record inserted successfully with the current date!";
    }catch(Throwable $t){
echo 'Cannot add that mf!<br>';
echo $t -> getMessage();
    } 
}
//Adding multiple polis

if(isset($_POST['article_min']) && isset($_POST['article_max']) && (!empty($_POST['article_min']) || !empty($_POST['article_max']))) {
    try {
        // Create the 'polis' table if it doesn't exist
        $createTableQuery = "
        CREATE TABLE IF NOT EXISTS polis(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            num_polis INTEGER NOT NULL,
            date_creation DATE,
            groupe INTEGER NOT NULL,
            champ INTEGER NOT NULL
        );";

        $pdo->exec($createTableQuery);
        echo "Table 'polis' is ready to use.<br>";

        // Get the min and max values from POST
        $min = (int)$_POST['article_min'];
        $max = (int)$_POST['article_max'];
        $current_date = date('Y-m-d');

        // Prepare the SQL statement once
        $query = $pdo->prepare('INSERT INTO polis (num_polis, date_creation, groupe, champ) VALUES (:num, :date, :groupe, :champ)');

        // Loop through the range and insert each value
        for ($i = $min; $i <= $max; $i++) {
            $group = intdiv($i + 1, 10) + 1;
            $champ = intdiv($i + 1, 500) + 1;
            $query->execute([
                ':num' => $i,
                ':date' => $current_date,
                ':groupe' => $group,  // Replace with actual value or variable
                ':champ' => $champ    // Replace with actual value or variable
            ]);
        }

        echo "Records inserted successfully for range $min to $max with the current date!";

    } catch (Throwable $t) {
        echo 'Cannot add records!<br>';
        echo $t->getMessage();
    }
}
?>

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout des articles</title>
</head>
<body>
    <h1>Ajouter des articles</h1>
    <form action="" method="POST">
        <table>
            <tr>
                <td>
                    Ajouter un rang des valeurs
                </td>
                <td>
                    <label for="min">
                        Min
                    </label>
                    <input type="number" id="min" name="article_min">
                </td>
                <td>
                    <label for="max">
                        Max
                    </label>
                    <input type="number" id="max" name="article_max">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="article">
                        Ajouter un seul article 
                    </label>
                </td>
                <td>
                    <input type="number" name="article" id="article">
                </td>
            </tr>
            <tr>
                <input type="submit">
            </tr>
        </table>
    </form>
</body>
</html>
