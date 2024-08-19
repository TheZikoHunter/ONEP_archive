<?php 
require_once '../connect.php';
$champ_taille_by_group = 50;
$min = ($_GET['champ'] - 1) * $champ_taille_by_group + 1;
$max = $min + $champ_taille_by_group - 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter groupes</title>
</head>
<body>
    We have many groupes here in the <?=$_GET['champ'] ?>
    We do the same thing, exactly the same thing!<br>
    Now we have a champ number. The groupes possible for a $champ are between $min = ($champ - 1) * 50 + 1 and $max = $min + 50 - 1. Again, we see which one to be activated 
    and which one doesn't. How to know again? It's simple.
    <ul>
        <?php
        for($i = $min; $i <= $max; $i ++):?>
    <li>
        <?php 
            $checkGroupe = $pdo -> query("SELECT num_polis FROM polis WHERE groupe = '$i'", PDO::FETCH_ASSOC) -> fetchAll();
            if(!($checkGroupe)):
                echo $i;
            else:
            ?>
            <a href="articles.php?groupe=<?=$i ?>">
                <?=$i ?>
            </a>
            <?php endif; ?>
    </li>
    <?php
    endfor;?>
    </ul>
</body>
</html>