<?php
// Demarre la session pour acceder aux donnees de l'utilisateur.
session_start();

// Supprime le statut administrateur de la session.
unset($_SESSION["admin"]);

// Redirige l'utilisateur vers la page d'accueil.
header('Location: ../index.php');

// Arrete l'execution du script apres la redirection.
exit;
?>