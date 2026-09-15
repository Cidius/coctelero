/* Menu hamburguesa del header (todas las paginas publicas). */
(function () {
  'use strict';

  var toggle = document.getElementById('nav-toggle');
  var panel = document.getElementById('nav-panel');
  if (!toggle || !panel) return;

  function close() {
    panel.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
  }
  function open() {
    panel.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
  }

  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    if (panel.hidden) open(); else close();
  });
  document.addEventListener('click', function (e) {
    if (!panel.hidden && e.target !== toggle && !panel.contains(e.target)) close();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') close();
  });
})();
