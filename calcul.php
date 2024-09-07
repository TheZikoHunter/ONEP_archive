<?php
require_once 'connect.php';

$min_visual = 1; $max_visual = ($pdo -> query("SELECT MAX(num_polis) as max FROM polis") -> fetch())['max']; //In this case dispo = visualised
$date_debut = '--';
$date_fin = '--';
if(isset($_POST['limits'])){
    $min_visual = $_POST['min'];
    $max_visual = $_POST['max'];
}elseif(isset($_POST['date_limits'])){
    $date_debut = $_POST['min_date'];
    $date_fin = $_POST['max_date'];
    $min_visual = (int)($pdo -> query("SELECT MIN(num_polis) as min FROM polis WHERE date_creation = " . $pdo -> quote($date_debut)) -> fetch())['min'];
    $max_visual = (int)($pdo -> query("SELECT MAX(num_polis) as max FROM polis WHERE date_creation = " . $pdo -> quote($date_fin)) -> fetch())['max'];
    var_dump($pdo -> query("SELECT MAX(num_polis) FROM polis WHERE date_creation = " . $pdo -> quote($date_fin)));
    echo 'Mais le max est : '. $max_visual;
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
?>


<form action="" method="POST">
        <label for="min">min</label>
        <input type="number" name="min" id="min" value="<?=$min_visual ?? 1 ?>">
        <label for="max">max</label>
        <input type="number" name="max" id="max" value="<?=$max_visual ?? '' ?>">
        <button name="limits" value="specified">
            Spécifier les valeurs limites
        </button>
    </form>

    <form action="" method="POST">
    <label for="min-date">Date début</label>
        <input type="number" id="min-date" name="min_date" value="<?=$date_debut ?>">
        <label for="max-date">Date fin</label>
        <input type="number" id="max-date" name="max_date" value="<?=$date_fin ?>">
        <button name="date_limits" value="specified">
            Spécifier les dates limites
        </button>
    </form>

<?php
echo '<pre>';
$visual_length = $max_visual - $min_visual + 1;
echo 'Les valeurs entre ' . $min_visual . ' et ' . $max_visual . '<br>';
echo "In total we have $count_all entre $visual_length<br>";

for($i = $min_visual; $i <= $max_visual; $i++){
    $visual[$i] = $infographie[$i];
}

$front = array();

$periodique = array(
    "state" => "",
    "min" => "",
    "max" => "",
    "couleur" => "",
    "percentage" => ""
);

foreach($visual as $num => $state){
    if(str_contains($state, 'start')){
        $periodique['state'] = explode('_', $state)[0];
        $periodique['min'] = $num;
        if(!str_contains($state, 'inconnue')){
            $periodique['couleur'] = 'green';
        }else{
            $periodique['couleur'] = 'red';
        }
    }
    if(str_contains($state, 'end')){
        $periodique['max'] = $num;
        $chunk_length = $periodique['max'] - $periodique['min'] + 1;
        $percentage = (int)(($chunk_length / $visual_length)*100);
        $periodique['percentage'] = $percentage;
        $front[] = $periodique;
        unset($periodique);
    }
}

echo '</pre>';
/**
 * ============= Cas de base ============
 * --> info needed :
 * + min-max of values
 * + boucle sur toutes les données
 */
echo <<<HTML
<div style="width:100%;">
HTML;
foreach($front as $i => $chunk){
    
?>
<div class="<?=$chunk['state'] ?>" style="height:50px; width:<?=$chunk['percentage'] ?>%; background-color:<?=$chunk['couleur'] ?>; float:left;">

</div>
<?php
}?>
</div>
    </div>