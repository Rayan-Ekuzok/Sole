<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$erreurs = $_SESSION['erreurs_login'] ?? [];
unset($_SESSION['erreurs_login']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion : SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="corps-login">

<div class="panneau-gauche">
    <div>
        <div class="logo-nav">SOLE</div>
        <div class="slogan-gauche">Votre boutique de chaussures</div>
    </div>
    <div class="texte-grand">STEP<br>INTO<br>STYLE</div>
    <div class="texte-bas-gauche">© <?= date('Y') ?> SOLE.</div>
</div>

<div class="panneau-droit">
    <div class="conteneur-formulaire">
        <h1 class="titre-formulaire">Connexion</h1>
        <p class="sous-titre-formulaire">Pas encore de compte ? <a href="register.php">Créer un compte</a></p>

        <?php if (!empty($erreurs)): ?>
        <div class="bloc-erreurs">
            <ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <form action="doLogin.php" method="POST">
            <div class="champ">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="exemple@mail.com" required>
            </div>
            <div class="champ">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-soumettre">Se connecter</button>
        </form>

        <div class="separateur">ou</div>
        <a href="index.php" class="lien-catalogue">Continuer sans connexion →</a>
    </div>
</div>

</body>
</html>