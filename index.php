<?php
declare(strict_types=1);

// Lokal ishlab chiqish: php -S localhost:8000 index.php
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file) && preg_match('#^' . preg_quote(__DIR__, '#') . '/(assets|uploads)/#', $file)) {
        return false;
    }
    if (basename($file) === 'install.php') {
        require __DIR__ . '/install.php';
        return;
    }
}

require __DIR__ . '/app/bootstrap.php';
require APP_DIR . '/site.php';
require APP_DIR . '/admin.php';

$path = current_path();
$seg  = $path === '' ? [] : explode('/', $path);

try {
    if (($seg[0] ?? '') === 'admin') {
        admin_route(array_slice($seg, 1));
    }

    switch (true) {
        case $path === '':
            site_home();
        case $path === 'ishlar':
            site_works();
        case ($seg[0] ?? '') === 'ishlar' && count($seg) === 2:
            site_work($seg[1]);
        case $path === 'blog':
            site_blog();
        case ($seg[0] ?? '') === 'blog' && count($seg) === 2:
            site_post($seg[1]);
        case $path === 'aloqa':
            is_post() ? site_contact_submit() : site_contact();
        case $path === 'sitemap.xml':
            site_sitemap();
        case $path === 'robots.txt':
            site_robots();
        case $path === 'feed.xml':
            site_feed();
        default:
            not_found();
    }
} catch (Throwable $ex) {
    error_log('[portfolio] ' . $ex->getMessage() . ' @ ' . $ex->getFile() . ':' . $ex->getLine());
    http_response_code(500);
    if (!empty($GLOBALS['config']['debug'])) {
        echo '<pre>' . e((string)$ex) . '</pre>';
    } else {
        echo '<h1>Server xatosi</h1><p>Birozdan so\'ng qayta urinib ko\'ring.</p>';
    }
}
