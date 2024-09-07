<?php
require_once 'connect.php';
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
    function consultation(string $choice, int $i, PDO $pdo): void{
        switch($choice){
            case 'annee':
                echo '<a href="articles.php?annee=' . ($pdo -> query("SELECT date_creation FROM polis WHERE num_polis = '$i'") -> fetch())['date_creation'] . '">Consulter l\'année</a>';
                break;
            case 'champ':
                echo '<a href="groupes.php?champ=' . $i . '">Consulter le champ</a>';
                break;
            default:
            echo '<a href="groupes.php?champ=' . $i . '">Consulter le champ</a>';
        }
    }
    if(isset($_GET['mode']) && !empty($_GET['mode'])){
        $choice = $_GET['choice'];
    }else{
        $_GET['mode']= 'champ';
    }
    if(isset($_POST['champ_ajoute']) && !empty($_POST['champ_ajoute'])) {
        // Get the min and max values from POST
        $min = ($_POST['champ_ajoute'] - 1) * $taille_groupe * $taille_champ + 1;
        $max = $min + $taille_groupe * $taille_champ - 1;

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
                    $prenom = 'Prenom Inconnu';
                    $query -> execute([
                        ':num' => $i,
                        ':date' => $date,
                        ':groupe' => $groupe,
                        ':champ' => $champ,
                        ':description' => $description,
                        ':cin' => $cin
                    ]);
                    $insert_client = $pdo -> prepare("INSERT INTO client (cin, nom, prenom) VALUES ('$cin', '$nom', '$prenom')");
                    $insert_client -> execute();
                    $abonnement -> execute([
                        ':compt' => 1,
                        ':polis' => ($pdo -> query("SELECT polis_id FROM polis WHERE num_polis = " . $pdo -> quote($i)) -> fetch())['polis_id'],
                        ':client' => ($pdo -> query("SELECT client_id FROM client INNER JOIN polis ON first_cin = cin where first_cin = " . $pdo -> quote($cin)) -> fetch())['client_id']
                    ]);
                }
            }
        
    }
    if(isset($_POST['champ_edit'])){
        $min = ($_POST['champ_edit'] - 1) * $taille_groupe * $taille_champ + 1;
        $max = $min + $taille_groupe * $taille_champ - 1;

        for ($i = $min; $i <= $max; $i++) {
            $verify = $pdo -> query("SELECT num_polis FROM polis WHERE num_polis = '$i'") -> fetch();
            if(!empty($verify)){
                $group = intdiv($i - 1, $taille_groupe) + 1;
                $query = $pdo -> prepare('UPDATE polis SET date_creation = ' . $pdo -> quote($_POST['date']) . ', description =  ' . $pdo -> quote($_POST['description'])  . ' WHERE num_polis = ' . $pdo -> quote($i));
                $query -> execute();
            }

    }
}
        if(isset($_POST['annee_edit'])){
            $min = ($pdo -> query("SELECT MIN(num_polis) as min FROM polis WHERE date_creation = " . $pdo -> quote($_POST['annee_supprime'])) -> fetch())['min'];
            $max = ($pdo -> query("SELECT MAX(num_polis) as max FROM polis WHERE date_creation = " . $pdo -> quote($_POST['annee_supprime'])) -> fetch())['max'];
            for($i = $min; $i <= $max; $i++){
                $verify = $pdo -> query("SELECT num_polis FROM polis WHERE num_polis = '$i'") -> fetch();
                if(!empty($verify)){
                    $group = intdiv($i - 1, $taille_groupe) + 1;
                    $query = $pdo -> prepare('UPDATE polis SET description =  ' . $pdo -> quote($_POST['description'])  . ' WHERE num_polis = ' . $pdo -> quote($i));
                    $query -> execute();
                }
            }
            $query = $pdo -> prepare('UPDATE polis SET description =  ' . $pdo -> quote($_POST['description']) . ' WHERE num_polis = ' . $pdo -> quote($i));
            $query -> execute();
    }
    if(isset($_POST['champ_supprime'])){

        $min = ($_POST['champ_supprime'] - 1) * $taille_groupe * $taille_champ + 1;
        $max = $min + $taille_groupe * $taille_champ - 1;
        $query = $pdo -> prepare('DELETE FROM polis WHERE champ = ' . $pdo -> quote($_POST['champ_supprime']));
        $query -> execute();
        $_POST['choice'] = 'annee';
    }

    if(isset($_POST['annee_supprime'])){
        $min = ($pdo -> query("SELECT MIN(num_polis) as min FROM polis WHERE date_creation = " . $pdo -> quote($_POST['annee_supprime'])) -> fetch())['min'];
        $max = ($pdo -> query("SELECT MAX(num_polis) as max FROM polis WHERE date_creation = " . $pdo -> quote($_POST['annee_supprime'])) -> fetch())['max'];
        $query = $pdo -> prepare("DELETE FROM polis WHERE num_polis BETWEEN '$min' AND '$max'");
        $query -> execute();

    }
    
    $minQuery = "SELECT MIN(champ) AS min_champ FROM polis";
    $maxQuery = "SELECT MAX(champ) AS max_champ FROM polis";

    // Execute the queries and fetch results
    $stmtMin = $pdo->query($minQuery);
    $minResult = $stmtMin->fetch(PDO::FETCH_ASSOC);

    $stmtMax = $pdo->query($maxQuery);
    $maxResult = $stmtMax->fetch(PDO::FETCH_ASSOC);

    // Output the results
    $minChamp = $minResult['min_champ'] ?? 1;
    $maxChamp = $maxResult['max_champ'] ?? 0;
    
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation</title>
    <link rel="icon" type="image/x-icon" href="archive.png">
    <link rel="stylesheet" href="champs.css">
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

        </nav>
            
        
    </header>
    <div class="container">
        <div class="choice">
            <div class="info">
            <img src="info.gif">
            <div class="paragraphe mode">
                <h1>Mode d'affichage</h1>
                Une fonctionalité intéressante permettant de basculer entre deux modes d'affichage: un selon les années et l'autre selon les champs. <br>
                Choisissez un des deux modes pour visualiser l'existant !
            </div>
            </div>
            
        <form action="" method="GET">
        <div class="paragraphe champ">
            <h2>Mode champ</h2>
                Le mode en champs visualise simplement les champs indépendament de leurs dates associées. Compatible pour des cas où on ne s'intéresse pas aux dates.
                Chaque champ contient <span style="color:red"><?=$taille_champ ?></span> groupes.
            </div>
        <input type="submit" id="champ" name="mode" value="champ">
    </form>

    <div class="line"></div>

    <form action="" method="GET">
    <div class="paragraphe champ">
                <h2>Mode Année</h2>
                Le mode en années visualise les données selon les dates associées. <br>Le nombre d'articles par année n'est pas limité/déterminé.
                un comportement indésirable pourrait être rencontré si on n'a pas bien fait attention aux dates au moment de l'ajout des articles.<br>
                Cependant, si vous voulez faire des statistiques via les années, configurer les un par un !
            </div>
        <input type="submit" id="annee" name="mode" value="annee">
    </form>
        </div>
   
<div class="separation"></div>
    <ul>
    <?php
    if($_GET['mode'] === 'annee'):
        for($i = (int)date('Y'); $i >= $first_date; $i--): ?>
        <li>
            

            
            <?php
        
        $checkDate = $pdo -> query("SELECT num_polis FROM polis WHERE date_creation = '$i'") -> fetchAll();
        $article_max = ($pdo -> query("SELECT MIN(num_polis) as min FROM polis WHERE date_creation = $i") -> fetch())['min'];
        $article_min = ($pdo -> query("SELECT MAX(num_polis) as max FROM polis WHERE date_creation = $i") -> fetch())['max'];
    
        if(empty($checkDate)):
            echo '<div class="content">';
            echo '<div class="empty-year">';
            echo '<div class="year">';
            echo $i;
            echo '</div>';
            echo '</div>';
            echo '</div>';?>
            <?php
        else:
            echo '<div class="content">';
            echo '<a href="/articles.php?annee='. $i. '" class="consult">';
            echo '<div class="year">';
            echo $i;
            echo '</div>';
            echo '</a>'; 
            ?>
            <div class="operation">
            <form action="" method="POST">
                    <button class="add" name="annee_supprime" value="<?=$i ?>">
                    <img src="delete.gif">
                    </button>
            </form>
            </div>

            </div>

            <div class="count">
                <div class="exist">
                <?=count($checkDate) ?>
                </div>
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
                                    <select name="date" class="date" disabled>
                                        <option value="<?=$i ?>"><?=$i ?></option>
                                    </select>
                                </div>
                                <textarea name="description" placeholder="Entrer une description, des informations complémentaires (supplémentaire). Pour les valeurs nouvelles."></textarea>
                                <div class="submit">
                                    <button name="annee_edit" value="<?=$i ?>">
                                        Editer l'année
                                    </button>
                                </div>
                            </form>
            </div>
            </div>
            
            <?php
        endif; 
            ?>
            </li>
            <?php
            endfor;

    else:
    $champ_taille_by_article = $taille_groupe * $taille_champ;
    for($i = 1; $i <= $maxChamp + 1; $i++):
    
    ?>
    
    <li>
        <div class="content">
        <?php 
            $checkChamp = $pdo -> query("SELECT num_polis FROM polis WHERE champ = '$i'") -> fetchAll();
            $article_min = ($i - 1) * $champ_taille_by_article + 1;
            $article_max = $article_min + $champ_taille_by_article - 1;
            if(empty($checkChamp)):
                echo '<a href="groupes.php?champ=' . $i . '" class="consult">';
                    echo '<div class="min">';
                    echo $article_min;
                    echo '</div>';
                    echo '<div class="max">';
                    echo $article_max;
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
                                        <option value="<?=(int)date('Y') ?>"><?=(int)date('Y') ?></option>
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
                                    <button name="champ_ajoute" value="<?=$i ?>">
                                        Ajouter le champ
                                    </button>
                                </div>
                            </form>
                </div>

                </div>
               
                </div>
                
                
                <?php
            else:
                echo '<a href="groupes.php?champ=' . $i . '" class="consult">';
                    echo '<div class="min">';
                    echo $article_min;
                    echo '</div>';
                    echo '<div class="max">';
                    echo $article_max;
                    echo '</div>';
                echo '</a>';
            ?>
            
            <br>
            <?php
            /**
             * Selon les articles
             */
                //known min/max - SQL request
                $known = $pdo -> query("SELECT MIN(num_polis) as min, MAX(num_polis) as max, COUNT(num_polis) as total FROM polis WHERE champ = '$i'") -> fetch();
                /**
                 * Selon les groupes
                 */
                //Default values for groupes
                $groupe_min = ($i - 1) * $taille_champ + 1;
                $groupe_max = $groupe_min + $taille_champ - 1;

                $groupe_meta = $pdo -> query("SELECT groupe, COUNT(num_polis) as num FROM polis WHERE champ = '$i' GROUP BY groupe") -> fetchAll();

                $groupes = array();
                for($j = 1; $j <= 10; $j++){
                    $groupes[$j] = 0; 
                }
                foreach($groupe_meta as $groupe){
                    if(!empty($groupe['num'])){
                        $groupes[$groupe['groupe']] = $groupe['num'];
                    }
                }
                
                /*
                echo 'Les groupes pleins sont : [';

                foreach($plein as $groupe){
                    echo 'groupe ' . $groupe . ', ';
                }
                echo ']';

                echo 'Les groupes partiellemnt pleins sont : ' . $partiel . '<br>';
                echo 'Les groupes vides sont : ' . $complet . '<br>';*/
                 ?>
                 <div class="operation">
                 <form action="" method="POST">
                    <button name="champ_supprime" value="<?=$i ?>">
                    <img src="delete.gif">
                    </button>
            </form> <?php
            if(count($checkChamp) !== $champ_taille_by_article): ?>
            <form action="" method="POST">
                <button class="complete-button">
                    <img src="complete.gif">
                </button>
                </form>
                <div class="complete-dialog">
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
                                    <button name="champ_ajoute" value="<?=$i ?>">
                                        Compléter le champ
                                    </button>
                                </div>
                            </form>
                </div>
                </div>
                
                </div>
                <?php
                else: ?>
                </div>
            <?php endif; ?>
            
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
                                    <button name="champ_edit" value="<?=$i ?>">
                                        Editer le champ
                                    </button>
                                </div>
                            </form>
            </div>
            </div>
            <div class="count">
                <div class="known">
                <?=$known['total'] ?>
                </div>
                <div class="cross"></div>
                <div class="total"><?=$taille_champ * $taille_groupe ?></div>
            </div>
            <?php endif; ?>
            
            
    </li>
   
    <?php
    
    endfor;
    endif; ?>
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
<script src="complete.js"></script>
<script src="add.js"></script>
</body>
</html>