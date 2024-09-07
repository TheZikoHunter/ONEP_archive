<?php 
require_once 'connect.php';

$champ = $_GET['champ'];

$minGroupe = ($champ - 1) * $taille_champ + 1;
$maxGroupe = $minGroupe + $taille_champ - 1;
if(isset($_POST['date']) && !empty($_POST['date'])){
    $date = $_POST['date'];
}else{
    $date = date('Y');
}
if(isset($_POST['description'])){
    $description = $_POST['description'];
}else{
    $description = 'Aucune description';
}
if(isset($_POST['groupe_ajoute']) && !empty($_POST['groupe_ajoute'])) {
    // Get the min and max values from POST
    $min = ((int)$_POST['groupe_ajoute'] - 1) * $taille_groupe + 1;
    $max = (int)$_POST['groupe_ajoute'] * $taille_groupe;
            $current_date = date('Y');
    
            // Prepare the SQL statement once
            $query = $pdo->prepare('INSERT INTO polis (num_polis, date_creation, groupe, champ, description, first_cin) VALUES (:num, :date, :groupe, :champ, :description, :cin)');
            $abonnement = $pdo -> prepare("INSERT INTO abonnement (compteur, polis_id, client_id) VALUES (:compt, :polis, :client)");
            // Loop through the range and insert each value
            for ($i = $min; $i <= $max; $i++) {
                $polis_exists = $pdo -> query("SELECT 1 FROM polis WHERE num_polis = " . $pdo -> quote($i)) -> fetch();
                if(!$polis_exists){
                    $groupe = intdiv((int)$i -1, $taille_groupe) + 1;
                    $champ = intdiv((int)$i -1, $taille_champ * $taille_groupe) + 1;
                    $cin = 'CLIENT_' . $i;
                    $nom = 'NOM INCONNU';
                    $prenom = 'Nom Inconnu';
                    $query -> execute([
                        ':num' => $i,
                        ':date' => $date,
                        ':groupe' => $groupe,
                        ':champ' => $champ,
                        ':description' => $description,
                        ':cin' => $cin
                    ]);
                    $insert_client = $pdo -> prepare("INSERT INTO client (cin, nom, prenom) VALUES ('" . $cin . "', '$nom', '$prenom')");
                    $insert_client -> execute();
                    $abonnement -> execute([
                        ':compt' => 1,
                        ':polis' => ($pdo -> query("SELECT polis_id FROM polis WHERE num_polis = " . $pdo -> quote($i)) -> fetch())['polis_id'],
                        ':client' => ($pdo -> query("SELECT client_id FROM client INNER JOIN polis ON first_cin = cin where first_cin = " . $pdo -> quote($cin)) -> fetch())['client_id']
                    ]);
                }
            }
    }
    if(isset($_POST['groupe_supprime']) && !empty($_POST['groupe_supprime'])){
        $query = $pdo -> prepare('DELETE FROM polis WHERE num_polis = :num');
        $min = ((int)$_POST['groupe_supprime'] - 1) * $taille_groupe + 1;
        $max = (int)$_POST['groupe_supprime'] * $taille_groupe;
        for ($i = $min; $i <= $max; $i++) {
            $query -> execute([
                ':num' => $i
            ]);
        }
    }
    if(isset($_POST['groupe_edit'])){
        $min = ($_POST['groupe_edit'] - 1) * $taille_groupe + 1;
        $max = $min + $taille_groupe - 1;

        for ($i = $min; $i <= $max; $i++) {
            $verify = $pdo -> query("SELECT num_polis FROM polis WHERE num_polis = '$i'") -> fetch();
            if(!empty($verify)){
                $group = intdiv($i - 1, $taille_groupe) + 1;
                $query = $pdo -> prepare('UPDATE polis SET date_creation = ' . $pdo -> quote($_POST['date']) . ', description =  ' . $pdo -> quote($_POST['description']) . ' WHERE num_polis = ' . $pdo -> quote($i));
                $query -> execute();
            }

    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter groupes</title>
    <link rel="icon" type="image/x-icon" href="archive.png">
    <link rel="stylesheet" href="groupes.css">
</head>
<body>
<header>
        
        <div class="head">
            <h1>Office National de L'Electricité et de l'Eau Potable</h1>
            <div class="line"></div>
            <h2>Branche Eau</h2>
            <div class="line"></div>
        <h3>Guelmim</h3>
        </div>
        <nav>
        <a href="/">Acceuil</a>
        <a href="/clients.php">Clientèle</a>
        <a href="/add.php">Ajouter</a>
        <a href="/statistiques.php">Statistiques</a>
        <a href="/champs.php">Champs</a>

        </nav>
        <form action="" method="post" style="height: fit-content; width: fit-content; position:absolute; top:25%;)">
            <button name="logout" value="clear" style="z-index:5;position:absolute; background:none; border:none; height:fit-content;width:fit-content; cursor: pointer;">
                <img src="logout.gif" alt="" style="height:50px; width: 50px">
            </button>
        </form>
        
    </header>
    <div class="container">

    
    <ul>
    <?php
    for($i = $minGroupe; $i <= $maxGroupe; $i++):?>
    <li>
        <?php 
            $checkGroupe = $pdo -> query("SELECT num_polis FROM polis WHERE groupe = '$i'") -> fetchAll();
            $article_min = ($i - 1) * $taille_groupe + 1;
            $article_max = $article_min + $taille_groupe - 1;

            if(empty($checkGroupe)):
                echo '<a href="articles.php?groupe=' . $i . '" class="consult">';
                    echo '<div class="info-groupe">';
                    echo '<p class="groupe">Groupe ' . $i . '</p>';
                    echo '<p class="min">De ' . $article_min . '</p>';
                    echo '<p class="max">à ' . $article_max . '</p>';
                    echo '</div>';
                echo '</a>';
                ?>
                <div class="operation">
                <form action="" method="POST">
                    <button class="add add-button">
                        <img src="add.gif">
                    </button>
                </form>
                <div class="add-dialog">
                            <form action="" method="POST">
                                <div class="input-area">
                                    <select name="date" class="date">
                                        <option value="<?=(int)date('Y') ?>">Date</option>
                                        <?php 
                                            $date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
                                            for($j = (int)date('Y'); $j >= (int)$date_debut; $j--):
                                        ?>
                                        <option value="<?=$j ?>"><?=$j ?></option>
                                                <?php endfor; ?>
                                    </select>
                                </div>
                                <textarea name="description" placeholder="Entrer une description, des informations complémentaires (supplémentaire). Pour les valeurs nouvelles."></textarea>
                                <div class="submit">
                                    <button name="groupe_ajoute" value="<?=$i ?>">
                                        Ajouter le groupe
                                    </button>
                                </div>
                            </form>
                </div>
                </div>
                
                <?php
            else:
                echo '<a href="articles.php?groupe=' . $i . '" class="consult">';
                    echo '<div class="info-groupe">';
                    echo '<p class="groupe">Groupe ' . $i . '</p>';
                    echo '</div>';
                echo '</a>';
            ?>

<div class="operation">
                 <form action="" method="POST">
                    <button name="groupe_supprime" value="<?=$i ?>">
                    <img src="delete.gif">
                    </button>
            </form>
            <?php if(count($checkGroupe) !== $taille_groupe): ?>
                <form action="" method="POST">
                <button class="complete-button">
                    <img src="complete.gif">
                </button>
                </form>
                <div class="complete-dialog">
                            <form action="" method="POST">
                                <div class="input-area">
                                    <select name="date" class="date">
                                        <option value="<?=(int)date('Y') ?>"><?=(int)date('Y') ?></option>
                                        <?php 
                                            $date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
                                            for($j = (int)date('Y'); $j >= (int)$date_debut; $j--):
                                        ?>
                                        <option value="<?=$j ?>"><?=$j ?></option>
                                                <?php endfor; ?>
                                    </select>
                                </div>
                                <textarea name="description" placeholder="Entrer une description, des informations complémentaires supplémentaires."></textarea>
                                <div class="submit">
                                    <button name="groupe_ajoute" value="<?=$i ?>">
                                        Compléter le groupe
                                    </button>
                                </div>
                            </form>
                </div>
                
            <?php endif; ?>
            </div>
            
                
            <div class="count">
                <div class="known">
                <?=count($checkGroupe) ?>
                </div>
                <div class="cross"></div>
                <div class="total"><?=$taille_groupe ?></div>
            </div>
            <div class="edit-operation">
                <form action="" method="POST">
                <button class="edit-button">
                    <img src="edit.gif">
                </button>
                </form>
                <div class="edit-dialog">
                    <form action="" method="POST">
                        <div class="input-area">
                            <select name="date" class="date">
                                <option value="<?=(int)date('Y') ?>"><?=(int)date('Y') ?></option>
                                <?php 
                                    $date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
                                    for($j = (int)date('Y'); $j >= (int)$date_debut; $j--):
                                ?>
                                <option value="<?=$j ?>"><?=$j ?></option>
                                        <?php endfor; ?>
                            </select>
                        </div>
                        <textarea name="description" placeholder="Entrer une description, des informations supplémentaires."></textarea>
                        <div class="submit">
                            <button name="groupe_edit" value="<?=$i ?>">
                                Modifier le groupe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php endif; ?>
    </li>
    <?php
    endfor;
    ?>
    </ul>
    </div>
    
    <footer>
    <div class="box">
    <p class="head">
    Cette application est réalisée par <b>DOUIH Zakaria</b>. Pour le contact :
    </p>
    <ul>
        <li>
            <a href="mailto:douihzakaria@gmail.com" target="_blank">
                <img src="gmail-default.png">
                <p>GMAIL</p>
            </a>
        </li>
        <li class="line"></li>
        <li>
            <a href="https://www.linkedin.com/in/zakaria-douih-427174274/" target="_blank">
                <img src="linkedin-default.png">
                <p>LinkedIn</p>
            </a>
            
        </li>
        <li class="line"></li>
        <li>
            <a href="https://github.com/TheZikoHunter" target="_blank">
                <img src="github-default.png">
                <p>GitHub</p>
            </a>
        </li>
    </ul>
    </div>
    
    <br>
    <div>
    Toutes les images utilisées sont de <a href="https://icons8.com" target="_blank">icon8</a>, <a href="https://lordicon.com" target="_blank">lordicon</a>.
</div>
</footer>
<script src="edit.js"></script>
<script src="add.js"></script>
<script src="complete.js"></script>
</body>
</html>