<?php $tags = tags_list($work['tags']); ?>
<article class="wrap">
  <header class="grid page-hd">
    <a class="label back c-3" href="<?= e(url('ishlar')) ?>">← Barcha ishlar</a>
    <div class="c-9">
      <h1 class="h1"><?= e($work['title']) ?></h1>
      <?php if ($work['summary']): ?><p class="lead"><?= e($work['summary']) ?></p><?php endif ?>
    </div>
  </header>

  <div class="grid">
    <div class="c-3">
      <dl class="meta sticky">
        <?php if ($work['client']): ?><div><dt>Mijoz</dt><dd><?= e($work['client']) ?></dd></div><?php endif ?>
        <?php if ($work['category']): ?><div><dt>Yo'nalish</dt><dd><?= e($work['category']) ?></dd></div><?php endif ?>
        <?php if ($work['year']): ?><div><dt>Yil</dt><dd><?= e($work['year']) ?></dd></div><?php endif ?>
        <?php if ($work['project_url']): ?><div><dt>Havola</dt><dd><a href="<?= e($work['project_url']) ?>" target="_blank" rel="noopener"><?= e(preg_replace('#^https?://(www\.)?#', '', rtrim($work['project_url'], '/'))) ?> ↗</a></dd></div><?php endif ?>
        <?php if ($tags): ?><div><dt>Teglar</dt><dd><?= e(implode(', ', $tags)) ?></dd></div><?php endif ?>
      </dl>
    </div>
    <div class="c-9">
      <?php if ($work['cover']): ?>
        <figure class="cover"><img src="<?= e(media($work['cover'])) ?>" alt="<?= e($work['title']) ?>" fetchpriority="high"></figure>
      <?php endif ?>
      <?php if (trim((string)$work['content']) !== ''): ?>
        <div class="prose body-row"><?= markdown($work['content']) ?></div>
      <?php endif ?>
    </div>
  </div>

  <?php if ($gallery): ?>
    <div class="gallery">
      <?php foreach ($gallery as $img): ?>
        <figure><img src="<?= e(media($img)) ?>" alt="<?= e($work['title']) ?>" loading="lazy" decoding="async"></figure>
      <?php endforeach ?>
    </div>
  <?php endif ?>

  <?php if ($next): ?>
    <a class="next" href="<?= e(url('ishlar/' . $next['slug'])) ?>" style="margin-top: var(--sec)">
      <span class="label">Keyingi loyiha</span>
      <span class="next__t"><span><?= e($next['title']) ?></span><span class="ar">→</span></span>
    </a>
  <?php endif ?>
</article>
