<?php
$fichier = "../produit.json";
$dossier = "../images/";

$produits = json_decode(file_get_contents($fichier), true);

$max_id = 0;
for ($i=0; $i < count($produits); $i++) { 
    if ($produits[$i]["id"]==$max_id) {
        $max_id++;
    }
}

// lire les produits existants dans le fichier JSON et les transformer en tableau PHP et le stocker dans $produits

if (isset($_POST["ajouter"])) // vérifier si l'utilisateur a envoyé le formulaire en cliquant sur "ajouter"
{
    // Récupérer les informations du produit saisi par l'utilisateur
    $id = $max_id;
    $libelle = $_POST["libelle"];
    $prix = $_POST["prix"];
    $description = $_POST["description"];
    $vignette = $_POST["vignette"];

    // vérifier que les champs sont remplis
    if (
        $libelle != "" &&
        $prix != "" &&
        $description != "" &&
        $vignette != "" &&
        isset($_FILES["photo"]) &&
        $_FILES["photo"]["error"] == 0
    ) {
        // récupérer le nom de la photo
        $nomPhoto = basename($_FILES["photo"]["name"]);

        // Chemin de la photo
        $cheminPhoto = $dossier . $nomPhoto;

        // Déplacer la photo dans le dossier images
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $cheminPhoto))
        {
            // Créer un nouveau produit
            $nouveauProduit = array(
                "id" => $id,
                "libelle" => $libelle,
                "prix" => (float)$prix,
                "description" => $description,
                "photo" => "images/" . $nomPhoto,
                "vignette" => $vignette
            );

            $produits[] = $nouveauProduit;
            // tableau $produits qui contient les produits

            // transformer les produits en JSON
            $json = json_encode(
                $produits,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            // JSON_PRETTY_PRINT : mise en forme du JSON
            // JSON_UNESCAPED_UNICODE : empêche de mettre des trucs bizarres à la place des accents
            // json_encode() : convertir un tableau PHP en une chaîne JSON

            file_put_contents($fichier, $json);
            // enregistrer les produits dans le fichier JSON

            header("Location: index.php");
            // rediriger l'utilisateur vers une autre page
            exit;
        }
        else
        {
            $erreur = "Erreur lors du téléchargement de la photo.";
        }
    }
    else
    {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un fromage</title>
</head>
<body>
<h1>Ajouter un fromage</h1>
<?php
if (isset($erreur)): ?>
    <p><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>
<form action="ajouter.php" method="POST"
      enctype="multipart/form-data">
    <p>
        <label>Nom du fromage :</label>
        <input type="text" name="libelle" required>
    </p>
    <p>
        <label>Description :</label>
        <textarea name="description" required></textarea>
    </p>
    <p>
        <label>Prix (€) :</label>
        <input type="number" name="prix"
               min="0" step="0.01" required>
    </p>
    <p>
        <label>Photo du fromage :</label>
        <input type="file" name="photo"
               accept="image/*" required>
    </p>
    <p>
        <label>Vignette :</label>
        <input type="text" name="vignette"
               placeholder="vignette/fromage.jpg" required>
    </p>
    <button type="submit" name="ajouter">
        Ajouter le produit
    </button>
</form>
<a href="index.php">Retour au back-office</a>
</body>
</html>
