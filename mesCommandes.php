<?php
session_start();
require_once 'bdd.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$commandes = getCommandesByUtilisateur($_SESSION['user_id']);

function statutStyle($statut) {
    return match($statut) {
        'en_attente' => ['label' => 'En attente',  'bg' => '#FFF8E6', 'color' => '#B8860B'],
        'expediee'   => ['label' => 'Expédiée',    'bg' => '#E6F0FF', 'color' => '#1A56DB'],
        'livree'     => ['label' => 'Livrée',      'bg' => '#E6FAF0', 'color' => '#0A7A45'],
        'annulee'    => ['label' => 'Annulée',     'bg' => '#FFF0F0', 'color' => '#C0392B'],
        default      => ['label' => ucfirst($statut), 'bg' => '#f0f0f0', 'color' => '#888'],
    };
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes — SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'menu.php'; ?>

<div class="page-commandes">
    <div class="entete-page">
        <h1 class="titre-principal">Mes commandes</h1>
        <p class="sous-titre-page">
            Bonjour <?= htmlspecialchars($_SESSION['user_prenom']) ?>,
            vous avez <?= count($commandes) ?> commande<?= count($commandes) > 1 ? 's' : '' ?>.
        </p>
    </div>

    <?php if (empty($commandes)): ?>
        <div class="etat-vide">
            <span class="etat-vide-grand">0</span>
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="index.php" class="btn-catalogue">Voir le catalogue</a>
        </div>
    <?php else: ?>
        <?php foreach ($commandes as $i => $c):
            $style  = statutStyle($c['statut']);
            $lignes = getLignesByCommande($c['Id_commande']);
        ?>
        <div class="commande">
            <div class="entete-commande">
                <div>
                    <div class="numero-commande">Commande #<?= $c['Id_commande'] ?></div>
                    <div class="date-commande"><?= date('d/m/Y à H:i', strtotime($c['date_commande'])) ?></div>
                </div>
                <div class="droite-commande">
                    <span class="badge-statut" style="background:<?= $style['bg'] ?>;color:<?= $style['color'] ?>">
                        <?= $style['label'] ?>
                    </span>
                    <?php if ($c['remise'] > 0): ?>
                        <span class="badge-remise">-<?= $c['remise'] ?>%</span>
                    <?php endif; ?>
                    <span class="total-commande"><?= number_format($c['montant_total'], 2, ',', ' ') ?> €</span>
                    <button class="btn-detail" onclick="basculerLignes(<?= $i ?>)" id="btn-<?= $i ?>">Détail</button>
                </div>
            </div>

            <div class="lignes-commande" id="lignes-<?= $i ?>">
                <?php foreach ($lignes as $l): ?>
                <div class="ligne-commande-detail">
                    <div>
                        <div class="produit-ligne"><?= htmlspecialchars($l['marque'] . ' ' . $l['modele']) ?></div>
                        <div class="detail-ligne"><?= htmlspecialchars($l['taille']) ?> · <?= htmlspecialchars($l['couleur']) ?></div>
                    </div>
                    <div class="droite-ligne">
                        <div class="prix-ligne"><?= number_format($l['prix_unitaire'], 2, ',', ' ') ?> €</div>
                        <div class="quantite-ligne">Qté : <?= $l['quantite'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function basculerLignes(i) {
    const el  = document.getElementById('lignes-' + i);
    const btn = document.getElementById('btn-' + i);
    btn.textContent = el.classList.toggle('ouvert') ? 'Masquer' : 'Détail';
}
</script>

</body>
</html>