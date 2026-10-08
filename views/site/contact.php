<?php $o = fn($k) => e($old[$k] ?? ''); ?>
<section class="page-head wrap">
  <p class="eyebrow">Aloqa</p>
  <h1 class="display"><?= accent(setting('contact_title')) ?></h1>
  <p class="lead"><?= e(setting('contact_text')) ?></p>
</section>
<section class="section section--tight wrap contact">
  <aside class="contact__aside">
    <ul class="contact-list contact-list--lg">
      <?php if (setting('email')): ?><li><?= icon('mail', 20) ?><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif ?>
      <?php if (setting('phone')): ?><li><?= icon('phone', 20) ?><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li><?php endif ?>
      <?php if (setting('location')): ?><li><?= icon('map-pin', 20) ?><?= e(setting('location')) ?></li><?php endif ?>
    </ul>
    <?php if ($links = social_links()): ?>
      <ul class="social-pills">
        <?php foreach ($links as $l): ?>
          <li><a href="<?= e($l['url']) ?>" target="_blank" rel="noopener me"><?= social_icon($l['platform'], 18) ?> <?= e(social_name($l['platform'])) ?></a></li>
        <?php endforeach ?>
      </ul>
    <?php endif ?>
  </aside>

  <div class="contact__form" id="forma">
    <?php if ($sent): ?>
      <div class="success">
        <span class="success__icon"><?= icon('check', 28) ?></span>
        <h2 class="h3">Rahmat! Xabaringiz yuborildi.</h2>
        <p>Tez orada siz bilan bog'lanaman.</p>
        <a class="btn btn--ghost" href="<?= e(url()) ?>">Bosh sahifaga qaytish</a>
      </div>
    <?php else: ?>
    <form method="post" action="<?= e(url('aloqa')) ?>#forma" class="form" novalidate>
      <?php if (!empty($errors['form'])): ?><div class="alert alert--error"><?= e($errors['form']) ?></div><?php endif ?>
      <input type="hidden" name="_t" value="<?= e(form_token()) ?>">
      <div class="hp" aria-hidden="true"><label>Veb-sayt <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <div class="form__row">
        <label class="field<?= isset($errors['name']) ? ' has-error' : '' ?>">
          <span>Ismingiz *</span>
          <input type="text" name="name" value="<?= $o('name') ?>" required autocomplete="name">
          <?php if (isset($errors['name'])): ?><small><?= e($errors['name']) ?></small><?php endif ?>
        </label>
        <label class="field<?= isset($errors['email']) ? ' has-error' : '' ?>">
          <span>Email *</span>
          <input type="email" name="email" value="<?= $o('email') ?>" required autocomplete="email">
          <?php if (isset($errors['email'])): ?><small><?= e($errors['email']) ?></small><?php endif ?>
        </label>
      </div>
      <div class="form__row">
        <label class="field">
          <span>Telefon</span>
          <input type="tel" name="phone" value="<?= $o('phone') ?>" autocomplete="tel" placeholder="+998">
        </label>
        <label class="field">
          <span>Byudjet</span>
          <select name="budget">
            <?php foreach (['', "500$ gacha", '500–1500$', '1500–5000$', "5000$ dan yuqori", 'Kelishiladi'] as $b): ?>
              <option value="<?= e($b) ?>"<?= ($old['budget'] ?? '') === $b ? ' selected' : '' ?>><?= $b === '' ? 'Tanlang' : e($b) ?></option>
            <?php endforeach ?>
          </select>
        </label>
      </div>
      <label class="field">
        <span>Mavzu</span>
        <input type="text" name="subject" value="<?= $o('subject') ?>" placeholder="Masalan: korporativ sayt">
      </label>
      <label class="field<?= isset($errors['message']) ? ' has-error' : '' ?>">
        <span>Xabar *</span>
        <textarea name="message" rows="6" required placeholder="Loyiha haqida qisqacha yozing…"><?= $o('message') ?></textarea>
        <?php if (isset($errors['message'])): ?><small><?= e($errors['message']) ?></small><?php endif ?>
      </label>
      <button class="btn btn--primary btn--lg" type="submit">Yuborish <?= icon('arrow-up-right', 20) ?></button>
    </form>
    <?php endif ?>
  </div>
</section>
