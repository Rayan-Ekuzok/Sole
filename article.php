<?php
session_start();
require_once 'bdd.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$modele = getModeleById($id);
if (!$modele) { header('Location: index.php'); exit; }

$exemplaires = getExemplairesByModele($id);
$avis        = getAvisByModele($id);

// Tailles et couleurs distinctes disponibles
$tailles  = [];
$couleurs = [];
foreach ($exemplaires as $e) {
    $tailles[$e['Id_taille']]   = $e['taille'];
    $couleurs[$e['Id_couleur']] = $e['couleur'];
}

// Note moyenne
$note_moy = 0;
if (!empty($avis)) {
    $note_moy = round(array_sum(array_column($avis, 'note')) / count($avis), 1);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($modele['nom']) ?> — SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root { --black:#0a0a0a; --white:#f5f5f3; --gray:#888; --light:#e8e8e6; }
        body { font-family:'DM Sans',sans-serif; background:var(--white); color:var(--black); }

        nav { display:flex; align-items:center; justify-content:space-between; padding:24px 48px; border-bottom:1px solid var(--light); position:sticky; top:0; background:var(--white); z-index:100; }
        .nav-logo { font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:3px; text-decoration:none; color:var(--black); }
        .nav-back { font-size:13px; color:var(--gray); text-decoration:none; letter-spacing:1px; text-transform:uppercase; transition:color .2s; }
        .nav-back:hover { color:var(--black); }

        .product-layout { display:grid; grid-template-columns:1fr 1fr; min-height:calc(100vh - 73px); }
        .product-img { background:var(--light); display:flex; align-items:center; justify-content:center; position:sticky; top:73px; height:calc(100vh - 73px); overflow:hidden; }
        .product-img-placeholder { font-family:'Bebas Neue',sans-serif; font-size:clamp(80px,15vw,160px); color:rgba(0,0,0,0.07); letter-spacing:8px; user-select:none; }
        .product-img img { width:100%; height:100%; object-fit:contain; padding:32px; }

        .product-info { padding:56px 48px; border-left:1px solid var(--light); }
        .product-marque { font-size:11px; font-weight:500; letter-spacing:2px; text-transform:uppercase; color:var(--gray); margin-bottom:8px; }
        .product-nom { font-family:'Bebas Neue',sans-serif; font-size:clamp(40px,5vw,64px); line-height:0.95; letter-spacing:2px; margin-bottom:4px; }
        .product-meta { font-size:14px; color:var(--gray); margin-bottom:24px; }
        .product-note { display:flex; align-items:center; gap:8px; margin-bottom:32px; }
        .stars { font-size:14px; }
        .note-val { font-size:13px; color:var(--gray); }
        .product-prix { font-family:'Bebas Neue',sans-serif; font-size:48px; margin-bottom:8px; }
        .prix-note { font-size:12px; color:var(--gray); margin-bottom:40px; }

        .section-label { font-size:11px; font-weight:500; letter-spacing:2px; text-transform:uppercase; color:var(--gray); margin-bottom:12px; }
        .options { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:32px; }
        .option-btn { padding:8px 18px; border:1px solid var(--light); background:transparent; border-radius:4px; font-family:'DM Sans',sans-serif; font-size:14px; cursor:pointer; transition:all .15s; }
        .option-btn:hover, .option-btn.active { border-color:var(--black); background:var(--black); color:var(--white); }
        .option-btn:disabled { opacity:0.35; cursor:not-allowed; border-color:var(--light); background:transparent; color:var(--black); }

        .product-desc { font-size:15px; line-height:1.7; color:#555; margin-bottom:40px; padding-bottom:40px; border-bottom:1px solid var(--light); }

        .btn-commander { width:100%; padding:18px; background:var(--black); color:var(--white); border:none; border-radius:6px; font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:3px; cursor:pointer; transition:background .2s, transform .1s; margin-bottom:12px; }
        .btn-commander:hover  { background:#222; }
        .btn-commander:active { transform:scale(0.99); }
        .btn-commander:disabled { background:var(--light); color:var(--gray); cursor:not-allowed; }
        .stock-info { text-align:center; font-size:13px; color:var(--gray); min-height:20px; }

        .avis-section { padding:64px 48px; border-top:1px solid var(--light); }
        .avis-header { display:flex; align-items:baseline; gap:16px; margin-bottom:40px; }
        .avis-title { font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:2px; }
        .avis-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:24px; }
        .avis-card { padding:24px; border:1px solid var(--light); border-radius:8px; }
        .avis-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .avis-auteur { font-weight:500; font-size:14px; }
        .avis-date { font-size:12px; color:var(--gray); }
        .avis-stars { font-size:13px; margin-bottom:10px; }
        .avis-commentaire { font-size:14px; line-height:1.6; color:#555; }
        .no-avis { color:var(--gray); font-size:14px; }

        @media(max-width:900px) {
            .product-layout { grid-template-columns:1fr; }
            .product-img { position:relative; height:320px; top:0; }
            .product-info { border-left:none; border-top:1px solid var(--light); padding:32px 24px; }
            nav, .avis-section { padding-left:24px; padding-right:24px; }
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">SOLE</a>
    <a href="index.php" class="nav-back">← Catalogue</a>
</nav>

<div class="product-layout">
    <div class="product-img">
        <?php if (!empty($modele['image'])): ?>
            <img src="<?= htmlspecialchars($modele['image']) ?>" alt="<?= htmlspecialchars($modele['nom']) ?>">
        <?php else: ?>
            <span class="product-img-placeholder"><?= strtoupper(substr($modele['nom'], 0, 2)) ?></span>
        <?php endif; ?>
    </div>

    <div class="product-info">
        <div class="product-marque"><?= htmlspecialchars($modele['marque']) ?></div>
        <h1 class="product-nom"><?= htmlspecialchars($modele['nom']) ?></h1>
        <div class="product-meta"><?= htmlspecialchars($modele['libelle']) ?> · <?= htmlspecialchars($modele['categorie']) ?></div>

        <?php if (!empty($avis)): ?>
        <div class="product-note">
            <span class="stars">
                <?php for ($i = 1; $i <= 5; $i++) echo $i <= round($note_moy) ? '★' : '☆'; ?>
            </span>
            <span class="note-val"><?= $note_moy ?>/5 (<?= count($avis) ?> avis)</span>
        </div>
        <?php endif; ?>

        <div class="product-prix" id="prix-display">
            <?= number_format($modele['prix'], 2, ',', ' ') ?> €
        </div>
        <p class="prix-note">Prix de base · peut varier selon la taille</p>

        <?php if (!empty($tailles)): ?>
        <div class="section-label">Taille</div>
        <div class="options" id="tailles">
            <?php foreach ($tailles as $id_t => $lib_t): ?>
                <button class="option-btn" data-type="taille" data-id="<?= $id_t ?>">
                    <?= htmlspecialchars($lib_t) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($couleurs)): ?>
        <div class="section-label">Couleur</div>
        <div class="options" id="couleurs">
            <?php foreach ($couleurs as $id_c => $lib_c): ?>
                <button class="option-btn" data-type="couleur" data-id="<?= $id_c ?>">
                    <?= htmlspecialchars($lib_c) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <p class="product-desc"><?= nl2br(htmlspecialchars($modele['description'])) ?></p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="commander.php" method="POST">
                <input type="hidden" name="id_exemplaire" id="id_exemplaire_selected" value="">
                <button type="submit" class="btn-commander" id="btn-commander" disabled>
                    Sélectionner une option
                </button>
            </form>
        <?php else: ?>
            <a href="login.php">
                <button class="btn-commander">Se connecter pour commander</button>
            </a>
        <?php endif; ?>

        <p class="stock-info" id="stock-info">
            <?= empty($exemplaires) ? 'Rupture de stock totale' : '' ?>
        </p>
    </div>
</div>

<!-- Avis -->
<section class="avis-section">
    <div class="avis-header">
        <h2 class="avis-title">Avis clients</h2>
        <?php if (!empty($avis)): ?>
            <span class="note-val"><?= count($avis) ?> avis · <?= $note_moy ?>/5</span>
        <?php endif; ?>
    </div>

    <?php if (empty($avis)): ?>
        <p class="no-avis">Aucun avis pour ce modèle.</p>
    <?php else: ?>
        <div class="avis-grid">
            <?php foreach ($avis as $a): ?>
            <div class="avis-card">
                <div class="avis-top">
                    <span class="avis-auteur"><?= htmlspecialchars($a['prenom'] . ' ' . substr($a['nom'], 0, 1) . '.') ?></span>
                    <span class="avis-date"><?= date('d/m/Y', strtotime($a['date_avis'])) ?></span>
                </div>
                <div class="avis-stars">
                    <?php for ($i = 1; $i <= 5; $i++) echo $i <= $a['note'] ? '★' : '☆'; ?>
                </div>
                <p class="avis-commentaire"><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<script>
// Données exemplaires passées en JS
const exemplaires = <?= json_encode($exemplaires) ?>;
const prixBase    = <?= $modele['prix'] ?>;

let selectedTaille  = null;
let selectedCouleur = null;

document.querySelectorAll('.option-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        if (this.disabled) return;
        const type = this.dataset.type;
        document.querySelectorAll(`.option-btn[data-type="${type}"]`).forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        if (type === 'taille')  selectedTaille  = parseInt(this.dataset.id);
        if (type === 'couleur') selectedCouleur = parseInt(this.dataset.id);
        updateUI();
    });
});

function updateUI() {
    // Trouver l'exemplaire correspondant
    const ex = exemplaires.find(e =>
        (!selectedTaille  || e.Id_taille  === selectedTaille) &&
        (!selectedCouleur || e.Id_couleur === selectedCouleur)
    );

    const btn   = document.getElementById('btn-commander');
    const info  = document.getElementById('stock-info');
    const input = document.getElementById('id_exemplaire_selected');
    const prix  = document.getElementById('prix-display');

    // Griser les tailles sans stock pour la couleur sélectionnée
    if (selectedCouleur) {
        document.querySelectorAll('.option-btn[data-type="taille"]').forEach(btn => {
            const has = exemplaires.some(e => e.Id_couleur === selectedCouleur && e.Id_taille === parseInt(btn.dataset.id));
            btn.disabled = !has;
        });
    }
    // Griser les couleurs sans stock pour la taille sélectionnée
    if (selectedTaille) {
        document.querySelectorAll('.option-btn[data-type="couleur"]').forEach(btn => {
            const has = exemplaires.some(e => e.Id_taille === selectedTaille && e.Id_couleur === parseInt(btn.dataset.id));
            btn.disabled = !has;
        });
    }

    if (!btn) return;

    if (ex && ex.quantite > 0) {
        btn.disabled = false;
        btn.textContent = 'Commander';
        if (input) input.value = ex.Id_exemplaire;
        // Afficher le prix final (prix modèle + augmentation taille)
        const prixFinal = parseFloat(ex.prix_final).toFixed(2).replace('.', ',');
        prix.textContent = prixFinal + ' €';
        info.textContent = ex.quantite <= 3 ? `Plus que ${ex.quantite} en stock !` : 'En stock';
    } else if (selectedTaille || selectedCouleur) {
        btn.disabled = true;
        btn.textContent = 'Rupture de stock';
        if (input) input.value = '';
        info.textContent = '';
    } else {
        btn.disabled = true;
        btn.textContent = 'Sélectionner une option';
        if (input) input.value = '';
        info.textContent = '';
    }
}
</script>

</body>
</html>