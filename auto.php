<?php
session_start();
require_once 'connect.php';

$user_check = $pdo -> query("SELECT 1 FROM user") -> fetch();
$operation = array();
if(empty($user_check)){
    $operation['titre'] = 'Ajouter l\'admin';
    $operation['button'] = 'Ajouter';
    $user_html = 'new_username';
    $pass_html = 'new_password';

    $insert_admin = $pdo -> prepare("INSERT INTO user (username, motdepasse) VALUES (:username, :password)");
    $insert_admin -> execute([
        ':username' => $_POST['username'],
        ':password' => password_hash($_POST['password'], PASSWORD_ARGON2I)
    ]);

}else{
    $operation['titre'] = 'Connexion';
    $operation['button'] = 'S\'authentifiez';
    $username = $_POST['username'];
    $user_html = 'username';
    $pass_html = 'password';

    $verify_exists = $pdo -> query("SELECT 1 FROM user WHERE username = '$username'") -> fetch();
    if(empty($verify_exists)){
        echo 'User not found !';
    }else{
        $hash = ($pdo -> query("SELECT motdepasse FROM user WHERE username = '$username'") -> fetch())['password'];

        if(password_verify($password, $hash)){
            $_SESSION['loged'] = $username;
        }else{
            echo 'Mot de passe incorrecte !!';
        }
    }
}
echo '<pre>';
var_dump($_SESSION);
echo '</pre>';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="auto.css">
    <title>Archivage</title>
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
    </header>
    <article style="">
        <h1>
            <?=$operation['titre'] ?>
        </h1>
        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        <label for="user">Nom d'utilisateur</label>
                    </td>
                    <td>
                        <input type="text" name="<?=$user_html ?>" id="user" placeholder="Entrez un nom">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password">Mot de passe</label>
                    </td>
                    <td>
                        <input type="password" name="<?=$pass_html ?>" id="password" placeholder="Mot de passe">
                    </td>
                </tr>
                <tr>
                    <td>

                    </td>
                    <td>
                        <button>
                            <?=$operation['button'] ?>
                        </button>
                    </td>
                </tr>
            </table>
        </form>
    </article>
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