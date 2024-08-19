<?php
require_once '../connect.php';

$minQuery = "SELECT MIN(champ) AS min_champ FROM polis";
$maxQuery = "SELECT MAX(champ) AS max_champ FROM polis";

    // Execute the queries and fetch results
    $stmtMin = $pdo->query($minQuery);
    $minResult = $stmtMin->fetch(PDO::FETCH_ASSOC);

    $stmtMax = $pdo->query($maxQuery);
    $maxResult = $stmtMax->fetch(PDO::FETCH_ASSOC);

    // Output the results
    $minChamp = $minResult['min_champ'] ?? 1;
    $maxChamp = $maxResult['max_champ'] ?? 1;
    echo "Minimum value of 'champ': " . $minChamp . "<br>";
    echo "Maximum value of 'champ': " . $maxChamp . "<br>";

    if(isset($_POST['article_min']) && isset($_POST['article_max']) && (!empty($_POST['article_min']) && !empty($_POST['article_max']))) {
        // Get the min and max values from POST
        $min = (int)$_POST['article_min'];
        $max = (int)$_POST['article_max'];
        if(isset($_POST['operation']) && $_POST['operation'] === 'ajouter'){
            try {
                
                $current_date = date('Y-m-d');
        
                // Prepare the SQL statement once
                $query = $pdo->prepare('INSERT INTO polis (num_polis, date_creation, groupe, champ) VALUES (:num, :date, :groupe, :champ)');
                // Loop through the range and insert each value
                for ($i = $min; $i <= $max; $i++) {
                    $verify = $pdo -> query("SELECT num_polis FROM polis WHERE num_polis = '$i'") -> fetchAll();
                    if(empty($verify)){
                        $group = intdiv($i - 1, 10) + 1;
                        $champ = intdiv($i - 1, 500) + 1;
                        $query->execute([
                            ':num' => $i,
                            ':date' => $current_date,
                            ':groupe' => $group,  // Replace with actual value or variable
                            ':champ' => $champ    // Replace with actual value or variable
                        ]);
                    }
                    
                    }
                    
        
            } catch (Throwable $t) {
                echo 'Cannot add records!<br>';
                echo $t->getMessage();
            }
        }elseif(isset($_POST['operation']) && $_POST['operation'] === 'supprimer'){
            $query = $pdo -> prepare('DELETE FROM polis WHERE num_polis = :num');
            for ($i = $min; $i <= $max; $i++) {
                $query -> execute([
                    ':num' => $i
                ]);
            }
        }elseif(!isset($_POST['operation'])){
            echo 'probleme !';
        }
    }else{
        echo '<br>rien de POST !<br>';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consult</title>
</head>
<body>
    <ul>

    
    <?php
    $champ_taille_by_article = 500;
    for($i = $minChamp; $i <= $maxChamp; $i ++):?>
    <li>
        <?php 
            $checkChamp = $pdo -> query("SELECT num_polis FROM polis WHERE champ = '$i'", PDO::FETCH_ASSOC) -> fetchAll();
            $article_min = ($i - 1) * $champ_taille_by_article + 1;
            $article_max = $article_min + $champ_taille_by_article - 1;
            if(!($checkChamp)):
                echo $i;?>
                <form action="" method="POST">
                    <input type="number" name="article_min" value="<?=$article_min ?>">
                    <input type="number" name="article_max" value="<?=$article_max ?>">
                    <input type="text" name="operation" value="ajouter">
                <button>
                    ajouter champ
                </button>
                </form>
                
                <?php
            else:
            ?>
            <a href="groupes.php?champ=<?=$i ?>">
                <?=$i ?>
            </a>
            <form action="" method="POST">
                    <input type="number" name="article_min" value="<?=$article_min ?>" hidden>
                    <input type="number" name="article_max" value="<?=$article_max ?>" hidden>
                    <input type="text" name="operation" value="supprimer" hidden>
                    <button>
                    supprimer champ
                    </button>
            </form>
            <?php endif; ?>
    </li>
    <?php
    endfor;
    ?>
    </ul>
</body>
</html>