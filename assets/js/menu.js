/* receta.php: el link "Volver" apunta a "/" fijo, perdiendo la busqueda,
   filtros o pagina desde donde se entro. Si venimos de esa misma pagina
   (referrer del mismo origen), lo redirigimos ahi en vez de al home pelado.
   Aparte, en su propia IIFE: el 404 de receta.php no tiene sidebar, y el
   menu de abajo corta temprano si no lo encuentra. */
(function () {
  'use strict';

  var back = document.getElementById('back-link');
  if (!back) return;

  try {
    var ref = document.referrer ? new URL(document.referrer) : null;
    if (ref && ref.origin === location.origin && (ref.pathname === '/' || ref.pathname === '/index.php')) {
      back.href = ref.pathname + ref.search;
    }
  } catch (e) { /* referrer invalido o URL no soportado: se queda con "/" */ }
})();

/* Menu hamburguesa -> sidebar (todas las paginas publicas). */
(function () {
  'use strict';

  var toggle = document.getElementById('nav-toggle');
  var panel = document.getElementById('nav-panel');
  var backdrop = document.getElementById('nav-backdrop');
  var closeBtn = document.getElementById('nav-close');
  if (!toggle || !panel || !backdrop) return;

  // El header tiene backdrop-filter, y eso convierte a cualquier hijo con
  // "position: fixed" en fijo respecto del header (no de la ventana). Sacamos
  // el panel y el fondo del <header> para que "fixed" sea relativo a la
  // pantalla, como corresponde a un sidebar.
  document.body.appendChild(backdrop);
  document.body.appendChild(panel);

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

  // Sugerencia de instalar como app (PWA). El boton vive en el sidebar,
  // oculto por defecto (ver UserAuth::headerHtml()); solo puede existir en
  // el sitio publico, el admin no lo imprime.
  var installBtn = document.getElementById('pwa-install-btn');
  if (installBtn) {
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches
      || window.navigator.standalone === true; // iOS instalado
    var isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    var deferredPrompt = null;

    if (!isStandalone) {
      if (isIos) {
        // Safari no tiene beforeinstallprompt: mostramos el boton igual,
        // con instrucciones manuales al tocarlo.
        installBtn.hidden = false;
        installBtn.addEventListener('click', function () {
          alert('Para instalar la app: tocá el ícono Compartir de Safari y elegí "Agregar a pantalla de inicio".');
        });
      } else {
        window.addEventListener('beforeinstallprompt', function (e) {
          e.preventDefault();
          deferredPrompt = e;
          installBtn.hidden = false;
        });
        installBtn.addEventListener('click', function () {
          if (!deferredPrompt) return;
          deferredPrompt.prompt();
          deferredPrompt.userChoice.finally(function () {
            deferredPrompt = null;
            installBtn.hidden = true;
          });
        });
      }
    }

    window.addEventListener('appinstalled', function () {
      installBtn.hidden = true;
    });
  }
})();
