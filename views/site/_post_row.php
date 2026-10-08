<?php /** @var array $p */ ?>
<article class="post-row reveal">
  <a href="<?= e(url('blog/' . $p['slug'])) ?>">
    <time datetime="<?= e(date('Y-m-d', strtotime($p['published_at']))) ?>"><?= e(fmt_date($p['published_at'])) ?></time>
    <div class="post-row__main">
      <h3><?= e($p['title']) ?></h3>
      <p><?= e($p['excerpt'] ?: excerpt($p['content'], 150)) ?></p>
    </div>
    <span class="post-row__meta"><?= reading_time($p['content']) ?> daq o'qish</span>
    <span class="post-row__arrow"><?= icon('arrow-right', 22) ?></span>
  </a>
</article>
