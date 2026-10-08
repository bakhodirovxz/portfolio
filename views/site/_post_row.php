<?php /** @var array $p */ ?>
<article class="post-r">
  <a href="<?= e(url('blog/' . $p['slug'])) ?>">
    <time class="mono muted" datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(date('d.m.Y', strtotime($p['published_at']))) ?></time>
    <div class="post-r__main">
      <h3 class="post-r__t"><?= e($p['title']) ?></h3>
      <p class="post-r__x"><?= e($p['excerpt'] ?: excerpt($p['content'], 160)) ?></p>
    </div>
    <span class="post-r__rt mono muted"><?= reading_time($p['content']) ?> daq</span>
  </a>
</article>
