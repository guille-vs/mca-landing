/**
 * Master Center Américas — landing behaviour.
 *
 * Three small, independent features. Each one bails out quietly when its
 * markup is absent so the file stays safe to enqueue on any WordPress page.
 */
(function () {
  'use strict';

  /* ---------------------------------------------------------------------
   * Sticky header: solidify the bar once the page leaves the hero.
   * ------------------------------------------------------------------ */
  function initStickyHeader() {
    const header = document.querySelector('[data-js="header"]');
    if (!header) return;

    const THRESHOLD = 40;
    let ticking = false;

    function update() {
      header.classList.toggle('is-stuck', window.scrollY > THRESHOLD);
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(update);
    }, { passive: true });

    update();
  }

  /* ---------------------------------------------------------------------
   * Mobile navigation drawer.
   * ------------------------------------------------------------------ */
  function initNavToggle() {
    const toggle = document.querySelector('[data-js="nav-toggle"]');
    const nav = document.querySelector('[data-js="nav"]');
    if (!toggle || !nav) return;

    function setOpen(open) {
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
      nav.dataset.open = String(open);
    }

    toggle.addEventListener('click', function () {
      setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    // Following an anchor should close the drawer.
    nav.addEventListener('click', function (event) {
      if (event.target.closest('a')) setOpen(false);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        setOpen(false);
        toggle.focus();
      }
    });

    // Leaving the mobile breakpoint must not strand the drawer open.
    const desktop = window.matchMedia('(min-width: 1024px)');
    desktop.addEventListener('change', function (event) {
      if (event.matches) setOpen(false);
    });
  }

  /* ---------------------------------------------------------------------
   * Scroll reveal. Elements are hidden by CSS only while `.js` is set, so
   * a failure here can never leave content invisible.
   * ------------------------------------------------------------------ */
  function initReveal() {
    const items = document.querySelectorAll('[data-reveal]');
    if (!items.length) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reducedMotion || !('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('is-revealed'); });
      return;
    }

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-revealed');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.1 });

    items.forEach(function (el) { observer.observe(el); });
  }

  /* ---------------------------------------------------------------------
   * Highlight the nav item matching the section in view.
   * ------------------------------------------------------------------ */
  function initScrollSpy() {
    const links = Array.prototype.slice.call(
      document.querySelectorAll('.nav__link[href^="#"]')
    );
    if (!links.length) return;

    const sections = links
      .map(function (link) { return document.querySelector(link.getAttribute('href')); })
      .filter(Boolean);
    if (!sections.length || !('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        links.forEach(function (link) {
          const active = link.getAttribute('href') === '#' + entry.target.id;
          if (active) {
            link.setAttribute('aria-current', 'true');
          } else {
            link.removeAttribute('aria-current');
          }
        });
      });
    }, { rootMargin: '-45% 0px -50% 0px' });

    sections.forEach(function (section) { observer.observe(section); });
  }

  function init() {
    initStickyHeader();
    initNavToggle();
    initReveal();
    initScrollSpy();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
