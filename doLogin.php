<?php
session_start();
require_once 'bdd.php';

function verifierFormulaire() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: login.php');
        exit;
    }
}

function validerChamps($email, $password) {
    $erreurs = [];
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $erreurs[] = "Email invalide.";
    if (empty($password) || strlen($password) < 6)
        $erreurs[] = "Mot de passe trop court.";
    return $erreurs;
}

function authentifier($email, $password) {
    $user = getUserByEmail($email);
    if (!$user) return null;
    if (hash('sha256', $password) !== $user['password']) return null;
    return $user;
}

function creerSession($user) {
    $_SESSION['user_id']     = $user['Id_utilisateur'];
    $_SESSION['user_nom']    = $user['nom'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_email']  = $user['email'];
}

// ── Point d'entrée ───────────────────────────────────────────
verifierFormulaire();

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$erreurs  = validerChamps($email, $password);

if (!empty($erreurs)) {
    $_SESSION['erreurs_login'] = $erreurs;
    header('Location: login.php');
    exit;
}

$user = authentifier($email, $password);

if (!$user) {
    $_SESSION['erreurs_login'] = ["Email ou mot de passe incorrect."];
    header('Location: login.php');
    exit;
}

creerSession($user);
header('Location: article.php');
exit;