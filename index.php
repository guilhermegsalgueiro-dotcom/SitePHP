<?php $produits = json_decode(file_get_contents("produit.json"),true);

session_start();

// Initialiser le panier
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
?>

<!DOCTYPE html>
<html lang="fr"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Fromagerie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body id="page-index">
    <header>
        <h1>La Fromagerie</h1>
        <nav>
            <a href="connexion.php">Accéder au back-office</a>
            <a href="panier.php">Voir son panier</a>
        </nav>
    </header>
    <main>
        <h2>Nos produits</h2>
        <section class="produits">
            <? foreach ($produits as $indice => $produit) :?>
                <article class="produit" id="$indice">
                    <a href="<?=$produit["photo"] ?>" target="_blank">
                    <img src="<?=$produit["photo"]?>" alt="<?=$produit["libelle"]?>">
                    </a>
                    <h3><?=$produit["libelle"]?></h3>
                    <p><?=$produit["description"]?></p>
                    <p class="prix"><?=number_format($produit["prix"], 2, ',', ' ')?> €</p>
                    <form action="panier.php" method="POST">
                        <input type="hidden" name="produit" value="<?= $indice ?>">
                        <button type="submit" class="bouton-panier" name="ajouter">Ajouter au panier</button>
                    </form>
                </article>
            <? endforeach; ?>
        </section>
    </main>
</body>
</html>
