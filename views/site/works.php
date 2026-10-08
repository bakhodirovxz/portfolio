<section class="page-head wrap">
  <p class="eyebrow">Portfolio</p>
  <h1 class="display">Ishlar va <em>loyihalar</em></h1>
  <p class="lead">Turli sohalardagi mijozlar uchun bajarilgan tanlangan ishlar.</p>
  <?php if ($categories): ?>
  <nav class="chips" aria-label="Kategoriyalar">
    <a href="<?= e(url('ishlar')) ?>" class="chip<?= $active === '' ? ' is-active' : '' ?>">Barchasi</a>
    <?php foreach ($categories as $c): ?>
      <a href="<?= e(url('ishlar?kategoriya=' . rawurlencode($c))) ?>" class="chip<?= $active === $c ? ' is-active' : '' ?>"><?= e($c) ?></a>
    <?php endforeach ?>
  </nav>
  <?php endif ?>
</section>
<section class="section section--tight wrap">
  <?php if ($works): ?>
    <div class="works-grid">
      <?php foreach ($works as $w) { require __DIR__ . '/_work_card.php'; } ?>
    </div>
  <?php else: ?>
    <div class="empty">Hozircha bu bo'limda ishlar yo'q.</div>
  <?php endif ?>
</section>
