<?php
session_start();
require_once 'bdd.php';

$modeles = getModeles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue : SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'menu.php'; ?>

<section class="hero">
    <h1 class="titre-hero">Notre<br>Catalogue</h1>
    <span class="compteur-hero"><?= count($modeles) ?> modèle<?= count($modeles) > 1 ? 's' : '' ?></span>
</section>

<section class="catalogue">
    <?php if (empty($modeles)): ?>
        <div class="etat-vide">
            <span class="etat-vide-grand">SOLE</span>
            <p>Aucun produit disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($modeles as $m): ?>
        <a href="article.php?id=<?= $m['Id_modèle'] ?>" class="carte">
            <div class="carte-image">
                <?php if (!empty($m['image'])): ?>
                    <img src="<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['nom']) ?>" loading="lazy">
                <?php else: ?>
                    <span class="carte-image-placeholder"><?= strtoupper(substr($m['nom'], 0, 2)) ?></span>
                <?php endif; ?>
            </div>
            <div class="carte-corps">
                <div class="carte-marque"><?= htmlspecialchars($m['marque']) ?></div>
                <div class="carte-nom"><?= htmlspecialchars($m['nom']) ?></div>
                <div class="carte-libelle"><?= htmlspecialchars($m['libelle']) ?></div>
                <div class="carte-pied">
                    <span class="carte-prix">À partir de <?= number_format($m['prix'], 2, ',', ' ') ?> €</span>
                    <span class="carte-categorie"><?= htmlspecialchars($m['categorie']) ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

</body>
</html>