<div class="toolbar">
  <div class="tabs tabs--pill">
    <a href="<?= e(url('admin/messages')) ?>" class="<?= $filter === '' ? 'is-active' : '' ?>">Barchasi (<?= $filter === '' ? $pg['total'] : '…' ?>)</a>
    <a href="<?= e(url('admin/messages?f=unread')) ?>" class="<?= $filter === 'unread' ? 'is-active' : '' ?>">O'qilmagan (<?= $unread ?>)</a>
  </div>
</div>
<?php if ($items): ?>
<div class="card">
  <ul class="msg-list msg-list--full">
    <?php foreach ($items as $m): ?>
      <li class="<?= $m['is_read'] ? '' : 'is-unread' ?>">
        <a href="<?= e(url('admin/messages/' . $m['id'])) ?>">
          <span class="avatar avatar--sm"><?= e(mb_strtoupper(mb_substr($m['name'], 0, 1))) ?></span>
          <span class="msg-list__body">
            <strong><?= e($m['name']) ?> <span class="muted">· <?= e($m['email']) ?></span></strong>
            <small><?php if ($m['subject']): ?><b><?= e($m['subject']) ?></b> — <?php endif ?><?= e(mb_strimwidth((string)$m['message'], 0, 120, '…')) ?></small>
          </span>
          <?php if ($m['budget']): ?><span class="pill"><?= e($m['budget']) ?></span><?php endif ?>
          <time><?= e(fmt_date($m['created_at'], true)) ?><br><span class="muted"><?= e(date('H:i', strtotime($m['created_at']))) ?></span></time>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</div>
<?php if ($pg['pages'] > 1): ?>
  <nav class="pager"><?php for ($i = 1; $i <= $pg['pages']; $i++): ?><a href="<?= e(url('admin/messages?p=' . $i . ($filter ? '&f=' . $filter : ''))) ?>"<?= $i === $pg['page'] ? ' aria-current="page"' : '' ?>><?= $i ?></a><?php endfor ?></nav>
<?php endif ?>
<?php else: ?>
  <div class="card empty"><?= icon('inbox', 32) ?><p>So'rovlar yo'q. Saytdagi aloqa formasi orqali yuborilgan xabarlar shu yerda ko'rinadi.</p></div>
<?php endif ?>
