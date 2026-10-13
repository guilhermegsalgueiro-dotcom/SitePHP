<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomUtilisateur = htmlspecialchars($_POST['nomUtilisateur']);
    $mdp = htmlspecialchars($_POST['mdp']);
    
    if ($nomUtilisateur === "admin" && $mdp === "admin") {
        header('Location: back-office/accueil.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body id="page-connexion">
    <section>
        <form action="" method="POST">
            <div class="login">
                <h2>Se connecter</h2>
                <div class="inputBox">
                    <input type="text" id="nomUtilisateur" name="nomUtilisateur" placeholder="Nom d'utilisateur">
                </div>
                <div class="inputBox">
                    <input type="password" id="mdp" name="mdp" placeholder="Mot de passe">
                </div>
                <div class="inputBox">
                    <input type="submit" value="Se connecter" id="btn">
                </div>
                <a href="index.php">Retour à l'accueil</a>
            </div>
        </form>
    </section>
</body>
</html>