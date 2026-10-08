(function () {
  var root = document.documentElement;

  // Yorug'/qorong'i rejim
  document.querySelectorAll('[data-theme-toggle]').forEach(function (b) {
    b.addEventListener('click', function () {
      var dark = root.dataset.theme ? root.dataset.theme === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
      root.dataset.theme = dark ? 'light' : 'dark';
      try { localStorage.setItem('theme', root.dataset.theme); } catch (e) {}
    });
  });

  // Mobil menyu
  var menuBtn = document.querySelector('[data-menu]');
  var nav = document.getElementById('nav');
  if (menuBtn && nav) {
    menuBtn.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      menuBtn.textContent = open ? 'Yopish' : 'Menyu';
    });
    nav.addEventListener('click', function (e) { if (e.target.closest('a')) nav.classList.remove('is-open'); });
  }

  // Mahalliy vaqt
  var clocks = document.querySelectorAll('[data-clock]');
  function tick() {
    clocks.forEach(function (c) {
      try {
        var t = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: c.dataset.tz }).format(new Date());
        c.querySelector('[data-clock-time]').textContent = t;
      } catch (e) {}
    });
  }
  if (clocks.length) { tick(); setInterval(tick, 20000); }

  // Ishlar ro'yxatida kursor ostida rasm
  var peek = document.querySelector('.peek');
  var list = document.querySelector('[data-peek-list]');
  if (peek && list && matchMedia('(hover: hover)').matches) {
    var raf = 0, x = 0, y = 0;
    var place = function () {
      raf = 0;
      var w = peek.offsetWidth, h = peek.offsetHeight;
      var left = Math.min(x + 24, window.innerWidth - w - 16);
      var top = Math.min(Math.max(y - h / 2, 72), window.innerHeight - h - 16);
      peek.style.transform = 'translate(' + left + 'px,' + top + 'px)';
    };
    peek.style.left = '0'; peek.style.top = '0';
    list.addEventListener('mousemove', function (e) { x = e.clientX; y = e.clientY; if (!raf) raf = requestAnimationFrame(place); });
    list.addEventListener('mouseover', function (e) {
      var row = e.target.closest('[data-peek]');
      if (row) { if (peek.getAttribute('src') !== row.dataset.peek) peek.src = row.dataset.peek; peek.classList.add('is-on'); }
      else peek.classList.remove('is-on');
    });
    list.addEventListener('mouseleave', function () { peek.classList.remove('is-on'); });
  }

  // Forma yuborilayotganda tugmani bloklash
  document.querySelectorAll('form').forEach(function (f) {
    f.addEventListener('submit', function () {
      var b = f.querySelector('[type=submit]');
      if (b) { b.disabled = true; b.style.opacity = '.5'; }
    });
  });
})();
