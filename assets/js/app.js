/* Recetario de Cocteles - filtrado sin recarga.
   Progressive enhancement: si esto no corre, el form GET sigue funcionando. */
(function () {
  'use strict';

  var app = document.getElementById('app');
  if (!app) return;

  var ENDPOINT = app.dataset.endpoint;
  var DETAIL = app.dataset.detail;
  var grid = document.getElementById('grid');
  var countEl = document.getElementById('result-count');
  var resetEl = document.getElementById('reset');
  var searchEl = app.querySelector('input[type="search"]');
  var form = app.querySelector('form.search');

  // Grupos de un solo valor (chip = radio). El grupo "tag" es multi.
  var SINGLE = ['method', 'volume', 'moment', 'family'];

  // Estado inicial desde la URL.
  var params = new URLSearchParams(location.search);
  var state = {
    q: (params.get('q') || '').trim(),
    tags: params.getAll('tag').reduce(function (acc, v) {
      String(v).split(',').forEach(function (s) {
        s = s.trim().toLowerCase();
        if (s && acc.indexOf(s) === -1) acc.push(s);
      });
      return acc;
    }, [])
  };
  SINGLE.forEach(function (k) { state[k] = params.get(k) || ''; });

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function hasFilter() {
    return state.q !== '' || state.tags.length > 0 ||
      SINGLE.some(function (k) { return state[k] !== ''; });
  }

  function buildQuery() {
    var p = new URLSearchParams();
    if (state.q) p.set('q', state.q);
    SINGLE.forEach(function (k) { if (state[k]) p.set(k, state[k]); });
    state.tags.forEach(function (t) { p.append('tag', t); });
    return p;
  }

  function cardHTML(r) {
    var thumb = r.image_url
      ? '<img src="' + esc(r.image_url) + '" alt="" loading="lazy">'
      : '🍸';
    var bits = [r.method_label];
    if (r.family) bits.push(r.family);
    if (r.glassware) bits.push(r.glassware);
    var meta = esc(bits.filter(Boolean).join(' · '));
    var tags = (r.tags || []).slice(0, 4).map(function (t) {
      return '<span>' + esc(t.name) + '</span>';
    }).join('');
    return '<a class="card" href="' + esc(DETAIL) + '?slug=' + encodeURIComponent(r.slug) + '">' +
      '<div class="thumb">' + thumb + '</div>' +
      '<div class="body">' +
      '<h3>' + esc(r.name) + '</h3>' +
      '<p class="meta">' + meta + '</p>' +
      (tags ? '<div class="card-tags">' + tags + '</div>' : '') +
      '</div></a>';
  }

  function syncChips() {
    app.querySelectorAll('[data-filter] .chip').forEach(function (chip) {
      var group = chip.closest('[data-filter]').dataset.filter;
      var val = chip.dataset.value;
      var on = group === 'tag' ? state.tags.indexOf(val) !== -1 : state[group] === val;
      chip.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
  }

  function render(payload) {
    var data = payload.data || [];
    var total = payload.meta ? payload.meta.total : data.length;
    countEl.textContent = total + ' resultado' + (total === 1 ? '' : 's');
    resetEl.hidden = !hasFilter();
    if (data.length === 0) {
      grid.innerHTML = '<div class="empty"><strong>Sin resultados</strong>' +
        'Probá con otra búsqueda o quitá filtros.</div>';
    } else {
      grid.innerHTML = data.map(cardHTML).join('');
    }
    syncChips();
  }

  var reqId = 0;
  function fetchResults() {
    var mine = ++reqId;
    var qs = buildQuery();
    history.replaceState(null, '', qs.toString() ? '?' + qs.toString() : location.pathname);
    grid.classList.add('is-loading');
    fetch(ENDPOINT + '?' + qs.toString(), { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (payload) {
        if (mine !== reqId) return; // llego una respuesta vieja
        grid.classList.remove('is-loading');
        render(payload);
      })
      .catch(function () {
        grid.classList.remove('is-loading');
      });
  }

  // --- listeners ---
  if (form) form.addEventListener('submit', function (e) { e.preventDefault(); });

  var t;
  if (searchEl) {
    searchEl.addEventListener('input', function () {
      clearTimeout(t);
      t = setTimeout(function () {
        state.q = searchEl.value.trim();
        fetchResults();
      }, 250);
    });
  }

  app.querySelectorAll('[data-filter] .chip').forEach(function (chip) {
    chip.addEventListener('click', function () {
      var group = chip.closest('[data-filter]').dataset.filter;
      var val = chip.dataset.value;
      if (group === 'tag') {
        var i = state.tags.indexOf(val);
        if (i === -1) state.tags.push(val); else state.tags.splice(i, 1);
      } else {
        state[group] = state[group] === val ? '' : val;
      }
      fetchResults();
    });
  });

  resetEl.addEventListener('click', function () {
    state.q = '';
    state.tags = [];
    SINGLE.forEach(function (k) { state[k] = ''; });
    if (searchEl) searchEl.value = '';
    fetchResults();
  });
})();
