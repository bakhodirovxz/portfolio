<?php
$isWork = $type === 'works';
$base = 'admin/' . $type;
$gallery = $isWork ? (json_decode((string)($item['gallery'] ?? '[]'), true) ?: []) : [];
$publicUrl = $item['id'] ? url(($isWork ? 'ishlar/' : 'blog/') . $item['slug']) : '';
?>
<?php foreach ($errors as $er): ?><div class="alert alert--error"><?= e($er) ?></div><?php endforeach ?>

<form class="editor-grid" method="post" enctype="multipart/form-data" action="<?= e(url($base . ($item['id'] ? '/' . $item['id'] . '/edit' : '/new'))) ?>">
  <?= csrf_field() ?>
  <div class="editor-main">
    <section class="card pad">
      <label class="field">
        <span>Sarlavha *</span>
        <input class="input-xl" name="title" value="<?= e($item['title']) ?>" required data-slug-source placeholder="<?= $isWork ? 'Loyiha nomi' : 'Maqola sarlavhasi' ?>">
      </label>
      <label class="field">
        <span>URL (slug)</span>
        <div class="input-prefix"><em><?= e(abs_url($isWork ? 'ishlar/' : 'blog/')) ?></em><input name="slug" value="<?= e($item['slug']) ?>" data-slug placeholder="avtomatik"></div>
      </label>
      <?php if ($isWork): ?>
        <label class="field"><span>Qisqa tavsif</span><textarea name="summary" rows="2" maxlength="500" placeholder="Loyiha haqida 1–2 jumla (kartalarda va SEO'da ko'rinadi)"><?= e($item['summary']) ?></textarea></label>
      <?php else: ?>
        <label class="field"><span>Qisqa mazmun (excerpt)</span><textarea name="excerpt" rows="2" maxlength="500" placeholder="Ro'yxatda va ijtimoiy tarmoqlarda ko'rinadigan qisqa matn"><?= e($item['excerpt']) ?></textarea></label>
      <?php endif ?>
    </section>

    <section class="card editor" data-editor>
      <div class="editor__bar">
        <div class="tabs" role="tablist">
          <button type="button" class="is-active" data-tab="write">Yozish</button>
          <button type="button" data-tab="preview">Ko'rish</button>
        </div>
        <div class="editor__tools">
          <button type="button" data-md="h2" title="Sarlavha">H2</button>
          <button type="button" data-md="h3" title="Kichik sarlavha">H3</button>
          <button type="button" data-md="bold" title="Qalin"><b>B</b></button>
          <button type="button" data-md="italic" title="Kursiv"><i>I</i></button>
          <button type="button" data-md="link" title="Havola"><?= icon('link', 16) ?></button>
          <button type="button" data-md="ul" title="Ro'yxat">•</button>
          <button type="button" data-md="quote" title="Iqtibos">❝</button>
          <button type="button" data-md="code" title="Kod">&lt;/&gt;</button>
          <label class="tool-upload" title="Rasm qo'shish"><?= icon('image', 16) ?><input type="file" accept="image/*" data-md-image hidden></label>
        </div>
      </div>
      <textarea name="content" class="editor__text" rows="22" data-editor-text placeholder="Matnni shu yerga yozing. Markdown qo'llab-quvvatlanadi: ## Sarlavha, **qalin**, *kursiv*, [havola](https://…), - ro'yxat. YouTube havolasini alohida qatorga qo'ysangiz, video joylanadi."><?= e($item['content']) ?></textarea>
      <div class="editor__preview prose" data-editor-preview hidden></div>
    </section>

    <?php if ($isWork): ?>
    <section class="card pad">
      <h2 class="card__title">Galereya</h2>
      <p class="muted small">Loyiha sahifasida muqovadan keyin ko'rinadigan rasmlar. Tartibni sudrab o'zgartirishingiz mumkin.</p>
      <div class="gallery-edit" data-sortable>
        <?php foreach ($gallery as $img): ?>
          <div class="gallery-edit__item" draggable="true">
            <img src="<?= e(media($img)) ?>" alt="">
            <input type="hidden" name="gallery_keep[]" value="<?= e($img) ?>">
            <button type="button" class="icon-btn icon-btn--danger" data-remove title="Olib tashlash"><?= icon('trash', 14) ?></button>
          </div>
        <?php endforeach ?>
      </div>
      <label class="dropzone"><?= icon('image', 22) ?><span>Rasmlarni tanlang yoki shu yerga tashlang</span><input type="file" name="gallery[]" accept="image/*" multiple data-preview-multi></label>
      <div class="gallery-edit gallery-edit--new" data-new-previews></div>
    </section>
    <?php endif ?>

    <section class="card pad">
      <h2 class="card__title"><?= icon('search', 18) ?> SEO</h2>
      <div class="serp" data-serp>
        <span class="serp__url"><?= e(abs_url(($isWork ? 'ishlar/' : 'blog/'))) ?><b data-serp-slug><?= e($item['slug']) ?></b></span>
        <span class="serp__title" data-serp-title><?= e($item['meta_title'] ?: $item['title'] ?: 'Sahifa sarlavhasi') ?></span>
        <span class="serp__desc" data-serp-desc><?= e($item['meta_description'] ?: ($isWork ? $item['summary'] : $item['excerpt']) ?: 'Sahifa tavsifi bu yerda ko\'rinadi…') ?></span>
      </div>
      <label class="field"><span>Meta sarlavha <small class="muted" data-count="60"></small></span><input name="meta_title" value="<?= e($item['meta_title']) ?>" placeholder="Bo'sh qolsa — sarlavha ishlatiladi" data-serp-in="title"></label>
      <label class="field"><span>Meta tavsif <small class="muted" data-count="160"></small></span><textarea name="meta_description" rows="2" placeholder="Bo'sh qolsa — qisqa tavsif ishlatiladi" data-serp-in="desc"><?= e($item['meta_description']) ?></textarea></label>
    </section>
  </div>

  <aside class="editor-side">
    <section class="card pad sticky">
      <label class="field">
        <span>Holat</span>
        <select name="status">
          <option value="published"<?= $item['status'] === 'published' ? ' selected' : '' ?>>Chop etilgan</option>
          <option value="draft"<?= $item['status'] === 'draft' ? ' selected' : '' ?>>Qoralama</option>
        </select>
      </label>
      <?php if (!$isWork): ?>
        <label class="field"><span>Chop etish sanasi</span><input type="datetime-local" name="published_at" value="<?= e(date('Y-m-d\TH:i', strtotime($item['published_at']))) ?>"></label>
      <?php else: ?>
        <label class="check"><input type="checkbox" name="featured" value="1"<?= $item['featured'] ? ' checked' : '' ?>> <span>Tanlangan (bosh sahifada birinchi)</span></label>
      <?php endif ?>
      <div class="side-actions">
        <button class="btn btn--primary btn--block" type="submit"><?= icon('check', 18) ?> Saqlash</button>
        <?php if ($publicUrl): ?><a class="btn btn--block" href="<?= e($publicUrl) ?>" target="_blank"><?= icon('external', 16) ?> Saytda ko'rish</a><?php endif ?>
      </div>
    </section>

    <section class="card pad">
      <h2 class="card__title">Muqova rasmi</h2>
      <label class="cover-drop<?= $item['cover'] ? ' has-image' : '' ?>">
        <img src="<?= e(media($item['cover'])) ?>" alt="" data-cover-preview<?= $item['cover'] ? '' : ' hidden' ?>>
        <span><?= icon('image', 22) ?> Rasm tanlang</span>
        <input type="file" name="cover" accept="image/*" data-cover-input>
      </label>
      <p class="muted small">Tavsiya: 1600×1200 px, JPG yoki WEBP, 500 KB gacha.</p>
      <?php if ($item['cover']): ?><label class="check"><input type="checkbox" name="remove_cover" value="1"> <span>Muqovani o'chirish</span></label><?php endif ?>
    </section>

    <section class="card pad">
      <?php if ($isWork): ?>
        <label class="field"><span>Kategoriya</span><input name="category" value="<?= e($item['category']) ?>" list="cats" placeholder="Veb-sayt, Brending…"><datalist id="cats"><?php foreach ($categories as $c): ?><option value="<?= e($c) ?>"><?php endforeach ?></datalist></label>
        <label class="field"><span>Mijoz</span><input name="client" value="<?= e($item['client']) ?>"></label>
        <div class="field-row">
          <label class="field"><span>Yil</span><input name="year" value="<?= e($item['year']) ?>"></label>
          <label class="field"><span>Tartib</span><input type="number" name="sort_order" value="<?= (int)$item['sort_order'] ?>"></label>
        </div>
        <label class="field"><span>Loyiha havolasi</span><input type="url" name="project_url" value="<?= e($item['project_url']) ?>" placeholder="https://"></label>
      <?php endif ?>
      <label class="field"><span>Teglar</span><input name="tags" value="<?= e($item['tags']) ?>" placeholder="vergul bilan: figma, ui, web"></label>
    </section>
  </aside>
</form>
