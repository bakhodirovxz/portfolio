<!doctype html>
<html lang="uz">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Kirish — <?= e(setting('site_name')) ?></title>
<link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600&family=IBM+Plex+Mono:wght@400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<script>try{var t=localStorage.getItem('theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
</head>
<body class="auth">
  <form class="auth__card" method="post" action="<?= e(url('admin/login')) ?>">
    <a class="side__logo" href="<?= e(url()) ?>"><span class="mark"></span><span><?= e(setting('site_name')) ?><small>Boshqaruv paneli</small></span></a>
    <h1>Kirish</h1>
    <?php if ($error): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif ?>
    <?= csrf_field() ?>
    <label class="field"><span>Login</span><input name="username" required autofocus autocomplete="username"></label>
    <label class="field"><span>Parol</span><input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn btn--primary btn--block" type="submit">Kirish</button>
    <a class="muted small" href="<?= e(url()) ?>">← Saytga qaytish</a>
  </form>
</body>
</html>
