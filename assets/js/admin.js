(function () {
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var csrf = ($('meta[name=csrf]') || {}).content || '';
  var base = (document.querySelector('.side__logo') || {}).getAttribute ? $('.side__logo').getAttribute('href').replace(/\/admin\/?$/, '') : '';
  var root = document.documentElement;

  // Rejim
  $$('[data-theme-toggle]').forEach(function (b) {
    b.addEventListener('click', function () {
      var dark = root.dataset.theme ? root.dataset.theme === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
      root.dataset.theme = dark ? 'light' : 'dark';
      try { localStorage.setItem('theme', root.dataset.theme); } catch (e) {}
    });
  });

  // Mobil yon menyu
  var side = $('#side');
  $$('[data-side]').forEach(function (b) { b.addEventListener('click', function () { side.classList.toggle('is-open'); }); });
  document.addEventListener('click', function (e) {
    if (side && side.classList.contains('is-open') && !e.target.closest('#side') && !e.target.closest('[data-side]')) side.classList.remove('is-open');
  });

  // Flash xabarlarni yashirish
  $$('[data-autohide]').forEach(function (el) { setTimeout(function () { el.style.opacity = '0'; setTimeout(function () { el.remove(); }, 400); }, 4000); });

  // O'chirishni tasdiqlash
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) { if (!confirm(f.dataset.confirm)) e.preventDefault(); });
  });

  // Nusxa olish
  $$('[data-copy]').forEach(function (el) {
    el.title = 'Nusxa olish uchun bosing';
    el.addEventListener('click', function () {
      if (navigator.clipboard) navigator.clipboard.writeText(el.textContent.trim()).then(function () {
        var t = el.textContent; el.textContent = '✓ Nusxa olindi'; setTimeout(function () { el.textContent = t; }, 1200);
      });
    });
  });

  // Slug avtomatik
  var slugSrc = $('[data-slug-source]'), slug = $('[data-slug]');
  var translit = function (s) {
    var map = { 'а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'yo','ж':'j','з':'z','и':'i','й':'y','к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f','х':'x','ц':'ts','ч':'ch','ш':'sh','щ':'sh','ъ':'','ы':'i','ь':'','э':'e','ю':'yu','я':'ya','ў':'o','қ':'q','ғ':'g','ҳ':'h' };
    return s.toLowerCase().split('').map(function (c) { return map[c] !== undefined ? map[c] : c; }).join('')
      .replace(/[ʻʼ‘’'`]/g, '').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 120);
  };
  if (slugSrc && slug) {
    var auto = !slug.value;
    slug.addEventListener('input', function () { auto = !slug.value; updateSerp(); });
    slugSrc.addEventListener('input', function () { if (auto) { slug.placeholder = translit(slugSrc.value) || 'avtomatik'; } updateSerp(); });
  }

  // SERP ko'rinishi va belgi hisoblagichi
  var serp = $('[data-serp]');
  function updateSerp() {
    if (!serp) return;
    var t = $('[data-serp-in=title]'), d = $('[data-serp-in=desc]');
    var summary = $('[name=summary]') || $('[name=excerpt]');
    $('[data-serp-title]', serp).textContent = (t && t.value) || (slugSrc && slugSrc.value) || 'Sahifa sarlavhasi';
    $('[data-serp-desc]', serp).textContent = (d && d.value) || (summary && summary.value) || "Sahifa tavsifi bu yerda ko'rinadi…";
    $('[data-serp-slug]', serp).textContent = (slug && (slug.value || slug.placeholder !== 'avtomatik' && slug.placeholder)) || '';
  }
  $$('[data-serp-in], [name=summary], [name=excerpt]').forEach(function (el) { el.addEventListener('input', updateSerp); });
  $$('[data-count]').forEach(function (c) {
    var input = c.closest('.field').querySelector('input, textarea');
    var max = +c.dataset.count;
    var upd = function () { c.textContent = '(' + input.value.length + '/' + max + ')'; c.classList.toggle('is-over', input.value.length > max); };
    input.addEventListener('input', upd); upd();
  });

  // Muqova oldindan ko'rish
  $$('[data-cover-input]').forEach(function (inp) {
    inp.addEventListener('change', function () {
      var f = inp.files[0]; if (!f) return;
      var box = inp.closest('.cover-drop'), img = $('[data-cover-preview]', box);
      img.src = URL.createObjectURL(f); img.hidden = false; box.classList.add('has-image');
    });
  });

  // Galereya: yangi rasmlar ko'rinishi
  $$('[data-preview-multi]').forEach(function (inp) {
    var out = $('[data-new-previews]');
    var zone = inp.closest('.dropzone');
    ['dragenter', 'dragover'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.add('is-over'); }); });
    ['dragleave', 'drop'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.remove('is-over'); }); });
    inp.addEventListener('change', function () {
      out.innerHTML = '';
      Array.prototype.forEach.call(inp.files, function (f) {
        var d = document.createElement('div'); d.className = 'gallery-edit__item';
        var i = document.createElement('img'); i.src = URL.createObjectURL(f); d.appendChild(i); out.appendChild(d);
      });
    });
  });

  // Galereya: o'chirish va sudrab tartiblash
  document.addEventListener('click', function (e) {
    var rm = e.target.closest('[data-remove]');
    if (rm) { var row = rm.closest('.gallery-edit__item, [data-social-row]'); if (row) row.remove(); }
  });
  $$('[data-sortable]').forEach(function (list) {
    var drag = null;
    list.addEventListener('dragstart', function (e) { drag = e.target.closest('.gallery-edit__item'); if (drag) drag.classList.add('is-drag'); });
    list.addEventListener('dragend', function () { if (drag) drag.classList.remove('is-drag'); drag = null; });
    list.addEventListener('dragover', function (e) {
      e.preventDefault();
      var over = e.target.closest('.gallery-edit__item');
      if (!drag || !over || over === drag) return;
      var r = over.getBoundingClientRect();
      list.insertBefore(drag, (e.clientX - r.left) > r.width / 2 ? over.nextSibling : over);
    });
  });

  // Markdown muharriri
  $$('[data-editor]').forEach(function (ed) {
    var ta = $('[data-editor-text]', ed), pv = $('[data-editor-preview]', ed);
    function wrap(before, after, ph) {
      var s = ta.selectionStart, e = ta.selectionEnd, sel = ta.value.slice(s, e) || ph || '';
      ta.setRangeText(before + sel + (after || ''), s, e, 'end');
      if (!ta.value.slice(s, e)) { ta.selectionStart = s + before.length; ta.selectionEnd = s + before.length + sel.length; }
      ta.focus();
    }
    function linePrefix(p) {
      var s = ta.selectionStart, ls = ta.value.lastIndexOf('\n', s - 1) + 1;
      ta.setRangeText(p, ls, ls, 'end'); ta.focus();
    }
    var actions = {
      h2: function () { linePrefix('## '); }, h3: function () { linePrefix('### '); },
      bold: function () { wrap('**', '**', 'qalin matn'); }, italic: function () { wrap('*', '*', 'kursiv'); },
      link: function () { var u = prompt('Havola manzili (URL):', 'https://'); if (u) wrap('[', '](' + u + ')', 'havola matni'); },
      ul: function () { linePrefix('- '); }, quote: function () { linePrefix('> '); },
      code: function () { wrap('\n```\n', '\n```\n', 'kod'); }
    };
    $$('[data-md]', ed).forEach(function (b) { b.addEventListener('click', function () { actions[b.dataset.md](); }); });
    ta.addEventListener('keydown', function (e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'b') { e.preventDefault(); actions.bold(); }
      if ((e.ctrlKey || e.metaKey) && e.key === 'i') { e.preventDefault(); actions.italic(); }
    });

    function upload(file) {
      var fd = new FormData(); fd.append('file', file); fd.append('_csrf', csrf);
      ed.classList.add('is-uploading');
      return fetch(base + '/admin/upload', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (j.error) throw new Error(j.error);
          var alt = file.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ');
          wrap('\n![' + alt + '](' + j.path + ')\n', '', '');
        })
        .catch(function (err) { alert('Yuklashda xato: ' + err.message); })
        .finally(function () { ed.classList.remove('is-uploading'); });
    }
    var fileIn = $('[data-md-image]', ed);
    if (fileIn) fileIn.addEventListener('change', function () { if (fileIn.files[0]) upload(fileIn.files[0]).then(function () { fileIn.value = ''; }); });
    ta.addEventListener('paste', function (e) {
      var item = Array.prototype.find.call((e.clipboardData || {}).items || [], function (i) { return i.type.indexOf('image') === 0; });
      if (item) { e.preventDefault(); upload(item.getAsFile()); }
    });
    ta.addEventListener('drop', function (e) {
      var f = e.dataTransfer.files[0];
      if (f && f.type.indexOf('image') === 0) { e.preventDefault(); upload(f); }
    });

    $$('[data-tab]', ed).forEach(function (t) {
      t.addEventListener('click', function () {
        $$('[data-tab]', ed).forEach(function (x) { x.classList.toggle('is-active', x === t); });
        var prev = t.dataset.tab === 'preview';
        ta.hidden = prev; pv.hidden = !prev;
        $('.editor__tools', ed).style.visibility = prev ? 'hidden' : '';
        if (prev) {
          pv.innerHTML = '<p class="muted">Yuklanmoqda…</p>';
          var fd = new FormData(); fd.append('content', ta.value); fd.append('_csrf', csrf);
          fetch(base + '/admin/preview', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.text(); })
            .then(function (h) { pv.innerHTML = h || '<p class="muted">Matn bo\'sh.</p>'; });
        }
      });
    });
  });

  // Sozlamalar tablari
  var tabsForm = $('[data-tabs]');
  if (tabsForm) {
    tabsForm.classList.add('js-tabs');
    var links = $$('[data-tab-link]', tabsForm), panels = $$('[data-tab-panel]', tabsForm), tabInput = $('[data-tab-input]', tabsForm);
    var show = function (id) {
      if (!panels.some(function (p) { return p.id === id; })) id = panels[0].id;
      panels.forEach(function (p) { p.classList.toggle('is-active', p.id === id); });
      links.forEach(function (l) { l.classList.toggle('is-active', l.getAttribute('href') === '#' + id); });
      tabInput.value = id;
    };
    links.forEach(function (l) { l.addEventListener('click', function (e) { e.preventDefault(); var id = l.getAttribute('href').slice(1); history.replaceState(null, '', '#' + id); show(id); }); });
    show(location.hash.slice(1));
  }

  // Ijtimoiy tarmoqlar
  var socials = $('[data-socials]');
  if (socials) {
    var icons = {};
    $$('i[data-k]', $('[data-social-icons]').content).forEach(function (i) { icons[i.dataset.k] = i.innerHTML; });
    socials.addEventListener('change', function (e) {
      if (e.target.matches('[data-social-select]')) $('[data-social-icon]', e.target.closest('[data-social-row]')).innerHTML = icons[e.target.value] || '';
    });
    socials.addEventListener('click', function (e) {
      var mv = e.target.closest('[data-move]'); if (!mv) return;
      var row = mv.closest('[data-social-row]');
      if (mv.dataset.move === '-1' && row.previousElementSibling) socials.insertBefore(row, row.previousElementSibling);
      if (mv.dataset.move === '1' && row.nextElementSibling) socials.insertBefore(row.nextElementSibling, row);
    });
    $('[data-add-social]').addEventListener('click', function () {
      var row = $('[data-social-template]').content.firstElementChild.cloneNode(true);
      socials.appendChild(row);
      $('input', row).focus();
    });
  }
})();
