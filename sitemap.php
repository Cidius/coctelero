<?php
declare(strict_types=1);

/**
 * Sitemap XML. Se sirve como /sitemap.xml via .htaccess.
 */

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/Recipe.php';

use App\Recipe;

use function App\boot_errors;
use function App\e;
use function App\url;

boot_errors();

header('Content-Type: application/xml; charset=utf-8');

$rows = Recipe::allForSitemap();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
echo '  <url><loc>' . e(url('/')) . '</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>' . "\n";
foreach ($rows as $r) {
    echo '  <url>'
        . '<loc>' . e(url('receta.php?slug=' . rawurlencode($r['slug']))) . '</loc>'
        . '<lastmod>' . e(substr((string) $r['updated_at'], 0, 10)) . '</lastmod>'
        . '<changefreq>monthly</changefreq>'
        . '</url>' . "\n";
}
echo '</urlset>' . "\n";
