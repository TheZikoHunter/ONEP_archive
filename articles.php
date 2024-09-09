<?php 
require_once 'connect.php';
/*
Supprime un article
*/
if(isset($_POST['article_supprime']) && !empty($_POST['article_supprime'])){
    $query = $pdo -> prepare("DELETE FROM polis WHERE num_polis = :num");
    $delete = $query -> execute([
        ':num' => $_POST['article_supprime']
    ]);
}

/*
Ajoute d'un article
*/
if(isset($_POST['article_add']) && !empty($_POST['article_add'])){
	//Definir la variable $insert = le polis a ajouter
    $insert = $_POST['article_add'];
}
/*
Elaboration de la requette
*/
if(isset($insert) && !empty($insert)){
	/*Verifie si le polis existe déjà*/
    $polis_exists = $pdo -> query("SELECT 1 FROM polis WHERE num_polis = $insert") -> fetch();
	/*
	Debut insertion d'un polis ==========================================================
	*/
    if(empty($polis_exists)):
		//Definir une date de l'article si entree, sinon enregistrer la date d'aujourd'hui
		if(isset($_POST['date']) && !empty($_POST['date'])){
			$date = $_POST['date'];
		}else{
			$date = date('Y');
		}
		//Definir le groupe et le champ et ajoute le nouveau polis
		$groupe = intdiv((int)$insert -1, $taille_groupe) + 1;
		$champ = intdiv((int)$insert -1, $taille_champ * $taille_groupe) + 1;
		//Ajouter une descriptiion
		if(isset($_POST['description']) && !empty($_POST['description'])){
			//On ajoute une description si entrée
			$description = $_POST['description'];
		}else{
			$description = 'Aucune description';
		}
		//Prepare la requette pour l'insertion
		$query = $pdo -> prepare("INSERT INTO polis (num_polis, date_creation, groupe, champ, description, first_cin) VALUES (:num, :date, :groupe, :champ, :description, :cin)");
		
		//Si le cin est entre, choisir le, sinon, entrer un client inconnu
		if(isset($_POST['client_cin']) && !empty($_POST['client_cin'])){
			$cin = strtoupper($_POST['client_cin']);
		}else{
			$cin = 'CLIENT_' . $insert;
		}
		//execute la requette utilisant les nouvelles données
		$query -> execute([
			':num' => $insert,
			':date' => $date,
			':groupe' => $groupe,
			':champ' => $champ,
			':description' => $description,
			':cin' => $cin
		]);
		/*
		Insertion d'un nouveau client a la table client
		*/
		//Verifie si le client existe deja
		$cin_existe = $pdo -> query("SELECT 1 FROM client WHERE cin = " . $pdo -> quote($cin)) -> fetch();
		//Insérer/mettre-a-jour le client selon le resultat
		if($cin_existe){
			if(isset($_POST['nom']) && !empty($_POST['nom'])){
				$update = $pdo -> prepare("UPDATE client SET nom = :nom WHERE cin = :cin");
				$update -> execute([
					':nom' => strtoupper($_POST['nom']),
					':cin' => $cin
				]);
			}
			if(isset($_POST['prenom']) && !empty($_POST['prenom'])){
				$update = $pdo -> prepare("UPDATE client SET prenom = :prenom WHERE cin = :cin");
				$update -> execute([
					':prenom' => ucwords($_POST['prenom']),
					':cin' => $cin
				]);
			}
		}else{
			$insert_client = $pdo -> prepare("INSERT INTO client (cin, nom, prenom) VALUES (:cin, :nom, :prenom)");

			if(isset($_POST['nom']) && !empty($_POST['nom'])){
				$nom = strtoupper($_POST['nom']);
			}else{
				$nom = 'NOM INCONNU';
			}
			if(isset($_POST['prenom']) && !empty($_POST['prenom'])){
				$prenom = ucwords($_POST['prenom']);
			}else{
				$prenom = 'Prenom Inconnu';
			}
			$insert_client -> execute([
				':cin' => $cin,
				':nom' => $nom,
				':prenom' => $prenom
			]);
		}
		/*
		Insertion d'une abonnement
		*/
		$compteur = 1;
		$verify_abonnement = $pdo -> query("SELECT 1 FROM (SELECT c.client_id as client_id, compteur, num_polis, cin, nom, prenom, date_creation FROM abonnement a 
			INNER JOIN polis p ON p.polis_id = a.polis_id 
			INNER JOIN client c ON c.client_id = a.client_id) AS subquery_alias
			WHERE num_polis = " . $pdo -> quote($insert) . " AND cin = " . $pdo -> quote($cin)) -> fetch();
			if(empty($verify_abonnement)){
				$abonnement = $pdo -> prepare("INSERT INTO abonnement (compteur, polis_id, client_id) VALUES (:compt, :polis, :client)");
				$abonnement -> execute([
					':compt' => $compteur,
					':polis' => ($pdo -> query("SELECT polis_id FROM polis WHERE num_polis = " . $pdo -> quote($insert)) -> fetch())['polis_id'],
					':client' => ($pdo -> query("SELECT client_id FROM client INNER JOIN polis ON first_cin = cin where first_cin = " . $pdo -> quote($cin)) -> fetch())['client_id']
				]);
			}
	endif;
	/*
	Fin insertion polis ====================================================================================
	*/


}
/*
Debut edition de polis ====================================================================================
*/
if(isset($_POST['article_edit']) && !empty($_POST['article_edit'])){
    $value = $_POST['article_edit'];
    //Verifie si le polis existe. Si oui, enchaine le travail de la mise-a-jour
    $verify = $pdo -> query("SELECT num_polis, description, date_creation FROM polis WHERE num_polis = " . $pdo -> quote($value)) -> fetch();
    if(!empty($verify)){
		if(isset($_POST['description']) && !empty($_POST['description'])){
			$description = $_POST['description'];
		}else{
			$description = $verify['description'];
		}
		if(isset($_POST['date']) && !empty($_POST['date'])){
			$date = $_POST['date'];
		}else{
			$date = $verify['date_creation'];
		}
        $query = $pdo -> prepare('UPDATE polis SET date_creation = ' . $pdo -> quote($date) . ', description =  ' . $pdo -> quote($description) . ' WHERE num_polis = ' . $pdo -> quote($value));
        $query -> execute();
    }
	/*
	Ajoute d'un client de nouveau compteur
	*/
    if(isset($_POST['cin']) && !empty($_POST['cin'])){
        $cin = $_POST['cin'];
		//Verifie si le client de cin ajoute existe. Si non, on prepare l'ajoute
        $cin_existe = $pdo -> query("SELECT 1 FROM client WHERE cin = " . $pdo -> quote($cin)) -> fetch();
        if(empty($cin_existe)){
			
            $insert_client = $pdo -> prepare("INSERT INTO client (cin, nom, prenom) VALUES (:cin, :nom, :prenom)");
			//Definir les info de clients selon les entres
			if(isset($_POST['nom']) && !empty($_POST['nom'])){
				$nom = strtoupper($_POST['nom']);
			}else{
				$nom = 'NOM INCONNU';
			}
			if(isset($_POST['prenom']) && !empty($_POST['prenom'])){
				$prenom = ucwords($_POST['prenom']);
			}else{
				$prenom = 'Prenom Inconnu';
			}
			
			//Execute la requette
			$insert_client -> execute([
				':cin' => $cin,
				':nom' => $nom,
				':prenom' => $prenom
			]);
        }
		/*
		Ajout d'un nouveau client associe au polis. Le cin actuel doit etre change
		*/
        
        $new_client = $pdo -> prepare("UPDATE polis SET first_cin = :new_client WHERE num_polis = :polis");
        $new_client -> execute([
            ':new_client' => $cin,
            ':polis' => $value
        ]);

        /**
         * Ajout de l'abonnement nouvel
         */
        if(empty($verify_abonnement)){
			
			//Savoir le compteur actuel
			
			$compteur = (int)($pdo -> query("SELECT MAX(compteur) AS compteur
			FROM (SELECT c.client_id as client_id, compteur, num_polis, cin, nom, prenom, date_creation FROM abonnement a 
			INNER JOIN polis p ON p.polis_id = a.polis_id 
			INNER JOIN client c ON c.client_id = a.client_id) AS subquery
			WHERE num_polis = '$value'") -> fetch())['compteur'] + 1;
			
            $abonnement = $pdo -> prepare("INSERT INTO abonnement (compteur, polis_id, client_id) VALUES (:compt, :polis, :client)");
			//Execute la requette
            $abonnement -> execute([
                ':compt' => $compteur,
                ':polis' => ($pdo -> query("SELECT polis_id FROM polis WHERE num_polis = " . $pdo -> quote($value)) -> fetch())['polis_id'],
                ':client' => ($pdo -> query("SELECT client_id FROM client INNER JOIN polis ON first_cin = cin where first_cin = " . $pdo -> quote($cin)) -> fetch())['client_id']
            ]);
        }
        
    }
}
if(isset($_GET['groupe'])){
    $groupe = $_GET['groupe'];
    $minArticle = ($groupe - 1) * $taille_groupe + 1;
    $maxArticle = $minArticle + $taille_groupe - 1;
    $polis_all = $pdo -> query("SELECT num_polis, date_creation FROM polis WHERE groupe = " . $pdo -> quote($groupe)) -> fetchAll();
}

if(isset($_GET['annee']) && !empty($_GET['annee'])){
    $annee = $_GET['annee'];
    $minArticle = ($pdo -> query("SELECT MIN(num_polis) as min FROM polis WHERE date_creation = " . $pdo -> quote($annee)) -> fetch())['min'];
    $maxArticle = ($pdo -> query("SELECT MAX(num_polis) as max FROM polis WHERE date_creation = " . $pdo -> quote($annee)) -> fetch())['max'];
    $polis_all = $pdo -> query("SELECT num_polis, date_creation FROM polis WHERE date_creation = " . $pdo -> quote($annee)) -> fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation</title>
    <link rel="icon" type="image/x-icon" href="archive.png">
    <link rel="stylesheet" href="articles.css">
</head>
<body>

<header>
<form action="" method="post" style="height: fit-content; width: fit-content; position:absolute; top:25%;)">
            <button name="logout" value="clear" style="z-index:5;position:absolute; background:none; border:none; height:fit-content;width:fit-content; cursor: pointer;">
                <img src="logout.gif" alt="" style="height:50px; width: 50px">
            </button>
        </form>
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
        <a href="/groupes.php?champ=<?=intdiv($groupe - 1, $taille_champ) + 1; ?>">Groupes</a>
        </nav>     
    </header>
    <div class="modal-overlay"></div>
    <?php 
    /**
     * Cas pour les groupes ============================================================================================================================================
     */
    if(isset($_GET['groupe']) && !empty($_GET['groupe'])): ?>
    <div class="container" style="height:fit-content">
        <ul>
            <?php 
            for($i = $minArticle; $i <= $maxArticle; $i++): ?>
                <li>
                    <div class="folder">
                        <?php
                        $info = $pdo -> query("SELECT num_polis, date_creation, first_cin, description FROM polis WHERE num_polis = " . $pdo -> quote($i)) -> fetch();
						
                        $check = (isset($info['num_polis']) && !empty($info['num_polis'])) ? $info['num_polis'] : '';
						//Cas pour un polis inexistant
                        if(empty($check)): ?>
							<div class="folder-empty">
								<img src="folder-empty.png" class="empty-folder">
								<h2><?=$i ?></h2>
								<form action="" method="POST">
									<button class="add add-button">
										<img src="add.gif">
									</button>
								</form>
								<?php 
								/**
								 * Insert dialog
								 */
								?>
								<div class="add-dialog">
									<img src="exit.gif" class="exit-button">
									<h1><?=$i ?></h1>
									<form action="" method="POST">
										<div class="input-area">
											<div class="left">
												<select name="date" class="date">
													<option value="<?=date('Y') ?>">Date</option>
													<?php 
														$date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
														for($j = (int)date('Y'); $j >= (int)$date_debut; $j--):
													?>
													<option value="<?=$j ?>"><?=$j ?></option>
															<?php endfor; ?>
												</select>
												<textarea name="description" placeholder="Entrer une description, des informations supplémentaires. Cette section est modifiable."></textarea>
											</div>
											
											<div class="separation"></div>
											
											<div class="right right-client">
												<table class="new-client">
													<tr class="new-client">
														<td><b><i>Compteur : 1</i></b></td>
														<td><input type="text" name="client_cin" placeholder="CIN" style="text-transform:uppercase"></td>
													</tr>
													
													<tr class="new-client">
														<td><input type="text" name="nom" value="" placeholder="Nom" style="text-transform:uppercase"></td>
														<td><input type="text" name="prenom" value="" placeholder="Prénom"></td>
													</tr>
												</table>
											</div>
										</div>

										<div class="submit">
											<button class="done" name="article_add" value="<?=$i ?>">
												Ajouter l'article
											</button>
										</div>
										
									</form>
								</div>
                        
                        <?php
						//Cas pour polis existant
                        else: ?>
                            <div class="folder-active">
                                <img src="folder.png" class="active-folder">
                                <h2><?=$check ?></h2>
                                <p><?=$info['date_creation'] ?></p>
                                <form action="" method="POST">
                                    <button name="article_supprime" value="<?=$i ?>" class="add">
                                        <img src="delete.gif">
                                    </button>
                                </form>

                                <form action="" method="POST" class="edit">
                                    <button class="edit-button">
                                        <img src="edit.gif">
                                    </button>
                                </form>
                                <?php
                                 /**
                                 * Update dialog
                                 * 
                                 */
                                $_clients = ($pdo -> query("SELECT c.cin AS cin, c.nom AS nom, c.prenom AS prenom, a.compteur AS compteur FROM abonnement a
                                INNER JOIN polis p ON a.polis_id = p.polis_id
                                INNER JOIN client c ON a.client_id = c.client_id
                                WHERE p.num_polis = " . $pdo -> quote($i))) -> fetchAll();
                                ?>
                                <div class="edit-dialog">
                                    <img src="exit.gif" class="exit-button">
                                    <h1><?=$i ?></h1>
                                    <form action="" method="POST">
										<div class="input-area">
											<div class="left">
												<select name="date" class="date">
													<option value="<?=$info['date_creation'] ?>"><?=$info['date_creation'] ?></option>
													<?php 
														$date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
														for($j = (int)date('Y'); $j >= (int)$date_debut; $j--): ?>
															<option value="<?=$j ?>"><?=$j ?></option>
														<?php endfor; ?>
												</select>
												<textarea name="description" placeholder="Entrer une description, des informations supplémentaires."><?=$info['description'] ?></textarea>
											</div>
											
											<div class="separation"></div>
											
											<div class="right">
												<table class="all-table">
													<tr class="all-table">
														<td>
															<table class="each-table">
																<tr class="each-table">
																	<td>Entrer client N° <?php
																		$compteur = (int)($pdo -> query("SELECT MAX(compteur) AS compteur
																										FROM (SELECT c.client_id as client_id, compteur, num_polis, cin, nom, prenom, date_creation FROM abonnement a 
																										INNER JOIN polis p ON p.polis_id = a.polis_id 
																										INNER JOIN client c ON c.client_id = a.client_id) AS subquery
																										WHERE num_polis = '$i'") -> fetch())['compteur'] + 1; 
																		echo $compteur; ?>
																	</td>
																	
																	<td class="separation"></td>
																	
																	<td><input type="text" name="cin" value="" style="text-transform:uppercase" placeholder="CIN"></td>
																</tr>
																
																<tr class="separation"><td></td><td></td><td></td></tr>
																
																<tr class="each-table">
																	<td><input type="text" name="nom" value="" placeholder="Nom" style="text-transform:uppercase"></td>
																	<td class="separation"></td>
																	<td><input type="text" name="prenom" value="" placeholder="Prenom"></td>
																</tr>
															</table>
														</td>
													</tr>
													<?php foreach($_clients AS $client): ?>
													<tr class="all-table">
														<td>
															<table class="each-table">
																<tr class="each-table">
																	<td><?=$client['compteur'] ?></td>
																	<td class="separation"></td>
																	<td><input type="text" value="<?=$client['cin'] ?>" style="text-transform:uppercase;font-weight:bold;color:black" disabled></td>
																</tr>
																<tr class="separation"><td></td><td></td><td></td></tr>
																<tr class="each-table">
																	<td><input type="text" value="<?=$client['nom'] ?>"style="text-transform:uppercase;font-weight:bold;color:black" disabled></td>
																	<td class="separation"></td>
																	<td><input type="text" value="<?=$client['prenom'] ?>" style="font-weight:bold;color:black" disabled></td>
																</tr>
															</table>
														</td>
													</tr>
													<?php endforeach; ?>
												</table>
											</div>
										</div>
										<div class="submit">
											<button class="done" name="article_edit" value="<?=$i ?>">
												Enregistrer
											</button>
										</div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php
            endfor;
        
            ?>
        </ul>
    </div>
    <?php else: 
        /**
     * Cas pour les années ============================================================================================================================================
     */
        ?>
    <div class="container" style="height:fit-content">
        <ul>
		<?php 
		foreach($polis_all as $polis): ?>
			<li>
				<div class="folder">
				<?php
				$info = $pdo -> query("SELECT num_polis, date_creation, description FROM polis WHERE num_polis = " . $pdo -> quote($polis['num_polis'])) -> fetch();
				$check = $info['num_polis'];
				$date = $info['date_creation']; ?>
                            <div class="folder-active">
                                <img src="folder.png" class="active-folder">
                                <h2><?=$check ?></h2>
                                <p><?=$date ?></p>
                                <form action="" method="POST">
                                    <button name="article_supprime" value="<?=$polis['num_polis'] ?>" class="add">
                                        <img src="delete.gif">
                                    </button>
                                </form>

                                <form action="" method="POST" class="edit">
                                    <button class="edit-button">
                                        <img src="edit.gif">
                                    </button>
                                </form>
                                <?php
                                /**
                                 * Case for the UPDATE DIALOG
                                 */
                                $_clients = ($pdo -> query("SELECT c.cin AS cin, c.nom AS nom, c.prenom AS prenom, a.compteur AS compteur FROM abonnement a
                                INNER JOIN polis p ON a.polis_id = p.polis_id
                                INNER JOIN client c ON a.client_id = c.client_id
                                WHERE p.num_polis = " . $pdo -> quote($polis['num_polis']))) -> fetchAll();
                                ?>
                                <div class="edit-dialog">
                                    <img src="exit.gif" class="exit-button">
                                    <h1><?=$polis['num_polis'] ?></h1>
                                    <form action="" method="POST">
                                        
                                        <div class="input-area">
                                            <div class="left">
                                            <select name="date" class="date">
                                                <option value="<?=$info['date_creation'] ?>"><?=$info['date_creation'] ?></option>
                                                <?php 
                                                    $date_debut = ($pdo -> query("SELECT first_date FROM meta") -> fetch())['first_date'];
                                                    for($j = (int)date('Y'); $j >= (int)$date_debut; $j--): ?>
                                                        <option value="<?=$j ?>"><?=$j ?></option>
                                                    <?php endfor; ?>
                                            </select>
                                            <textarea name="description" placeholder="Entrer une description, des informations supplémentaires."><?=$info['description'] ?></textarea>
                                            </div>
                                            <div class="separation"></div>
                                            <div class="right">
                                                        <table class="all-table">
                                                                <tr class="all-table">
                                                                    <td>
                                                                        <table class="each-table">
                                                                            <tr class="each-table">
                                                                                <td>Entrer client N°  <?php
																				$compteur = (int)($pdo -> query("SELECT MAX(compteur) AS compteur
			FROM (SELECT c.client_id as client_id, compteur, num_polis, cin, nom, prenom, date_creation FROM abonnement a 
			INNER JOIN polis p ON p.polis_id = a.polis_id 
			INNER JOIN client c ON c.client_id = a.client_id) AS subquery
			WHERE num_polis = '" . $polis['num_polis'] . "'") -> fetch())['compteur'] + 1; 
			echo $compteur; ?></td>
                                                                                <td class="separation"></td>
                                                                                <td><input type="text" name="cin" value="" style="text-transform:uppercase" placeholder="CIN"></td>
                                                                            </tr>
                                                                            <tr class="separation"><td></td><td></td><td></td></tr>
                                                                            <tr class="each-table">
                                                                                <td><input type="text" name="nom" value="" placeholder="Nom" style="text-transform:uppercase"></td>
                                                                                <td class="separation"></td>
                                                                                <td><input type="text" name="prenom" value="" placeholder="Prénom"></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            <?php foreach($_clients AS $client): ?>
                                                                <tr class="all-table">
                                                                    <td>
                                                                        <table class="each-table">
                                                                            <tr class="each-table">
                                                                                <td><?=$client['compteur'] ?></td>
                                                                                <td class="separation"></td>
                                                                                <td><input type="text"value="<?=$client['cin'] ?>" style="text-transform:uppercase;font-weight:bold;color:black" disabled></td>
                                                                            </tr>
                                                                            <tr class="separation"><td></td><td></td><td></td></tr>
                                                                            <tr class="each-table">
                                                                                <td><input type="text" value="<?=$client['nom'] ?>"style="text-transform:uppercase;font-weight:bold;color:black" disabled></td>
                                                                                <td class="separation"></td>
                                                                                <td><input type="text" value="<?=$client['prenom'] ?>" style="font-weight:bold;color:black" disabled></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>  
                                                            
                                                        </table>
                                            </div>
                                        </div>

                                        
                                        <div class="submit">
                                            <button class="done" name="article_edit" value="<?=$polis['num_polis'] ?>">
                                                Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                    </div>
                </li>
            <?php
            endforeach;
        
            ?>
            </ul>
    </div>
    <?php endif; ?>
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
<script src="add.js"></script>
<script src="edit.js"></script>
</body>
</html>