<?php 
require_once 'connect.php';
$page = $_GET['page'] ?? 1;
/*$abonnements = $pdo -> query("SELECT c.CIN as CIN, nom, prenom, num_polis, compteur FROM abonnement a
LEFT JOIN client c ON c.client_id = a.client_id
LEFT JOIN polis p ON p.polis_id = a.polis_id") -> fetchAll();*/
if(isset($_POST['to_edit']) && !empty($_POST['to_edit'])){
    $id = $_POST['to_edit'];
    $new_nom =  ($_POST['nom'] ?? 'nom inconnu');
    $new_prenom =  ($_POST['prenom'] ?? 'prenom inconnu');
    $new_cin =  ($_POST['cin'] ?? 'client_' . $id);
    $edit = $pdo -> prepare("UPDATE client SET cin = :cin, nom = :nom, prenom = :prenom WHERE client_id = :id");

    $edit -> execute([
        ':id' => $id,
        ':nom' =>  ($new_nom),
        ':prenom' =>  ($new_prenom),
        ':cin' =>  ($new_cin)
    ]);
}
$query = "SELECT c.client_id as client_id, compteur, num_polis, cin, nom, prenom, date_creation FROM abonnement a 
INNER JOIN polis p ON p.polis_id = a.polis_id 
INNER JOIN client c ON c.client_id = a.client_id";


if(isset($_GET['s']) && !empty($_GET['s'])){
    $pattern =  strtoupper(htmlentities($_GET['s']));
    $query .= " WHERE UPPER(compteur) LIKE '%$pattern%' OR UPPER(num_polis) LIKE '%$pattern%' OR UPPER(cin) LIKE '%$pattern%' OR UPPER(nom) LIKE '%$pattern%' OR UPPER(prenom) LIKE '%$pattern%'";
}

$order = ' ORDER BY num_polis DESC';
if(isset($_GET['by']) && !empty($_GET['by'])){
    $order = ' ORDER BY ';
    switch($_GET['by']){
        case 'alpha':
            $order .= 'cin, nom, prenom';
            break;
        case 'ancien':
            $order .= 'date_creation ASC';
            break;
        case 'nouveau':
            $order .= 'date_creation DESC';
            break;
        default:
            $order = '';
            break;
    }
}
$query .= $order;
$offset = ((int)$page === 1) ? 0 : ((int)$page - 1) * 20;
$limit = 20;
$count = $query . ' LIMIT -1 OFFSET ' . $offset;
$query .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;

$rest = count($pdo -> query($count) -> fetchAll());

$abonnements = $pdo -> query($query) -> fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter clientèlle</title>
    <link rel="stylesheet" href="clients.css">
    <link rel="shortcut icon" href="archive.png" type="image/x-icon">
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
        <a href="/add.php">Ajouter</a>
        <a href="/champs.php">Consulter</a>
        <a href="/statistiques.php">Statistiques</a>

        </nav>
            
        
    </header>
    <div class="container">
    <div class="modal-overlay"></div>
        <table>
            <thead>
                <tr>
                    <td colspan="6">
                        <form action="" method="GET" class="search">
                        <label for="tri-list">Trier Par :</label>
                        <select name="by" id=tri-list"">
                            <option value="">Choisir un mode</option>
                            <option value="alpha">Alphabétique</option>
                            <option value="ancien">Les plus anciens</option>
                            <option value="nouveau">Les plus récents</option>
                        </select>
                        <input type="search" name="s" placeholder="Entrez un mot clé">
                        <button>
                            Recherche
                        </button>
                        </form>

                    </td>
                    
                </tr>
                <tr>
                    <th>CIN Client</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Polis</th>
                    <th>Date d'abonnement</th>
                    <th>Compteur</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($abonnements as $abonnement):
                ?>
                <tr>
                    <td>
                        <div class="edit-dialog">
                            <form action="" method="POST">
                                    <button class="annuler exit-button">
                                        Annuler
                                    </button>
                                    <button name="to_edit" value="<?=$abonnement['client_id'] ?>">
                                        Enregistrer
                                    </button>
                                <label for="cin_<?=$abonnement['cin'] ?>">CIN : </label>
                                <input id="cin_<?=$abonnement['cin'] ?>" type="text" value="<?=$abonnement['cin'] ?>" placeholder="CIN" name="cin" style="text-transform:uppercase">
                                <label for="nom_<?=$abonnement['nom'] ?>">Nom client :</label>
                                <input id="nom_<?=$abonnement['nom'] ?>" type="text" value="<?=$abonnement['nom'] ?>" placeholder="Nom" name="nom" style="text-transform:uppercase">
                                <label for="prenom_<?=$abonnement['prenom'] ?>">Prénom</label>
                                <input type="prenom_<?=$abonnement['prenom'] ?>" value="<?=$abonnement['prenom'] ?>" placeholder="Prénom" name="prenom">
                            </form>
                        </div>
                
                        <button class="edit-button">
                            <img src="edit.gif">
                        </button>
                    <?=$abonnement['cin'] ?></td>
                    <td><?=$abonnement['nom'] ?></td>
                    <td><?=$abonnement['prenom'] ?></td>
                    <td><?=$abonnement['num_polis'] ?></td>
                    <td><?=$abonnement['date_creation'] ?></td>
                    <td><?=$abonnement['compteur'] ?></td>
                </tr>
                <?php
                endforeach;
                ?>
            </tbody>
                
        </table>
                <form action="" method="GET" class="pagination">
                    <?php if($page > 1): ?>
                    <button name="page" value="<?=$page - 1 ?>">
                        Précédent
                    </button>
                    <?php endif; ?>
                    <p><?=$page ?></p>
                    <?php if($rest > 20): ?>
                    <button name="page" value="<?=$page + 1 ?>">
                        Suivant
                    </button>
                    <?php endif; ?>
                </form>
    </div>
    
    <script src="edit_client.js"></script>
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