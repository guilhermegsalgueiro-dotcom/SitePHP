<?php
session_start();

$produits = json_decode(file_get_contents("produit.json"), true);
$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
</head>

<body id="page-panier">
    <header>
        <h1>Panier</h1>
        <nav>
            <a href="index.php">Revenir à l'accueil</a>
        </nav>
    </header>

    <main>
        <h1>Mon panier</h1>

        <?php if (empty($_SESSION['panier'])): ?>
            <p>Votre panier est vide</p>
        <?php else: ?>
            <?php foreach ($_SESSION['panier'] as $indice => $quantite): ?>
                <?php
                // Ignore les produits qui ne figurent plus dans le catalogue.
                if (!isset($produits[$indice])) {
                    continue;
                }

                // Calcule le montant de chaque ligne du panier.
                $total += $produits[$indice]["prix"] * $quantite;
                ?>

                <article class="produit" id="<?= (int)$indice ?>">
                    <a href="images/<?= htmlspecialchars($produits[$indice]["photo"], ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                        <img
                            src="vignettes/vignette_<?= htmlspecialchars($produits[$indice]["photo"], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($produits[$indice]["libelle"], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </a>

                    <h3><?= htmlspecialchars($produits[$indice]["libelle"], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($produits[$indice]["description"], ENT_QUOTES, 'UTF-8') ?></p>

                    <p class="prix">
                        Prix : <?= number_format($produits[$indice]["prix"], 2, ',', ' ') ?> €
                    </p>

                    <p>Quantité : <?= (int)$quantite ?></p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($total > 0): ?>
            <p>Total : <?= number_format($total, 2, ',', ' ') ?> €</p>

            <form action="paiement.php" method="POST">
                <button type="submit">Passer au paiement</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>