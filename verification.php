<?php
// Demarre la session pour acceder aux donnees de l'utilisateur.
session_start();

// Verifie si le formulaire a ete envoye avec la methode POST.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recupere le nom d'utilisateur et le mot de passe saisis.
    $nomUtilisateur = $_POST['nomUtilisateur'];
    $mdp = $_POST['mdp'];

    // Verifie les identifiants de connexion de l'administrateur.
    if ($nomUtilisateur === "admin" && $mdp === "sesameouvretoi") {

        // Enregistre le statut administrateur dans la session.
        $_SESSION["admin"] = true;

        // Redirige vers la page du back-office.
        header('Location: back-office/index.php');

    } else {

        // Redirige vers la page de connexion si les identifiants sont incorrects.
        header('Location: connexion.php');
    }

    // Arrete l'execution du script apres la redirection.
    exit();
}
?>