<?php 
require_once '../connect.php';
if(isset($_GET['groupe'])){
    $groupe = $_GET['groupe'];
    $lines = $pdo -> query("SELECT * FROM polis WHERE groupe = ". $pdo -> quote($groupe), PDO::FETCH_ASSOC) -> fetchAll();
}
else{
    $lines = $pdo -> query('SELECT * FROM polis', PDO::FETCH_ASSOC) -> fetchAll();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation</title>
</head>
<body>
    <table>
        <thead>
            <th>
                <td>
                    ID
                </td>
                <td>
                    num_polis
                </td>
                <td>
                    date_creation
                </td>
                <td>
                    groupe
                </td>
                <td>
                    champ
                </td>
            </th>
        </thead>
        <tbody>
            <?php
            foreach($lines as $line){
                echo '<tr>';
                    foreach($line as $name => $value){
                        echo '<td>';
                        echo $value;
                        echo '</td>';
                    }
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</body>
</html>