<?php
require_once 'connect.php';


?>
<html>
<head>
    <title>
        Acceuil
    </title>
    <link rel="icon" type="image/x-icon" href="archive.png">
    <link rel="stylesheet" href="acceuil.css">
</head>
<body>
    <?php
    if(!empty($enchain)):
        ?>
    <header>
        <form action="" method="post" style="height: fit-content; width: fit-content; position:absolute; top:25%;)">
            <button name="logout" value="clear" style="position:absolute; background:none; border:none; height:fit-content;width:fit-content; cursor: pointer;">
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
        

    </header>
    
    
    
        <div class="circle-container">
            <div class="home">
                <img src="home.gif">
                <div class="description">
                    Pour plus d'information sur l'ONEP, visitez : <br>
                    <a href="http://www.onep.ma/" target="_blank">onep</a>
                </div>
            </div>
            
        <div class="circle">

        <div class="element add">
            
        <a href="add.php" id="add">
            <figure>
            <img src="add.gif">
            <p>Ajouter des articles</p>
            </figure>
        </a>
        <div class="description">
             Il s'agit de la meilleur méthode d'ajouter les articles en termes de spécificité et de <span style="color:green">personalisation.</span><br>
            </div>
        </div>
        

        <div class="element consult">
        <a href="champs.php" id="consult">
                    <figure>
                    <img src="consult.gif">
                    <p>Consulter l'archive</p>
                    </figure>
                
                </a>
                <div class="description">
                    C'est l'endroit parfait pour consulter et naviguer les articles. Des fonctionalités avancées sont introduites pour interagir avec vous !
                </div>
        </div>
        
        <div class="element stat">
        <a href="statistiques.php" id="stat">
            <figure>
            <img src="stat.gif">
            <p>Voir les statistiques</p>
            </figure>
        
        </a>
        <div class="description">
            Pour visualiser l'existant de l'archive en plus de détail et faire des statistiques.
        </div>
        </div>


        <div class="element client">
        <a href="clients.php" id="client">
            <figure>
            <img src="avatar.gif">
            <p>Abonnements</p>
            </figure>
        
        </a>
        <div class="description">
            Pour consulter les abonnements concernés par l'existant de l'archive.
        </div>
        </div>
        

        </div>
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
<?php else: ?>
    <form action="" method="POST" class="meta">
    <div class="paragraphe">
        Déterminer la valuer de début de l'enregistrement dans l'archive en vie réelle sachant que les polises vont toujours commencer à la valuer 1 !<br>
        Cette valeur est non modifiable plus tard et est indisponsable pour le bon fonctionnement de l'application.
    </div>
    <div class="input-first-date">
        <input type="number" name="first-date" placeholder="Première date">
    </div>
    <div class="paragraphe">
        En réalité, on a des cartons contenant les dossiers associés aux polises. Cette notion est très bien expliquer sur l'application sous le nom "groupes". Choisissez le nombre des polises contenu dans chaque groupe. <span style="color:green">Ce nombre s'agit de la taille des groupes</span>.
    </div>
    <div class="paragraphe">
        Pour faciliter la visualisation de données encore, l'application propose de travailler avec des champs de valeurs de taille fixe. Considérez les comme des chambres contient un nombre des groupes. <span style="color:green">Ce nombre s'agit de la taille des champs</span>.
    </div>
        <div class="input-info">
        <input type="number" name="input-groupe" placeholder="Taille de groupe">
        <div class="line"></div>
        <input type="number" name="input-champ" placeholder="Taille de champ">
    </div>
    <div class="commencer">
        <button>
            Commencer le travail !
        </button>
    </div>
</form>
<?php endif; ?>

</body>
</html>
