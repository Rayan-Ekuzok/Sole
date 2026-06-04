<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$erreurs = $_SESSION['erreurs_register'] ?? [];
$donnees = $_SESSION['donnees_register'] ?? [];
unset($_SESSION['erreurs_register'], $_SESSION['donnees_register']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte : SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="corps-login">

<div class="panneau-gauche">
    <div>
        <div class="logo-nav">SOLE</div>
        <div class="slogan-gauche">Votre boutique de chaussures</div>
    </div>
    <div class="texte-grand">JOIN<br>THE<br>SOLE</div>
    <div class="texte-bas-gauche">© <?= date('Y') ?> SOLE.</div>
</div>

<div class="panneau-droit">
    <div class="conteneur-formulaire conteneur-formulaire--large">
        <h1 class="titre-formulaire">Créer un compte</h1>
        <p class="sous-titre-formulaire">Déjà inscrit ? <a href="login.php">Se connecter</a></p>

        <?php if (!empty($erreurs)): ?>
        <div class="bloc-erreurs">
            <ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <form action="doRegister.php" method="POST">

            <div class="grille-deux-colonnes">
                <div class="champ">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Dupont" value="<?= htmlspecialchars($donnees['nom'] ?? '') ?>" required>
                </div>
                <div class="champ">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Marie" value="<?= htmlspecialchars($donnees['prenom'] ?? '') ?>" required>
                </div>
            </div>

            <div class="champ">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="exemple@mail.com" value="<?= htmlspecialchars($donnees['email'] ?? '') ?>" required>
            </div>

            <div class="champ">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="separateur-section">Adresse de livraison</div>

            <div class="champ">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" placeholder="12 rue des Lilas" value="<?= htmlspecialchars($donnees['adresse'] ?? '') ?>" required>
            </div>

            <div class="grille-trois-colonnes">
                <div class="champ">
                    <label for="code_postal">Code postal</label>
                    <input type="text" id="code_postal" name="code_postal" placeholder="75011" value="<?= htmlspecialchars($donnees['code_postal'] ?? '') ?>" required>
                </div>
                <div class="champ col-span-2">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" placeholder="Paris" value="<?= htmlspecialchars($donnees['ville'] ?? '') ?>" required>
                </div>
            </div>

            <div class="champ">
                <label for="pays">Pays</label>
                <input type="text" id="pays" name="pays" placeholder="France" value="<?= htmlspecialchars($donnees['pays'] ?? 'France') ?>" required>
            </div>

            <button type="submit" class="btn-soumettre">Créer mon compte</button>
        </form>

        <div class="separateur">ou</div>
        <a href="index.php" class="lien-catalogue">Continuer sans connexion →</a>
    </div>
</div>

</body>
</html>