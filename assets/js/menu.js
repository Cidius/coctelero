/* Menu hamburguesa -> sidebar (todas las paginas publicas). */
(function () {
  'use strict';

  var toggle = document.getElementById('nav-toggle');
  var panel = document.getElementById('nav-panel');
  var backdrop = document.getElementById('nav-backdrop');
  var closeBtn = document.getElementById('nav-close');
  if (!toggle || !panel || !backdrop) return;

  function open() {
    panel.classList.add('open');
    backdrop.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }
  function close() {
    panel.classList.remove('open');
    backdrop.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', function () {
    if (panel.classList.contains('open')) close(); else open();
  });
  if (closeBtn) closeBtn.addEventListener('click', close);
  backdrop.addEventListener('click', close);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') close();
  });
})();
