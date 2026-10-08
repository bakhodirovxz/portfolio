<?php
$u = current_user();
$path = current_path();
$is = fn($p) => $path === $p || ($p !== 'admin' && str_starts_with($path, $p)) ? ' aria-current="page"' : '';
?><!doctype html>
<html lang="uz">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="csrf" content="<?= e(csrf_token()) ?>">
<title><?= e($title ?? 'Admin') ?> — <?= e(setting('site_name')) ?> admin</title>
<link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<script>try{var t=localStorage.getItem('theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
</head>
<body>
<div class="shell">
  <aside class="side" id="side">
    <a class="side__logo" href="<?= e(url('admin')) ?>">
      <span class="mark"><?= e(mb_strtoupper(mb_substr(setting('site_name'), 0, 1))) ?></span>
      <span><?= e(setting('site_name')) ?><small>Boshqaruv</small></span>
    </a>
    <nav class="side__nav">
      <a href="<?= e(url('admin')) ?>"<?= $is('admin') ?>><?= icon('dashboard', 19) ?> Panel</a>
      <a href="<?= e(url('admin/works')) ?>"<?= $is('admin/works') ?>><?= icon('briefcase', 19) ?> Ishlar</a>
      <a href="<?= e(url('admin/posts')) ?>"<?= $is('admin/posts') ?>><?= icon('file-text', 19) ?> Maqolalar</a>
      <a href="<?= e(url('admin/messages')) ?>"<?= $is('admin/messages') ?>><?= icon('inbox', 19) ?> So'rovlar <?php if ($unread): ?><b class="count"><?= $unread ?></b><?php endif ?></a>
      <a href="<?= e(url('admin/settings')) ?>"<?= $is('admin/settings') ?>><?= icon('settings', 19) ?> Sozlamalar</a>
      <a href="<?= e(url('admin/account')) ?>"<?= $is('admin/account') ?>><?= icon('user', 19) ?> Hisob</a>
    </nav>
    <div class="side__foot">
      <a href="<?= e(url()) ?>" target="_blank"><?= icon('external', 18) ?> Saytni ochish</a>
      <form method="post" action="<?= e(url('admin/logout')) ?>"><?= csrf_field() ?><button type="submit"><?= icon('logout', 18) ?> Chiqish</button></form>
    </div>
  </aside>
  <div class="main">
    <header class="top">
      <button class="icon-btn menu" type="button" data-side aria-label="Menyu"><?= icon('menu', 20) ?></button>
      <h1><?= e($title ?? '') ?></h1>
      <div class="top__right">
        <button class="icon-btn" type="button" data-theme-toggle aria-label="Rejim"><?= icon('moon', 18) ?></button>
        <span class="avatar" title="<?= e($u['username']) ?>"><?= e(mb_strtoupper(mb_substr($u['name'] ?: $u['username'], 0, 1))) ?></span>
      </div>
    </header>
    <div class="content">
      <?php foreach (flashes() as $f): ?>
        <div class="alert alert--<?= e($f['type']) ?>" data-autohide><?= e($f['msg']) ?></div>
      <?php endforeach ?>
      <?= $content ?>
    </div>
  </div>
</div>
<script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</body>
</html>
