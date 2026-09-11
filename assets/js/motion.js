/*
 * motion.js — web-design-system, copiado tal cual. Reveal de scroll con stagger acotado y
 * estado de header pegajoso. Sin dependencias. Presupuesto: <=15% de los elementos animan
 * (plan §4 fase 4). `prefers-reduced-motion: reduce` apaga todo.
 */
(function () {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var d = document;

  var items = d.querySelectorAll('[data-reveal]');
  if (reduce || !('IntersectionObserver' in window)) {
    items.forEach(function (el) { el.style.opacity = 1; el.style.transform = 'none'; });
  } else {
    items.forEach(function (el) {
      el.style.opacity = 0;
      el.style.transform = 'translateY(18px)';
      el.style.transition = 'opacity 280ms cubic-bezier(.16,1,.3,1), transform 280ms cubic-bezier(.16,1,.3,1)';
    });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        var i = Math.min(+(e.target.dataset.reveal || 0), 6);
        e.target.style.transitionDelay = (i * 70) + 'ms';
        e.target.style.opacity = 1;
        e.target.style.transform = 'none';
        io.unobserve(e.target);
      });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.15 });
    items.forEach(function (el) { io.observe(el); });
  }

  var hdr = d.querySelector('[data-sticky-header]');
  if (hdr) {
    var tick = false;
    window.addEventListener('scroll', function () {
      if (tick) return;
      tick = true;
      requestAnimationFrame(function () {
        hdr.classList.toggle('is-stuck', window.scrollY > 24);
        tick = false;
      });
    }, { passive: true });
  }
  /*
   * Fase 9 — barra pegajosa de CTA: se esconde mientras el formulario está a la vista, para
   * no tapar el campo que se está completando. Sin IntersectionObserver (o sin JS) la barra
   * queda visible, que es el comportamiento correcto por defecto.
   */
  var bar = d.querySelector('[data-cta-bar]');
  var form = d.getElementById('cotizar');
  if (bar && form && 'IntersectionObserver' in window) {
    var barIo = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { bar.classList.toggle('is-hidden', e.isIntersecting); });
    }, { threshold: 0 });
    barIo.observe(form);
  }
})();
