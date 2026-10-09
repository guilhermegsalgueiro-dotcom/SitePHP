<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $json = json_decode(file_get_contents("produit.json"), true);
    $tailleJson = count($json['produit']);

    echo "Liste des produits:<br>";
    for ($i=0; $i < $tailleJson; $i++) { 
        echo $json['produit'][$i]["libelle"];
    }
    ?>
</body>
</html>
