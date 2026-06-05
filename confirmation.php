<?php
session_start();

if (!isset($_SESSION['confirmation'])) {
    header('Location: index.php');
    exit;
}

$c = $_SESSION['confirmation'];
unset($_SESSION['confirmation']);

$est_panier = isset($c['lignes']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande confirmée : SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="corps-confirmation">

<div class="overlay-confirmation" id="overlay-confirmation">
    <div class="cercle-validation">
        <div class="icone-validation">
            <svg viewBox="0 0 40 40"><path d="M8 20 L16 28 L32 12"/></svg>
        </div>
    </div>
    <div class="titre-overlay">Achat effectué</div>
    <div class="sous-titre-overlay">Merci pour votre commande</div>
    <div class="numero-overlay">Commande #<?= $c['id_commande'] ?></div>
    <div class="barre-progression"></div>
</div>

<div class="page-detail-confirmation">
    <div class="icone-detail">✓</div>
    <h1 class="titre-confirmation">Commande confirmée</h1>
    <p class="sous-titre-confirmation">Votre commande a bien été enregistrée.</p>
    <div class="numero-commande-confirmation">Commande #<?PHP ECHo $c['id_commande'] ?></div>

    <div class="recap-confirmation">
        <?php if ($est_panier): ?>
            <?php foreach ($c['lignes'] as $l): ?>
            <div class="article-recap">
                <div class="entete-article-recap">
                    <span class="nom-article-recap"><?= htmlspecialchars($l['marque'] . ' ' . $l['modele']) ?></span>
                    <span class="prix-article-recap"><?= number_format($l['sous_total'], 2, ',', ' ') ?> €</span>
                </div>
                <div class="detail-article-recap">
                    <?PHP ECHO $l['taille'] ?> · <?PHP ECHO $l['couleur'] ?> · Qté <?PHP ECHO $l['quantite'] ?> · <?PHP ECHO number_format($l['prix_unitaire'], 2, ',', ' ') ?> € /u
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="article-recap">
                <div class="entete-article-recap">
                    <span class="nom-article-recap"><?PHP ECHO $c['modele'] ?></span>
                    <span class="prix-article-recap"><?PHP ECHO number_format($c['montant_total'], 2, ',', ' ') ?> €</span>
                </div>
                <div class="detail-article-recap">
                    <?PHP ECHO $c['taille'] ?> · <?PHP ECHO $c['couleur'] ?> · Qté <?PHP ECHO $c['quantite'] ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="separateur-recap"></div>
        <div class="ligne-total-recap">
            <span class="label-total-recap">Total</span>
            <span class="valeur-total-recap"><?PHP ECHO number_format($c['montant_total'], 2, ',', ' ') ?> €</span>
        </div>
    </div>

    <div class="actions-confirmation">
        <a href="mesCommandes.php" class="btn-principal">Mes commandes</a>
        <a href="index.php" class="btn-secondaire">Continuer les achats</a>
    </div>
</div>

<script>
    document.getElementById('overlay-confirmation').addEventListener('animationend', function () {
        this.remove();
    });
</script>
</body>
</html>