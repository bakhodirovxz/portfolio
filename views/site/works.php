<?php
$q = fn(array $p) => url('ishlar' . (($p = array_filter($p)) ? '?' . http_build_query($p) : ''));
$view = $mode === 'grid' ? 'tor' : '';
?>
<section class="wrap">
  <header class="grid page-hd">
    <p class="label c-3">Ishlar</p>
    <div class="c-9">
      <h1 class="h1">Loyihalar arxivi</h1>
      <p class="lead">Turli mijozlar uchun bajarilgan veb-saytlar, interfeyslar va brending ishlari.</p>
    </div>
  </header>

  <div class="toolbar">
    <nav class="filters" aria-label="Kategoriyalar">
      <a href="<?= e($q(['korinish' => $view])) ?>" class="<?= $active === '' ? 'is-active' : '' ?>">Barchasi<sup><?= (int)$total ?></sup></a>
      <?php foreach ($categories as $c => $cnt): ?>
        <a href="<?= e($q(['kategoriya' => $c, 'korinish' => $view])) ?>" class="<?= $active === $c ? 'is-active' : '' ?>"><?= e($c) ?><sup><?= $cnt ?></sup></a>
      <?php endforeach ?>
    </nav>
    <nav class="views mono" aria-label="Ko'rinish">
      <a href="<?= e($q(['kategoriya' => $active])) ?>" class="<?= $mode === 'list' ? 'is-active' : '' ?>">Ro'yxat</a>
      <a href="<?= e($q(['kategoriya' => $active, 'korinish' => 'tor'])) ?>" class="<?= $mode === 'grid' ? 'is-active' : '' ?>">To'r</a>
    </nav>
  </div>

  <?php if (!$works): ?>
    <p class="empty">Bu bo'limda hozircha ishlar yo'q.</p>
  <?php elseif ($mode === 'grid'): ?>
    <div style="margin-top: 32px"><?php require __DIR__ . '/_works_grid.php'; ?></div>
  <?php else: ?>
    <div class="index" data-peek-list>
      <div class="index__head label" aria-hidden="true">
        <span>#</span><span>Loyiha</span><span>Yo'nalish</span><span>Mijoz</span><span style="text-align:right">Yil</span><span></span>
      </div>
      <?php foreach ($works as $i => $w): ?>
        <a class="index__row" href="<?= e(url('ishlar/' . $w['slug'])) ?>"<?= $w['cover'] ? ' data-peek="' . e(media($w['cover'])) . '"' : '' ?>>
          <span class="index__num muted"><?= sprintf('%02d', $i + 1) ?></span>
          <span class="index__t"><?= e($w['title']) ?></span>
          <span class="index__c"><?= e($w['category'] ?: '—') ?></span>
          <span class="index__cl muted"><?= e($w['client'] ?: '—') ?></span>
          <span class="index__y muted"><?= e($w['year']) ?></span>
          <span class="ar">↗</span>
        </a>
      <?php endforeach ?>
    </div>
  <?php endif ?>
</section>
