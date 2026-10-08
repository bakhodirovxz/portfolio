<?php
$stats = array_map(fn($l) => array_pad(explode('|', $l, 2), 2, ''), lines(setting('stats')));
$skills = array_values(array_filter(array_map('trim', explode(',', setting('skills')))));
$services = lines(setting('services'));
$n = 0;
$num = function () use (&$n) { return sprintf('(%02d)', ++$n); };
$tz = $GLOBALS['config']['timezone'] ?? 'Asia/Tashkent';
?>
<section class="hero wrap">
  <h1 class="hero__name"><?= e(setting('owner_name')) ?><span class="sr-only"> — <?= e(setting('role')) ?></span></h1>
  <div class="grid hero__grid">
    <p class="label c-3"><?= e(setting('role')) ?></p>
    <div class="hero__text c-6">
      <p class="statement"><?= accent(setting('hero_title')) ?></p>
      <?php if (setting('hero_text')): ?><p class="lead"><?= e(setting('hero_text')) ?></p><?php endif ?>
      <p class="links">
        <a class="btn" href="<?= e(url('ishlar')) ?>">Ishlarni ko'rish <span class="ar">→</span></a>
        <a class="tlink" href="<?= e(url('aloqa')) ?>">Loyiha haqida yozish</a>
      </p>
    </div>
    <dl class="facts c-3">
      <?php if (setting('availability')): ?><div><dt>Holat</dt><dd class="status"><?= e(setting('availability')) ?></dd></div><?php endif ?>
      <?php if (setting('location')): ?><div><dt>Joylashuv</dt><dd><?= e(setting('location')) ?></dd></div><?php endif ?>
      <div><dt>Mahalliy vaqt</dt><dd data-clock data-tz="<?= e($tz) ?>"><span data-clock-time><?= e(date('H:i')) ?></span> <span class="muted">(UTC<?= e(date('P')) ?>)</span></dd></div>
      <?php if (setting('email')): ?><div><dt>Email</dt><dd><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></dd></div><?php endif ?>
    </dl>
  </div>
</section>

<section class="sec wrap" id="ishlar">
  <div class="grid sec__head">
    <p class="label c-3"><?= $num() ?> Tanlangan ishlar</p>
    <div class="sec__more">
      <h2 class="h2">Loyihalar</h2>
      <?php if ($worksTotal > 0): ?><a class="tlink" href="<?= e(url('ishlar')) ?>">Barcha ishlar <span class="mono">(<?= (int)$worksTotal ?>)</span></a><?php endif ?>
    </div>
  </div>
  <?php if ($works): ?>
    <?php require __DIR__ . '/_works_grid.php'; ?>
  <?php else: ?>
    <p class="empty">Ishlar tez orada qo'shiladi.</p>
  <?php endif ?>
</section>

<section class="sec wrap" id="haqida">
  <div class="grid sec__head">
    <p class="label c-3"><?= $num() ?> <?= e(setting('about_title')) ?></p>
  </div>
  <div class="grid">
    <div class="c-3">
      <?php if (setting('avatar')): ?><img class="about__photo" src="<?= e(media(setting('avatar'))) ?>" alt="<?= e(setting('owner_name')) ?>" loading="lazy"><?php endif ?>
    </div>
    <div class="c-7">
      <div class="about__text"><?= markdown(setting('about_text')) ?></div>
      <?php if (setting('resume_url')): ?>
        <p class="about__cv"><a class="tlink" href="<?= e(media(setting('resume_url'))) ?>" target="_blank" rel="noopener">Rezyume (CV) <span class="mono">↗</span></a></p>
      <?php endif ?>
    </div>
  </div>

  <?php if ($stats): ?>
  <div class="grid sub">
    <p class="label c-3">Raqamlarda</p>
    <dl class="stats c-9">
      <?php foreach ($stats as [$v, $l]): ?><div><dt><?= e($v) ?></dt><dd><?= e($l) ?></dd></div><?php endforeach ?>
    </dl>
  </div>
  <?php endif ?>

  <?php if ($services): ?>
  <div class="grid sub">
    <p class="label c-3">Xizmatlar</p>
    <ol class="rows c-9">
      <?php foreach ($services as $k => $s): ?><li><span class="mono"><?= sprintf('%02d', $k + 1) ?></span><span><?= e($s) ?></span></li><?php endforeach ?>
    </ol>
  </div>
  <?php endif ?>

  <?php if ($skills): ?>
  <div class="grid sub">
    <p class="label c-3">Asboblar</p>
    <p class="skills c-9"><?= e(implode(', ', $skills)) ?>.</p>
  </div>
  <?php endif ?>
</section>

<?php if ($posts): ?>
<section class="sec wrap">
  <div class="grid sec__head">
    <p class="label c-3"><?= $num() ?> Blog</p>
    <div class="sec__more">
      <h2 class="h2">So'nggi yozuvlar</h2>
      <a class="tlink" href="<?= e(url('blog')) ?>">Barcha maqolalar</a>
    </div>
  </div>
  <div class="posts">
    <?php foreach ($posts as $p) { require __DIR__ . '/_post_row.php'; } ?>
  </div>
</section>
<?php endif ?>

<section class="sec wrap">
  <div class="grid sec__head">
    <p class="label c-3"><?= $num() ?> Aloqa</p>
    <div class="c-9 cta__body">
      <p class="cta__title"><?= accent(setting('contact_title')) ?></p>
      <p class="lead"><?= e(setting('contact_text')) ?></p>
      <p class="links">
        <a class="btn" href="<?= e(url('aloqa')) ?>">So'rov yuborish <span class="ar">→</span></a>
        <?php if (setting('email')): ?><a class="tlink" href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a><?php endif ?>
      </p>
    </div>
  </div>
</section>
