/* Formulario de receta: toggle de "método otro" + autocompletado de tags. */
(function () {
  'use strict';

  // --- selects con opción "Otro…" que revela un campo de texto ---
  // <select data-other="id-del-campo" [data-other-value="otro"]>
  document.querySelectorAll('select[data-other]').forEach(function (sel) {
    var target = document.getElementById(sel.dataset.other);
    if (!target) return;
    var trigger = sel.dataset.otherValue || '__otro__';
    var sync = function () { target.hidden = sel.value !== trigger; };
    sel.addEventListener('change', sync);
    sync();
  });

  // --- familia -> autocompleta volumen si está sin clasificar ---
  var famSel = document.getElementById('family-select');
  var volSel = document.getElementById('volume-select');
  if (famSel && volSel) {
    famSel.addEventListener('change', function () {
      var opt = famSel.options[famSel.selectedIndex];
      var typical = opt ? opt.getAttribute('data-volume') : '';
      if (typical && volSel.value === '') volSel.value = typical;
    });
  }

  // --- autocompletado de tags ---
  var input = document.getElementById('tags-input');
  var box = document.getElementById('tags-suggest');
  if (!input || !box) return;

  var endpoint = input.dataset.endpoint;
  var timer, lastQ = null, controller = null;

  function currentSegment() {
    var parts = input.value.split(',');
    return parts[parts.length - 1].trim();
  }

  function replaceSegment(value) {
    var parts = input.value.split(',');
    parts[parts.length - 1] = ' ' + value;
    // dedup simple
    var seen = {}, out = [];
    parts.forEach(function (p) {
      var t = p.trim();
      if (!t) return;
      var key = t.toLowerCase();
      if (seen[key]) return;
      seen[key] = 1;
      out.push(t);
    });
    input.value = out.join(', ') + ', ';
    hide();
    input.focus();
  }

  function hide() { box.hidden = true; box.innerHTML = ''; }

  function render(items) {
    if (!items.length) { hide(); return; }
    box.innerHTML = items.map(function (t) {
      return '<button type="button" data-name="' + t.name.replace(/"/g, '&quot;') + '">' +
        t.name + '</button>';
    }).join('');
    box.hidden = false;
  }

  function lookup() {
    var q = currentSegment();
    if (q === lastQ) return;
    lastQ = q;
    if (controller) controller.abort();
    controller = new AbortController();
    fetch(endpoint + '?q=' + encodeURIComponent(q), { signal: controller.signal, headers: { Accept: 'application/json' } })
      .then(function (r) { return r.ok ? r.json() : []; })
      .then(render)
      .catch(function () {});
  }

  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(lookup, 180);
  });
  input.addEventListener('focus', lookup);
  input.addEventListener('blur', function () { setTimeout(hide, 150); });

  box.addEventListener('click', function (e) {
    var btn = e.target.closest('button[data-name]');
    if (btn) replaceSegment(btn.dataset.name);
  });
})();
