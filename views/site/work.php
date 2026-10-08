<?php $tags = tags_list($work['tags']); ?>
<article class="work">
  <header class="page-head wrap">
    <a class="back" href="<?= e(url('ishlar')) ?>"><?= icon('arrow-left', 18) ?> Barcha ishlar</a>
    <?php if ($work['category']): ?><p class="eyebrow"><?= e($work['category']) ?></p><?php endif ?>
    <h1 class="display"><?= e($work['title']) ?></h1>
    <?php if ($work['summary']): ?><p class="lead"><?= e($work['summary']) ?></p><?php endif ?>
    <dl class="facts">
      <?php if ($work['client']): ?><div><dt>Mijoz</dt><dd><?= e($work['client']) ?></dd></div><?php endif ?>
      <?php if ($work['year']): ?><div><dt>Yil</dt><dd><?= e($work['year']) ?></dd></div><?php endif ?>
      <?php if ($work['category']): ?><div><dt>Yo'nalish</dt><dd><?= e($work['category']) ?></dd></div><?php endif ?>
      <?php if ($work['project_url']): ?><div><dt>Havola</dt><dd><a href="<?= e($work['project_url']) ?>" target="_blank" rel="noopener"><?= e(preg_replace('#^https?://(www\.)?#', '', rtrim($work['project_url'], '/'))) ?> <?= icon('arrow-up-right', 14) ?></a></dd></div><?php endif ?>
    </dl>
  </header>

  <?php if ($work['cover']): ?>
    <figure class="work__cover wrap wrap--wide">
      <img src="<?= e(media($work['cover'])) ?>" alt="<?= e($work['title']) ?>" fetchpriority="high">
    </figure>
  <?php endif ?>

  <?php if (trim((string)$work['content']) !== ''): ?>
    <div class="wrap wrap--narrow prose"><?= markdown($work['content']) ?></div>
  <?php endif ?>

  <?php if ($gallery): ?>
    <div class="gallery wrap wrap--wide">
      <?php foreach ($gallery as $img): ?>
        <figure class="reveal"><img src="<?= e(media($img)) ?>" alt="<?= e($work['title']) ?>" loading="lazy" decoding="async"></figure>
      <?php endforeach ?>
    </div>
  <?php endif ?>

  <?php if ($tags): ?>
    <div class="wrap wrap--narrow tags"><?php foreach ($tags as $t): ?><span class="chip"><?= e($t) ?></span><?php endforeach ?></div>
  <?php endif ?>

  <?php if ($next): ?>
  <section class="section wrap">
    <a class="next-work reveal" href="<?= e(url('ishlar/' . $next['slug'])) ?>">
      <span class="eyebrow">Keyingi loyiha</span>
      <span class="next-work__title"><?= e($next['title']) ?> <?= icon('arrow-right', 40) ?></span>
    </a>
  </section>
  <?php endif ?>
</article>
