<?php 
session_start(); 
$produits = json_decode(file_get_contents("produit.json"),true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
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
        <? if (empty($_SESSION['panier'])): ?>
            <p>Votre panier est vide</p>
        <? else: ?>
            <? foreach ($_SESSION['panier'] as $indice => $quantite): ?>
                <article class="produit" id="$indice">
                    <a href="<?=$produits[$indice]["photo"] ?>" target="_blank">
                    <img src="<?=$produits[$indice]["photo"]?>" alt="<?=$produits[$indice]["libelle"]?>">
                    </a>
                    <h3><?=$produits[$indice]["libelle"]?></h3>
                    <p><?=$produits[$indice]["description"]?></p>
                    <p class="prix"><?=number_format($produits[$indice]["prix"], 2, ',', ' ')?> €</p>
                    <p>Quantité : <?=$quantite?></p>
                </article>
            <? endforeach; ?>
        <? endif; ?>
    </main>
</body>
</html>