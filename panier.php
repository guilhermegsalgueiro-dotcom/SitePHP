<?php
// Demarre la session pour acceder aux donnees du panier.
session_start();

// Recupere la liste des produits depuis le fichier JSON.
$produits = json_decode(file_get_contents("produit.json"), true);

// Initialise le montant total du panier.
$total = 0;

// Parcourt les produits du catalogue pour retrouver les identifiants.
$produitsParId = [];

foreach ($produits as $produit) {
    $produitsParId[$produit["id"]] = $produit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Definit l'encodage et adapte l'affichage aux appareils mobiles. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Definit le titre de la page et charge la feuille de style. -->
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
</head>

<body id="page-panier">

    <!-- Affiche l'en-tete de la page et le lien de retour a l'accueil. -->
    <header>
        <h1>Panier</h1>

        <nav>
            <a href="index.php">Revenir a l'accueil</a>
        </nav>
    </header>

    <!-- Affiche les produits du panier et le montant total. -->
    <main>
        <h1>Mon panier</h1>

        <!-- Affiche un message si le panier est vide. -->
        <?php if (empty($_SESSION['panier'])): ?>
            <p>Votre panier est vide</p>

        <?php else: ?>

            <!-- Parcourt les identifiants et les quantites du panier. -->
            <?php foreach ($_SESSION['panier'] as $id => $quantite): ?>

                <?php
                // Ignore les produits qui ne figurent plus dans le catalogue.
                if (!isset($produitsParId[$id])) {
                    continue;
                }

                // Recupere le produit correspondant a son identifiant.
                $produit = $produitsParId[$id];

                // Calcule le montant de la ligne et l'ajoute au total.
                $total += $produit["prix"] * $quantite;
                ?>

                <!-- Affiche les informations du produit present dans le panier. -->
                <article class="produit" id="<?= (int)$id ?>">

                    <!-- Affiche la photo originale dans un nouvel onglet au clic. -->
                    <a href="images/<?= htmlspecialchars($produit["photo"], ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                        <img
                            src="vignettes/vignette_<?= htmlspecialchars($produit["photo"], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($produit["libelle"], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </a>

                    <!-- Affiche le nom et la description du produit. -->
                    <h3><?= htmlspecialchars($produit["libelle"], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($produit["description"], ENT_QUOTES, 'UTF-8') ?></p>

                    <!-- Affiche le prix unitaire du produit. -->
                    <p class="prix">
                        Prix : <?= number_format($produit["prix"], 2, ',', ' ') ?> €
                    </p>

                    <!-- Affiche la quantite du produit dans le panier. -->
                    <p>Quantite : <?= (int)$quantite ?></p>
                </article>

            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Affiche le total et le bouton de paiement si le montant est positif. -->
        <?php if ($total > 0): ?>
            <p>Total : <?= number_format($total, 2, ',', ' ') ?> €</p>

            <!-- Envoie l'utilisateur vers la page de paiement. -->
            <form action="paiement.php" method="POST">
                <button type="submit">Passer au paiement</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>