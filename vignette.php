<?php
$nom = $photo;

$source = __DIR__ . "/images/" . $nom;

$src = imagecreatefromjpeg($source);
$taille = getimagesize($source);

$dst = imagecreatetruecolor(250, 250);

imagecopyresampled(
    $dst,
    $src,
    0, 0, 0, 0,
    250, 250,
    $taille[0], $taille[1]
);

imagejpeg($dst, __DIR__ . "/vignettes/vignette_" . $nom);
?>