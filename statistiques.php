
<?php
require_once 'connect.php';

$min_visual = 1; $max_visual = ($pdo -> query("SELECT MAX(num_polis) as max FROM polis") -> fetch())['max']; //In this case dispo = visualised
$date_debut = '--';
$date_fin = '--';
if(isset($_POST['limits']) && !empty($_POST['min']) && !empty($_POST['max'])){
    $min_visual = $_POST['min'];
    if((int)$min_visual === 0){$min_visual = 1;}
    $max_visual = $_POST['max'];
    $start_index = $min_visual;
    $end_index = $max_visual;
    $type = 'polis';
}elseif(isset($_POST['date_limits']) && !empty($_POST['date_limits'])){
    $date_debut = $_POST['min_date'];
    $date_fin = $_POST['max_date'];
    $start_index = $date_debut;
    $end_index = $date_fin;
    $min_visual = (int)($pdo -> query("SELECT MIN(num_polis) as min FROM polis WHERE date_creation >= " . $pdo -> quote($date_debut)) -> fetch())['min'];
    if((int)$min_visual === 0){$min_visual = 1;}
    $max_visual = (int)($pdo -> query("SELECT MAX(num_polis) as max FROM polis WHERE date_creation <= " . $pdo -> quote($date_fin)) -> fetch())['max'];
    $type = 'year';
}

$count_query = "SELECT COUNT(num_polis) as num FROM polis WHERE num_polis BETWEEN '$min_visual' AND '$max_visual'";

$count_all = ($pdo -> query($count_query) -> fetch())['num'];



$infographie = array('INFO');

for($i=1; $i <= $max_visual; $i++){
    $last = $i - 1;
    $verify_last = $pdo -> query("SELECT * FROM polis WHERE num_polis = '$last'") -> fetch();
    $verify = $pdo -> query("SELECT * FROM polis WHERE num_polis = '$i'") -> fetch();
    $next = $i + 1;
    $verify_next = $pdo -> query("SELECT * FROM polis WHERE num_polis = '$next'") -> fetch();
if(empty($verify)){
    if(empty($verify_last)){
        if($i === (int)$min_visual){
            if(empty($verify_next)){
                if($i === (int)$max_visual){
                    $infographie[] = 'inconnue_start_end';
                }else{
                    $infographie[] = 'inconnue_start';
                }
            }else{
                $infographie[] = 'inconnue_start_end';
            }
        }else{
            if(empty($verify_next)){
                if($i === (int)$max_visual){
                    $infographie[] = 'inconnue_end';
                }else{
                    
                    $infographie[] = 'inconnue';
                }
            }else{
                $infographie[] = 'inconnue_end';
            }
        }
        
    }else{
        if(empty($verify_next)){
            if($i === (int)$max_visual){
                $infographie[] = 'inconnue_start_end';
            }else{
                $infographie[] = 'inconnue_start';
            }
        }else{
            $infographie[] = 'inconnue_start_end';
        }
    }
    
}else{
    if(empty($verify_last)){
        if(empty($verify_next)){
            $infographie[] = 'connue_start_end';
        }else{
            if($i === (int)$max_visual){
                $infographie[] = 'connue_start_end';
            }else{
                $infographie[] = 'connue_start';
            }
        }
    }else{
        if($i === (int)$min_visual){
            if(empty($verify_next)){
                $infographie[] = 'connue_start_end';
            }else{
                if($i === (int)$max_visual){
                    $infographie[] = 'connue_end';
                }else{
                    $infographie[] = 'connue';
                }
            }
        }else{
            if(empty($verify_next)){
                $infographie[] = 'connue_end';
            }else{
                if($i === (int)$max_visual){
                    $infographie[] = 'connue_end';
                }else{
                    $infographie[] = 'connue';
                }
            }
        }
        
        
    }
    
}
}
$visual_length = (int)$max_visual - (int)$min_visual + 1;

for($i = $min_visual; $i <= $max_visual; $i++){
    $visual[$i] = $infographie[$i];
}

$front = array();


foreach($visual as $num => $state){
    if(str_contains($state, 'start')){
        $periodique['state'] = explode('_', $state)[0];
        $periodique['min'] = $num;
    }
    if(str_contains($state, 'end')){
        $periodique['max'] = $num;
        $chunk_length = (int)$periodique['max'] - (int)$periodique['min'] + 1;
        $percentage = (int)(($chunk_length / $visual_length)*100);
        $periodique['percentage'] = $percentage;
        $front[] = $periodique;
        unset($periodique);
    }
}
if(isset($type) && isset($start_index) && isset($end_index)){
if(!isset($_COOKIE[$type . '_' . $start_index . '_' . $end_index]) && (isset($_POST['date_limits']) || isset($_POST['limits']))){
    setcookie(
        $type . '_' . $start_index . '_' . $end_index,
        serialize($front),
        time() + 3600,
        "/"
    );
    $current_stat = array($type . '_' . $start_index . '_' . $end_index, serialize($front));
}
}

if(isset($_POST['clear'])){
    if (isset($_COOKIE)) {
        foreach ($_COOKIE as $key => $value) {
            if(str_contains($key, 'year') || str_contains($key, 'polis')){
                setcookie($key, "", time() - 3600, "/");
            }
            
        }
    }
    $cleared = 'set';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="statistiques.css">
    <link rel="shortcut icon" href="archive.png" type="image/x-icon">
</head>
<body>
<header>
        
        <?php

        $periodique = array(
            "state" => "",
            "min" => "",
            "max" => "",
            "couleur" => "",
            "percentage" => ""
        );
        ?>
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
        <a href="/champs.php">Consulter</a>
        <a href="/clients.php">Clientèle</a>
        <a href="/add.php">Ajouter</a>
        

        </nav>
            
        
    </header>
    <div class="container">
        
        
        <div class="configuration">
            <ul class="head">
                <li class="polis">Polis</li>
                <li class="annee">Année</li>
            </ul>

            <div id="body-polis">
                <form action="" method="POST" class="inner-body">
                    <input type="number" name="min" placeholder="Polis min">
                    <img src="vector.png" class="arrow">
                    <input type="number" name="max" placeholder="Polis max">
                <button name="limits" value="specified">
                    Tracer
                </button>
                </form>
                <form action="" method="POST">
                    <button id="nettoyer" name="clear" value="specified">
                    Nettoyer</button>
                    </form>
            </div>

            <div id="body-annee">
                <form action="" method="POST" class="inner-body" id="annee-interval">
                    <input type="number" name="min_date" placeholder="Année min">
                    <img src="vector.png" class="arrow">
                    <input type="number" name="max_date" placeholder="Année max">
                    <button id="tracer" name="date_limits" value="specified">
                        Tracer
                    </button>
                    
                </form>
                <form action="" method="POST">
                    <button name="clear" value="specified">Nettoyer</button>
                    </form>
            </div>
            
            
        </div>
        <?php
        if((!empty($current_stat) || count($_COOKIE) > 1) && !isset($cleared)): ?>
        <ul id="statistiques" style="width=600px">
        
        <?php
        $keys = array_keys($_COOKIE);
        foreach($_COOKIE as $name => $case): 
            if(str_contains($name, 'year') || str_contains($name, 'polis')):
            $front = unserialize($case);
            ?>
            <li  style="width=600px; <?php
            $index = array_search($name, $keys);
            if(count($_COOKIE) - 1 === $index && empty($current_stat)){echo 'border-bottom: 0;';}
            ?>">
            <?php if(explode('_', $name)[1] === explode('_', $name)[2]): ?>
                <?php if(explode('_', $name)[0] === 'year'): ?>
                    <p>
                        Les valeurs de l'année <?=explode('_', $name)[1] ?>
                    </p>
                <?php else: ?>
                    <p>
                        La valeur <?=explode('_', $name)[1] ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(explode('_', $name)[1] !== explode('_', $name)[2]): ?>
                <?php if(explode('_', $name)[0] === 'year'): ?>
                    <p>
                        Les polises entre les années <?=explode('_', $name)[1] ?> et <?=explode('_', $name)[2] ?>
                    </p>
                <?php else: ?>
                    <p>
                        Les polises entre les valeurs <?=explode('_', $name)[1] ?> et <?=explode('_', $name)[2] ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
                <div class="figure" style="">
                <div class="delimiter" style="border:2px solid black">
                    <div class="left-number"><?=$front[0]['min'] ?></div>
                </div>
                <div class="inconnue del" style="width:40px;border:2px solid black;"></div>
                    <?php foreach($front as $i => $chunk): ?>
                        
                        <div class="<?=$chunk['state'] ?>" style="border:2px solid black; width:<?=$chunk['percentage'] ?>%;">
                            <?php if($chunk['state'] === 'connue'): ?>
                            
                                <?php if($chunk['min'] === $chunk['max']): ?>
                                    <div class="number"><?=$chunk['min'] ?></div>
                                <?php else: ?>
                                    <?php if($i !== 0): ?>
                                        <div class="left-number"><?=$chunk['min'] ?></div>  
                                    <?php endif; ?>
                                    <?php if($i !== count($front) - 1): ?>
                                        <div class="right-number"><?=$chunk['max'] ?></div>  
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                            <?php endif; ?>  
                        </div>
                    <?php endforeach; ?>
                    <div class="inconnue del" style="width:40px;border:2px solid black;"></div>
                    <div class="delimiter" style="border:2px solid black;">
                        <div class="right-number"><?=$front[count($front) - 1]['max'] ?></div>
                    </div>
            </li>
            <?php endif; ?>
            <?php endforeach; 
            /**
             * Case for the last value ===================================================================================
             */
                if(!empty($current_stat)):
                $front = unserialize($current_stat[1]);
                ?>
                <li  style="width=600px; <?='border-bottom:0' ?>">
                <?php if(explode('_', $current_stat[0])[1] === explode('_', $current_stat[0])[2]): ?>
                    <?php if(explode('_', $current_stat[0])[0] === 'year'): ?>
                        <p>
                            Les valeurs de l'année <?=explode('_', $current_stat[0])[1] ?>
                        </p>
                    <?php else: ?>
                        <p>
                            La valeur <?=explode('_', $current_stat[0])[1] ?>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(explode('_', $current_stat[0])[1] !== explode('_', $current_stat[0])[2]): ?>
                    <?php if(explode('_', $current_stat[0])[0] === 'year'): ?>
                        <p>
                            Les polises entre les années <?=explode('_', $current_stat[0])[1] ?> et <?=explode('_', $current_stat[0])[2] ?>
                        </p>
                    <?php else: ?>
                        <p>
                            Les polises entre les valeurs <?=explode('_', $current_stat[0])[1] ?> et <?=explode('_', $current_stat[0])[2] ?>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
                    <div class="figure" style="">
                    <div class="delimiter" style="border:2px solid black;">
                        <div class="left-number"><?=$front[0]['min'] ?></div>
                    </div>
                    <div class="inconnue del" style="width:40px;border:2px solid black;"></div>
                        <?php foreach($front as $i => $chunk): ?>
                            
                            <div class="<?=$chunk['state'] ?>" style="border:2px solid black; width:<?=$chunk['percentage'] ?>%;">
                                <?php if($chunk['state'] === 'connue'): ?>
                                
                                    <?php if($chunk['min'] === $chunk['max']): ?>
                                        <div class="number"><?=$chunk['min'] ?></div>
                                    <?php else: ?>
                                        <?php if($i !== 0): ?>
                                            <div class="left-number"><?=$chunk['min'] ?></div>  
                                        <?php endif; ?>
                                        <?php if($i !== count($front) - 1): ?>
                                            <div class="right-number"><?=$chunk['max'] ?></div>  
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                <?php endif; ?>  
                            </div>
                        <?php endforeach; ?>
                        <div class="inconnue del" style="width:40px;border:2px solid black;"></div>
                        <div class="delimiter" style="border:2px solid black;">
                            <div class="right-number"><?=$front[count($front) - 1]['max'] ?></div>
                        </div>
                </li>
            <?php
            endif;
            /**
             * END ===================================================================================
             */
            ?>
        </ul>
        <?php endif; ?>
    </div>
    <script src="stat.js"></script>
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