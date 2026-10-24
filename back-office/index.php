<?php
// Demarre la session pour acceder aux informations de l'utilisateur.
session_start();

// Verifie que l'utilisateur est connecte en tant qu'administrateur.
if (!isset($_SESSION["admin"])) {
    header('Location: ../index.php');
    exit();
}

// Recupere la liste des produits depuis le fichier JSON.
$produits = json_decode(file_get_contents("../produit.json"), true);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Definit l'encodage des caracteres et l'affichage sur les appareils mobiles. -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Definit le titre de la page et charge la feuille de style. -->
    <title>La Fromagerie</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body id="page-index">

    <!-- Affiche l'en-tete du back-office et les liens de navigation. -->
    <header>
        <h1>La Fromagerie</h1>

        <nav>
            <!-- Permet d'acceder au formulaire d'ajout d'un produit. -->
            <a href="ajouter.php">Ajouter un produit</a>

            <!-- Permet de revenir a la page d'accueil du site. -->
            <a href="../index.php">Revenir a l'accueil</a>

            <!-- Permet de fermer la session administrateur. -->
            <a href="deconnexion.php">Se deconnecter</a>
        </nav>
    </header>

    <!-- Contient la liste des produits geres depuis le back-office. -->
    <main>
        <h2>Vous vous trouvez dans le back-office</h2>

        <section class="produits">

            <!-- Parcourt tous les produits du catalogue. -->
            <? foreach ($produits as $indice => $produit) : ?>

                <!-- Affiche les informations et les actions de chaque produit. -->
                <article class="produit">

                    <!-- Affiche la photo originale du produit dans un nouvel onglet au clic. -->
                    <a href="../images/<?= $produit["photo"] ?>" target="_blank">
                        <img
                            src="../vignettes/vignette_<?= $produit["photo"] ?>"
                            alt="<?= $produit["libelle"] ?>"
                        >
                    </a>

                    <!-- Affiche le nom et la description du produit. -->
                    <h3><?= $produit["libelle"] ?></h3>
                    <p><?= $produit["description"] ?></p>

                    <!-- Affiche le prix du produit avec deux decimales. -->
                    <p class="prix">
                        Prix : <?= number_format($produit["prix"], 2, ',', ' ') ?> €
                    </p>

                    <!-- Formulaire permettant de demander la suppression du produit. -->
                    <form action="supprimer.php" method="POST">

                        <!-- Transmet l'identifiant du produit a supprimer. -->
                        <input
                            type="hidden"
                            name="produit"
                            value="<?= $produit["id"] ?>"
                        >

                        <!-- Bouton qui envoie la demande de suppression. -->
                        <button type="submit" class="bouton-panier">
                            Supprimer le produit
                        </button>
                    </form>
                </article>

            <? endforeach; ?>
        </section>
    </main>
</body>

</html>