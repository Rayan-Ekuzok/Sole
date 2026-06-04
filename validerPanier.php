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

$id_user  = $_SESSION['user_id'];
$articles = getPanierByUtilisateur($id_user);

if (empty($articles)) {
    header('Location: panier.php');
    exit;
}

// ── Construire les items pour creerCommande() ─────────────────
$items = [];
$erreurs = [];

foreach ($articles as $a) {
    // Revérifier le stock en temps réel
    $ex = getExemplaireById($a['Id_exemplaire']);
    if (!$ex || $ex['quantite'] < $a['quantite']) {
        $erreurs[] = "Stock insuffisant pour {$a['modele']} ({$a['taille']} / {$a['couleur']}).";
        continue;
    }
    $items[] = [
        'id_exemplaire' => $a['Id_exemplaire'],
        'quantite'      => $a['quantite'],
        'prix_unitaire' => floatval($a['prix_final']),
    ];
}

if (!empty($erreurs)) {
    $_SESSION['erreurs_panier'] = $erreurs;
    header('Location: panier.php');
    exit;
}

// ── Créer la commande ─────────────────────────────────────────
try {
    $id_commande = creerCommande($id_user, $items);

    // Vider le panier après commande
    viderPanier($id_user);

    // Préparer la confirmation avec le détail de chaque article
    $lignes = [];
    foreach ($articles as $a) {
        $lignes[] = [
            'modele'       => $a['modele'],
            'marque'       => $a['marque'],
            'taille'       => $a['taille'],
            'couleur'      => $a['couleur'],
            'quantite'     => $a['quantite'],
            'prix_unitaire'=> floatval($a['prix_final']),
            'sous_total'   => floatval($a['sous_total']),
        ];
    }

    $montant_total = array_sum(array_column($lignes, 'sous_total'));

    $_SESSION['confirmation'] = [
        'id_commande'   => $id_commande,
        'lignes'        => $lignes,
        'montant_total' => $montant_total,
        'nb_articles'   => count($lignes),
    ];

    header('Location: confirmation.php');
    exit;

} catch (Exception $e) {
    $_SESSION['erreurs_panier'] = ["Erreur lors de la commande. Veuillez réessayer."];
    header('Location: panier.php');
    exit;
}