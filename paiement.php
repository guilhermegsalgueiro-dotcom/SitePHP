<?php
// Demarre la session pour acceder aux donnees du panier.
session_start();

// Initialise les variables utilisees pour les erreurs et le resultat du paiement.
$erreurs = [];
$succes = false;

// Recupere la liste des produits depuis le fichier JSON.
$produits = json_decode(file_get_contents("produit.json"), true);

// Recupere le panier enregistre dans la session, ou un tableau vide s'il n'existe pas.
$panier = $_SESSION['panier'] ?? [];

// Verifie que le catalogue et le panier sont valides et que le panier n'est pas vide.
if (!is_array($produits) || !is_array($panier) || empty($panier)) {
    die("Votre panier est vide ou invalide. Veuillez retourner au panier.");
}

// Cree un tableau associatif pour retrouver les produits par leur identifiant.
$produitsParId = [];

foreach ($produits as $produit) {
    $produitsParId[$produit["id"]] = $produit;
}

// Initialise le montant total du panier.
$total = 0;

// Parcourt les produits du panier pour calculer le montant total.
foreach ($panier as $id => $quantite) {

    // Verifie que l'identifiant du produit est valide.
    if (!ctype_digit((string)$id)) {
        die("Panier invalide. Veuillez retourner au panier.");
    }

    // Verifie que le produit existe et que son prix et sa quantite sont valides.
    if (
        !isset($produitsParId[$id]) ||
        !isset($produitsParId[$id]['prix']) ||
        !is_numeric($produitsParId[$id]['prix']) ||
        !is_int($quantite) ||
        $quantite <= 0
    ) {
        die("Panier invalide. Veuillez retourner au panier.");
    }

    // Recupere le prix du produit depuis le catalogue.
    $prix = (float)$produitsParId[$id]['prix'];

    // Verifie que le prix est un nombre fini et qu'il n'est pas negatif.
    if (!is_finite($prix) || $prix < 0) {
        die("Prix invalide. Veuillez contacter le vendeur.");
    }

    // Ajoute le montant de la ligne au total.
    $total += $prix * $quantite;
}

// Arrondit le montant final a deux decimales.
$total = round($total, 2);

// Refuse un montant nul ou negatif.
if ($total <= 0) {
    die("Le montant a payer est invalide.");
}

// Verifie que le formulaire a ete envoye en POST et qu'un numero de carte a ete fourni.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['numero_carte'])
) {
    // Recupere le numero de carte en supprimant les espaces au debut et a la fin.
    $numero = trim($_POST['numero_carte'] ?? '');

    // Recupere la date de validite, ou une chaine vide si elle n'a pas ete fournie.
    $dateValidite = $_POST['date_validite'] ?? '';

    // Verifie que le numero contient exactement 16 chiffres.
    if (!preg_match('/^[0-9]{16}$/', $numero)) {
        $erreurs[] = "Le numero de carte doit contenir exactement 16 chiffres.";

    // Verifie que le dernier chiffre est identique au premier.
    } elseif ($numero[15] !== $numero[0]) {
        $erreurs[] = "Le dernier chiffre doit etre identique au premier.";
    }

    // Verifie que la date de validite respecte le format AAAA-MM.
    if (!preg_match('/^\d{4}-\d{2}$/', $dateValidite)) {
        $erreurs[] = "Veuillez saisir une date de validite correcte.";

    } else {
        // Convertit la date reçue en date correspondant au premier jour du mois.
        $date = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $dateValidite . '-01'
        );

        // Recupere la date du jour, sans tenir compte de l'heure.
        $dateDuJour = new DateTimeImmutable('today');

        // Calcule la date situee trois mois apres aujourd'hui.
        $dateLimite = $dateDuJour->modify('+3 months');

        // Verifie que la date a ete correctement creee et correspond au mois fourni.
        if (!$date || $date->format('Y-m') !== $dateValidite) {
            $erreurs[] = "La date de validite est incorrecte.";

        } else {
            // Determine le dernier jour du mois de validite indique.
            $finValidite = $date->modify('last day of this month');

            // Refuse la carte si sa date de validite ne depasse pas la date limite.
            if ($finValidite <= $dateLimite) {
                $erreurs[] =
                    "La carte doit etre valide au-dela de 3 mois a partir d'aujourd'hui.";
            }
        }
    }

    // Confirme le resultat uniquement si aucune erreur n'a ete detectee.
    if (empty($erreurs)) {
        $succes = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Definit l'encodage et l'affichage adapte aux appareils mobiles. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Definit le titre de la page affiche dans l'onglet du navigateur. -->
    <title>Paiement</title>

    <!-- Charge la feuille de style du site. -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- En-tete de la page avec le titre et le lien vers le panier. -->
    <header>
        <h1>Paiement</h1>

        <nav>
            <a href="panier.php">Revenir au panier</a>
        </nav>
    </header>

    <!-- Contenu principal de la page de paiement. -->
    <main>
        <h1>Paiement</h1>

        <!-- Affiche le montant total calcule cote serveur. -->
        <p>
            Montant a payer :
            <strong><?= number_format($total, 2, ',', ' ') ?> €</strong>
        </p>

        <!-- Affiche un message de confirmation si le paiement est valide. -->
        <?php if ($succes): ?>
            <p><strong>Paiement realise !</strong></p>

            <a href="index.php">Retour a l'accueil</a>

        <?php else: ?>

            <!-- Affiche chaque erreur detectee lors de la validation du formulaire. -->
            <?php foreach ($erreurs as $erreur): ?>
                <p style="color: red;">
                    <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endforeach; ?>

            <!-- Formulaire permettant de saisir les informations de paiement. -->
            <form action="paiement.php" method="POST">

                <!-- Champ de saisie du numero de carte. -->
                <p>
                    <label for="numero_carte">
                        Numero de carte (16 chiffres) :
                    </label><br>

                    <input
                        type="text"
                        id="numero_carte"
                        name="numero_carte"
                        inputmode="numeric"
                        pattern="[0-9]{16}"
                        maxlength="16"
                        minlength="16"
                        autocomplete="off"
                        required
                    >
                </p>

                <!-- Champ de saisie du mois et de l'annee de validite. -->
                <p>
                    <label for="date_validite">Date de validite :</label><br>

                    <input
                        type="month"
                        id="date_validite"
                        name="date_validite"
                        required
                    >
                </p>

                <!-- Bouton qui envoie le formulaire au serveur. -->
                <button type="submit">Valider le paiement</button>
            </form>

        <?php endif; ?>
    </main>
</body>

</html>
