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
  var pageSizeEl = document.getElementById('per-page-select');
  var pageSizeForm = document.getElementById('per-page-form');
  var paginationEl = document.getElementById('pagination');
  var searchEl = app.querySelector('input[type="search"]');
  var form = app.querySelector('form.search');

  var PAGE_SIZES = [10, 20, 50];

  // Grupos de un solo valor (chip = radio).
  var SINGLE = ['method', 'moment', 'family'];
  // Grupos multi-valor (chip = toggle): "tag" -> state.tags, "flavor" -> state.flavors.
  var MULTI = ['tag', 'flavor'];

  // Estado inicial desde la URL.
  var params = new URLSearchParams(location.search);
  var state = {
    q: (params.get('q') || '').trim(),
    page: Math.max(1, parseInt(params.get('page'), 10) || 1),
    perPage: PAGE_SIZES.indexOf(parseInt(params.get('per_page'), 10)) !== -1
      ? parseInt(params.get('per_page'), 10) : PAGE_SIZES[0]
  };
  SINGLE.forEach(function (k) { state[k] = params.get(k) || ''; });
  MULTI.forEach(function (key) {
    // El fallback server-side (index.php) arma "flavor[]=..." para que PHP
    // no pise valores repetidos; contemplamos ambos formatos aca.
    state[key + 's'] = params.getAll(key).concat(params.getAll(key + '[]')).reduce(function (acc, v) {
      String(v).split(',').forEach(function (s) {
        s = s.trim().toLowerCase();
        if (s && acc.indexOf(s) === -1) acc.push(s);
      });
      return acc;
    }, []);
  });

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function hasFilter() {
    return state.q !== '' ||
      MULTI.some(function (k) { return state[k + 's'].length > 0; }) ||
      SINGLE.some(function (k) { return state[k] !== ''; });
  }

  function buildQuery() {
    var p = new URLSearchParams();
    if (state.q) p.set('q', state.q);
    SINGLE.forEach(function (k) { if (state[k]) p.set(k, state[k]); });
    MULTI.forEach(function (k) { state[k + 's'].forEach(function (v) { p.append(k, v); }); });
    if (state.perPage !== PAGE_SIZES[0]) p.set('per_page', state.perPage);
    if (state.page > 1) p.set('page', state.page);
    return p;
  }

  function cardHTML(r) {
    var thumb = r.image_url
      ? '<img src="' + esc(r.image_url) + '" alt="" loading="lazy">'
      : '🍸';
    var bits = [];
    if (r.family) bits.push(r.family);
    if (r.glassware) bits.push(r.glassware);
    var meta = esc(bits.join(' · '));

    var allTags = r.tags || [];
    var spiritNames = allTags.filter(function (t) {
      return !!t.is_spirit;
    }).map(function (t) { return t.name; });
    var spirits = esc(spiritNames.join(', '));
    var charTags = allTags.filter(function (t) {
      return !t.is_spirit;
    }).slice(0, 4).map(function (t) {
      return '<span>' + esc(t.name) + '</span>';
    }).join('');

    return '<a class="card" href="' + esc(DETAIL) + '?slug=' + encodeURIComponent(r.slug) + '">' +
      '<div class="thumb">' + thumb + '</div>' +
      '<div class="body">' +
      '<h3>' + esc(r.name) + '</h3>' +
      (meta ? '<p class="meta">' + meta + '</p>' : '') +
      (spirits ? '<p class="spirits">' + spirits + '</p>' : '') +
      (charTags ? '<div class="card-tags">' + charTags + '</div>' : '') +
      '</div></a>';
  }

  function syncChips() {
    app.querySelectorAll('[data-filter] .chip').forEach(function (chip) {
      var group = chip.closest('[data-filter]').dataset.filter;
      var val = chip.dataset.value;
      var on = MULTI.indexOf(group) !== -1
        ? state[group + 's'].indexOf(val) !== -1
        : state[group] === val;
      chip.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
  }

  // Query string de una pagina puntual, con los filtros vigentes (para los
  // href de la paginacion; el click real lo maneja JS sin recargar).
  function pageHref(n) {
    var p = new URLSearchParams();
    if (state.q) p.set('q', state.q);
    SINGLE.forEach(function (k) { if (state[k]) p.set(k, state[k]); });
    MULTI.forEach(function (k) { state[k + 's'].forEach(function (v) { p.append(k, v); }); });
    if (state.perPage !== PAGE_SIZES[0]) p.set('per_page', state.perPage);
    if (n > 1) p.set('page', n);
    var qs = p.toString();
    return qs ? '?' + qs : location.pathname;
  }

  // Primeras 2, ultimas 2 y la actual, con '...' en los huecos (espejo de
  // $pageTokens en index.php) para que no se pase de una linea en mobile.
  function pageTokens(current, pages) {
    var show = [];
    [1, 2, pages - 1, pages, current].forEach(function (p) {
      if (p >= 1 && p <= pages && show.indexOf(p) === -1) show.push(p);
    });
    show.sort(function (a, b) { return a - b; });
    var tokens = [];
    var prev = null;
    show.forEach(function (p) {
      if (prev !== null && p - prev > 1) tokens.push('...');
      tokens.push(p);
      prev = p;
    });
    return tokens;
  }

  function renderPagination(meta) {
    if (!paginationEl) return;
    var page = (meta && meta.page) || 1;
    var pages = (meta && meta.pages) || 1;
    if (pages <= 1) {
      paginationEl.innerHTML = '';
      return;
    }
    var html = '';
    html += page > 1
      ? '<a class="page-btn" href="' + esc(pageHref(page - 1)) + '" data-page="' + (page - 1) + '" rel="prev" aria-label="Anterior">‹</a>'
      : '<span class="page-btn disabled" aria-hidden="true">‹</span>';
    html += '<div class="page-numbers">';
    pageTokens(page, pages).forEach(function (tok) {
      if (tok === '...') {
        html += '<span class="page-ellipsis">…</span>';
      } else if (tok === page) {
        html += '<span class="page-num current" aria-current="page">' + tok + '</span>';
      } else {
        html += '<a class="page-num" href="' + esc(pageHref(tok)) + '" data-page="' + tok + '">' + tok + '</a>';
      }
    });
    html += '</div>';
    html += page < pages
      ? '<a class="page-btn" href="' + esc(pageHref(page + 1)) + '" data-page="' + (page + 1) + '" rel="next" aria-label="Siguiente">›</a>'
      : '<span class="page-btn disabled" aria-hidden="true">›</span>';
    paginationEl.innerHTML = html;
  }

  function render(payload) {
    var data = payload.data || [];
    var meta = payload.meta || {};
    var total = meta.total != null ? meta.total : data.length;
    countEl.textContent = total + ' resultado' + (total === 1 ? '' : 's');
    resetEl.hidden = !hasFilter();
    if (data.length === 0) {
      grid.innerHTML = '<div class="empty"><strong>Sin resultados</strong>' +
        'Probá con otra búsqueda o quitá filtros.</div>';
    } else {
      grid.innerHTML = data.map(cardHTML).join('');
    }
    renderPagination(meta);
    syncChips();
    updateFilterCount();
  }

  // --- panel de filtros colapsable (mobile y desktop) ---
  var filtersEl = document.getElementById('filters');
  var toggleEl = document.getElementById('filters-toggle');
  var fcountEl = document.getElementById('filters-count');

  function activeFilterCount() {
    return MULTI.reduce(function (n, k) { return n + state[k + 's'].length; }, 0)
      + SINGLE.reduce(function (n, k) { return n + (state[k] ? 1 : 0); }, 0);
  }
  function updateFilterCount() {
    if (!fcountEl) return;
    var n = activeFilterCount();
    fcountEl.textContent = n;
    fcountEl.hidden = n === 0;
  }
  if (toggleEl && filtersEl) {
    toggleEl.hidden = false; // estaba oculto para el caso sin JS
    // Arranca cerrado en mobile; abierto en desktop.
    var startCollapsed = window.matchMedia('(max-width: 700px)').matches;
    filtersEl.classList.toggle('collapsed', startCollapsed);
    toggleEl.setAttribute('aria-expanded', startCollapsed ? 'false' : 'true');

    toggleEl.addEventListener('click', function () {
      var collapsed = filtersEl.classList.toggle('collapsed');
      toggleEl.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
    });
    updateFilterCount();
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
        state.page = 1;
        fetchResults();
      }, 250);
    });
  }

  app.querySelectorAll('[data-filter] .chip').forEach(function (chip) {
    chip.addEventListener('click', function () {
      var group = chip.closest('[data-filter]').dataset.filter;
      var val = chip.dataset.value;
      if (MULTI.indexOf(group) !== -1) {
        var arr = state[group + 's'];
        var i = arr.indexOf(val);
        if (i === -1) arr.push(val); else arr.splice(i, 1);
      } else {
        state[group] = state[group] === val ? '' : val;
      }
      state.page = 1;
      fetchResults();
    });
  });

  resetEl.addEventListener('click', function () {
    state.q = '';
    state.page = 1;
    MULTI.forEach(function (k) { state[k + 's'] = []; });
    SINGLE.forEach(function (k) { state[k] = ''; });
    if (searchEl) searchEl.value = '';
    fetchResults();
  });

  if (pageSizeForm) pageSizeForm.addEventListener('submit', function (e) { e.preventDefault(); });
  if (pageSizeEl) {
    pageSizeEl.addEventListener('change', function () {
      var v = parseInt(pageSizeEl.value, 10);
      state.perPage = PAGE_SIZES.indexOf(v) !== -1 ? v : PAGE_SIZES[0];
      state.page = 1;
      fetchResults();
    });
  }

  if (paginationEl) {
    paginationEl.addEventListener('click', function (e) {
      var a = e.target.closest('a[data-page]');
      if (!a) return;
      e.preventDefault();
      state.page = parseInt(a.dataset.page, 10) || 1;
      fetchResults();
      grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }
})();
