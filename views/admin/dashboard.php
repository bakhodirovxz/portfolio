<?php $done = count(array_filter($checklist, fn($c) => $c[1])); ?>
<div class="grid-stats">
  <a class="stat" href="<?= e(url('admin/works')) ?>"><span><?= icon('briefcase', 18) ?> Ishlar</span><strong><?= $stats['works'] ?></strong></a>
  <a class="stat" href="<?= e(url('admin/posts')) ?>"><span><?= icon('file-text', 18) ?> Maqolalar</span><strong><?= $stats['posts'] ?></strong></a>
  <a class="stat" href="<?= e(url('admin/messages')) ?>"><span><?= icon('inbox', 18) ?> So'rovlar</span><strong><?= $stats['messages'] ?></strong><?php if ($unread): ?><em><?= $unread ?> yangi</em><?php endif ?></a>
  <div class="stat"><span><?= icon('eye', 18) ?> Ko'rishlar</span><strong><?= number_format($stats['views'], 0, '.', ' ') ?></strong></div>
</div>

<div class="quick">
  <a class="btn btn--primary" href="<?= e(url('admin/works/new')) ?>"><?= icon('plus', 18) ?> Yangi ish</a>
  <a class="btn" href="<?= e(url('admin/posts/new')) ?>"><?= icon('plus', 18) ?> Yangi maqola</a>
</div>

<div class="cols">
  <section class="card">
    <header class="card__head"><h2>So'nggi so'rovlar</h2><a href="<?= e(url('admin/messages')) ?>">Barchasi →</a></header>
    <?php if ($messages): ?>
      <ul class="msg-list">
        <?php foreach ($messages as $m): ?>
          <li class="<?= $m['is_read'] ? '' : 'is-unread' ?>">
            <a href="<?= e(url('admin/messages/' . $m['id'])) ?>">
              <span class="avatar avatar--sm"><?= e(mb_strtoupper(mb_substr($m['name'], 0, 1))) ?></span>
              <span class="msg-list__body"><strong><?= e($m['name']) ?></strong><small><?= e(mb_strimwidth($m['subject'] ?: $m['message'], 0, 70, '…')) ?></small></span>
              <time><?= e(fmt_date($m['created_at'], true)) ?></time>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
    <?php else: ?>
      <p class="muted pad">Hozircha so'rovlar yo'q.</p>
    <?php endif ?>
  </section>

  <section class="card">
    <header class="card__head"><h2>Sozlash ro'yxati</h2><span class="muted"><?= $done ?>/<?= count($checklist) ?></span></header>
    <div class="progress"><span style="width: <?= round($done / count($checklist) * 100) ?>%"></span></div>
    <ul class="checklist">
      <?php foreach ($checklist as [$label, $ok, $link]): ?>
        <li class="<?= $ok ? 'is-done' : '' ?>"><a href="<?= e(url('admin/' . $link)) ?>"><span class="tick"><?= $ok ? icon('check', 14) : '' ?></span><?= e($label) ?></a></li>
      <?php endforeach ?>
    </ul>
  </section>
</div>

<div class="cols">
  <section class="card">
    <header class="card__head"><h2>Ko'p ko'rilgan ishlar</h2></header>
    <?php if ($topWorks): ?><ol class="rank"><?php foreach ($topWorks as $w): ?><li><a href="<?= e(url('admin/works/' . $w['id'] . '/edit')) ?>"><?= e($w['title']) ?></a><span><?= (int)$w['views'] ?></span></li><?php endforeach ?></ol>
    <?php else: ?><p class="muted pad">Ma'lumot yo'q.</p><?php endif ?>
  </section>
  <section class="card">
    <header class="card__head"><h2>Ko'p o'qilgan maqolalar</h2></header>
    <?php if ($topPosts): ?><ol class="rank"><?php foreach ($topPosts as $p): ?><li><a href="<?= e(url('admin/posts/' . $p['id'] . '/edit')) ?>"><?= e($p['title']) ?></a><span><?= (int)$p['views'] ?></span></li><?php endforeach ?></ol>
    <?php else: ?><p class="muted pad">Ma'lumot yo'q.</p><?php endif ?>
  </section>
</div>

<section class="card card--note">
  <h2><?= icon('search', 18) ?> SEO va analitika</h2>
  <p>Sitemap manzili — Google Search Console'ga shu havolani qo'shing:</p>
  <p><code class="copy" data-copy><?= e(abs_url('sitemap.xml')) ?></code></p>
  <p class="muted small">Batafsil statistika (tashriflar, manbalar, qidiruv so'zlari) uchun Google Analytics va Search Console'dan foydalaning. Bu yerdagi ko'rishlar — sayt ichki hisoblagichi.</p>
</section>
