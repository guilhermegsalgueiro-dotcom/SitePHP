<?php
// Demarre la session pour acceder aux informations de l'utilisateur.
session_start();

// Verifie que l'utilisateur est connecte en tant qu'administrateur.
if (!isset($_SESSION["admin"])) {
    header('Location: ../index.php');
    exit();
}

// Definit le chemin du fichier contenant les produits et celui des images.
$fichier = "../produit.json";
$dossier = "../images/";

// Recupere les produits existants depuis le fichier JSON.
$produits = json_decode(file_get_contents($fichier), true);

// Initialise l'identifiant maximal des produits existants.
$max_id = -1;

// Parcourt les produits pour trouver le plus grand identifiant utilise.
foreach ($produits as $produit) {
    if ($produit["id"] > $max_id) {
        $max_id = $produit["id"];
    }
}

// Definit le prochain identifiant disponible.
$max_id++;

// Verifie si le formulaire d'ajout a ete envoye.
if (isset($_POST["ajouter"])) {

    // Recupere les informations saisies dans le formulaire.
    $id = $max_id;
    $libelle = $_POST["libelle"];
    $prix = $_POST["prix"];
    $description = $_POST["description"];

    // Verifie que tous les champs sont remplis et que la photo a ete envoyee correctement.
    if (
        $libelle != "" &&
        $prix != "" &&
        $description != "" &&
        isset($_FILES["photo"]) &&
        $_FILES["photo"]["error"] == 0
    ) {

        // Recupere le nom du fichier image sans son chemin.
        $photo = basename($_FILES["photo"]["name"]);

        // Definit le chemin de destination de la photo.
        $cheminPhoto = $dossier . $photo;

        // Deplace la photo envoyee vers le dossier des images.
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $cheminPhoto)) {

            // Cree un tableau contenant les informations du nouveau produit.
            $nouveauProduit = array(
                "id" => $id,
                "libelle" => $libelle,
                "prix" => (float)$prix,
                "description" => $description,
                "photo" => $photo,
            );

            // Ajoute le nouveau produit a la liste existante.
            $produits[] = $nouveauProduit;

            // Convertit le tableau PHP en chaine de caracteres au format JSON.
            $json = json_encode(
                $produits,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            // JSON_PRETTY_PRINT : formate le JSON pour faciliter sa lecture.
            // JSON_UNESCAPED_UNICODE : conserve les caracteres Unicode sans les echapper.

            // Enregistre la liste mise a jour dans le fichier JSON.
            file_put_contents($fichier, $json);

            // Genere la vignette correspondant a la nouvelle photo.
            include("../vignette.php");

            // Redirige vers la page principale du back-office.
            header("Location: index.php");
            exit();

        } else {

            // Affiche une erreur si le deplacement de la photo a echoue.
            $erreur = "Erreur lors du telechargement de la photo.";
        }

    } else {

        // Signale que certains champs sont manquants ou que la photo est invalide.
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Definit l'encodage des caracteres et le titre de la page. -->
    <meta charset="UTF-8">
    <title>Ajouter un fromage</title>

    <!-- Charge la feuille de style du site. -->
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <!-- Affiche le titre de la page d'ajout de produit. -->
    <h1>Ajouter un fromage</h1>

    <!-- Affiche le message d'erreur si une erreur a ete detectee. -->
    <?php if (isset($erreur)): ?>
        <p><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <!-- Formulaire permettant d'ajouter un produit avec sa photo. -->
    <form action="ajouter.php" method="POST" enctype="multipart/form-data">

        <!-- Champ de saisie du nom du fromage. -->
        <p>
            <label>Nom du fromage :</label>
            <input type="text" name="libelle" required>
        </p>

        <!-- Champ de saisie de la description du produit. -->
        <p>
            <label>Description :</label>
            <textarea name="description" required></textarea>
        </p>

        <!-- Champ de saisie du prix en euros. -->
        <p>
            <label>Prix (€) :</label>
            <input
                type="number"
                name="prix"
                min="0"
                step="0.01"
                required
            >
        </p>

        <!-- Champ permettant de selectionner la photo du fromage. -->
        <p>
            <label>Photo (.jpg) :</label>
            <input
                type="file"
                name="photo"
                accept="image/*"
                required
            >
        </p>

        <!-- Bouton permettant d'envoyer le formulaire. -->
        <button type="submit" name="ajouter">
            Ajouter le produit
        </button>
    </form>

    <!-- Lien permettant de revenir au back-office. -->
    <a href="index.php">Retour au back-office</a>

</body>
</html>