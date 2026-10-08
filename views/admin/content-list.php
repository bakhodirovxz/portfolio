<?php $base = 'admin/' . $type; $isWork = $type === 'works'; ?>
<div class="toolbar">
  <form class="search" method="get" action="<?= e(url($base)) ?>">
    <?= icon('search', 18) ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Qidirish…">
  </form>
  <a class="btn btn--primary" href="<?= e(url($base . '/new')) ?>"><?= icon('plus', 18) ?> <?= $isWork ? 'Yangi ish' : 'Yangi maqola' ?></a>
</div>
<?php if ($items): ?>
<div class="card table-wrap">
  <table class="table">
    <thead><tr>
      <th></th><th>Sarlavha</th><th><?= $isWork ? 'Kategoriya' : 'Sana' ?></th><th>Holat</th><th class="num">Ko'rish</th><th></th>
    </tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td class="thumb"><?php if ($it['cover']): ?><img src="<?= e(media($it['cover'])) ?>" alt=""><?php else: ?><span><?= icon('image', 16) ?></span><?php endif ?></td>
        <td>
          <a class="strong" href="<?= e(url($base . '/' . $it['id'] . '/edit')) ?>"><?= e($it['title']) ?></a>
          <?php if ($isWork && $it['featured']): ?><span class="pill pill--accent"><?= icon('star', 12) ?> Tanlangan</span><?php endif ?>
          <div class="muted small">/<?= $isWork ? 'ishlar' : 'blog' ?>/<?= e($it['slug']) ?></div>
        </td>
        <td class="muted"><?= $isWork ? e($it['category'] ?: '—') : e(fmt_date($it['published_at'], true)) ?></td>
        <td>
          <?php if ($it['status'] === 'draft'): ?><span class="pill">Qoralama</span>
          <?php elseif (!$isWork && $it['published_at'] > now()): ?><span class="pill pill--warn">Rejalashtirilgan</span>
          <?php else: ?><span class="pill pill--ok">Chop etilgan</span><?php endif ?>
        </td>
        <td class="num muted"><?= (int)$it['views'] ?></td>
        <td class="row-actions">
          <a class="icon-btn" href="<?= e(url(($isWork ? 'ishlar/' : 'blog/') . $it['slug'])) ?>" target="_blank" title="Ko'rish"><?= icon('external', 16) ?></a>
          <a class="icon-btn" href="<?= e(url($base . '/' . $it['id'] . '/edit')) ?>" title="Tahrirlash"><?= icon('edit', 16) ?></a>
          <form method="post" action="<?= e(url($base . '/' . $it['id'] . '/delete')) ?>" data-confirm="«<?= e($it['title']) ?>» o'chirilsinmi? Bu amalni qaytarib bo'lmaydi.">
            <?= csrf_field() ?><button class="icon-btn icon-btn--danger" type="submit" title="O'chirish"><?= icon('trash', 16) ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>
<?php else: ?>
  <div class="card empty">
    <p><?= $q !== '' ? 'Hech narsa topilmadi.' : ($isWork ? "Hali ishlar qo'shilmagan." : "Hali maqolalar yozilmagan.") ?></p>
    <a class="btn btn--primary" href="<?= e(url($base . '/new')) ?>"><?= icon('plus', 18) ?> Qo'shish</a>
  </div>
<?php endif ?>
