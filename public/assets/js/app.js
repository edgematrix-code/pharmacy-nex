// Run a callback once the DOM is ready (script is loaded at end of body, so usually immediate).
const ready = fn => (document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', fn) : fn());

/* ------------------------------------------------------------------ *
 * Hero product carousel (right side of the home hero).
 * Shows one product per view and auto-advances every 3 seconds.
 * Self-contained so it always runs even if another script errors.
 * ------------------------------------------------------------------ */
ready(function () {
  const root = document.querySelector('.hero-carousel');
  if (!root) return;
  const slides = Array.from(root.querySelectorAll('.hero-slide'));
  if (slides.length < 2) return;
  const dots = Array.from(root.querySelectorAll('.hero-dots span'));
  const DELAY = 3000;
  let i = 0;
  let timer = null;

  function show(n) {
    i = (n + slides.length) % slides.length;
    slides.forEach((s, k) => s.classList.toggle('on', k === i));
    dots.forEach((d, k) => d.classList.toggle('on', k === i));
  }
  function next() { show(i + 1); }
  function start() { stop(); timer = setInterval(next, DELAY); }
  function stop() { if (timer) { clearInterval(timer); timer = null; } }

  show(0);
  start();

  // Pause while the visitor is interacting with the hero.
  root.addEventListener('mouseenter', stop);
  root.addEventListener('mouseleave', start);
  root.addEventListener('focusin', stop);
  root.addEventListener('focusout', start);

  // Keep rotating when the tab becomes visible again.
  document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
});

/* ------------------------------------------------------------------ *
 * Quantity steppers + live tier price on product cards.
 * ------------------------------------------------------------------ */
document.addEventListener('click', e => {
  const b = e.target.closest('.qty button[data-d]');
  if (!b) return;
  const i = b.parentElement.querySelector('input');
  i.value = Math.max(1, Math.min(99, (+i.value || 1) + +b.dataset.d));
  i.dispatchEvent(new Event('input', { bubbles: true }));
});

ready(function () {
  const fmt = n => '$' + n.toFixed(2);
  document.querySelectorAll('.pcard .buy, .tr-card .buy').forEach(f => {
    const tierSel = f.querySelector('.tier-select');
    const q = f.querySelector('input[name=qty]');
    const p = f.querySelector('[data-price]');
    const s = f.querySelector('select.tiers');
    if (!tierSel || !q || !p || !s) return;
    const base = +tierSel.dataset.base;
    const upd = () => {
      const n = +q.value || 1;
      p.textContent = fmt(n >= 5 ? Math.round(base * 90) / 100 : n >= 3 ? Math.round(base * 95) / 100 : base);
      s.selectedIndex = n >= 5 ? 2 : n >= 3 ? 1 : 0;
    };
    q.addEventListener('input', upd);
    s.addEventListener('change', () => { q.value = Math.max(+q.value, +s.value); if (+s.value === 1) q.value = 1; upd(); });
  });
});

// Auto-dismiss the flash toast.
ready(function () {
  const t = document.getElementById('toast');
  if (t) setTimeout(() => t.remove(), 4500);
});

/* ------------------------------------------------------------------ *
 * Carousel arrows for the Top Rated Products track.
 * ------------------------------------------------------------------ */
ready(function () {
  document.querySelectorAll('.tr-carousel').forEach(c => {
    const track = c.querySelector('.tr-track');
    if (!track) return;
    c.querySelectorAll('.tr-nav').forEach(b => b.addEventListener('click', () => {
      track.scrollBy({ left: (b.classList.contains('prev') ? -1 : 1) * (track.clientWidth / 3 + 20), behavior: 'smooth' });
    }));
  });
});

// Dismissible floating promo banner.
ready(function () {
  document.querySelectorAll('.promo-close').forEach(b => b.addEventListener('click', () => b.closest('.promo-banner').remove()));
});

/* ------------------------------------------------------------------ *
 * Shop grid view switcher (2 / 3 / 4 columns / list).
 * ------------------------------------------------------------------ */
ready(function () {
  const sw = document.getElementById('view-switcher');
  const grid = document.getElementById('product-grid');
  if (!sw || !grid) return;
  const KEY = 'shopView';
  const apply = v => {
    grid.classList.remove('cols-2', 'cols-3', 'cols-4', 'list');
    grid.classList.add(v === 'list' ? 'list' : 'cols-' + v);
    sw.querySelectorAll('button').forEach(b => b.classList.toggle('on', b.dataset.view === v));
  };
  let saved = '4';
  try { saved = localStorage.getItem(KEY) || '4'; } catch (e) {}
  apply(saved);
  sw.addEventListener('click', e => {
    const b = e.target.closest('button[data-view]');
    if (!b) return;
    const v = b.dataset.view;
    try { localStorage.setItem(KEY, v); } catch (e) {}
    apply(v);
  });
});

/* ------------------------------------------------------------------ *
 * Copy-to-clipboard for wallet addresses on the checkout page.
 * ------------------------------------------------------------------ */
ready(function () {
  document.addEventListener('click', e => {
    const b = e.target.closest('.copy-btn');
    if (!b) return;
    const text = b.dataset.copy || '';
    const done = () => {
      const old = b.textContent;
      b.textContent = 'Copied!';
      setTimeout(() => { b.textContent = old; }, 1500);
    };
    const fallback = () => {
      const ta = document.createElement('textarea');
      ta.value = text;
      ta.setAttribute('readonly', '');
      ta.style.position = 'fixed';
      ta.style.top = '-1000px';
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); done(); } catch (err) {}
      document.body.removeChild(ta);
    };
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(done).catch(fallback);
    } else {
      fallback();
    }
  });
});
