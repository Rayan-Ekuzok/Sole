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
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root { --black:#0a0a0a; --white:#f5f5f3; --gray:#888; --light:#e8e8e6; }
        body { font-family:'DM Sans',sans-serif; background:var(--white); color:var(--black); }

        nav { display:flex; align-items:center; justify-content:space-between; padding:24px 48px; border-bottom:1px solid var(--light); position:sticky; top:0; background:var(--white); z-index:100; }
        .nav-logo { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:3px; text-decoration:none; color:var(--black); }
        .nav-links { display:flex; gap:24px; list-style:none; }
        .nav-links a { font-size:13px; color:var(--gray); text-decoration:none; letter-spacing:1px; text-transform:uppercase; transition:color .2s; }
        .nav-links a:hover { color:var(--black); }

        .page { max-width:900px; margin:0 auto; padding:56px 48px 80px; }
        .page-header { margin-bottom:48px; padding-bottom:32px; border-bottom:1px solid var(--light); }
        .page-title { font-family:'Bebas Neue',sans-serif; font-size:56px; letter-spacing:2px; margin-bottom:4px; }
        .page-subtitle { font-size:14px; color:var(--gray); }

        .commande { border:1px solid var(--light); border-radius:8px; margin-bottom:24px; overflow:hidden; }
        .commande-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; background:#fafaf8; border-bottom:1px solid var(--light); gap:16px; flex-wrap:wrap; }
        .commande-id { font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:1px; }
        .commande-date { font-size:13px; color:var(--gray); }
        .commande-right { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
        .statut-badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500; }
        .commande-total { font-family:'Bebas Neue',sans-serif; font-size:22px; }
        .remise-badge { font-size:11px; color:#0A7A45; background:#E6FAF0; padding:3px 8px; border-radius:10px; }

        .toggle-btn { background:none; border:1px solid var(--light); border-radius:4px; padding:6px 12px; font-family:'DM Sans',sans-serif; font-size:12px; color:var(--gray); cursor:pointer; letter-spacing:1px; text-transform:uppercase; transition:all .15s; }
        .toggle-btn:hover { border-color:var(--black); color:var(--black); }

        .lignes { padding:0 24px; display:none; }
        .lignes.open { display:block; }
        .ligne { display:flex; align-items:center; justify-content:space-between; padding:16px 0; border-bottom:1px solid var(--light); gap:16px; flex-wrap:wrap; }
        .ligne:last-child { border-bottom:none; }
        .ligne-produit { font-size:15px; font-weight:500; }
        .ligne-detail { font-size:13px; color:var(--gray); margin-top:2px; }
        .ligne-right { text-align:right; }
        .ligne-prix { font-family:'Bebas Neue',sans-serif; font-size:20px; }
        .ligne-qte { font-size:12px; color:var(--gray); }

        .empty { text-align:center; padding:80px 0; color:var(--gray); }
        .empty-big { font-family:'Bebas Neue',sans-serif; font-size:72px; color:var(--light); display:block; margin-bottom:16px; }
        .btn-catalogue { display:inline-block; margin-top:24px; padding:14px 32px; background:var(--black); color:var(--white); text-decoration:none; border-radius:6px; font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; }

        @media(max-width:600px) { nav, .page { padding-left:24px; padding-right:24px; } .page { padding-top:32px; } }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">SOLE</a>
    <ul class="nav-links">
        <li><a href="index.php">Catalogue</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</nav>

<div class="page">
    <div class="page-header">
        <h1 class="page-title">Mes commandes</h1>
        <p class="page-subtitle">
            Bonjour <?= htmlspecialchars($_SESSION['user_prenom']) ?>,
            vous avez <?= count($commandes) ?> commande<?= count($commandes) > 1 ? 's' : '' ?>.
        </p>
    </div>

    <?php if (empty($commandes)): ?>
        <div class="empty">
            <span class="empty-big">0</span>
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="index.php" class="btn-catalogue">Voir le catalogue</a>
        </div>
    <?php else: ?>
        <?php foreach ($commandes as $i => $c):
            $style  = statutStyle($c['statut']);
            $lignes = getLignesByCommande($c['Id_commande']);
        ?>
        <div class="commande">
            <div class="commande-header">
                <div>
                    <div class="commande-id">Commande #<?= $c['Id_commande'] ?></div>
                    <div class="commande-date"><?= date('d/m/Y à H:i', strtotime($c['date_commande'])) ?></div>
                </div>
                <div class="commande-right">
                    <span class="statut-badge" style="background:<?= $style['bg'] ?>;color:<?= $style['color'] ?>">
                        <?= $style['label'] ?>
                    </span>
                    <?php if ($c['remise'] > 0): ?>
                        <span class="remise-badge">-<?= $c['remise'] ?>%</span>
                    <?php endif; ?>
                    <span class="commande-total"><?= number_format($c['montant_total'], 2, ',', ' ') ?> €</span>
                    <button class="toggle-btn" onclick="toggleLignes(<?= $i ?>)" id="btn-<?= $i ?>">Détail</button>
                </div>
            </div>

            <div class="lignes" id="lignes-<?= $i ?>">
                <?php foreach ($lignes as $l): ?>
                <div class="ligne">
                    <div>
                        <div class="ligne-produit"><?= htmlspecialchars($l['marque'] . ' ' . $l['modele']) ?></div>
                        <div class="ligne-detail"><?= htmlspecialchars($l['taille']) ?> · <?= htmlspecialchars($l['couleur']) ?></div>
                    </div>
                    <div class="ligne-right">
                        <div class="ligne-prix"><?= number_format($l['prix_unitaire'], 2, ',', ' ') ?> €</div>
                        <div class="ligne-qte">Qté : <?= $l['quantite'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function toggleLignes(i) {
    const el  = document.getElementById('lignes-' + i);
    const btn = document.getElementById('btn-' + i);
    btn.textContent = el.classList.toggle('open') ? 'Masquer' : 'Détail';
}
</script>

</body>
</html>