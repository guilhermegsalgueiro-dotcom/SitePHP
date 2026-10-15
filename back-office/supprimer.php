<?php 
session_start();
$produits = json_decode(file_get_contents("../produit.json"),true);
$indice = $_POST["produit"];
unset($produits[$indice]);
unset($_SESSION['panier'][$indice]);
$produits = array_values($produits); // Reindex array
file_put_contents("../produit.json", json_encode($produits, JSON_PRETTY_PRINT));
header('Location: index.php');
exit();
?>