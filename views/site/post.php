<?php
$tags = tags_list($post['tags']);
$u = rawurlencode(abs_url('blog/' . $post['slug']));
$tt = rawurlencode($post['title']);
?>
<article class="wrap">
  <header class="grid page-hd">
    <a class="label back c-3" href="<?= e(url('blog')) ?>">← Blog</a>
    <div class="c-9">
      <h1 class="h1"><?= e($post['title']) ?></h1>
      <?php if ($post['excerpt']): ?><p class="lead"><?= e($post['excerpt']) ?></p><?php endif ?>
    </div>
  </header>
  <div class="grid">
    <div class="c-3">
      <dl class="meta sticky">
        <div><dt>Sana</dt><dd><time datetime="<?= e(date('c', strtotime($post['published_at']))) ?>"><?= e(fmt_date($post['published_at'])) ?></time></dd></div>
        <div><dt>O'qish vaqti</dt><dd><?= reading_time($post['content']) ?> daqiqa</dd></div>
        <?php if ($tags): ?><div><dt>Teglar</dt><dd class="tags"><?php foreach ($tags as $t): ?><a href="<?= e(url('blog?teg=' . rawurlencode($t))) ?>">#<?= e($t) ?></a><?php endforeach ?></dd></div><?php endif ?>
        <div><dt>Ulashish</dt><dd class="share">
          <a href="https://t.me/share/url?url=<?= $u ?>&text=<?= $tt ?>" target="_blank" rel="noopener">Telegram</a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $u ?>" target="_blank" rel="noopener">LinkedIn</a>
          <a href="https://x.com/intent/tweet?url=<?= $u ?>&text=<?= $tt ?>" target="_blank" rel="noopener">X</a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $u ?>" target="_blank" rel="noopener">Facebook</a>
        </dd></div>
      </dl>
    </div>
    <div class="c-9">
      <?php if ($post['cover']): ?>
        <figure class="cover"><img src="<?= e(media($post['cover'])) ?>" alt="<?= e($post['title']) ?>" fetchpriority="high"></figure>
      <?php endif ?>
      <div class="prose<?= $post['cover'] ? ' body-row' : '' ?>"><?= markdown($post['content']) ?></div>
    </div>
  </div>

  <?php if ($related): ?>
  <section class="related">
    <div class="grid sec__head" style="margin-bottom: 0"><p class="label c-3">Boshqa yozuvlar</p></div>
    <div class="posts" style="border-top-color: var(--line)">
      <?php foreach ($related as $p) { require __DIR__ . '/_post_row.php'; } ?>
    </div>
  </section>
  <?php endif ?>
</article>
