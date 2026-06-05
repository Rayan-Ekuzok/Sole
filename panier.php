<?php
session_start();
require_once 'bdd.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['user_id'];

// ── Actions AJAX ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    switch ($_POST['action']) {
        case 'supprimer':
            supprimerDuPanier(intval($_POST['id_panier']), $id_user);
            echo json_encode(['ok' => true]);
            exit;

        case 'maj_quantite':
            mettreAJourPanier(intval($_POST['id_panier']), $id_user, intval($_POST['quantite']));
            echo json_encode(['ok' => true]);
            exit;
    }
    echo json_encode(['ok' => false]);
    exit;
}

$articles = getPanierByUtilisateur($id_user);
$nb       = getNbArticlesPanier($id_user);
$total    = array_sum(array_column($articles, 'sous_total'));

$erreurs_panier = $_SESSION['erreurs_panier'] ?? [];
unset($_SESSION['erreurs_panier']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier : SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'menu.php'; ?>

<?php if (!empty($erreurs_panier)): ?>
<div class="toast-erreur-stock" id="toast-erreur-stock">
    <span class="toast-icone">⚠️</span>
    <div class="toast-erreur-corps">
        <strong>Stock insuffisant</strong>
        <ul class="liste-erreurs-stock">
            <?php foreach ($erreurs_panier as $e): ?>
                <li><?php echo $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <button onclick="document.getElementById('toast-erreur-stock').remove()" class="toast-fermer">✕</button>
</div>
<?php endif; ?>

<?php if (empty($articles)): ?>
<div style="max-width:1000px;margin:0 auto;padding:56px 48px 80px;">
    <div class="etat-vide">
        <span class="etat-vide-grand">0</span>
        <p>Votre panier est vide.</p>
        <a href="index.php" class="btn-catalogue">Voir le catalogue</a>
    </div>
</div>

<?php else: ?>
<div class="page-panier">
    <div>
        <h1 class="titre-page">Mon panier</h1>
        <p class="compteur-page" id="label-compteur"><?= $nb ?> article<?= $nb > 1 ? 's' : '' ?></p>

        <div class="liste-articles" id="liste-articles">
            <?php foreach ($articles as $a): ?>
            <div class="carte-article" id="carte-<?= $a['Id_panier'] ?>">

                <div class="article-image">
                    <?php if (!empty($a['image'])): ?>
                        <img src="<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['modele']) ?>">
                    <?php else: ?>
                        <span class="article-image-placeholder"><?= strtoupper(substr($a['modele'], 0, 2)) ?></span>
                    <?php endif; ?>
                </div>

                <div class="article-infos">
                    <div class="article-marque"><?PHP ECHO $a['marque'] ?></div>
                    <div class="article-nom"><?PHP ECHO $a['modele'] ?></div>
                    <div class="article-meta"><?PHP ECHO $a['taille'] ?> · <?PHP ECHO $a['couleur'] ?></div>
                    <div class="article-prix"><?PHP ECHO number_format($a['prix_final'], 2, ',', ' ') ?> € / unité</div>
                </div>

                <div class="article-actions">
                    <div class="ligne-quantite">
                        <button class="btn-qte" onclick="changerQuantite(<?= $a['Id_panier'] ?>, -1)">−</button>
                        <input class="numero-qte" id="qte-<?PHP ECHO $a['Id_panier'] ?>" type="number" value="<?= $a['quantite'] ?>" min="1" readonly>
                        <button class="btn-qte" onclick="changerQuantite(<?= $a['Id_panier'] ?>, +1)">+</button>
                    </div>
                    <button class="btn-retirer" onclick="supprimerArticle(<?= $a['Id_panier'] ?>)">✕ Retirer</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="boite-recap">
        <div class="titre-recap">Récapitulatif</div>
        <?php foreach ($articles as $a): ?>
        <div class="ligne-recap" id="recap-<?PHP ECHO $a['Id_panier'] ?>">
            <span class="label-recap"><?PHP ECHO $a['modele'] ?> ×<?PHP ECHO $a['quantite'] ?></span>
            <span><?= number_format($a['sous_total'], 2, ',', ' ') ?> €</span>
        </div>
        <?php endforeach; ?>
        <div class="total-recap" id="total-recap"><?= number_format($total, 2, ',', ' ') ?> €</div>

        <a href="validerPanier.php" class="btn-valider" id="btn-valider">
            Valider la commande
        </a>
        <p class="note-recap">Stock vérifié à la validation</p>
    </div>
</div>
<?php endif; ?>

<script>
let articles = <?= json_encode(array_map(fn($a) => [
    'id'       => $a['Id_panier'],
    'quantite' => $a['quantite'],
    'prix'     => floatval($a['prix_final']),
    'nom'      => $a['modele'],
], $articles)) ?>;

function envoyerRequete(action, donnees) {
    return fetch('panier.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action, ...donnees })
    }).then(r => r.json());
}

function changerQuantite(id, delta) {
    const art = articles.find(a => a.id === id);
    const nouvelleQte = art.quantite + delta;

    if (nouvelleQte <= 0) {
        supprimerArticle(id);
        return;
    }

    art.quantite = nouvelleQte;
    document.getElementById('qte-' + id).value = nouvelleQte;

    envoyerRequete('maj_quantite', { id_panier: id, quantite: nouvelleQte })
        .then(() => recalculer());
}

function supprimerArticle(id) {
    const carte = document.getElementById('carte-' + id);
    carte.classList.add('en-suppression');
    envoyerRequete('supprimer', { id_panier: id }).then(() => {
        setTimeout(() => {
            carte.remove();
            const recap = document.getElementById('recap-' + id);
            if (recap) recap.remove();
            articles = articles.filter(a => a.id !== id);
            recalculer();
            if (articles.length === 0) location.reload();
        }, 300);
    });
}

function recalculer() {
    const total = articles.reduce((s, a) => s + a.prix * a.quantite, 0);
    const nb    = articles.reduce((s, a) => s + a.quantite, 0);

    document.getElementById('total-recap').textContent =
        total.toFixed(2).replace('.', ',') + ' €';
    document.getElementById('label-compteur').textContent =
        nb + ' article' + (nb > 1 ? 's' : '');

    articles.forEach(a => {
        const el = document.getElementById('recap-' + a.id);
        if (el) {
            el.querySelector('span:last-child').textContent =
                (a.prix * a.quantite).toFixed(2).replace('.', ',') + ' €';
            el.querySelector('.label-recap').textContent =
                a.nom + ' ×' + a.quantite;
        }
    });
}
</script>

</body>
</html>