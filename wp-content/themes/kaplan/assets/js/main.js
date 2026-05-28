(function () {
  'use strict';

  /* ---------- Mobile Nav ---------- */
  const navToggle = document.getElementById('nav-toggle');
  const nav = document.getElementById('primary-nav');
  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      navToggle.classList.toggle('is-open', open);
      navToggle.setAttribute('aria-expanded', String(open));
    });
    nav.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        if (window.innerWidth <= 820 && !a.parentElement.classList.contains('has-sub')) {
          nav.classList.remove('is-open');
          navToggle.classList.remove('is-open');
        }
      });
    });
  }

  /* ---------- Sticky header shadow ---------- */
  const header = document.getElementById('site-header');
  const onScroll = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 12);
    const st = document.getElementById('scroll-top');
    if (st) st.classList.toggle('is-visible', window.scrollY > 480);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Scroll to top ---------- */
  const scrollTop = document.getElementById('scroll-top');
  if (scrollTop) {
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- Hero slider ---------- */
  const slides = document.querySelectorAll('.hero__slide');
  const dotsBox = document.getElementById('hero-dots');
  const dots = dotsBox ? dotsBox.querySelectorAll('button') : [];
  let current = 0;
  let timer = null;

  function show(i) {
    if (!slides.length) return;
    current = (i + slides.length) % slides.length;
    slides.forEach((s, idx) => s.classList.toggle('is-active', idx === current));
    dots.forEach((d, idx) => d.classList.toggle('is-active', idx === current));
  }
  function next() { show(current + 1); }
  function prev() { show(current - 1); }
  function autoplay() {
    stop();
    timer = setInterval(next, 6000);
  }
  function stop() { if (timer) { clearInterval(timer); timer = null; } }

  if (slides.length) {
    dots.forEach((d, idx) => d.addEventListener('click', () => { show(idx); autoplay(); }));
    const nextBtn = document.getElementById('hero-next');
    const prevBtn = document.getElementById('hero-prev');
    if (nextBtn) nextBtn.addEventListener('click', () => { next(); autoplay(); });
    if (prevBtn) prevBtn.addEventListener('click', () => { prev(); autoplay(); });
    autoplay();
    // pause on hover
    const heroEl = document.querySelector('.hero');
    if (heroEl) {
      heroEl.addEventListener('mouseenter', stop);
      heroEl.addEventListener('mouseleave', autoplay);
    }
  }

  /* ---------- Tabs ---------- */
  document.querySelectorAll('[data-tabs]').forEach(group => {
    const buttons = group.querySelectorAll('.tabs button');
    const panels = group.querySelectorAll('.tab-panel');
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const target = btn.dataset.tab;
        buttons.forEach(b => b.classList.toggle('is-active', b === btn));
        panels.forEach(p => p.classList.toggle('is-active', p.dataset.panel === target));
      });
    });
  });

  /* ---------- Counters ---------- */
  const counters = document.querySelectorAll('.stat__number');
  if ('IntersectionObserver' in window && counters.length) {
    const animate = (el) => {
      const target = parseInt(el.dataset.target || '0', 10);
      const duration = 1400;
      const start = performance.now();
      const step = (t) => {
        const p = Math.min((t - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.floor(eased * target);
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = target;
      };
      requestAnimationFrame(step);
    };
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          animate(e.target);
          io.unobserve(e.target);
        }
      });
    }, { threshold: .4 });
    counters.forEach(c => io.observe(c));
  }

})();

/* ===== Görsel Lightbox (galeri modalı) ===== */
(function () {
  var links = [].slice.call(document.querySelectorAll('a.gallery-item[href]'));
  if (!links.length) return;

  var modal = document.createElement('div');
  modal.className = 'kpl-lightbox';
  modal.setAttribute('role', 'dialog');
  modal.setAttribute('aria-modal', 'true');
  modal.innerHTML =
    '<button class="kpl-lightbox__close" aria-label="Kapat">&times;</button>' +
    '<figure class="kpl-lightbox__fig"><img alt="" /><figcaption></figcaption></figure>';
  document.body.appendChild(modal);

  var img = modal.querySelector('img');
  var cap = modal.querySelector('figcaption');

  function open(src, caption) {
    img.src = src;
    img.alt = caption || '';
    cap.textContent = caption || '';
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }
  function close() {
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
    setTimeout(function () { img.src = ''; }, 250);
  }

  links.forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      var label = a.querySelector('.gallery-item__label');
      open(a.getAttribute('href'), label ? label.textContent.trim() : '');
    });
  });

  modal.addEventListener('click', function (e) {
    if (e.target === modal || e.target.classList.contains('kpl-lightbox__close')) close();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('is-open')) close();
  });
})();
