<?php
$stats = array_map(fn($l) => array_pad(explode('|', $l, 2), 2, ''), lines(setting('stats')));
$skills = array_values(array_filter(array_map('trim', explode(',', setting('skills')))));
$services = lines(setting('services'));
?>
<section class="hero wrap">
  <div class="hero__top">
    <?php if (setting('availability')): ?>
      <p class="badge"><span class="dot"></span><?= e(setting('availability')) ?></p>
    <?php endif ?>
    <p class="hero__who"><?= e(setting('owner_name')) ?> · <?= e(setting('role')) ?></p>
  </div>
  <h1 class="display hero__title"><?= accent(setting('hero_title')) ?></h1>
  <div class="hero__bottom">
    <p class="lead"><?= e(setting('hero_text')) ?></p>
    <div class="actions">
      <a class="btn btn--primary" href="<?= e(url('ishlar')) ?>">Ishlarni ko'rish <?= icon('arrow-right', 18) ?></a>
      <a class="btn btn--ghost" href="<?= e(url('aloqa')) ?>">Loyiha muhokamasi</a>
    </div>
  </div>
</section>

<?php if ($skills): ?>
<div class="marquee" aria-label="Ko'nikmalar">
  <div class="marquee__track">
    <?php for ($r = 0; $r < 2; $r++): ?>
      <ul <?= $r ? 'aria-hidden="true"' : '' ?>>
        <?php foreach ($skills as $s): ?><li><?= e($s) ?></li><?php endforeach ?>
      </ul>
    <?php endfor ?>
  </div>
</div>
<?php endif ?>

<section class="section wrap" id="ishlar">
  <div class="section__head">
    <div>
      <p class="eyebrow">Tanlangan ishlar</p>
      <h2 class="h2">So'nggi <em>loyihalar</em></h2>
    </div>
    <?php if ($worksTotal > 0): ?>
      <a class="link-arrow" href="<?= e(url('ishlar')) ?>">Barchasi (<?= (int)$worksTotal ?>) <?= icon('arrow-right', 18) ?></a>
    <?php endif ?>
  </div>
  <?php if ($works): ?>
    <div class="works-grid">
      <?php foreach ($works as $w) { require __DIR__ . '/_work_card.php'; } ?>
    </div>
  <?php else: ?>
    <div class="empty">Tez orada bu yerda ishlar paydo bo'ladi.</div>
  <?php endif ?>
</section>

<section class="section wrap about" id="haqida">
  <div class="about__aside">
    <?php if (setting('avatar')): ?>
      <img class="about__photo reveal" src="<?= e(media(setting('avatar'))) ?>" alt="<?= e(setting('owner_name')) ?>" loading="lazy">
    <?php endif ?>
    <ul class="contact-list">
      <?php if (setting('location')): ?><li><?= icon('map-pin', 18) ?><?= e(setting('location')) ?></li><?php endif ?>
      <?php if (setting('email')): ?><li><?= icon('mail', 18) ?><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif ?>
      <?php if (setting('phone')): ?><li><?= icon('phone', 18) ?><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li><?php endif ?>
    </ul>
  </div>
  <div class="about__main">
    <p class="eyebrow"><?= e(setting('about_title')) ?></p>
    <div class="prose prose--lg reveal"><?= markdown(setting('about_text')) ?></div>
    <?php if (setting('resume_url')): ?>
      <a class="btn btn--ghost" href="<?= e(media(setting('resume_url'))) ?>" target="_blank" rel="noopener"><?= icon('download', 18) ?> Rezyume (CV)</a>
    <?php endif ?>

    <?php if ($stats): ?>
    <dl class="stats">
      <?php foreach ($stats as [$num, $label]): ?>
        <div class="reveal"><dt><?= e($num) ?></dt><dd><?= e($label) ?></dd></div>
      <?php endforeach ?>
    </dl>
    <?php endif ?>

    <?php if ($services): ?>
    <div class="services">
      <p class="eyebrow">Xizmatlar</p>
      <ol>
        <?php foreach ($services as $i => $s): ?>
          <li class="reveal"><span class="services__n"><?= sprintf('%02d', $i + 1) ?></span><span><?= e($s) ?></span></li>
        <?php endforeach ?>
      </ol>
    </div>
    <?php endif ?>
  </div>
</section>

<?php if ($posts): ?>
<section class="section wrap">
  <div class="section__head">
    <div>
      <p class="eyebrow">Blog</p>
      <h2 class="h2">Yozganlarim va <em>fikrlarim</em></h2>
    </div>
    <a class="link-arrow" href="<?= e(url('blog')) ?>">Barcha maqolalar <?= icon('arrow-right', 18) ?></a>
  </div>
  <div class="post-list">
    <?php foreach ($posts as $p) { require __DIR__ . '/_post_row.php'; } ?>
  </div>
</section>
<?php endif ?>

<section class="section wrap">
  <div class="cta reveal">
    <p class="eyebrow">Aloqa</p>
    <h2 class="display display--sm"><?= accent(setting('contact_title')) ?></h2>
    <p class="lead"><?= e(setting('contact_text')) ?></p>
    <a class="btn btn--primary btn--lg" href="<?= e(url('aloqa')) ?>">So'rov yuborish <?= icon('arrow-up-right', 20) ?></a>
  </div>
</section>
