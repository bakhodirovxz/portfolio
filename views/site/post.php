<?php $tags = tags_list($post['tags']); ?>
<article class="post">
  <header class="page-head wrap wrap--narrow">
    <a class="back" href="<?= e(url('blog')) ?>"><?= icon('arrow-left', 18) ?> Blog</a>
    <p class="eyebrow">
      <time datetime="<?= e(date('c', strtotime($post['published_at']))) ?>"><?= e(fmt_date($post['published_at'])) ?></time>
      · <?= reading_time($post['content']) ?> daqiqa o'qish
    </p>
    <h1 class="display display--post"><?= e($post['title']) ?></h1>
    <?php if ($post['excerpt']): ?><p class="lead"><?= e($post['excerpt']) ?></p><?php endif ?>
  </header>
  <?php if ($post['cover']): ?>
    <figure class="post__cover wrap"><img src="<?= e(media($post['cover'])) ?>" alt="<?= e($post['title']) ?>" fetchpriority="high"></figure>
  <?php endif ?>
  <div class="wrap wrap--narrow prose"><?= markdown($post['content']) ?></div>
  <footer class="wrap wrap--narrow post__footer">
    <?php if ($tags): ?>
      <div class="tags"><?php foreach ($tags as $t): ?><a class="chip" href="<?= e(url('blog?teg=' . rawurlencode($t))) ?>">#<?= e($t) ?></a><?php endforeach ?></div>
    <?php endif ?>
    <div class="share">
      <span>Ulashish:</span>
      <?php $u = rawurlencode(abs_url('blog/' . $post['slug'])); $tt = rawurlencode($post['title']); ?>
      <a href="https://t.me/share/url?url=<?= $u ?>&text=<?= $tt ?>" target="_blank" rel="noopener" aria-label="Telegram"><?= social_icon('telegram', 18) ?></a>
      <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $u ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><?= social_icon('linkedin', 18) ?></a>
      <a href="https://x.com/intent/tweet?url=<?= $u ?>&text=<?= $tt ?>" target="_blank" rel="noopener" aria-label="X"><?= social_icon('x', 18) ?></a>
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $u ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= social_icon('facebook', 18) ?></a>
    </div>
  </footer>
  <?php if ($related): ?>
  <section class="section wrap wrap--narrow">
    <p class="eyebrow">Boshqa maqolalar</p>
    <div class="related">
      <?php foreach ($related as $r): ?>
        <a href="<?= e(url('blog/' . $r['slug'])) ?>" class="related__item">
          <time><?= e(fmt_date($r['published_at'])) ?></time>
          <strong><?= e($r['title']) ?></strong>
        </a>
      <?php endforeach ?>
    </div>
  </section>
  <?php endif ?>
</article>
