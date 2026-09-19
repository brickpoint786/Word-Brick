/* BrickPoint front-end behaviour: sticky header, mobile drawer, reveal animations,
   product gallery, quotation form (AJAX). Vanilla JS, no dependencies. */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); }
  }

  /* Sticky header shadow */
  function initHeader() {
    var headers = document.querySelectorAll('.bp-header');
    if (!headers.length) { return; }
    var update = function () {
      var scrolled = window.scrollY > 10;
      headers.forEach(function (h) { h.classList.toggle('scrolled', scrolled); });
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
  }

  /* Mobile drawer */
  function initDrawer() {
    document.addEventListener('click', function (e) {
      var toggle = e.target.closest('[data-bp-drawer-open]');
      var close = e.target.closest('[data-bp-drawer-close]');
      if (toggle) {
        e.preventDefault();
        var id = toggle.getAttribute('data-bp-drawer-open');
        var drawer = document.getElementById(id) || document.querySelector('.bp-drawer');
        if (drawer) {
          drawer.classList.add('open');
          document.body.classList.add('bp-drawer-open');
          drawer.setAttribute('aria-hidden', 'false');
          var first = drawer.querySelector('a, button');
          if (first) { first.focus(); }
        }
      }
      if (close) {
        e.preventDefault();
        var d = close.closest('.bp-drawer');
        if (d) {
          d.classList.remove('open');
          d.setAttribute('aria-hidden', 'true');
          document.body.classList.remove('bp-drawer-open');
        }
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.bp-drawer.open').forEach(function (d) {
          d.classList.remove('open');
          d.setAttribute('aria-hidden', 'true');
        });
        document.body.classList.remove('bp-drawer-open');
      }
    });
    /* close drawer when a link inside it is clicked */
    document.addEventListener('click', function (e) {
      var link = e.target.closest('.bp-drawer a');
      if (link && !link.closest('.bp-drawer-head')) {
        var d = link.closest('.bp-drawer');
        d.classList.remove('open');
        document.body.classList.remove('bp-drawer-open');
      }
    });
  }

  /* Scroll reveal */
  function initReveal() {
    var items = document.querySelectorAll('.bp-reveal');
    if (!items.length) { return; }
    if (!('IntersectionObserver' in window)) {
      items.forEach(function (i) { i.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    items.forEach(function (i) { io.observe(i); });
  }

  /* Product gallery thumbnails */
  function initGallery() {
    /* Product gallery thumbnails (.bp-thumb buttons → #bp-main-image / .bp-img-main img) */
    document.querySelectorAll('.bp-product-gallery').forEach(function (g) {
      if (g.dataset.bpInit) { return; }
      g.dataset.bpInit = '1';
      var main = g.querySelector('.bp-img-main img');
      g.querySelectorAll('.bp-thumb').forEach(function (t) {
        t.addEventListener('click', function () {
          var src = t.getAttribute('data-src');
          if (!main || !src) { return; }
          main.src = src;
          main.removeAttribute('srcset');
          g.querySelectorAll('.bp-thumb').forEach(function (x) { x.classList.remove('active'); });
          t.classList.add('active');
        });
      });
    });
    document.querySelectorAll('.bp-gallery').forEach(function (g) {
      var main = g.querySelector('.bp-gallery-main img');
      g.querySelectorAll('.bp-gallery-thumbs img').forEach(function (t) {
        t.addEventListener('click', function () {
          if (!main) { return; }
          var src = t.getAttribute('data-full') || t.src;
          main.src = src;
          if (t.srcset) { main.removeAttribute('srcset'); }
          g.querySelectorAll('.bp-gallery-thumbs img').forEach(function (x) { x.style.borderColor = ''; });
          t.style.borderColor = '#ea580c';
        });
      });
    });
  }

  /* Hero play button → unmute / open modal-less controls */
  function initHeroPlay() {
    document.querySelectorAll('[data-bp-play]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        var target = document.querySelector(btn.getAttribute('data-bp-play'));
        if (!target) { return; }
        e.preventDefault();
        if (target.tagName === 'VIDEO') {
          target.muted = false;
          target.controls = true;
          target.play();
          target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    });
  }

  /* Quotation form */
  function initQuoteForm() {
    document.querySelectorAll('.bp-quote-form').forEach(function (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('button[type=submit]');
        var err = form.querySelector('.bp-form-error');
        var wrap = form.closest('.bp-quote-form-wrap');
        var success = wrap ? wrap.querySelector('.bp-form-success') : null;
        var name = form.querySelector('[name=name]');
        var phone = form.querySelector('[name=phone]');
        if (!name.value.trim() || !phone.value.trim()) {
          if (err) { err.hidden = false; err.textContent = 'Please enter your name and phone number.'; }
          return;
        }
        if (err) { err.hidden = true; }
        btn.disabled = true;
        var label = btn.querySelector('span');
        var old = label ? label.textContent : '';
        if (label) { label.textContent = 'Sending…'; }
        var data = new FormData(form);
        var url = (window.brickpointData && window.brickpointData.ajaxUrl) || form.getAttribute('action') || '/wp-admin/admin-ajax.php';
        fetch(url, { method: 'POST', body: data, credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (json) {
            if (json && json.success) {
              form.hidden = true;
              if (success) { success.hidden = false; }
            } else {
              throw new Error((json && json.data && json.data.message) || 'error');
            }
          })
          .catch(function () {
            if (err) { err.hidden = false; }
          })
          .finally(function () {
            btn.disabled = false;
            if (label) { label.textContent = old; }
          });
      });
    });
  }

  /* Smooth anchor scroll offset for sticky header */
  function initAnchors() {
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href^="#"]');
      if (!a || a.getAttribute('href') === '#') { return; }
      var el = document.querySelector(a.getAttribute('href'));
      if (!el) { return; }
      e.preventDefault();
      var top = el.getBoundingClientRect().top + window.scrollY - 80;
      window.scrollTo({ top: top, behavior: 'smooth' });
    });
  }

  function init() {
    initHeader();
    initDrawer();
    initReveal();
    initGallery();
    initHeroPlay();
    initQuoteForm();
    initAnchors();
  }

  ready(init);
  /* Re-init inside Elementor editor / after AJAX loaded widgets */
  window.addEventListener('elementor/frontend/init', function () {
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
      window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function () {
        initReveal(); initGallery(); initQuoteForm(); initHeroPlay();
      });
    }
  });
})();
