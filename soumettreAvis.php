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

$id_modele    = intval($_POST['id_modele']    ?? 0);
$note         = intval($_POST['note']         ?? 0);
$commentaire  = trim($_POST['commentaire']    ?? '');
$id_user      = $_SESSION['user_id'];

if (!$id_modele) {
    header('Location: index.php');
    exit;
}

$erreurs = [];

if ($note < 1 || $note > 5)
    $erreurs[] = "Veuillez sélectionner une note entre 1 et 5.";

if (strlen($commentaire) < 5)
    $erreurs[] = "Le commentaire doit faire au moins 5 caractères.";

if (strlen($commentaire) > 255)
    $erreurs[] = "Le commentaire ne peut pas dépasser 255 caractères.";

if (!utilisateurACommandeModele($id_user, $id_modele))
    $erreurs[] = "Vous devez avoir commandé ce produit pour laisser un avis.";

if (utilisateurADejaCommente($id_user, $id_modele))
    $erreurs[] = "Vous avez déjà laissé un avis sur ce produit.";

if (!empty($erreurs)) {
    $_SESSION['erreurs_avis'] = $erreurs;
    header("Location: article.php?id=$id_modele");
    exit;
}

ajouterAvis($id_user, $id_modele, $note, $commentaire);

$_SESSION['succes_avis'] = "Votre avis a bien été publié. Merci !";
header("Location: article.php?id=$id_modele");
exit;