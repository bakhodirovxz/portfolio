<?php
declare(strict_types=1);

/* ---------- Chiqish / URL ---------- */

function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_PATH . '/' . ltrim($path, '/');
}

function site_origin(): string
{
    $cfg = trim((string)($GLOBALS['config']['base_url'] ?? ''));
    if ($cfg !== '') {
        // base_url ichida yo'l bo'lsa ham faqat origin qismini olamiz (yo'l BASE_PATH dan keladi)
        $p = parse_url($cfg);
        if (!empty($p['host'])) {
            return ($p['scheme'] ?? 'https') . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
        }
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    return ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

function abs_url(string $path = ''): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return site_origin() . url($path);
}

function asset(string $path): string
{
    $file = ROOT_DIR . '/assets/' . $path;
    $v = is_file($file) ? substr((string)filemtime($file), -6) : '1';
    return url('assets/' . $path) . '?v=' . $v;
}

function media(?string $path): string
{
    if (!$path) {
        return '';
    }
    return preg_match('#^https?://#i', $path) ? $path : url($path);
}

function redirect(string $path, int $code = 302): never
{
    $to = preg_match('#^https?://#i', $path) ? $path : url($path);
    header('Location: ' . $to, true, $code);
    exit;
}

function current_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $uri = rawurldecode($uri);
    if (BASE_PATH !== '' && str_starts_with($uri, BASE_PATH)) {
        $uri = substr($uri, strlen(BASE_PATH));
    }
    $uri = trim($uri, '/');
    if ($uri === 'index.php') {
        $uri = '';
    }
    return $uri;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function input(string $key, $default = '')
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $v;
}

function client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

/* ---------- Ko'rinishlar ---------- */

function render(string $view, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require VIEW_DIR . '/' . $view . '.php';
    return (string)ob_get_clean();
}

function view(string $view, array $data = [], string $layout = 'site/layout'): never
{
    $content = render($view, $data);
    echo render($layout, $data + ['content' => $content]);
    exit;
}

function not_found(): never
{
    http_response_code(404);
    view('site/404', ['meta' => ['title' => 'Sahifa topilmadi', 'robots' => 'noindex']]);
}

/* ---------- Sozlamalar ---------- */

function settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = default_settings();
        try {
            foreach (db_all('SELECT setting_key, setting_value FROM settings') as $r) {
                $cache[$r['setting_key']] = (string)$r['setting_value'];
            }
        } catch (Throwable $e) {
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $s = settings();
    return isset($s[$key]) && $s[$key] !== '' ? (string)$s[$key] : $default;
}

function save_setting(string $key, string $value): void
{
    $exists = db_val('SELECT COUNT(*) FROM settings WHERE setting_key = ?', [$key]);
    if ($exists) {
        db_exec('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
    } else {
        db_exec('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
    }
}

function social_links(): array
{
    $links = json_decode(setting('social_links', '[]'), true);
    if (!is_array($links)) {
        return [];
    }
    return array_values(array_filter($links, fn($l) => !empty($l['url']) && !preg_match('#^https?://[^/]+/?(in/)?$#', $l['url'])));
}

function lines(string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', $text))));
}

/** Matndagi *so'z* ni kursiv serif aksentga aylantiradi (sarlavhalar uchun). */
function accent(string $text): string
{
    return preg_replace('/\*(.+?)\*/u', '<em>$1</em>', e($text));
}

/* ---------- Matn yordamchilari ---------- */

function slugify(string $text): string
{
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'j','з'=>'z','и'=>'i','й'=>'y',
        'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f',
        'х'=>'x','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sh','ъ'=>'','ы'=>'i','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        'ў'=>'o','қ'=>'q','ғ'=>'g','ҳ'=>'h','ʻ'=>'','ʼ'=>'','‘'=>'','’'=>'',"'"=>'','`'=>'',
    ];
    $text = mb_strtolower($text);
    $text = strtr($text, $map);
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($t !== false) {
            $text = $t;
        }
    }
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim((string)$text, '-');
    return $text !== '' ? substr($text, 0, 120) : 'item-' . substr(bin2hex(random_bytes(3)), 0, 6);
}

function unique_slug(string $table, string $slug, int $ignoreId = 0): string
{
    $base = $slug;
    $i = 2;
    while (db_val("SELECT COUNT(*) FROM $table WHERE slug = ? AND id <> ?", [$slug, $ignoreId])) {
        $slug = $base . '-' . $i++;
    }
    return $slug;
}

function excerpt(?string $text, int $len = 160): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags(markdown((string)$text))));
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    return mb_strlen($text) > $len ? rtrim(mb_substr($text, 0, $len - 1)) . '…' : $text;
}

function reading_time(?string $text): int
{
    $words = str_word_count(strip_tags((string)$text), 0, 'ʻʼ‘’\'ЁёА-яЎўҚқҒғҲҳ');
    return max(1, (int)ceil($words / 200));
}

function fmt_date(?string $date, bool $short = false): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    $months = ['yanvar','fevral','mart','aprel','may','iyun','iyul','avgust','sentabr','oktabr','noyabr','dekabr'];
    $m = $months[(int)date('n', $ts) - 1];
    if ($short) {
        $m = mb_substr($m, 0, 3);
    }
    return date('j', $ts) . '-' . $m . ', ' . date('Y', $ts);
}

function tags_list(?string $tags): array
{
    return array_values(array_filter(array_map('trim', explode(',', (string)$tags))));
}

/* ---------- Xavfsizlik ---------- */

function app_key(): string
{
    return (string)($GLOBALS['config']['app_key'] ?? 'change-me');
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = str_starts_with(site_origin(), 'https://');
    session_name('pf_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => BASE_PATH . '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    start_session();
    $t = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($t) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $t)) {
        http_response_code(419);
        exit('Sessiya muddati tugagan. Sahifani yangilab qayta urinib ko\'ring.');
    }
}

/** Ochiq forma uchun sessiyasiz (cookie'siz) imzolangan token. */
function form_token(): string
{
    $t = (string)time();
    return $t . '.' . hash_hmac('sha256', 'contact|' . $t, app_key());
}

function form_token_valid(string $token, int $minAge = 3, int $maxAge = 7200): bool
{
    [$t, $sig] = array_pad(explode('.', $token, 2), 2, '');
    if (!ctype_digit($t) || !hash_equals(hash_hmac('sha256', 'contact|' . $t, app_key()), $sig)) {
        return false;
    }
    $age = time() - (int)$t;
    return $age >= $minAge && $age <= $maxAge;
}

function flash(string $type, string $msg): void
{
    start_session();
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function flashes(): array
{
    start_session();
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function current_user(): ?array
{
    start_session();
    static $user = false;
    if ($user === false) {
        $user = !empty($_SESSION['uid']) ? db_one('SELECT id, username, name FROM users WHERE id = ?', [$_SESSION['uid']]) : null;
    }
    return $user;
}

function require_admin(): array
{
    $u = current_user();
    if (!$u) {
        redirect('admin/login');
    }
    return $u;
}

/* ---------- Yuklash ---------- */

/**
 * Rasmni xavfsiz yuklaydi. $file — $_FILES elementi. Nisbiy yo'lni qaytaradi yoki xato matnini tashlaydi.
 */
function store_upload(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Fayl yuklanmadi (xato kodi: ' . ($file['error'] ?? '?') . ').');
    }
    if ($file['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('Fayl hajmi 8 MB dan oshmasligi kerak.');
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/avif' => 'avif'];
    $info = @getimagesize($file['tmp_name']);
    $mime = $info['mime'] ?? '';
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Faqat JPG, PNG, WEBP, GIF yoki AVIF rasmlar qabul qilinadi.');
    }
    $dir = UPLOAD_DIR . '/' . date('Y/m');
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('uploads papkasiga yozib bo\'lmadi. Ruxsatlarni tekshiring (755).');
    }
    $name = date('dHis') . '-' . bin2hex(random_bytes(5)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('Faylni saqlab bo\'lmadi.');
    }
    return 'uploads/' . date('Y/m') . '/' . $name;
}

/** Bir nechta fayl kiritilgan input'ni oddiy massivga aylantiradi. */
function files_array(string $field): array
{
    $f = $_FILES[$field] ?? null;
    if (!$f || !is_array($f['name'])) {
        return $f && ($f['error'] ?? 4) !== UPLOAD_ERR_NO_FILE ? [$f] : [];
    }
    $out = [];
    foreach ($f['name'] as $i => $n) {
        if ($f['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $out[] = ['name' => $n, 'type' => $f['type'][$i], 'tmp_name' => $f['tmp_name'][$i], 'error' => $f['error'][$i], 'size' => $f['size'][$i]];
    }
    return $out;
}

function delete_upload(?string $path): void
{
    if ($path && str_starts_with($path, 'uploads/') && !str_contains($path, '..')) {
        $full = ROOT_DIR . '/' . $path;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}

/* ---------- Sahifalash ---------- */

function paginate(int $total, int $perPage, int $page): array
{
    $pages = max(1, (int)ceil($total / max(1, $perPage)));
    $page = min(max(1, $page), $pages);
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'limit' => $perPage, 'total' => $total];
}
