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
<meta name="theme-color" content="#f6f6f4" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0d0d0d" media="(prefers-color-scheme: dark)">
<link rel="icon" href="<?= e(url('assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="alternate" type="application/rss+xml" title="<?= e($siteName) ?> — Blog" href="<?= e(abs_url('feed.xml')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600&family=IBM+Plex+Mono:wght@400&display=swap" rel="stylesheet">
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
<?php $tz = $GLOBALS['config']['timezone'] ?? 'Asia/Tashkent'; $city = setting('location') ? trim(explode(',', setting('location'))[0]) : 'Toshkent'; ?>

<header class="hdr" id="top">
  <div class="wrap grid hdr__in">
    <a class="hdr__name" href="<?= e(url()) ?>"><?= e(setting('owner_name')) ?></a>
    <span class="hdr__role mono"><?= e(setting('role')) ?></span>
    <nav class="hdr__nav" id="nav" aria-label="Asosiy menyu">
      <a href="<?= e(url('ishlar')) ?>"<?= $nav === 'works' ? ' aria-current="page"' : '' ?>>Ishlar</a>
      <a href="<?= e(url('blog')) ?>"<?= $nav === 'blog' ? ' aria-current="page"' : '' ?>>Blog</a>
      <a href="<?= e(url('#haqida')) ?>">Haqida</a>
      <a href="<?= e(url('aloqa')) ?>"<?= $nav === 'contact' ? ' aria-current="page"' : '' ?>>Aloqa</a>
    </nav>
    <div class="hdr__tools mono">
      <span class="hdr__clock muted" data-clock data-tz="<?= e($tz) ?>" title="Mahalliy vaqt"><?= e($city) ?> <span data-clock-time><?= e(date('H:i')) ?></span></span>
      <button type="button" data-theme-toggle aria-label="Rangli rejimni almashtirish"><span class="only-light">Qorong'i</span><span class="only-dark">Yorug'</span></button>
      <button class="menu-btn" type="button" data-menu aria-controls="nav" aria-expanded="false">Menyu</button>
    </div>
  </div>
</header>

<main id="main">
<?= $content ?>
</main>

<footer class="ftr">
  <div class="wrap">
    <div class="grid ftr__grid">
      <div class="c-3">
        <p class="ftr__name"><?= e(setting('owner_name')) ?></p>
        <p class="muted"><?= e(setting('role')) ?></p>
      </div>
      <div class="c-3">
        <h2>Aloqa</h2>
        <ul>
          <?php if (setting('email')): ?><li><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif ?>
          <?php if (setting('phone')): ?><li><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li><?php endif ?>
          <li><a href="<?= e(url('aloqa')) ?>">So'rov yuborish</a></li>
        </ul>
      </div>
      <div class="c-3">
        <?php if ($links = social_links()): ?>
        <h2>Tarmoqlar</h2>
        <ul>
          <?php foreach ($links as $l): ?>
            <li><a href="<?= e($l['url']) ?>" target="_blank" rel="noopener me"><?= e(social_name($l['platform'])) ?> <span class="mono muted">↗</span></a></li>
          <?php endforeach ?>
        </ul>
        <?php endif ?>
      </div>
      <div class="c-3">
        <h2>Sahifalar</h2>
        <ul>
          <li><a href="<?= e(url('ishlar')) ?>">Ishlar</a></li>
          <li><a href="<?= e(url('blog')) ?>">Blog</a></li>
          <li><a href="<?= e(url('feed.xml')) ?>">RSS</a></li>
        </ul>
      </div>
    </div>
    <div class="ftr__bottom mono">
      <span>© <?= date('Y') ?> <?= e(setting('owner_name')) ?><?= setting('footer_text') ? ' — ' . e(setting('footer_text')) : '' ?></span>
      <a href="#top">Yuqoriga ↑</a>
    </div>
  </div>
</footer>
<img class="peek" alt="" aria-hidden="true">
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</body>
</html>
