<?php
/*
 * ============================================================
 * Projet              : La Fromagerie
 * Description         : Site de vente de fromages avec back-end
 * Auteurs             : GRAVE SALGUEIRO Guilherme & TAJAN Alexandra
 * Date creation       :  9 Octobre 2026
 * Date derniere modif : 24 Octobre 2026
 * Plus d'informations : Les commentaires et le CSS ont ete realises a l'aide de l'IA
 * ============================================================
 */

// Demarre la session pour acceder aux donnees de l'utilisateur.
session_start();

// Recupere la liste des produits depuis le fichier JSON.
$produits = json_decode(file_get_contents("produit.json"), true);

// Initialise le panier lors de la premiere visite.
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Verifie si une action sur le panier a ete envoyee.
if (isset($_POST["produit"], $_POST["action"])) {

    // Recupere l'identifiant du produit et l'action demandee.
    $id = $_POST["produit"];
    $action = $_POST["action"];

    // Recherche le produit correspondant a l'identifiant fourni.
    $produitExiste = false;

    foreach ($produits as $produit) {
        if ((string)$produit["id"] === (string)$id) {
            $produitExiste = true;
            break;
        }
    }

    // Verifie que le produit existe dans le catalogue.
    if ($produitExiste) {

        // Ajoute une unite du produit au panier.
        if ($action === "ajouter") {

            if (isset($_SESSION['panier'][$id])) {
                $_SESSION['panier'][$id]++;
            } else {
                $_SESSION['panier'][$id] = 1;
            }

        // Retire une unite du produit du panier.
        } elseif ($action === "soustraire") {

            if (isset($_SESSION['panier'][$id])) {

                // Supprime le produit si sa quantite atteint zero.
                if ($_SESSION['panier'][$id] == 1) {
                    unset($_SESSION['panier'][$id]);

                // Diminue la quantite d'une unite.
                } else {
                    $_SESSION['panier'][$id]--;
                }
            }
        }
    }

    // Redirige vers la page d'accueil apres la modification du panier.
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Definit l'encodage des caracteres et l'affichage sur les appareils mobiles. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Definit le titre affiche dans l'onglet du navigateur. -->
    <title>La Fromagerie</title>

    <!-- Charge la feuille de style du site. -->
    <link rel="stylesheet" href="style.css">
</head>

<body id="page-index">

    <!-- En-tete du site contenant le nom de la fromagerie et la navigation. -->
    <header>
        <h1>La Fromagerie</h1>

        <nav>
            <!-- Affiche le lien adapte selon que l'utilisateur est administrateur ou non. -->
            <? if (isset($_SESSION["admin"])): ?>
                <a href="back-office/index.php">Acceder au back-office</a>
            <? else: ?>
                <a href="connexion.php">Acceder au back-office</a>
            <? endif; ?>

            <!-- Permet d'acceder a la page du panier. -->
            <a href="panier.php">Voir son panier</a>
        </nav>
    </header>

    <!-- Contenu principal de la page. -->
    <main>
        <h2>Nos produits</h2>

        <!-- Affiche l'ensemble des produits disponibles dans le catalogue. -->
        <section class="produits">

            <? foreach ($produits as $indice => $produit) :
                // Recupere le nom du fichier image du produit.
                $photo = $produit["photo"];

                // Genere la vignette uniquement si elle n'existe pas encore.
                if (!file_exists(__DIR__ . "/vignettes/" . $photo)) {
                    include("vignette.php");
                }
            ?>

                <!-- Presente les informations et les actions associees a un produit. -->
                <article class="produit" id="<?= $indice ?>">

                    <!-- Affiche la photo originale du produit dans un nouvel onglet au clic. -->
                    <a href="images/<?= $produit["photo"] ?>" target="_blank">
                        <img
                            src="vignettes/vignette_<?= $produit["photo"] ?>"
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

                    <!-- Formulaire permettant d'ajouter ou de retirer le produit du panier. -->
                    <form action="" method="POST">

                        <!-- Transmet l'identifiant du produit au serveur. -->
                        <input
                            type="hidden"
                            name="produit"
                            value="<?= $produit["id"] ?>"
                        >

                        <!-- Affiche le bouton d'ajout si le produit n'est pas dans le panier. -->
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

                            <!-- Affiche le bouton permettant de retirer une unite du panier. -->
                            <button
                                type="submit"
                                class="bouton-panier"
                                name="action"
                                value="soustraire"
                            >-</button>

                            <!-- Affiche la quantite actuelle du produit dans le panier. -->
                            <p><?= $_SESSION['panier'][$produit["id"]] ?></p>

                            <!-- Affiche le bouton permettant d'ajouter une unite supplementaire. -->
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
