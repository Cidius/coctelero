<?php
declare(strict_types=1);

/**
 * Politica de privacidad. Publica (sin login), la pide Google Cloud
 * para poder publicar la pantalla de consentimiento de OAuth.
 */

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/UserAuth.php';

use App\UserAuth;

use function App\asset;
use function App\boot_errors;
use function App\e;
use function App\pwa_head;
use function App\seo_head;
use function App\url;

boot_errors();

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Política de privacidad · El Coctelero Online</title>
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <?php seo_head(
        'Política de privacidad',
        'Qué datos usa El Coctelero Online y para qué.',
        url('privacidad.php')
    ); ?>
    <?php pwa_head(); ?>
</head>
<body>
<header class="site-header">
    <div class="wrap">
        <h1><a class="brand" href="<?= e(url('/')) ?>">
            <img class="brand-mark" src="<?= e(asset('assets/logo/mark.png')) ?>" alt="">
            El Coctelero Online
        </a></h1>
        <?= UserAuth::headerHtml() ?>
    </div>
</header>

<main class="wrap detail">
    <p class="back"><a href="<?= e(url('/')) ?>">← Volver</a></p>
    <h1>Política de privacidad</h1>
    <p class="muted small">Última actualización: septiembre de 2026.</p>

    <h2>Qué es El Coctelero Online</h2>
    <p>
        El Coctelero Online (coctelero.online) es un buscador de recetas de
        cócteles. Este documento explica qué información se recolecta de
        quienes usan el sitio y para qué se usa.
    </p>

    <h2>Datos que se recolectan</h2>
    <p>
        Para buscar y ver recetas <strong>no hace falta crear una cuenta ni
        entregar ningún dato</strong>. Solo se pide iniciar sesión para
        marcar recetas como favoritas o para escribirle al coctelero.
    </p>
    <p>El inicio de sesión es exclusivamente con Google. Al loguearte, Google nos comparte:</p>
    <ul class="ingredients">
        <li>Tu nombre</li>
        <li>Tu dirección de email</li>
        <li>Tu foto de perfil (si la tenés configurada en Google)</li>
    </ul>
    <p>
        No pedimos ni almacenamos contraseñas: la autenticación la maneja
        Google por completo.
    </p>

    <h2>Para qué se usan</h2>
    <ul class="ingredients">
        <li>Identificarte al volver a entrar y mostrar tu nombre/foto en el menú.</li>
        <li>Guardar tus recetas favoritas, asociadas a tu cuenta.</li>
        <li>Si nos escribís desde el formulario de contacto, tu nombre y email viajan junto con el mensaje para poder responderte.</li>
    </ul>
    <p>Esta información no se vende ni se comparte con terceros con fines publicitarios.</p>

    <h2>Cookies</h2>
    <p>
        Se usan cookies técnicas propias para mantener tu sesión iniciada y
        para no contar dos veces la misma visita a una receta. No se usan
        cookies de rastreo ni de publicidad de terceros.
    </p>

    <h2>Borrar tu cuenta o tus datos</h2>
    <p>
        Escribinos a <a href="mailto:coctelero.online.pna@gmail.com">coctelero.online.pna@gmail.com</a>
        pidiendo la baja y eliminamos tu cuenta, tus favoritos y los mensajes
        asociados.
    </p>

    <h2>Contacto</h2>
    <p>
        Cualquier consulta sobre esta política:
        <a href="mailto:coctelero.online.pna@gmail.com">coctelero.online.pna@gmail.com</a>
    </p>
</main>

<footer class="site-footer">
    <div class="wrap"><a href="<?= e(url('/')) ?>">El machete necesario para cualquier bartender <span class="by">by Cidius</span></a></div>
</footer>

<script src="<?= e(asset('assets/js/menu.js')) ?>" defer></script>
</body>
</html>
