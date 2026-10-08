<section class="wrap">
  <header class="grid page-hd">
    <p class="label c-3">Blog</p>
    <div class="c-9">
      <h1 class="h1">Yozuvlar</h1>
      <p class="lead">Dizayn, dasturlash va ish jarayoni haqidagi qaydlar.</p>
    </div>
  </header>
  <?php if ($tag !== ''): ?>
    <div class="toolbar"><span class="filters"><a class="is-active">#<?= e($tag) ?></a></span><a class="mono tlink" href="<?= e(url('blog')) ?>">Filtrni tozalash</a></div>
  <?php endif ?>
  <?php if ($posts): ?>
    <div class="posts">
      <?php foreach ($posts as $p) { require __DIR__ . '/_post_row.php'; } ?>
    </div>
    <?php if ($pg['pages'] > 1): ?>
      <nav class="pager" aria-label="Sahifalar">
        <?php for ($i = 1; $i <= $pg['pages']; $i++): ?>
          <a href="<?= e(url('blog?sahifa=' . $i . ($tag !== '' ? '&teg=' . rawurlencode($tag) : ''))) ?>"<?= $i === $pg['page'] ? ' aria-current="page"' : '' ?>><?= sprintf('%02d', $i) ?></a>
        <?php endfor ?>
      </nav>
    <?php endif ?>
  <?php else: ?>
    <p class="empty">Hozircha maqolalar yo'q.</p>
  <?php endif ?>
</section>
