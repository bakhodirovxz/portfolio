<?php
declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));
define('APP_DIR', __DIR__);
define('VIEW_DIR', ROOT_DIR . '/views');
define('UPLOAD_DIR', ROOT_DIR . '/uploads');
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/'));

if (!is_file(ROOT_DIR . '/config.php')) {
    header('Location: ' . BASE_PATH . '/install.php');
    exit;
}

$GLOBALS['config'] = require ROOT_DIR . '/config.php';

date_default_timezone_set($GLOBALS['config']['timezone'] ?? 'Asia/Tashkent');
mb_internal_encoding('UTF-8');

if (!empty($GLOBALS['config']['debug'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

require APP_DIR . '/db.php';
require APP_DIR . '/helpers.php';
require APP_DIR . '/markdown.php';
require APP_DIR . '/icons.php';
