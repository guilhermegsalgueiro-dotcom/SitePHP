<?php 
session_start();
if (!isset($_SESSION["admin"])) {
    header('Location: ../index.php');
    exit();
}

$produits = json_decode(file_get_contents("../produit.json"),true);
?>
<!DOCTYPE html>
<!-- saved from url=(0066)file:///J:/WWW/Ressources_des_TPs_PHP/TP8-9/affichage/accueil.html -->
<html lang="fr"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Fromagerie</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body id="page-index">
    <header>
        <h1>La Fromagerie</h1>
        <nav>
            <a href="ajouter.php">Ajouter un produit</a>
            <a href="../index.php">Revenir à l'accueil</a>
            <a href="deconnexion.php">Se déconnecter</a>
        </nav>
    </header>
    <main>
        <h2>Vous vous trouvez dans le back-office</h2>
        <section class="produits">
            <? foreach ($produits as $indice => $produit) :?>
                <article class="produit">
                    <a href="../images/<?=$produit["photo"] ?>" target="_blank">
                        <img src="../vignettes/vignette_<?= $produit["photo"] ?>" alt="<?= $produit["libelle"] ?>">
                    </a>
                    <h3><?=$produit["libelle"]?></h3>
                    <p><?=$produit["description"]?></p>
                    <p class="prix">Prix: <?=number_format($produit["prix"], 2, ',', ' ')?> €</p>
                    <form action="supprimer.php" method="POST">
                        <input type="hidden" name="produit" value="<?= $produit["id"] ?>">
                        <button type="submit" class="bouton-panier">Supprimer le produit</button>
                    </form>
                </article>
            <? endforeach; ?>
        </section>
    </main>
</body>
</html>