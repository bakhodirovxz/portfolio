<?php
$links = json_decode($s['social_links'] ?? '[]', true) ?: [];
$platforms = social_platforms();
$f = fn($k) => e($s[$k] ?? '');
?>
<form method="post" enctype="multipart/form-data" action="<?= e(url('admin/settings')) ?>" class="settings" data-tabs>
  <?= csrf_field() ?>
  <input type="hidden" name="_tab" value="umumiy" data-tab-input>
  <nav class="tabs tabs--pill" role="tablist">
    <a href="#umumiy" data-tab-link>Umumiy</a>
    <a href="#bosh" data-tab-link>Bosh sahifa</a>
    <a href="#aloqa" data-tab-link>Aloqa</a>
    <a href="#tarmoqlar" data-tab-link>Ijtimoiy tarmoqlar</a>
    <a href="#seo" data-tab-link>SEO va analitika</a>
    <a href="#kod" data-tab-link>Qo'shimcha kod</a>
  </nav>

  <section class="card pad" id="umumiy" data-tab-panel>
    <div class="field-row">
      <label class="field"><span>Sayt nomi</span><input name="site_name" value="<?= $f('site_name') ?>"></label>
      <label class="field"><span>To'liq ismingiz</span><input name="owner_name" value="<?= $f('owner_name') ?>"></label>
    </div>
    <label class="field"><span>Kasbingiz / rolingiz</span><input name="role" value="<?= $f('role') ?>" placeholder="Dizayner & dasturchi"></label>
    <div class="field-row">
      <div class="field">
        <span>Profil rasmi (avatar)</span>
        <label class="cover-drop cover-drop--sq<?= $s['avatar'] ? ' has-image' : '' ?>">
          <img src="<?= e(media($s['avatar'])) ?>" alt="" data-cover-preview<?= $s['avatar'] ? '' : ' hidden' ?>>
          <span><?= icon('image', 20) ?> Tanlash</span>
          <input type="file" name="avatar" accept="image/*" data-cover-input>
        </label>
        <?php if ($s['avatar']): ?><label class="check"><input type="checkbox" name="remove_avatar" value="1"> <span>O'chirish</span></label><?php endif ?>
      </div>
      <div class="field">
        <span>Ijtimoiy tarmoq rasmi (OG image, 1200×630)</span>
        <label class="cover-drop<?= $s['og_image'] ? ' has-image' : '' ?>">
          <img src="<?= e(media($s['og_image'])) ?>" alt="" data-cover-preview<?= $s['og_image'] ? '' : ' hidden' ?>>
          <span><?= icon('image', 20) ?> Tanlash</span>
          <input type="file" name="og_image" accept="image/*" data-cover-input>
        </label>
        <?php if ($s['og_image']): ?><label class="check"><input type="checkbox" name="remove_og_image" value="1"> <span>O'chirish</span></label><?php endif ?>
      </div>
    </div>
    <label class="field"><span>Rezyume (CV) havolasi</span><input name="resume_url" value="<?= $f('resume_url') ?>" placeholder="https://drive.google.com/…"></label>
    <div class="field-row">
      <label class="field"><span>Footer matni</span><input name="footer_text" value="<?= $f('footer_text') ?>" placeholder="Barcha huquqlar himoyalangan."></label>
      <label class="field"><span>Blogda sahifadagi maqolalar soni</span><input type="number" min="3" max="50" name="posts_per_page" value="<?= $f('posts_per_page') ?>"></label>
    </div>
  </section>

  <section class="card pad" id="bosh" data-tab-panel>
    <p class="hint">Sarlavhalarda so'zni <code>*yulduzcha*</code> ichiga olsangiz, u kursiv-aksent shriftda chiqadi.</p>
    <label class="field"><span>Hero sarlavha</span><input class="input-xl" name="hero_title" value="<?= $f('hero_title') ?>"></label>
    <label class="field"><span>Hero matn</span><textarea name="hero_text" rows="3"><?= $f('hero_text') ?></textarea></label>
    <label class="field"><span>Holat belgisi (bo'sh qoldirsangiz yashiriladi)</span><input name="availability" value="<?= $f('availability') ?>"></label>
    <label class="field"><span>Ko'nikmalar (vergul bilan — yuguruvchi lentada)</span><input name="skills" value="<?= $f('skills') ?>"></label>
    <hr>
    <label class="field"><span>"Men haqimda" sarlavhasi</span><input name="about_title" value="<?= $f('about_title') ?>"></label>
    <label class="field"><span>"Men haqimda" matni (Markdown)</span><textarea name="about_text" rows="6"><?= $f('about_text') ?></textarea></label>
    <div class="field-row">
      <label class="field"><span>Xizmatlar (har biri yangi qatorda)</span><textarea name="services" rows="5"><?= $f('services') ?></textarea></label>
      <label class="field"><span>Raqamlar (format: <code>5+|Yillik tajriba</code>)</span><textarea name="stats" rows="5"><?= $f('stats') ?></textarea></label>
    </div>
  </section>

  <section class="card pad" id="aloqa" data-tab-panel>
    <div class="field-row">
      <label class="field"><span>Email</span><input type="email" name="email" value="<?= $f('email') ?>"></label>
      <label class="field"><span>Telefon</span><input name="phone" value="<?= $f('phone') ?>" placeholder="+998 90 123 45 67"></label>
    </div>
    <label class="field"><span>Manzil</span><input name="location" value="<?= $f('location') ?>"></label>
    <label class="field"><span>Aloqa sarlavhasi</span><input name="contact_title" value="<?= $f('contact_title') ?>"></label>
    <label class="field"><span>Aloqa matni</span><textarea name="contact_text" rows="2"><?= $f('contact_text') ?></textarea></label>
    <label class="field"><span>Yangi so'rov haqida xabar yuboriladigan email</span><input type="email" name="notify_email" value="<?= $f('notify_email') ?>" placeholder="Bo'sh bo'lsa — yuqoridagi email"></label>
    <p class="muted small">cPanel'da PHP mail() odatda ishlaydi. Xat "Spam"ga tushsa, cPanel → Email Deliverability bo'limida SPF/DKIM ni yoqing.</p>
  </section>

  <section class="card pad" id="tarmoqlar" data-tab-panel>
    <p class="hint">Tarmoqni tanlang va havolani kiriting. Tartibni ↑↓ tugmalari bilan o'zgartiring. Bo'sh qatorlar saqlanmaydi.</p>
    <div class="socials-edit" data-socials>
      <?php foreach ($links ?: [['platform' => 'telegram', 'url' => '']] as $l): ?>
        <div class="social-row" data-social-row>
          <span class="social-row__icon" data-social-icon><?= social_icon($l['platform'], 18) ?></span>
          <select name="social_platform[]" data-social-select>
            <?php foreach ($platforms as $k => [$name]): ?><option value="<?= e($k) ?>"<?= $l['platform'] === $k ? ' selected' : '' ?>><?= e($name) ?></option><?php endforeach ?>
          </select>
          <input name="social_url[]" value="<?= e($l['url']) ?>" placeholder="https://…">
          <button type="button" class="icon-btn" data-move="-1" title="Yuqoriga">↑</button>
          <button type="button" class="icon-btn" data-move="1" title="Pastga">↓</button>
          <button type="button" class="icon-btn icon-btn--danger" data-remove title="O'chirish"><?= icon('trash', 14) ?></button>
        </div>
      <?php endforeach ?>
    </div>
    <button type="button" class="btn" data-add-social><?= icon('plus', 16) ?> Tarmoq qo'shish</button>
    <template data-social-template>
      <div class="social-row" data-social-row>
        <span class="social-row__icon" data-social-icon><?= social_icon('telegram', 18) ?></span>
        <select name="social_platform[]" data-social-select><?php foreach ($platforms as $k => [$name]): ?><option value="<?= e($k) ?>"><?= e($name) ?></option><?php endforeach ?></select>
        <input name="social_url[]" value="" placeholder="https://…">
        <button type="button" class="icon-btn" data-move="-1" title="Yuqoriga">↑</button>
        <button type="button" class="icon-btn" data-move="1" title="Pastga">↓</button>
        <button type="button" class="icon-btn icon-btn--danger" data-remove title="O'chirish"><?= icon('trash', 14) ?></button>
      </div>
    </template>
    <template data-social-icons><?php foreach ($platforms as $k => $p): ?><i data-k="<?= e($k) ?>"><?= social_icon($k, 18) ?></i><?php endforeach ?></template>
  </section>

  <section class="card pad" id="seo" data-tab-panel>
    <h2 class="card__title">Qidiruv tizimlari</h2>
    <label class="field"><span>Bosh sahifa meta sarlavhasi <small class="muted" data-count="60"></small></span><input name="meta_title" value="<?= $f('meta_title') ?>" placeholder="<?= e($s['owner_name'] . ' — ' . $s['role']) ?>"></label>
    <label class="field"><span>Meta tavsif <small class="muted" data-count="160"></small></span><textarea name="meta_description" rows="3"><?= $f('meta_description') ?></textarea></label>
    <label class="field"><span>Kalit so'zlar (ixtiyoriy)</span><input name="meta_keywords" value="<?= $f('meta_keywords') ?>"></label>
    <hr>
    <h2 class="card__title"><?= icon('search', 18) ?> Google Search Console</h2>
    <ol class="steps">
      <li><a href="https://search.google.com/search-console" target="_blank" rel="noopener">search.google.com/search-console</a> ga kiring → <b>Add property</b> → <b>URL prefix</b> → <code><?= e(abs_url()) ?></code></li>
      <li><b>HTML tag</b> usulini tanlang va berilgan meta tegni (yoki faqat <code>content</code> qiymatini) quyidagi maydonga joylang, saqlang.</li>
      <li>Search Console'da <b>Verify</b> bosing, so'ng <b>Sitemaps</b> bo'limiga <code data-copy class="copy">sitemap.xml</code> ni qo'shing.</li>
    </ol>
    <label class="field"><span>Google tasdiqlash kodi</span><input name="gsc_verification" value="<?= $f('gsc_verification') ?>" placeholder='<meta name="google-site-verification" content="…"> yoki faqat kod'></label>
    <label class="field"><span>Yandex Webmaster tasdiqlash kodi (ixtiyoriy)</span><input name="yandex_verification" value="<?= $f('yandex_verification') ?>"></label>
    <hr>
    <h2 class="card__title"><?= icon('chart', 18) ?> Google Analytics 4</h2>
    <ol class="steps">
      <li><a href="https://analytics.google.com" target="_blank" rel="noopener">analytics.google.com</a> → Admin → <b>Create property</b> → Web stream yarating.</li>
      <li><b>Measurement ID</b> (<code>G-XXXXXXXXXX</code>) ni nusxalab shu yerga qo'ying.</li>
    </ol>
    <label class="field"><span>Measurement ID</span><input name="ga_id" value="<?= $f('ga_id') ?>" placeholder="G-XXXXXXXXXX"></label>
    <p class="muted small">Foydali havolalar: <a href="<?= e(abs_url('sitemap.xml')) ?>" target="_blank">sitemap.xml</a> · <a href="<?= e(abs_url('robots.txt')) ?>" target="_blank">robots.txt</a> · <a href="<?= e(abs_url('feed.xml')) ?>" target="_blank">RSS</a></p>
  </section>

  <section class="card pad" id="kod" data-tab-panel>
    <p class="hint">Bu yerga Yandex Metrika, Facebook Pixel, Microsoft Clarity kabi xizmatlar kodini joylashtirishingiz mumkin. Ehtiyot bo'ling — kod saytga o'zgarishsiz qo'shiladi.</p>
    <label class="field"><span>&lt;head&gt; ichiga</span><textarea class="mono" name="head_code" rows="7"><?= $f('head_code') ?></textarea></label>
    <label class="field"><span>&lt;body&gt; boshiga</span><textarea class="mono" name="body_code" rows="5"><?= $f('body_code') ?></textarea></label>
  </section>

  <div class="savebar"><button class="btn btn--primary" type="submit"><?= icon('check', 18) ?> Sozlamalarni saqlash</button></div>
</form>
