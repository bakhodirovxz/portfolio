<?php $o = fn($k) => e($old[$k] ?? ''); ?>
<section class="wrap">
  <header class="grid page-hd">
    <p class="label c-3">Aloqa</p>
    <div class="c-9">
      <h1 class="cta__title"><?= accent(setting('contact_title')) ?></h1>
      <p class="lead" style="margin-top: 24px"><?= e(setting('contact_text')) ?></p>
    </div>
  </header>

  <div class="grid" id="forma" style="row-gap: 48px">
    <aside class="c-3">
      <ul class="plain-list">
        <?php if (setting('email')): ?><li><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif ?>
        <?php if (setting('phone')): ?><li><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li><?php endif ?>
        <?php if (setting('location')): ?><li><?= e(setting('location')) ?></li><?php endif ?>
        <?php foreach (social_links() as $l): ?>
          <li><a href="<?= e($l['url']) ?>" target="_blank" rel="noopener me"><?= e(social_name($l['platform'])) ?></a><span class="mono muted">↗</span></li>
        <?php endforeach ?>
      </ul>
    </aside>

    <div class="c-7">
      <?php if ($sent): ?>
        <div class="done">
          <p class="h2">Rahmat, xabaringiz yetib keldi.</p>
          <p class="lead">Tez orada javob yozaman.</p>
          <p><a class="tlink" href="<?= e(url()) ?>">← Bosh sahifaga</a></p>
        </div>
      <?php else: ?>
      <form method="post" action="<?= e(url('aloqa')) ?>#forma" class="form" novalidate>
        <?php if (!empty($errors['form'])): ?><p class="alert"><?= e($errors['form']) ?></p><?php endif ?>
        <input type="hidden" name="_t" value="<?= e(form_token()) ?>">
        <div class="hp" aria-hidden="true"><label>Veb-sayt <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <div class="form__row">
          <label class="field<?= isset($errors['name']) ? ' has-error' : '' ?>">
            <span>Ism *</span>
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
                <option value="<?= e($b) ?>"<?= ($old['budget'] ?? '') === $b ? ' selected' : '' ?>><?= $b === '' ? 'Tanlanmagan' : e($b) ?></option>
              <?php endforeach ?>
            </select>
          </label>
        </div>
        <label class="field">
          <span>Mavzu</span>
          <input type="text" name="subject" value="<?= $o('subject') ?>" placeholder="Masalan: kompaniya sayti">
        </label>
        <label class="field<?= isset($errors['message']) ? ' has-error' : '' ?>">
          <span>Xabar *</span>
          <textarea name="message" rows="5" required placeholder="Loyiha, muddat va kutilgan natija haqida qisqacha"><?= $o('message') ?></textarea>
          <?php if (isset($errors['message'])): ?><small><?= e($errors['message']) ?></small><?php endif ?>
        </label>
        <button class="btn" type="submit">Yuborish <span class="ar">→</span></button>
      </form>
      <?php endif ?>
    </div>
  </div>
</section>
