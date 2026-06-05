<?php
// ============================================================
//  menu.php — Barre de navigation commune à toutes les pages
//  À inclure avec : require_once 'menu.php';
//  (session_start() doit avoir été appelé AVANT l'include)
// ============================================================

if (isset($_SESSION['user_id'])) {
    $now = time();

    if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > 600) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['timeout_message'] = "Vous avez été déconnecté automatiquement après 10 minutes d'inactivité.";

            header('Location: login.php');
    } else {
        $_SESSION['last_activity'] = $now;
    }
} elseif (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

$nb_panier = 0;
if (isset($_SESSION['user_id'])) {
    include_once'bdd.php';
    $nb_panier = getNbArticlesPanier($_SESSION['user_id']);
}

$page_active = basename($_SERVER['PHP_SELF']);

$message_timeout = '';
if (isset($_SESSION['timeout_message'])) {
    $message_timeout = $_SESSION['timeout_message'];
    unset($_SESSION['timeout_message']);
}

$message_bienvenue = '';
if (isset($_SESSION['succes_inscription'])) {
    $message_bienvenue = $_SESSION['succes_inscription'];
    unset($_SESSION['succes_inscription']);
}
?>

<nav>
    <a href="index.php" class="logo-nav">SOLE</a>

    <ul class="liens-nav">
        <?php if (isset($_SESSION['user_id'])): ?>

            <li>
                <a href="index.php" class="<?= $page_active === 'index.php' ? 'lien-actif' : '' ?>">
                    Catalogue
                </a>
            </li>
            <li>
                <a href="mesCommandes.php" class="<?= $page_active === 'mesCommandes.php' ? 'lien-actif' : '' ?>">
                    Mes commandes
                </a>
            </li>
            <li class="badge-panier">
                <a href="panier.php" class="<?= $page_active === 'panier.php' ? 'lien-actif' : '' ?>">
                    🛒 Panier
                    <?php if ($nb_panier > 0): ?>
                        <span class="badge"><?= $nb_panier ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-user">
                <span class="nom-utilisateur">
                    <?php echo $_SESSION['user_prenom'] . ' ' . substr($_SESSION['user_nom'], 0, 1) . '.' ?>
                </span>
            </li>
            <li>
                <a href="logout.php" class="btn-deconnexion">Déconnexion</a>
            </li>

        <?php else: ?>

            <li>
                <a href="index.php" class="<?= $page_active === 'index.php' ? 'lien-actif' : '' ?>">
                    Catalogue
                </a>
            </li>
            <li>
                <a href="login.php" class="btn-nav">Se connecter</a>
            </li>

        <?php endif; ?>
    </ul>

    <button class="burger-nav" id="burger-nav" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<?php if ($message_timeout): ?>
<div class="toast-timeout" id="toast-timeout">
    <span class="toast-icone">⏱</span>
    <span><?= htmlspecialchars($message_timeout) ?></span>
    <button onclick="document.getElementById('toast-timeout').remove()" class="toast-fermer">✕</button>
</div>
<?php endif; ?>

<?php if ($message_bienvenue): ?>
<div class="toast-bienvenue" id="toast-bienvenue">
    <span class="toast-icone">🎉</span>
    <span><?= htmlspecialchars($message_bienvenue) ?></span>
    <button onclick="document.getElementById('toast-bienvenue').remove()" class="toast-fermer">✕</button>
</div>
<?php endif; ?>

<script>
    const burger = document.getElementById('burger-nav');
    const liensNav = document.querySelector('.liens-nav');
    if (burger && liensNav) {
        burger.addEventListener('click', () => liensNav.classList.toggle('ouvert'));
    }

    const toast = document.getElementById('toast-timeout');
    if (toast) {
        setTimeout(() => toast.remove(), 5400);
    }

    const toastBienvenue = document.getElementById('toast-bienvenue');
    if (toastBienvenue) {
        setTimeout(() => toastBienvenue.remove(), 5400);
    }

    <?php if (isset($_SESSION['user_id'])): ?>
    (function() {
        const TIMEOUT_MS  = <?= 600 * 1000 ?>;
        const WARN_BEFORE = 60 * 1000;
        let minuterieAvertissement, minuterieDeconnexion;

        function reinitialiserMinuteries() {
            clearTimeout(minuterieAvertissement);
            clearTimeout(minuterieDeconnexion);
            minuterieAvertissement = setTimeout(afficherAvertissement, TIMEOUT_MS - WARN_BEFORE);
            minuterieDeconnexion   = setTimeout(deconnexionForcee,     TIMEOUT_MS);
        }

        function afficherAvertissement() {
            if (document.getElementById('avert-session')) return;
            const div = document.createElement('div');
            div.id = 'avert-session';
            div.style.cssText = `
                position:fixed;bottom:32px;left:50%;transform:translateX(-50%);
                background:#B8860B;color:#fff;padding:14px 24px;border-radius:8px;
                font-size:14px;z-index:9999;display:flex;align-items:center;gap:12px;
                box-shadow:0 8px 32px rgba(0,0,0,0.2);max-width:480px;width:90%;
            `;
            div.innerHTML = `
                <span style="font-size:18px;flex-shrink:0">⚠️</span>
                <span>Votre session expire dans <strong id="compte-rebours">60</strong> secondes.</span>
                <button onclick="this.parentNode.remove()" style="background:none;border:none;color:rgba(255,255,255,0.6);cursor:pointer;font-size:16px;margin-left:auto;padding:0 4px">✕</button>
            `;
            document.body.appendChild(div);

            let restant = 60;
            const intervalle = setInterval(() => {
                restant--;
                const el = document.getElementById('compte-rebours');
                if (el) el.textContent = restant;
                if (restant <= 0) clearInterval(intervalle);
            }, 1000);
        }

        function deconnexionForcee() {
            window.location.href = 'logout.php?timeout=1';
        }

        ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, reinitialiserMinuteries, { passive: true });
        });

        reinitialiserMinuteries();
    })();
    <?php endif; ?>
</script>