<section class="page-head wrap">
  <p class="eyebrow">Blog</p>
  <h1 class="display">Maqolalar va <em>yangiliklar</em></h1>
  <p class="lead">Dizayn, dasturlash va ish jarayonidagi tajribalarim haqida yozaman.</p>
  <?php if ($tag !== ''): ?>
    <p class="chips"><span class="chip is-active">#<?= e($tag) ?></span> <a class="chip" href="<?= e(url('blog')) ?>">Filtrni tozalash ✕</a></p>
  <?php endif ?>
</section>
<section class="section section--tight wrap">
  <?php if ($posts): ?>
    <?php $first = $pg['page'] === 1 && $tag === '' ? array_shift($posts) : null; ?>
    <?php if ($first): ?>
      <a class="post-feature reveal" href="<?= e(url('blog/' . $first['slug'])) ?>">
        <?php if ($first['cover']): ?><div class="post-feature__media"><img src="<?= e(media($first['cover'])) ?>" alt="<?= e($first['title']) ?>"></div><?php endif ?>
        <div class="post-feature__body">
          <p class="eyebrow"><?= e(fmt_date($first['published_at'])) ?> · <?= reading_time($first['content']) ?> daq</p>
          <h2 class="h2"><?= e($first['title']) ?></h2>
          <p class="lead"><?= e($first['excerpt'] ?: excerpt($first['content'], 200)) ?></p>
          <span class="link-arrow">O'qish <?= icon('arrow-right', 18) ?></span>
        </div>
      </a>
    <?php endif ?>
    <div class="post-list">
      <?php foreach ($posts as $p) { require __DIR__ . '/_post_row.php'; } ?>
    </div>
    <?php if ($pg['pages'] > 1): ?>
      <nav class="pager" aria-label="Sahifalar">
        <?php for ($i = 1; $i <= $pg['pages']; $i++): ?>
          <a href="<?= e(url('blog?sahifa=' . $i . ($tag !== '' ? '&teg=' . rawurlencode($tag) : ''))) ?>"<?= $i === $pg['page'] ? ' aria-current="page"' : '' ?>><?= $i ?></a>
        <?php endfor ?>
      </nav>
    <?php endif ?>
  <?php else: ?>
    <div class="empty">Hozircha maqolalar yo'q.</div>
  <?php endif ?>
</section>
