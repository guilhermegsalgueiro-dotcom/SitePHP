<?php
// Demarre la session pour acceder aux informations de l'utilisateur.
session_start();

// Verifie que l'utilisateur est connecte en tant qu'administrateur.
if (!isset($_SESSION["admin"])) {
    header('Location: ../index.php');
    exit();
}

// Verifie que l'identifiant du produit a supprimer a ete transmis.
if (!isset($_POST["produit"])) {
    header('Location: index.php');
    exit();
}

// Recupere l'identifiant du produit a supprimer.
$id = $_POST["produit"];

// Recupere la liste des produits depuis le fichier JSON.
$fichier = "../produit.json";
$produits = json_decode(file_get_contents($fichier), true);

// Verifie que le catalogue est un tableau valide.
if (!is_array($produits)) {
    die("Impossible de lire le catalogue des produits.");
}

// Parcourt les produits pour rechercher celui qui correspond a l'identifiant.
foreach ($produits as $indice => $produit) {

    // Compare l'identifiant du produit avec celui recu du formulaire.
    if ((string)$produit["id"] === (string)$id) {

        // Supprime le produit du catalogue.
        unset($produits[$indice]);

        // Arrete la recherche apres avoir trouve le produit.
        break;
    }
}

// Supprime egalement ce produit du panier de la session, s'il est present.
unset($_SESSION['panier'][$id]);

// Enregistre le catalogue sans reattribuer les identifiants des produits.
file_put_contents(
    $fichier,
    json_encode(
        array_values($produits),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    )
);

// Redirige vers la page principale du back-office.
header('Location: index.php');
exit();
?>