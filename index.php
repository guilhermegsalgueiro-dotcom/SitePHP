<?php
session_start();

$produits = json_decode(file_get_contents("produit.json"), true);

// Initialise le panier lors de la première visite.
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Met à jour la quantité d'un produit dans le panier.
if (isset($_POST["produit"])) {
    $indice = $_POST["produit"];
    $action = $_POST["action"];

    if ($action == "ajouter") {
        if (isset($_SESSION['panier'][$indice])) {
            $_SESSION['panier'][$indice]++;
        } else {
            $_SESSION['panier'][$indice] = 1;
        }
    } elseif ($action == "soustraire") {
        // Supprime le produit du panier lorsque sa quantité atteint zéro.
        if ($_SESSION['panier'][$indice] == 1) {
            unset($_SESSION['panier'][$indice]);
        } else {
            $_SESSION['panier'][$indice]--;
        }
    }

    // Évite de renvoyer le formulaire lors de l'actualisation de la page.
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Fromagerie</title>
    <link rel="stylesheet" href="style.css">
</head>

<body id="page-index">
    <header>
        <h1>La Fromagerie</h1>
        <nav>
            <? if (isset($_SESSION["admin"])): ?>
                <a href="back-office/index.php">Accéder au back-office</a>
            <? else: ?>
                <a href="connexion.php">Accéder au back-office</a>
            <? endif; ?>

            <a href="panier.php">Voir son panier</a>
        </nav>
    </header>

    <main>
        <h2>Nos produits</h2>

        <section class="produits">
            <? foreach ($produits as $indice => $produit) :
                $photo = $produit["photo"];

                // Génère la vignette uniquement si elle n'existe pas encore.
                if (!file_exists(__DIR__ . "/vignettes/" . $photo)) {
                    include("vignette.php");
                }
            ?>
                <article class="produit" id="<?= $indice ?>">
                    <a href="images/<?= $produit["photo"] ?>" target="_blank">
                        <img
                            src="vignettes/vignette_<?= $produit["photo"] ?>"
                            alt="<?= $produit["libelle"] ?>"
                        >
                    </a>

                    <h3><?= $produit["libelle"] ?></h3>
                    <p><?= $produit["description"] ?></p>

                    <p class="prix">
                        Prix : <?= number_format($produit["prix"], 2, ',', ' ') ?> €
                    </p>

                    <form action="" method="POST">
                        <input
                            type="hidden"
                            name="produit"
                            value="<?= $produit["id"] ?>"
                        >

                        <? if (!isset($_SESSION['panier'][$produit["id"]]) || $_SESSION['panier'][$produit["id"]] == 0): ?>
                            <button
                                type="submit"
                                class="bouton-panier"
                                name="action"
                                value="ajouter"
                            >
                                Ajouter au panier
                            </button>
                        <? else: ?>
                            <button
                                type="submit"
                                class="bouton-panier"
                                name="action"
                                value="soustraire"
                            >-</button>

                            <p><?= $_SESSION['panier'][$produit["id"]] ?></p>

                            <button
                                type="submit"
                                class="bouton-panier"
                                name="action"
                                value="ajouter"
                            >+</button>
                        <? endif; ?>
                    </form>
                </article>
            <? endforeach; ?>
        </section>
    </main>
</body>
</html>