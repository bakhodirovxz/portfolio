<?php /** @var array $w @var int $i @var int $span */ ?>
<article class="wk c-<?= (int)$span ?>">
  <a href="<?= e(url('ishlar/' . $w['slug'])) ?>">
    <div class="wk__img">
      <?php if ($w['cover']): ?>
        <img src="<?= e(media($w['cover'])) ?>" alt="<?= e($w['title']) ?>" loading="<?= $i < 2 ? 'eager' : 'lazy' ?>" decoding="async">
      <?php else: ?>
        <span class="wk__empty">Rasm qo'shilmagan</span>
      <?php endif ?>
    </div>
    <div class="wk__cap">
      <span class="mono muted"><?= sprintf('%02d', $i + 1) ?></span>
      <h3 class="wk__title"><?= e($w['title']) ?></h3>
      <span class="wk__meta mono muted"><?= e(implode(', ', array_filter([$w['category'], $w['year']]))) ?></span>
    </div>
  </a>
</article>
