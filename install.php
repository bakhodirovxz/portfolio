<?php
declare(strict_types=1);
/**
 * Bir martalik o'rnatuvchi. config.php yaratadi, jadvallarni quradi va admin hisobini ochadi.
 * O'rnatishdan so'ng config.php mavjud bo'lgani uchun bu sahifa avtomatik yopiladi.
 */

$root = __DIR__;
define('ROOT_DIR', $root);
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/'));
require $root . '/app/db.php';

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

if (is_file($root . '/config.php')) {
    http_response_code(403);
    exit('<!doctype html><meta charset="utf-8"><title>O\'rnatilgan</title><p style="font:16px system-ui;padding:40px">Sayt allaqachon o\'rnatilgan. <a href="' . h(BASE_PATH . '/admin') . '">Admin panelga o\'tish →</a><br><br>Xavfsizlik uchun <code>install.php</code> faylini serverdan o\'chirib tashlashingiz mumkin.</p>');
}

$checks = [
    'PHP 8.1 yoki yangiroq'          => version_compare(PHP_VERSION, '8.1.0', '>='),
    'PDO kengaytmasi'                => extension_loaded('pdo'),
    'pdo_sqlite yoki pdo_mysql'      => extension_loaded('pdo_sqlite') || extension_loaded('pdo_mysql'),
    'GD/fileinfo (rasm tekshiruvi)'  => function_exists('getimagesize'),
    'Asosiy papkaga yozish mumkin'   => is_writable($root),
    'data/ papkasiga yozish mumkin'  => is_writable($root . '/data'),
    'uploads/ papkasiga yozish mumkin' => is_writable($root . '/uploads'),
];
$ok = !in_array(false, $checks, true);

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
$guessUrl = ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_PATH;

$v = [
    'base_url' => $_POST['base_url'] ?? $guessUrl,
    'owner_name' => $_POST['owner_name'] ?? '',
    'site_name' => $_POST['site_name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'username' => $_POST['username'] ?? 'admin',
    'driver' => $_POST['driver'] ?? (extension_loaded('pdo_sqlite') ? 'sqlite' : 'mysql'),
    'db_host' => $_POST['db_host'] ?? 'localhost',
    'db_name' => $_POST['db_name'] ?? '',
    'db_user' => $_POST['db_user'] ?? '',
];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ok) {
    $pass = (string)($_POST['password'] ?? '');
    try {
        if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $v['username'])) {
            throw new RuntimeException('Login 3–40 ta lotin harf/raqamdan iborat bo\'lsin.');
        }
        if (strlen($pass) < 8) {
            throw new RuntimeException('Parol kamida 8 ta belgidan iborat bo\'lsin.');
        }
        if (trim($v['owner_name']) === '') {
            throw new RuntimeException('Ismingizni kiriting.');
        }

        $db = $v['driver'] === 'mysql'
            ? ['driver' => 'mysql', 'host' => $v['db_host'], 'port' => 3306, 'name' => $v['db_name'], 'user' => $v['db_user'], 'pass' => (string)($_POST['db_pass'] ?? '')]
            : ['driver' => 'sqlite', 'path' => $root . '/data/site.sqlite'];

        $pdo = db_connect($db);
        foreach (db_schema($db['driver']) as $sql) {
            $pdo->exec($sql);
        }
        $now = date('Y-m-d H:i:s');
        $pdo->prepare('INSERT INTO users (username, name, password_hash, created_at) VALUES (?, ?, ?, ?)')
            ->execute([$v['username'], trim($v['owner_name']), password_hash($pass, PASSWORD_DEFAULT), $now]);

        $settings = default_settings();
        $settings['owner_name'] = trim($v['owner_name']);
        $settings['site_name'] = trim($v['site_name']) ?: trim($v['owner_name']);
        $settings['email'] = trim($v['email']);
        $ins = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)');
        foreach ($settings as $k => $val) {
            $ins->execute([$k, $val]);
        }

        // config.php — sqlite yo'li nisbiy saqlanadi (papka ko'chirilsa ham ishlaydi)
        $dbExport = $db['driver'] === 'sqlite'
            ? "['driver' => 'sqlite', 'path' => __DIR__ . '/data/site.sqlite']"
            : var_export($db, true);
        $config = "<?php\n// Avtomatik yaratildi: " . $now . "\nreturn [\n"
            . "    'base_url' => " . var_export(rtrim(trim($v['base_url']), '/'), true) . ",\n"
            . "    'timezone' => 'Asia/Tashkent',\n"
            . "    'debug'    => false,\n"
            . "    'app_key'  => " . var_export(bin2hex(random_bytes(32)), true) . ",\n"
            . "    'db'       => " . $dbExport . ",\n"
            . "];\n";
        if (file_put_contents($root . '/config.php', $config, LOCK_EX) === false) {
            throw new RuntimeException('config.php faylini yozib bo\'lmadi.');
        }
        @chmod($root . '/config.php', 0640);
        header('Location: ' . BASE_PATH . '/admin/login');
        exit;
    } catch (Throwable $ex) {
        $error = $ex->getMessage();
        if ($v['driver'] === 'sqlite' && is_file($root . '/data/site.sqlite') && !is_file($root . '/config.php')) {
            @unlink($root . '/data/site.sqlite');
        }
    }
}
?><!doctype html>
<html lang="uz">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>O'rnatish</title>
<link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600&family=IBM+Plex+Mono:wght@400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(BASE_PATH) ?>/assets/css/admin.css">
</head>
<body class="auth install">
<form class="auth__card" method="post">
  <a class="side__logo"><span class="mark"></span><span>Portfolio<small>O'rnatish</small></span></a>
  <h1>O'rnatish</h1>

  <?php if (!$ok): ?>
    <div class="alert alert--error">Server talablari bajarilmagan:</div>
    <ul class="checklist">
      <?php foreach ($checks as $label => $pass): ?>
        <li class="<?= $pass ? 'is-done' : '' ?>"><a><span class="tick"><?= $pass ? '✓' : '' ?></span><?= h($label) ?></a></li>
      <?php endforeach ?>
    </ul>
    <p class="muted small">cPanel → File Manager orqali <code>data</code> va <code>uploads</code> papkalariga 755 ruxsat bering, PHP versiyasini "Select PHP Version" bo'limida 8.1+ qiling.</p>
  <?php else: ?>
    <?php if ($error): ?><div class="alert alert--error"><?= h($error) ?></div><?php endif ?>
    <fieldset>
      <legend>Sayt</legend>
      <label class="field"><span>Sayt manzili</span><input name="base_url" value="<?= h($v['base_url']) ?>" required></label>
      <div class="field-row">
        <label class="field"><span>Ismingiz</span><input name="owner_name" value="<?= h($v['owner_name']) ?>" required placeholder="Asadbek Bakhodirov"></label>
        <label class="field"><span>Sayt nomi</span><input name="site_name" value="<?= h($v['site_name']) ?>" placeholder="Bakhodirov"></label>
      </div>
      <label class="field"><span>Email (so'rovlar shu manzilga keladi)</span><input type="email" name="email" value="<?= h($v['email']) ?>"></label>
    </fieldset>
    <fieldset>
      <legend>Admin hisobi</legend>
      <div class="field-row">
        <label class="field"><span>Login</span><input name="username" value="<?= h($v['username']) ?>" required autocomplete="username"></label>
        <label class="field"><span>Parol (8+ belgi)</span><input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
      </div>
    </fieldset>
    <fieldset>
      <legend>Ma'lumotlar bazasi</legend>
      <label class="field"><span>Turi</span>
        <select name="driver" onchange="document.getElementById('mysql').hidden=this.value!=='mysql'">
          <?php if (extension_loaded('pdo_sqlite')): ?><option value="sqlite"<?= $v['driver'] === 'sqlite' ? ' selected' : '' ?>>SQLite — sozlash shart emas (tavsiya)</option><?php endif ?>
          <?php if (extension_loaded('pdo_mysql')): ?><option value="mysql"<?= $v['driver'] === 'mysql' ? ' selected' : '' ?>>MySQL / MariaDB</option><?php endif ?>
        </select>
      </label>
      <div id="mysql"<?= $v['driver'] === 'mysql' ? '' : ' hidden' ?>>
        <p class="muted small">cPanel → MySQL® Databases: baza va foydalanuvchi yarating, foydalanuvchini bazaga "ALL PRIVILEGES" bilan qo'shing.</p>
        <div class="field-row">
          <label class="field"><span>Host</span><input name="db_host" value="<?= h($v['db_host']) ?>"></label>
          <label class="field"><span>Baza nomi</span><input name="db_name" value="<?= h($v['db_name']) ?>" placeholder="cpuser_portfolio"></label>
        </div>
        <div class="field-row">
          <label class="field"><span>Foydalanuvchi</span><input name="db_user" value="<?= h($v['db_user']) ?>"></label>
          <label class="field"><span>Parol</span><input type="password" name="db_pass"></label>
        </div>
      </div>
    </fieldset>
    <button class="btn btn--primary btn--block" type="submit">O'rnatish</button>
  <?php endif ?>
</form>
</body>
</html>
