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

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
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


  /* ---------------------------------------------------------------------
   * Carousel. The scrolling itself is native scroll-snap; this only wires
   * the arrows and dots and keeps them in sync. Chrome stays hidden unless
   * the track genuinely overflows, so above the mobile breakpoint — where
   * every section is a plain grid — nothing is shown.
   * ------------------------------------------------------------------ */
  function initCarousels() {
    document.querySelectorAll('[data-carousel]').forEach(setupCarousel);
  }

  function setupCarousel(root) {
    const viewport = root.querySelector('.carousel__viewport');
    const prev = root.querySelector('[data-carousel-prev]');
    const next = root.querySelector('[data-carousel-next]');
    const dotsBox = root.querySelector('[data-carousel-dots]');
    if (!viewport) return;

    const items = Array.prototype.slice.call(viewport.children);
    if (items.length < 2) return;

    // One dot per item: the tracks use fractional slide widths, so items —
    // not pages — are what the scroll snaps to.
    const dots = items.map(function (item, i) {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'carousel__dot';
      b.setAttribute('role', 'tab');
      b.setAttribute('aria-label', 'Ir al elemento ' + (i + 1));
      b.addEventListener('click', function () { scrollToIndex(i); });
      if (dotsBox) dotsBox.appendChild(b);
      return b;
    });

    // The carousel keeps its own idea of which slide is current. Deriving it
    // from scrollLeft every time stalls autoplay whenever a smooth scroll is
    // interrupted or has not landed yet; the scroll listener still corrects
    // this value after a manual swipe.
    let index = 0;

    function scrollToIndex(i) {
      const clamped = Math.max(0, Math.min(items.length - 1, i));
      index = clamped;
      viewport.scrollTo({
        left: items[clamped].offsetLeft - items[0].offsetLeft,
        behavior: prefersReducedMotion() ? 'auto' : 'smooth'
      });
      // Do not wait for the scroll to settle to move the indicator; the scroll
      // listener will correct it if the browser lands somewhere else.
      render(clamped);
    }

    function currentIndex() {
      const x = viewport.scrollLeft + items[0].offsetLeft;
      let best = 0;
      let bestDist = Infinity;
      items.forEach(function (item, i) {
        const d = Math.abs(item.offsetLeft - x);
        if (d < bestDist) { bestDist = d; best = i; }
      });
      return best;
    }

    // With fractional slide widths the last items share the final screen, so
    // there are fewer scroll positions than items. Anything past the last
    // reachable one would be a dot the arrows can never arrive at.
    function reachableCount() {
      const maxScroll = viewport.scrollWidth - viewport.clientWidth;
      const base = items[0].offsetLeft;
      let n = 0;
      items.forEach(function (item) {
        if (item.offsetLeft - base <= maxScroll + 2) n++;
      });
      return Math.max(1, n);
    }

    // Paint the controls for a given index. Split out from sync() so a click
    // can update them immediately instead of waiting for the scroll to settle.
    function render(i) {
      const reachable = reachableCount();
      const active = Math.max(0, Math.min(i, reachable - 1));
      // Disable by index, not by pixel: the track's left padding means resting
      // on the first item is not the same as scrollLeft === 0.
      if (prev) prev.disabled = active <= 0;
      if (next) next.disabled = active >= reachable - 1;
      dots.forEach(function (d, k) {
        d.hidden = k >= reachable;
        if (k === active) { d.setAttribute('aria-current', 'true'); }
        else { d.removeAttribute('aria-current'); }
      });
    }

    function sync() {
      // 2px of slack absorbs sub-pixel layout rounding.
      const scrollable = viewport.scrollWidth - viewport.clientWidth > 2;
      const was = root.getAttribute('data-scrollable');
      root.setAttribute('data-scrollable', String(scrollable));
      if (!scrollable) { stop(); return; }
      if (was !== 'true') play();
      index = currentIndex();
      render(index);
    }

    // Debounced rather than rAF-throttled: this only needs to run once the
    // scroll settles, and a timer cannot get wedged if frames stop being
    // delivered (background tab, throttled renderer).
    let settle = 0;
    viewport.addEventListener('scroll', function () {
      clearTimeout(settle);
      settle = setTimeout(sync, 80);
    }, { passive: true });

    if ('ResizeObserver' in window) {
      new ResizeObserver(sync).observe(viewport);
    } else {
      window.addEventListener('resize', sync);
    }

    /* -------------------------------------------------------------------
     * Autoplay.
     *
     * Motion that starts on its own has to be escapable, so it stops for
     * good the moment the reader takes over — a swipe, a dot, or keyboard
     * focus — and it never starts at all under prefers-reduced-motion.
     * It also idles while the section is off-screen or the tab is hidden,
     * so nothing scrolls where nobody is looking.
     * ---------------------------------------------------------------- */
    const INTERVAL = 3000;
    let timer = 0;
    let surrendered = false;
    let onScreen = true;

    function play() {
      stop();
      if (surrendered || prefersReducedMotion() || document.hidden || !onScreen) return;
      if (root.getAttribute('data-scrollable') !== 'true') return;
      timer = setInterval(advance, INTERVAL);
    }

    function stop() {
      clearInterval(timer);
      timer = 0;
    }

    function advance() {
      const last = reachableCount() - 1;
      scrollToIndex(index >= last ? 0 : index + 1);
    }

    // Any deliberate interaction hands control over permanently.
    function surrender() {
      surrendered = true;
      stop();
    }
    if (prev) prev.addEventListener('click', function () { scrollToIndex(index - 1); });
    if (next) next.addEventListener('click', function () { scrollToIndex(index + 1); });

    viewport.addEventListener('pointerdown', surrender, { passive: true });
    viewport.addEventListener('keydown', surrender);
    if (dotsBox) dotsBox.addEventListener('click', surrender);
    if (prev) prev.addEventListener('click', surrender);
    if (next) next.addEventListener('click', surrender);

    // Hovering is not a takeover, just a pause.
    root.addEventListener('pointerenter', stop);
    root.addEventListener('pointerleave', play);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', play);

    document.addEventListener('visibilitychange', play);

    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        onScreen = entries[0].isIntersecting;
        play();
      }, { threshold: 0.25 }).observe(root);
    }

    sync();
    play();
  }

  /* ---------------------------------------------------------------------
   * Count-up numbers. The final value is already in the HTML, so this only
   * ever replaces a correct value with another correct value — if the script
   * never runs, or motion is reduced, the reader still sees the real figure.
   * ------------------------------------------------------------------ */
  function initCounters() {
    const els = document.querySelectorAll('[data-count-to]');
    if (!els.length) return;

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) return;

    // Observe the surrounding card, not the number itself: a two-character
    // <span> is small enough that a 60%-visible threshold could be satisfied
    // (or missed) almost arbitrarily. The card is a reliable target.
    const targets = new Map();
    els.forEach(function (el) {
      const card = el.closest('.stats__card') || el.parentElement;
      targets.set(card, (targets.get(card) || []).concat(el));
    });

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        observer.unobserve(entry.target);
        (targets.get(entry.target) || []).forEach(countUp);
      });
    }, { threshold: 0.35 });

    targets.forEach(function (_els, card) { observer.observe(card); });
  }

  function countUp(el) {
    const target = parseInt(el.getAttribute('data-count-to'), 10);
    if (isNaN(target)) return;

    const DURATION = 1400;
    let start = null;

    function frame(now) {
      if (start === null) start = now;
      const t = Math.min((now - start) / DURATION, 1);
      // Ease-out cubic: fast first, settling on the final number.
      const eased = 1 - Math.pow(1 - t, 3);
      el.textContent = String(Math.round(target * eased));
      if (t < 1) window.requestAnimationFrame(frame);
    }

    el.textContent = '0';
    window.requestAnimationFrame(frame);
  }

  function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }


  /* ---------------------------------------------------------------------
   * Meeting-request modal.
   *
   * <dialog> supplies the focus trap, Esc and the inert background. This adds
   * opening, dismissal, Spanish field validation and submission.
   * ------------------------------------------------------------------ */
  function initMeetingModal() {
    const dialog = document.getElementById('modal-reunion');
    if (!dialog || typeof dialog.showModal !== 'function') return;

    let opener = null;

    document.querySelectorAll('[data-modal-open]').forEach(function (el) {
      el.addEventListener('click', function (event) {
        event.preventDefault();
        opener = el;
        dialog.showModal();
        document.documentElement.style.overflow = 'hidden';
      });
    });

    // Idempotent, and called from every dismissal path rather than only from
    // the `close` event, so the page can never be left scroll-locked.
    function restore() {
      document.documentElement.style.overflow = '';
      if (opener) { opener.focus(); opener = null; }
    }

    function closeDialog() {
      dialog.close();
      restore();
    }

    dialog.querySelectorAll('[data-modal-close]').forEach(function (el) {
      el.addEventListener('click', closeDialog);
    });

    // A click that lands on <dialog> itself is a click on the backdrop: the
    // panel fills the element, so anything else would have hit the panel.
    dialog.addEventListener('click', function (event) {
      if (event.target === dialog) closeDialog();
    });

    dialog.addEventListener('cancel', restore);
    dialog.addEventListener('close', restore);
    dialog.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') restore();
    });

    initMeetingForm(dialog);
  }

  const FIELD_MESSAGES = {
    nombre: 'Ingresa tus nombres completos.',
    correo: 'Ingresa un correo válido.',
    celular: 'Ingresa un número de celular válido.',
    empresa: 'Indica el nombre de tu empresa.',
    mensaje: 'Cuéntanos brevemente tu requerimiento.'
  };

  function initMeetingForm(scope) {
    const form = scope.querySelector('[data-meeting-form]');
    if (!form) return;

    const status = form.querySelector('[data-form-status]');
    const fields = Array.prototype.slice.call(form.querySelectorAll('.form__input'));

    // Give every error slot an id so its input can point at it; without this a
    // screen reader announces the field as invalid but never says why.
    fields.forEach(function (input) {
      const slot = input.closest('.form__field').querySelector('[data-error]');
      if (slot) slot.id = 'err-' + input.name;
    });

    function problemWith(input) {
      const value = input.value.trim();
      if (!value) return FIELD_MESSAGES[input.name] || 'Este campo es obligatorio.';
      if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
        return FIELD_MESSAGES.correo;
      }
      if (input.type === 'tel' && value.replace(/\D/g, '').length < 6) {
        return FIELD_MESSAGES.celular;
      }
      if (input.name === 'mensaje' && value.length < 10) {
        return 'Describe tu requerimiento con un poco más de detalle.';
      }
      return '';
    }

    function mark(input) {
      const field = input.closest('.form__field');
      const slot = field.querySelector('[data-error]');
      const problem = problemWith(input);
      if (problem) {
        field.setAttribute('data-invalid', '');
        input.setAttribute('aria-invalid', 'true');
        input.setAttribute('aria-describedby', slot.id);
        slot.textContent = problem;
      } else {
        field.removeAttribute('data-invalid');
        input.removeAttribute('aria-invalid');
        input.removeAttribute('aria-describedby');
        slot.textContent = '';
      }
      return !problem;
    }

    fields.forEach(function (input) {
      // Do not scold while the reader is still typing: check on blur, then stay
      // live only for a field that is already flagged.
      input.addEventListener('blur', function () { mark(input); });
      input.addEventListener('input', function () {
        if (input.closest('.form__field').hasAttribute('data-invalid')) mark(input);
      });
    });

    function say(state, text) {
      status.setAttribute('data-state', state);
      status.textContent = text;
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();

      const bad = fields.filter(function (input) { return !mark(input); });
      if (bad.length) {
        say('error', 'Revisa los campos marcados antes de enviar.');
        bad[0].focus();
        return;
      }

      const endpoint = form.getAttribute('action');
      if (!endpoint) {
        // No backend wired yet. Say so plainly rather than show a thank-you
        // screen for a message that was never sent.
        say('error', 'El formulario aún no está conectado a un destino. ' +
                     'Configura el atributo action antes de publicar.');
        return;
      }

      say('pending', 'Enviando…');
      const submit = form.querySelector('button[type="submit"]');
      if (submit) submit.disabled = true;

      fetch(endpoint, { method: 'POST', body: new FormData(form) })
        .then(function (response) {
          if (!response.ok) throw new Error(response.status);
          form.reset();
          say('success', '¡Gracias! Un consultor te contactará para coordinar la reunión.');
        })
        .catch(function () {
          say('error', 'No pudimos enviar tu solicitud. Inténtalo de nuevo o escríbenos por WhatsApp.');
        })
        .then(function () {
          if (submit) submit.disabled = false;
        });
    });
  }

  function init() {
    initStickyHeader();
    initNavToggle();
    initReveal();
    initScrollSpy();
    initCarousels();
    initCounters();
    initMeetingModal();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
