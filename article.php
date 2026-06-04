<?php
session_start();
require_once 'bdd.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$modele = getModeleById($id);
if (!$modele) { header('Location: index.php'); exit; }

$exemplaires = getExemplairesByModele($id);
$avis        = getAvisByModele($id);

$tailles  = [];
$couleurs = [];
foreach ($exemplaires as $e) {
    $tailles[$e['Id_taille']]   = $e['taille'];
    $couleurs[$e['Id_couleur']] = $e['couleur'];
}

$note_moy = 0;
if (!empty($avis)) {
    $note_moy = round(array_sum(array_column($avis, 'note')) / count($avis), 1);
}

// Droits de commentaire
$peut_commenter  = false;
$deja_commente   = false;
$erreurs_avis    = $_SESSION['erreurs_avis']  ?? [];
$succes_avis     = $_SESSION['succes_avis']   ?? '';
unset($_SESSION['erreurs_avis'], $_SESSION['succes_avis']);

if (isset($_SESSION['user_id'])) {
    $deja_commente  = utilisateurADejaCommente($_SESSION['user_id'], $id);
    $peut_commenter = !$deja_commente && utilisateurACommandeModele($_SESSION['user_id'], $id);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $modele['nom'] ?> : SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php require_once 'menu.php'; ?>

<div class="mise-en-page-produit">
    <div class="produit-image">
        <?php if (!empty($modele['image'])): ?>
            <img src="<?= $modele['image'] ?>" alt="<?= $modele['nom'] ?>">
        <?php else: ?>
            <span class="produit-image-placeholder"><?= strtoupper(substr($modele['nom'], 0, 2)) ?></span>
        <?php endif; ?>
    </div>

    <div class="produit-infos">
        <div class="produit-marque"><?= htmlspecialchars($modele['marque']) ?></div>
        <h1 class="produit-nom"><?= htmlspecialchars($modele['nom']) ?></h1>
        <div class="produit-meta"><?= htmlspecialchars($modele['libelle']) ?> · <?= htmlspecialchars($modele['categorie']) ?></div>

        <?php if (!empty($avis)): ?>
        <div class="produit-note">
            <span class="etoiles">
                <?php for ($i = 1; $i <= 5; $i++) echo $i <= round($note_moy) ? '★' : '☆'; ?>
            </span>
            <span class="valeur-note"><?= $note_moy ?>/5 (<?= count($avis) ?> avis)</span>
        </div>
        <?php endif; ?>

        <div class="produit-prix" id="affichage-prix">
            <?= number_format($modele['prix'], 2, ',', ' ') ?> €
        </div>
        <p class="note-prix">Prix de base · peut varier selon la taille</p>

        <?php if (!empty($tailles)): ?>
        <div class="label-section">Taille</div>
        <div class="options" id="tailles">
            <?php foreach ($tailles as $id_t => $lib_t): ?>
                <button class="btn-option" data-type="taille" data-id="<?= $id_t ?>">
                    <?= htmlspecialchars($lib_t) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($couleurs)): ?>
        <div class="label-section">Couleur</div>
        <div class="options" id="couleurs">
            <?php foreach ($couleurs as $id_c => $lib_c): ?>
                <button class="btn-option" data-type="couleur" data-id="<?= $id_c ?>">
                    <?= htmlspecialchars($lib_c) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <p class="produit-description"><?= nl2br(htmlspecialchars($modele['description'])) ?></p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="ajouterPanier.php" method="POST">
                <input type="hidden" name="id_exemplaire" id="exemplaire-selectionne" value="">
                <div class="label-section" id="label-quantite" style="display:none">Quantité</div>
                <div class="conteneur-quantite" id="conteneur-quantite" style="display:none">
                    <button type="button" class="btn-quantite" id="btn-moins">−</button>
                    <input type="number" name="quantite" id="quantite" value="1" min="1" max="99" class="champ-quantite" readonly>
                    <button type="button" class="btn-quantite" id="btn-plus">+</button>
                </div>
                <button type="submit" class="btn-commander" id="btn-commander" disabled>
                    Sélectionner une option
                </button>
            </form>
        <?php else: ?>
            <a href="login.php">
                <button class="btn-commander">Se connecter pour commander</button>
            </a>
        <?php endif; ?>

        <p class="info-stock" id="info-stock">
            <?= empty($exemplaires) ? 'Rupture de stock totale' : '' ?>
        </p>
    </div>
</div>

<section class="section-avis">
    <div class="entete-avis">
        <h2 class="titre-avis">Avis clients</h2>
        <?php if (!empty($avis)): ?>
            <span class="valeur-note"><?= count($avis) ?> avis · <?= $note_moy ?>/5</span>
        <?php endif; ?>
    </div>

    <?php if (!empty($succes_avis)): ?>
        <div class="bloc-succes-avis"><?= htmlspecialchars($succes_avis) ?></div>
    <?php endif; ?>

    <?php if ($peut_commenter): ?>
    <div class="formulaire-avis">
        <h3 class="titre-formulaire-avis">Laisser un avis</h3>

        <?php if (!empty($erreurs_avis)): ?>
        <div class="bloc-erreurs" style="margin-bottom:20px">
            <ul><?php foreach ($erreurs_avis as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <form action="soumettreAvis.php" method="POST">
            <input type="hidden" name="id_modele" value="<?= $id ?>">

            <div class="champ-note">
                <div class="label-section">Note</div>
                <div class="etoiles-interactives" id="etoiles-input">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <button type="button" class="etoile-btn" data-val="<?= $i ?>">★</button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="note" id="note-valeur" value="0">
                <span class="note-label" id="note-label">Cliquez pour noter</span>
            </div>

            <div class="champ">
                <div class="label-section">Commentaire</div>
                <textarea name="commentaire" class="champ-textarea" placeholder="Partagez votre expérience avec ce produit..." maxlength="255" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn-soumettre-avis">Publier mon avis</button>
        </form>
    </div>
    <?php elseif ($deja_commente): ?>
        <div class="info-avis-existant">✓ Vous avez déjà publié un avis sur ce produit.</div>
    <?php elseif (isset($_SESSION['user_id'])): ?>
        <div class="info-avis-existant">Vous devez avoir commandé ce produit pour laisser un avis.</div>
    <?php endif; ?>

    <?php if (empty($avis)): ?>
        <p class="aucun-avis">Aucun avis pour ce modèle.</p>
    <?php else: ?>
        <div class="grille-avis">
            <?php foreach ($avis as $a): ?>
            <div class="carte-avis">
                <div class="entete-carte-avis">
                    <span class="auteur-avis"><?= htmlspecialchars($a['prenom'] . ' ' . substr($a['nom'], 0, 1) . '.') ?></span>
                    <span class="date-avis"><?= date('d/m/Y', strtotime($a['date_avis'])) ?></span>
                </div>
                <div class="etoiles-avis">
                    <?php for ($i = 1; $i <= 5; $i++) echo $i <= $a['note'] ? '★' : '☆'; ?>
                </div>
                <p class="commentaire-avis"><?= nl2br(htmlspecialchars($a['commentaire'])) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<script>
const exemplaires = <?= json_encode($exemplaires) ?>;
const prixBase    = <?= $modele['prix'] ?>;

let tailleSelectionnee  = null;
let couleurSelectionnee = null;

document.querySelectorAll('.btn-option').forEach(btn => {
    btn.addEventListener('click', function () {
        if (this.disabled) return;
        const type = this.dataset.type;
        document.querySelectorAll(`.btn-option[data-type="${type}"]`).forEach(b => b.classList.remove('actif'));
        this.classList.add('actif');
        if (type === 'taille')  tailleSelectionnee  = parseInt(this.dataset.id);
        if (type === 'couleur') couleurSelectionnee = parseInt(this.dataset.id);
        mettreAJourInterface();
    });
});

function mettreAJourInterface() {
    const ex = exemplaires.find(e =>
        (!tailleSelectionnee  || e.Id_taille  === tailleSelectionnee) &&
        (!couleurSelectionnee || e.Id_couleur === couleurSelectionnee)
    );

    const btn   = document.getElementById('btn-commander');
    const info  = document.getElementById('info-stock');
    const input = document.getElementById('exemplaire-selectionne');
    const prix  = document.getElementById('affichage-prix');

    if (couleurSelectionnee) {
        document.querySelectorAll('.btn-option[data-type="taille"]').forEach(btn => {
            const disponible = exemplaires.some(e => e.Id_couleur === couleurSelectionnee && e.Id_taille === parseInt(btn.dataset.id));
            btn.disabled = !disponible;
        });
    }
    if (tailleSelectionnee) {
        document.querySelectorAll('.btn-option[data-type="couleur"]').forEach(btn => {
            const disponible = exemplaires.some(e => e.Id_taille === tailleSelectionnee && e.Id_couleur === parseInt(btn.dataset.id));
            btn.disabled = !disponible;
        });
    }

    if (!btn) return;

    if (ex && ex.quantite > 0) {
        btn.disabled = false;
        btn.textContent = 'Ajouter au panier';
        if (input) input.value = ex.Id_exemplaire;
        const prixFinal = parseFloat(ex.prix_final).toFixed(2).replace('.', ',');
        prix.textContent = prixFinal + ' €';
        info.textContent = ex.quantite <= 3 ? `Plus que ${ex.quantite} en stock !` : 'En stock';
    } else if (tailleSelectionnee || couleurSelectionnee) {
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

<script>
// ── Étoiles interactives ──────────────────────────────────────
const etoilesBtns = document.querySelectorAll('.etoile-btn');
const noteInput   = document.getElementById('note-valeur');
const noteLabel   = document.getElementById('note-label');
const labels      = ['', 'Mauvais', 'Passable', 'Bien', 'Très bien', 'Excellent'];

let noteSelectionnee = 0;

function colorierEtoiles(n) {
    etoilesBtns.forEach(b => {
        b.classList.toggle('active', parseInt(b.dataset.val) <= n);
    });
}

etoilesBtns.forEach(btn => {
    btn.addEventListener('mouseenter', () => colorierEtoiles(parseInt(btn.dataset.val)));
    btn.addEventListener('mouseleave', () => colorierEtoiles(noteSelectionnee));
    btn.addEventListener('click', () => {
        noteSelectionnee = parseInt(btn.dataset.val);
        noteInput.value  = noteSelectionnee;
        noteLabel.textContent = labels[noteSelectionnee];
        colorierEtoiles(noteSelectionnee);
    });
});
</script>

</body>
</html>
</html>