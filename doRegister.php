<?php
session_start();
require_once 'bdd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$champs = ['nom', 'prenom', 'email', 'password', 'adresse', 'code_postal', 'ville', 'pays'];
$donnees = [];
foreach ($champs as $c) {
    $donnees[$c] = trim($_POST[$c] ?? '');
}

// Validation ---------------------------------------
$erreurs = [];

if (empty($donnees['nom']))
    $erreurs[] = "Le nom est requis.";

if (empty($donnees['prenom']))
    $erreurs[] = "Le prénom est requis.";

if (empty($donnees['email']) || !filter_var($donnees['email'], FILTER_VALIDATE_EMAIL))
    $erreurs[] = "Adresse email invalide.";

if (strlen($donnees['password']) < 6)
    $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";

if ($donnees['password'] !== $donnees['password_confirm'])
    $erreurs[] = "Les mots de passe ne correspondent pas.";

if (empty($donnees['adresse']))
    $erreurs[] = "L'adresse est requise.";

if (empty($donnees['code_postal']))
    $erreurs[] = "Le code postal est requis.";

if (empty($donnees['ville']))
    $erreurs[] = "La ville est requise.";

if (empty($donnees['pays']))
    $erreurs[] = "Le pays est requis.";

// Vérifier si l'email est déjà utilisé
if (empty($erreurs) && getUserByEmail($donnees['email'])) {
    $erreurs[] = "Cette adresse email est déjà associée à un compte.";
}

// en cas d'erreurs retour formulaire -------------------------------------------
if (!empty($erreurs)) {
    $_SESSION['erreurs_register'] = $erreurs;
    // On repasse les données sauf les mots de passe
    unset($donnees['password']);
    $_SESSION['donnees_register'] = $donnees;
    header('Location: register.php');
    exit;
}

//  Création du compte 6666666666666666666666666
$user = creerUtilisateur(
    $donnees['nom'],
    $donnees['prenom'],
    $donnees['email'],
    $donnees['password'],
    $donnees['adresse'],
    $donnees['code_postal'],
    $donnees['pays'],
    $donnees['ville']
);

// Connexion automatique après inscription
$_SESSION['user_id']     = $user['Id_utilisateur'];
$_SESSION['user_nom']    = $user['nom'];
$_SESSION['user_prenom'] = $user['prenom'];
$_SESSION['user_email']  = $user['email'];

$_SESSION['succes_inscription'] = "Bienvenue, {$user['prenom']} ! Votre compte a bien été créé.";
header('Location: index.php');
exit;