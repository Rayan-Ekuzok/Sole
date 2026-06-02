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
    <title>Catalogue — SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --black:#0a0a0a; --white:#f5f5f3; --gray:#888; --light:#e8e8e6; }
        body { font-family:'DM Sans',sans-serif; background:var(--white); color:var(--black); }

        nav {
            display:flex; align-items:center; justify-content:space-between;
            padding:24px 48px; border-bottom:1px solid var(--light);
            position:sticky; top:0; background:var(--white); z-index:100;
        }
        .nav-logo { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:3px; text-decoration:none; color:var(--black); }
        .nav-links { display:flex; align-items:center; gap:32px; list-style:none; }
        .nav-links a { font-size:13px; font-weight:500; text-decoration:none; color:var(--gray); letter-spacing:1px; text-transform:uppercase; transition:color .2s; }
        .nav-links a:hover { color:var(--black); }
        .btn-nav { padding:10px 24px; background:var(--black); color:var(--white) !important; border-radius:4px; font-size:12px !important; }

        .hero { padding:80px 48px 48px; display:flex; align-items:flex-end; justify-content:space-between; gap:24px; }
        .hero-title { font-family:'Bebas Neue',sans-serif; font-size:clamp(48px,8vw,96px); line-height:0.9; letter-spacing:2px; }
        .hero-count { font-size:13px; color:var(--gray); white-space:nowrap; padding-bottom:8px; }

        .catalogue { padding:0 48px 80px; display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:2px; }

        .card { background:var(--white); border:1px solid var(--light); overflow:hidden; cursor:pointer; transition:box-shadow .2s; text-decoration:none; color:inherit; display:block; }
        .card:hover { box-shadow:0 8px 32px rgba(0,0,0,0.08); z-index:1; position:relative; }

        .card-img { aspect-ratio:4/3; background:var(--light); display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .card-img img { width:100%; height:100%; object-fit:cover; transition:transform .3s; }
        .card:hover .card-img img { transform:scale(1.04); }
        .card-img-placeholder { font-family:"Bebas Neue",sans-serif; font-size:64px; color:rgba(0,0,0,0.06); letter-spacing:4px; user-select:none; }

        .card-body { padding:20px; }
        .card-marque { font-size:10px; font-weight:500; letter-spacing:2px; text-transform:uppercase; color:var(--gray); margin-bottom:4px; }
        .card-nom { font-size:16px; font-weight:500; margin-bottom:4px; }
        .card-libelle { font-size:13px; color:var(--gray); margin-bottom:16px; }
        .card-footer { display:flex; align-items:center; justify-content:space-between; }
        .card-prix { font-family:'Bebas Neue',sans-serif; font-size:24px; letter-spacing:1px; }
        .card-categorie { font-size:11px; padding:4px 10px; border:1px solid var(--light); border-radius:20px; color:var(--gray); text-transform:uppercase; letter-spacing:1px; }

        .empty { grid-column:1/-1; text-align:center; padding:80px 0; color:var(--gray); }
        .empty-icon { font-family:'Bebas Neue',sans-serif; font-size:72px; color:var(--light); display:block; margin-bottom:16px; }

        @media(max-width:768px) { nav { padding:16px 24px; } .hero { padding:40px 24px 32px; } .catalogue { padding:0 24px 48px; } }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">SOLE</a>
    <ul class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="mesCommandes.php">Mes commandes</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="login.php" class="btn-nav">Se connecter</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="hero">
    <h1 class="hero-title">Notre<br>Catalogue</h1>
    <span class="hero-count"><?= count($modeles) ?> modèle<?= count($modeles) > 1 ? 's' : '' ?></span>
</section>

<section class="catalogue">
    <?php if (empty($modeles)): ?>
        <div class="empty">
            <span class="empty-icon">SOLE</span>
            <p>Aucun produit disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($modeles as $m): ?>
        <a href="article.php?id=<?= $m['Id_modèle'] ?>" class="card">
            <div class="card-img">
                <?php if (!empty($m['image'])): ?>
                    <img src="<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['nom']) ?>" loading="lazy">
                <?php else: ?>
                    <span class="card-img-placeholder"><?= strtoupper(substr($m['nom'], 0, 2)) ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="card-marque"><?= htmlspecialchars($m['marque']) ?></div>
                <div class="card-nom"><?= htmlspecialchars($m['nom']) ?></div>
                <div class="card-libelle"><?= htmlspecialchars($m['libelle']) ?></div>
                <div class="card-footer">
                    <span class="card-prix">À partir de <?= number_format($m['prix'], 2, ',', ' ') ?> €</span>
                    <span class="card-categorie"><?= htmlspecialchars($m['categorie']) ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

</body>
</html>