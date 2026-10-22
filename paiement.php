<?php
session_start();

$erreurs = [];
$succes = false;

$produits = json_decode(file_get_contents("produit.json"), true);
$panier = $_SESSION['panier'] ?? [];

// Vérifie que le catalogue et le panier sont valides avant le paiement.
if (!is_array($produits) || !is_array($panier) || empty($panier)) {
    die("Votre panier est vide ou invalide. Veuillez retourner au panier.");
}

$total = 0;

foreach ($panier as $indice => $quantite) {
    // Vérifie l'identifiant, le prix et la quantité de chaque produit.
    if (
        !is_int($indice) && !ctype_digit((string)$indice)
    ) {
        die("Panier invalide. Veuillez retourner au panier.");
    }

    if (
        !isset($produits[$indice]) ||
        !isset($produits[$indice]['prix']) ||
        !is_numeric($produits[$indice]['prix']) ||
        !is_int($quantite) ||
        $quantite <= 0
    ) {
        die("Panier invalide. Veuillez retourner au panier.");
    }

    $prix = (float)$produits[$indice]['prix'];

    if (!is_finite($prix) || $prix < 0) {
        die("Prix invalide. Veuillez contacter le vendeur.");
    }

    // Recalcule le montant à partir des prix du catalogue côté serveur.
    $total += $prix * $quantite;
}

// Arrondit le montant final à deux décimales.
$total = round($total, 2);

if ($total <= 0) {
    die("Le montant à payer est invalide.");
}

// Valide les informations envoyées par le formulaire de paiement.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['numero_carte'])
) {
    $numero = trim($_POST['numero_carte'] ?? '');
    $dateValidite = $_POST['date_validite'] ?? '';

    // Vérifie le format du numéro de carte et la règle du dernier chiffre.
    if (!preg_match('/^[0-9]{16}$/', $numero)) {
        $erreurs[] = "Le numéro de carte doit contenir exactement 16 chiffres.";
    } elseif ($numero[15] !== $numero[0]) {
        $erreurs[] = "Le dernier chiffre doit être identique au premier.";
    }

    // Vérifie le format de la date de validité et sa cohérence.
    if (!preg_match('/^\d{4}-\d{2}$/', $dateValidite)) {
        $erreurs[] = "Veuillez saisir une date de validité correcte.";
    } else {
        $date = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $dateValidite . '-01'
        );

        $dateDuJour = new DateTimeImmutable('today');
        $dateLimite = $dateDuJour->modify('+3 months');

        if (!$date || $date->format('Y-m') !== $dateValidite) {
            $erreurs[] = "La date de validité est incorrecte.";
        } else {
            // Considère la carte valide jusqu'au dernier jour du mois indiqué.
            $finValidite = $date->modify('last day of this month');

            if ($finValidite <= $dateLimite) {
                $erreurs[] =
                    "La carte doit être valide au-delà de 3 mois à partir d'aujourd'hui.";
            }
        }
    }

    // Confirme le paiement uniquement si toutes les vérifications passent.
    if (empty($erreurs)) {
        $succes = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paiement</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Paiement</h1>
        <nav>
            <a href="panier.php">Revenir au panier</a>
        </nav>
    </header>

    <main>
        <h1>Paiement</h1>

        <p>
            Montant à payer :
            <strong><?= number_format($total, 2, ',', ' ') ?> €</strong>
        </p>

        <?php if ($succes): ?>
            <p><strong>Paiement réalisé !</strong></p>
            <a href="index.php">Retour à l'accueil</a>
        <?php else: ?>

            <?php foreach ($erreurs as $erreur): ?>
                <p style="color: red;">
                    <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endforeach; ?>

            <form action="paiement.php" method="POST">
                <p>
                    <label for="numero_carte">
                        Numéro de carte (16 chiffres) :
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

                <p>
                    <label for="date_validite">Date de validité :</label><br>

                    <input
                        type="month"
                        id="date_validite"
                        name="date_validite"
                        required
                    >
                </p>

                <button type="submit">Valider le paiement</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>