<?php
/** @var array $meta @var string $content */
$meta = $meta ?? [];
$siteName = setting('site_name');
$title = $meta['title'] ?? $siteName;
if (empty($meta['raw_title']) && $title !== $siteName) {
    $title .= ' — ' . $siteName;
}
$desc = mb_substr((string)($meta['description'] ?? setting('meta_description')), 0, 300);
$canonical = $meta['canonical'] ?? abs_url(current_path());
$image = !empty($meta['image']) ? $meta['image'] : setting('og_image', setting('avatar'));
$imageUrl = $image ? abs_url($image) : '';
$type = $meta['type'] ?? 'website';
$nav = $nav ?? '';
$ga = preg_replace('/[^A-Za-z0-9-]/', '', setting('ga_id'));
$jsonld = $meta['jsonld'] ?? [];
?><!doctype html>
<html lang="uz">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<?php if (setting('meta_keywords')): ?><meta name="keywords" content="<?= e(setting('meta_keywords')) ?>">
<?php endif ?>
<meta name="robots" content="<?= e($meta['robots'] ?? 'index, follow, max-image-preview:large') ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="author" content="<?= e(setting('owner_name')) ?>">
<?php if (setting('gsc_verification')): ?><meta name="google-site-verification" content="<?= e(setting('gsc_verification')) ?>">
<?php endif ?>
<?php if (setting('yandex_verification')): ?><meta name="yandex-verification" content="<?= e(setting('yandex_verification')) ?>">
<?php endif ?>
<meta property="og:type" content="<?= e($type) ?>">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:locale" content="uz_UZ">
<?php if ($imageUrl): ?><meta property="og:image" content="<?= e($imageUrl) ?>">
<meta name="twitter:image" content="<?= e($imageUrl) ?>">
<?php endif ?>
<?php if (!empty($meta['published'])): ?><meta property="article:published_time" content="<?= e(date('c', strtotime($meta['published']))) ?>">
<meta property="article:modified_time" content="<?= e(date('c', strtotime($meta['modified'] ?? $meta['published']))) ?>">
<?php endif ?>
<meta name="twitter:card" content="<?= $imageUrl ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title" content="<?= e($title) ?>">
<meta name="twitter:description" content="<?= e($desc) ?>">
<meta name="theme-color" content="#f3f2ee" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0f0f0e" media="(prefers-color-scheme: dark)">
<link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="alternate" type="application/rss+xml" title="<?= e($siteName) ?> — Blog" href="<?= e(abs_url('feed.xml')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
<script>document.documentElement.classList.add('js');try{var t=localStorage.getItem('theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>
<?php if ($jsonld): ?>
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@graph' => array_values($jsonld)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endif ?>
<?php if ($ga): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= e($ga) ?>');</script>
<?php endif ?>
<?= setting('head_code') /* Admin tomonidan kiritilgan ishonchli kod */ ?>
</head>
<body class="page-<?= e($nav ?: 'default') ?>">
<?= setting('body_code') ?>
<a class="skip" href="#main">Asosiy kontentga o'tish</a>

<header class="header" id="top">
  <div class="wrap header__inner">
    <a class="logo" href="<?= e(url()) ?>" aria-label="<?= e($siteName) ?> — bosh sahifa">
      <span class="logo__mark"><?= e(mb_strtoupper(mb_substr($siteName, 0, 1))) ?></span>
      <span class="logo__text"><?= e($siteName) ?></span>
    </a>
    <nav class="nav" id="nav" aria-label="Asosiy menyu">
      <a href="<?= e(url('ishlar')) ?>"<?= $nav === 'works' ? ' aria-current="page"' : '' ?>>Ishlar</a>
      <a href="<?= e(url('blog')) ?>"<?= $nav === 'blog' ? ' aria-current="page"' : '' ?>>Blog</a>
      <a href="<?= e(url('#haqida')) ?>">Men haqimda</a>
      <a href="<?= e(url('aloqa')) ?>" class="nav__cta<?= $nav === 'contact' ? ' is-active' : '' ?>">Bog'lanish <?= icon('arrow-up-right', 16) ?></a>
    </nav>
    <div class="header__tools">
      <button class="icon-btn" type="button" data-theme-toggle aria-label="Rangli rejimni almashtirish">
        <span class="only-light"><?= icon('moon', 18) ?></span><span class="only-dark"><?= icon('sun', 18) ?></span>
      </button>
      <button class="icon-btn menu-btn" type="button" data-menu aria-controls="nav" aria-expanded="false" aria-label="Menyu">
        <?= icon('menu', 20) ?>
      </button>
    </div>
  </div>
</header>

<main id="main">
<?= $content ?>
</main>

<footer class="footer">
  <div class="wrap">
    <div class="footer__top">
      <div>
        <p class="eyebrow">Loyihangiz bormi?</p>
        <?php if (setting('email')): ?>
          <a class="footer__mail" href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?> <?= icon('arrow-up-right', 28) ?></a>
        <?php else: ?>
          <a class="footer__mail" href="<?= e(url('aloqa')) ?>">Menga yozing <?= icon('arrow-up-right', 28) ?></a>
        <?php endif ?>
      </div>
      <?php if ($links = social_links()): ?>
      <ul class="socials" aria-label="Ijtimoiy tarmoqlar">
        <?php foreach ($links as $l): ?>
          <li><a href="<?= e($l['url']) ?>" target="_blank" rel="noopener me" aria-label="<?= e(social_name($l['platform'])) ?>" title="<?= e(social_name($l['platform'])) ?>"><?= social_icon($l['platform']) ?></a></li>
        <?php endforeach ?>
      </ul>
      <?php endif ?>
    </div>
    <div class="footer__bottom">
      <span>© <?= date('Y') ?> <?= e(setting('owner_name')) ?>. <?= e(setting('footer_text', 'Barcha huquqlar himoyalangan.')) ?></span>
      <nav aria-label="Pastki menyu">
        <a href="<?= e(url('ishlar')) ?>">Ishlar</a>
        <a href="<?= e(url('blog')) ?>">Blog</a>
        <a href="<?= e(url('aloqa')) ?>">Aloqa</a>
        <a href="<?= e(url('feed.xml')) ?>">RSS</a>
        <a href="#top">Yuqoriga ↑</a>
      </nav>
    </div>
  </div>
</footer>
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</body>
</html>
