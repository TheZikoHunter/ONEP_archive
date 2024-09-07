<?php
require_once 'connect.php';

//Defining the variables

if(isset($_POST['article']) && !empty($_POST['article'])){
    $insert = $_POST['article'];
}
if(isset($_POST['date']) && !empty($_POST['date'])){
    $date = $_POST['date'];
}else{
    $date = date('Y');
}
if(isset($_POST['article_min']) && !empty($_POST['article_min'])){
    $min = $_POST['article_min'];
    if(isset($_POST['article_max']) && !empty($_POST['article_max'])){
        $max = $_POST['article_max'];
    }else{
        $max = ($pdo -> query("SELECT MAX(num_polis) as max FROM polis") -> fetch())['max'];
        if((int)$max === 0){
            $max = 1;
        }
    }
}else{
    if(isset($_POST['article_max']) && !empty($_POST['article_max'])){
        $min = ($pdo -> query("SELECT MIN(num_polis) as min FROM polis") -> fetch())['min'];
        if((int)$min === 0){
            $min = 1;
        }
        $max = $_POST['article_max'];
    }
}
if(isset($insert)){
    $polis_exists = $pdo -> query("SELECT 1 FROM polis WHERE num_polis = $insert") -> fetch();
    if(!($polis_exists)):
    $groupe = intdiv((int)$insert -1, $taille_groupe) + 1;
    $champ = intdiv((int)$insert -1, $taille_champ * $taille_groupe) + 1;
    if(isset($_POST['description'])){
        $description = $_POST['description'];
    }else{
        $description = 'Aucune description';
    }
    $query = $pdo -> prepare("INSERT INTO polis (num_polis, date_creation, groupe, champ, description, first_cin) VALUES (:num, :date, :groupe, :champ, :description, :cin)");
    

    if(isset($_POST['client_cin']) && !empty($_POST['client_cin'])){
        $cin =  strtoupper($_POST['client_cin']);
    }else{
        $cin =  ('CLIENT_' . $insert);
    }
    $query -> execute([
        ':num' => $insert,
        ':date' => $date,
        ':groupe' => $groupe,
        ':champ' => $champ,
        ':description' => $description,
        ':cin' =>  ($cin)
    ]);

    $cin_existe = $pdo -> query("SELECT 1 FROM client WHERE cin = " . $pdo -> quote( ($cin))) -> fetch();
    if($cin_existe){
        if(isset($_POST['nom'])){
            $update = $pdo -> prepare("UPDATE client SET nom = :nom WHERE cin = :cin");
            $update -> execute([
                ':nom' => ucwords($_POST['nom']),
                ':cin' =>  ($cin)
            ]);
        }
        if(isset($_POST['prenom'])){
            $update = $pdo -> prepare("UPDATE client SET prenom = :prenom WHERE cin = :cin");
            $update -> execute([
                ':prenom' => ucwords($_POST['prenom']),
                ':cin' =>  ($cin)
            ]);
        }
    }else{
        $insert_client = $pdo -> prepare("INSERT INTO client (cin, nom, prenom) VALUES (:cin, :nom, :prenom)");

        if(isset($_POST['nom']) && !empty($_POST['nom'])){
            $nom = strtoupper($_POST['nom']);
        }else{
            $nom = 'NOM INCUNNU';
        }
        if(isset($_POST['prenom']) && !empty($_POST['prenom'])){
            $prenom = ucwords($_POST['prenom']);
        }else{
            $prenom =  ('Prenom Inconnu');
        }
        $insert_client -> execute([
            ':cin' => ($cin),
            ':nom' => ($nom),
            ':prenom' => ($prenom)
        ]);
    }
endif;
$abonnement = $pdo -> prepare("INSERT INTO abonnement (compteur, polis_id, client_id) VALUES (:compt, :polis, :client)");
$abonnement -> execute([
    ':compt' => 1,
    ':polis' => ($pdo -> query("SELECT polis_id FROM polis WHERE num_polis = " . $pdo -> quote($insert)) -> fetch())['polis_id'],
    ':client' => ($pdo -> query("SELECT client_id FROM client INNER JOIN polis ON first_cin = cin where first_cin = " . $pdo -> quote( ($cin))) -> fetch())['client_id']
]);
}

if(isset($min) && isset($max)){
    $query = $pdo -> prepare("INSERT INTO polis (num_polis, date_creation, groupe, champ, description, first_cin) VALUES (:num, :date, :groupe, :champ, :description, :cin)");
    $abonnement = $pdo -> prepare("INSERT INTO abonnement (compteur, polis_id, client_id) VALUES (:compt, :polis, :client)");
    if(isset($_POST['description'])){
        $description = $_POST['description'];
    }else{
        $description = 'Aucune description';
    }
    if(isset($_POST['date']) && !empty($_POST['date'])){
        $date = $_POST['date'];
    }else{
        $date = date('Y');
    }
    for($i = $min; $i <= $max; $i++){
        
        $polis_exists = $pdo -> query("SELECT 1 FROM polis WHERE num_polis = " . $pdo -> quote($i)) -> fetch();
        if(!$polis_exists){
            $groupe = intdiv((int)$i -1, $taille_groupe) + 1;
            $champ = intdiv((int)$i -1, $taille_champ * $taille_groupe) + 1;
            $cin = 'client_' . $i;
            $nom = 'nom inconnu';
            $prenom = 'prenom inconnu';
            $query -> execute([
                ':num' => $i,
                ':date' => $date,
                ':groupe' => $groupe,
                ':champ' => $champ,
                ':description' => $description,
                ':cin' =>  ($cin)
            ]);
            $insert_client = $pdo -> prepare("INSERT INTO client (cin, nom, prenom) VALUES ('$cin', '$nom', '$prenom')");
            $insert_client -> execute();
            $abonnement -> execute([
                ':compt' => 1,
                ':polis' => ($pdo -> query("SELECT polis_id FROM polis WHERE num_polis = " . $pdo -> quote($i)) -> fetch())['polis_id'],
                ':client' => ($pdo -> query("SELECT client_id FROM client INNER JOIN polis ON first_cin = cin where first_cin = " . $pdo -> quote( ($cin))) -> fetch())['client_id']
            ]);
        }
        
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout des données</title>
    <link rel="icon" type="image/x-icon" href="archive.png">
    <link rel="stylesheet" href="add.css">
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
        <a href="/champs.php">Consulter</a>
        <a href="/clients.php">Clientèle</a>
        <a href="/statistiques.php">Statistiques</a>
        </nav>
            
        
    </header>
    
    <div class="container">
    
    <form action="" method="POST">
        
        <div class="left">
        <p>
            <i>
            Cette section est dédiée à l'insertion d'une seule valeur à la fois. Vous pouvez entrer la date correspondante à cette valeur. <br>
            Sinon, l'application considère l'année actuelle <span style="color:brown"><?=date('Y') ?> </span>.<br>
            Ajouter un client si vous le saviez. Sinon, il reste inconnu jusqu'à une modification ultérieure.<br>
            </i>
        </p>
        <div class="input">
            <div class="article">
            <input type="number" name="article" placeholder="Seule valeur">
            </div>
            
            <div class="line"></div>
            <select name="date" class="date">
                <option value="<?=(int)date('Y') ?>">Date</option>
                <?php 
                    $date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
                    for($i = (int)date('Y'); $i >= (int)$date_debut; $i--):
                ?>
                <option value="<?=$i ?>"><?=$i ?></option>
                        <?php endfor; ?>
            </select>
        </div>
        <table class="client">
                        <tr class="client">
                            <td>1</td>
                            <td><input type="text" name="client_cin" placeholder="CIN" style="text-transform:uppercase"></td>
                        </tr>
                        <tr class="client">
                            <td><input type="text" name="nom" placeholder="Nom" style="text-transform:uppercase"></td>
                            <td><input type="text" name="prenom" placeholder="Prénom"></td>
                        </tr>
                    </table>


        </div>
        <div class="right">
            <p><i>
                Cette section est réservée pour l'insertion d'une plange des valeurs. <span style="color:blue">Chaque valeur aura automatiquement un client inconnu</span> à modifier plus tard ainsi que la
                même la date de l'abonnement.
                <br>
                Vous ne vous inquiétez pas concernant la redondance. C'est parfaitement géré.</i>
            </p>
            <div class="input">
                <div class="article">
                <input type="number" id="min" name="article_min" placeholder="Valeur minimale">
                <img src="vector.png" id="vector">
                <input type="number" id="max" name="article_max" placeholder="Valeur maximale">
                </div>
                <div class="line"></div>
                <select name="date" class="date">
                <option value="<?=(int)date('Y') ?>">Date</option>
                <?php 
                    $date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
                    for($i = (int)date('Y'); $i >= (int)$date_debut; $i--):
                ?>
                <option value="<?=$i ?>"><?=$i ?></option>
                        <?php endfor; ?>
            </select>
            </div>
            
        </div>
        <button class="plus add-button"></button>
        <div class="add-dialog">
            <div class="dialog">
            <textarea name="description" placeholder="Entrez une description, des informations supplémentaires."></textarea>
                        <button>
                            Ajouter l'/les article(s)
                        </button>
            </div>
                        
        </div>
<script src="add-page.js"></script>
</form>


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
</body>
</html>
