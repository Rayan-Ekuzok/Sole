<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

session_start();
require_once 'bdd.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id_exemplaire = intval($_POST['id_exemplaire'] ?? 0);
$quantite      = intval($_POST['quantite']      ?? 1);

if (!$id_exemplaire || $quantite < 1) {
    header('Location: index.php');
    exit;
}

// Vérifier que le stock existe
$exemplaire = getExemplaireById($id_exemplaire);
if (!$exemplaire || $exemplaire['quantite'] < $quantite) {
    $_SESSION['erreur_panier'] = "Stock insuffisant.";
    header('Location: index.php');
    exit;
}

ajouterAuPanier($_SESSION['user_id'], $id_exemplaire, $quantite);

$_SESSION['succes_panier'] = "Article ajouté au panier !";
header('Location: panier.php');
exit;