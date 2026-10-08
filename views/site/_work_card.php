<?php /** @var array $w */ ?>
<article class="work-card reveal">
  <a href="<?= e(url('ishlar/' . $w['slug'])) ?>" class="work-card__link">
    <div class="work-card__media">
      <?php if ($w['cover']): ?>
        <img src="<?= e(media($w['cover'])) ?>" alt="<?= e($w['title']) ?>" loading="lazy" decoding="async">
      <?php else: ?>
        <div class="placeholder" aria-hidden="true"><span><?= e(mb_substr($w['title'], 0, 1)) ?></span></div>
      <?php endif ?>
      <span class="work-card__arrow"><?= icon('arrow-up-right', 22) ?></span>
    </div>
    <div class="work-card__body">
      <h3 class="work-card__title"><?= e($w['title']) ?></h3>
      <p class="work-card__meta">
        <?php if ($w['category']): ?><span><?= e($w['category']) ?></span><?php endif ?>
        <?php if ($w['year']): ?><span><?= e($w['year']) ?></span><?php endif ?>
      </p>
    </div>
  </a>
</article>
