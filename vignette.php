<?php
// Definit le chemin de l'image originale du produit.
$source = __DIR__ . "/images/" . $photo;

// Charge l'image JPEG depuis le fichier source.
$src = imagecreatefromjpeg($source);

// Recupère les dimensions de l'image originale.
$taille = getimagesize($source);

// Cree une nouvelle image vide de 250 pixels sur 250.
$dst = imagecreatetruecolor(250, 250);

// Redimensionne l'image originale pour remplir la vignette.
imagecopyresampled(
    $dst,           // Image de destination.
    $src,           // Image source.
    0, 0,           // Coordonnees de depart sur l'image de destination.
    0, 0,           // Coordonnees de depart sur l'image source.
    250, 250,       // Dimensions de la vignette.
    $taille[0],     // Largeur de l'image originale.
    $taille[1]      // Hauteur de l'image originale.
);

// Enregistre la vignette au format JPEG dans le dossier prevu.
imagejpeg($dst, __DIR__ . "/vignettes/vignette_" . $photo);
?>