<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomUtilisateur = $_POST['nomUtilisateur'];
    $mdp = $_POST['mdp'];
    
    if ($nomUtilisateur === "admin" && $mdp === "admin") {
        $_SESSION["admin"] = true;
        header('Location: back-office/index.php');
    }
    else {
        header('Location: connexion.php');
    }
    exit();
}
?>