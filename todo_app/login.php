<?php
// Ca demarre
session_start();


// Plusieurs users, donc tableau associatif
$usersDb = [
    "brendon" => "password",
    "pierre" => "password1",
    "paul" => "password2",
    "jacques" => "password3"
];

$errorMsg = "";

// Si validation du form (avec sécurité htmlspecialchars)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    // Ca match, go todolist
    if (isset($usersDb[$username]) && $usersDb[$username] === $password) {
        $_SESSION['username'] = $username;
        echo 'Connexion réussie';
        header('Location: index.php');
        exit;
        
    } else {
        // Variable pour stocker erreur
        $errorMsg = "Identifiant ou mot de passe incorrect(s)";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <div class="login-form container">
        <h2>Connexion</h2>
        <form action="" method="POST">
            <div class="usernameArea">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="passwordArea">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="addBtn">Se connecter</button>
        </form>
        <!-- Si variable $errorMsg pas vide, ça s'affiche -->
        <?php if(!empty($errorMsg)): ?>
            <p style="color: red;"><?php echo $errorMsg; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>