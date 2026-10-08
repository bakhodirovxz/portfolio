<div class="toolbar">
  <a class="btn" href="<?= e(url('admin/messages')) ?>"><?= icon('arrow-left', 18) ?> Orqaga</a>
  <div class="row-actions">
    <form method="post" action="<?= e(url('admin/messages/' . $m['id'] . '/unread')) ?>"><?= csrf_field() ?><button class="btn" type="submit">O'qilmagan deb belgilash</button></form>
    <form method="post" action="<?= e(url('admin/messages/' . $m['id'] . '/delete')) ?>" data-confirm="So'rov o'chirilsinmi?"><?= csrf_field() ?><button class="btn btn--danger" type="submit"><?= icon('trash', 16) ?> O'chirish</button></form>
  </div>
</div>
<article class="card message">
  <header class="message__head">
    <span class="avatar avatar--lg"><?= e(mb_strtoupper(mb_substr($m['name'], 0, 1))) ?></span>
    <div>
      <h2><?= e($m['name']) ?></h2>
      <p class="muted"><?= e(fmt_date($m['created_at'])) ?>, <?= e(date('H:i', strtotime($m['created_at']))) ?></p>
    </div>
  </header>
  <dl class="message__meta">
    <div><dt>Email</dt><dd><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></dd></div>
    <?php if ($m['phone']): ?><div><dt>Telefon</dt><dd><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $m['phone'])) ?>"><?= e($m['phone']) ?></a></dd></div><?php endif ?>
    <?php if ($m['subject']): ?><div><dt>Mavzu</dt><dd><?= e($m['subject']) ?></dd></div><?php endif ?>
    <?php if ($m['budget']): ?><div><dt>Byudjet</dt><dd><?= e($m['budget']) ?></dd></div><?php endif ?>
  </dl>
  <div class="message__body"><?= nl2br(e($m['message'])) ?></div>
  <footer class="message__foot">
    <a class="btn btn--primary" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . ($m['subject'] ?: 'Sizning so\'rovingiz')) ?>"><?= icon('mail', 18) ?> Email orqali javob berish</a>
    <?php if ($m['phone']): ?><a class="btn" href="https://t.me/<?= e(preg_replace('/[^\d+]/', '', $m['phone'])) ?>" target="_blank" rel="noopener"><?= social_icon('telegram', 16) ?> Telegram</a><?php endif ?>
    <span class="muted small">IP: <?= e($m['ip']) ?></span>
  </footer>
</article>
