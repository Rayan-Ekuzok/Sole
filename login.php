<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: article.php'); exit; }

$erreurs = $_SESSION['erreurs_login'] ?? [];
unset($_SESSION['erreurs_login']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — SOLE</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root { --black:#0a0a0a; --white:#f5f5f3; --gray:#888; --light:#e8e8e6; }
        body { font-family:'DM Sans',sans-serif; background:var(--white); color:var(--black); min-height:100vh; display:grid; grid-template-columns:1fr 1fr; }

        .panel-left { background:var(--black); display:flex; flex-direction:column; justify-content:space-between; padding:48px; overflow:hidden; position:relative; }
        .panel-left::before { content:''; position:absolute; top:-100px; left:-100px; width:500px; height:500px; border-radius:50%; background:rgba(255,255,255,0.03); }
        .panel-left .logo { font-family:'Bebas Neue',sans-serif; font-size:48px; color:var(--white); letter-spacing:4px; }
        .panel-left .tagline { font-size:13px; color:rgba(255,255,255,0.4); letter-spacing:2px; text-transform:uppercase; margin-top:8px; }
        .panel-left .big-text { font-family:'Bebas Neue',sans-serif; font-size:clamp(72px,10vw,120px); color:rgba(255,255,255,0.06); line-height:0.9; user-select:none; }
        .panel-left .bottom-text { font-size:13px; color:rgba(255,255,255,0.25); }

        .panel-right { display:flex; align-items:center; justify-content:center; padding:48px; }
        .form-wrapper { width:100%; max-width:380px; }
        .form-title { font-family:'Bebas Neue',sans-serif; font-size:42px; letter-spacing:2px; margin-bottom:8px; }
        .form-subtitle { font-size:14px; color:var(--gray); margin-bottom:40px; }
        .form-subtitle a { color:var(--black); font-weight:500; text-decoration:none; border-bottom:1px solid var(--black); }

        .erreurs { background:#fff0f0; border-left:3px solid #c0392b; padding:12px 16px; border-radius:4px; margin-bottom:24px; font-size:13px; color:#c0392b; }
        .erreurs ul { padding-left:16px; }

        .field { margin-bottom:20px; }
        .field label { display:block; font-size:11px; font-weight:500; letter-spacing:1.5px; text-transform:uppercase; color:var(--gray); margin-bottom:8px; }
        .field input { width:100%; padding:14px 16px; background:transparent; border:1px solid var(--light); border-radius:6px; font-family:'DM Sans',sans-serif; font-size:15px; color:var(--black); transition:border-color .2s; outline:none; }
        .field input:focus { border-color:var(--black); }

        .btn-submit { width:100%; padding:16px; background:var(--black); color:var(--white); border:none; border-radius:6px; font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:2px; cursor:pointer; margin-top:8px; transition:background .2s; }
        .btn-submit:hover { background:#222; }

        .divider { display:flex; align-items:center; gap:12px; margin:28px 0; color:var(--light); font-size:12px; }
        .divider::before, .divider::after { content:''; flex:1; height:1px; background:var(--light); }

        .link-catalogue { display:block; text-align:center; font-size:14px; color:var(--gray); text-decoration:none; transition:color .2s; }
        .link-catalogue:hover { color:var(--black); }

        @media(max-width:768px) { body { grid-template-columns:1fr; } .panel-left { display:none; } .panel-right { padding:32px 24px; } }
    </style>
</head>
<body>

<div class="panel-left">
    <div>
        <div class="logo">SOLE</div>
        <div class="tagline">Votre boutique de chaussures</div>
    </div>
    <div class="big-text">STEP<br>INTO<br>STYLE</div>
    <div class="bottom-text">© <?= date('Y') ?> SOLE.</div>
</div>

<div class="panel-right">
    <div class="form-wrapper">
        <h1 class="form-title">Connexion</h1>
        <p class="form-subtitle">Pas encore de compte ? <a href="index.php">Voir le catalogue</a></p>

        <?php if (!empty($erreurs)): ?>
        <div class="erreurs"><ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form action="doLogin.php" method="POST">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="exemple@mail.com" required>
            </div>
            <div class="field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>

        <div class="divider">ou</div>
        <a href="index.php" class="link-catalogue">Continuer sans connexion →</a>
    </div>
</div>

</body>
</html>