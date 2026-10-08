<?php
declare(strict_types=1);

/**
 * PDO ulanish. SQLite (standart, sozlash shart emas) yoki MySQL.
 */
function db_connect(array $cfg): PDO
{
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    if (($cfg['driver'] ?? 'sqlite') === 'mysql') {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $cfg['host'] ?? 'localhost', (int)($cfg['port'] ?? 3306), $cfg['name'] ?? '');
        return new PDO($dsn, $cfg['user'] ?? '', $cfg['pass'] ?? '', $opts);
    }
    $path = $cfg['path'] ?? (ROOT_DIR . '/data/site.sqlite');
    $pdo = new PDO('sqlite:' . $path, null, null, $opts);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    return $pdo;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = db_connect($GLOBALS['config']['db']);
    }
    return $pdo;
}

function db_all(string $sql, array $params = []): array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    $row = $st->fetch();
    return $row === false ? null : $row;
}

function db_val(string $sql, array $params = [])
{
    $st = db()->prepare($sql);
    $st->execute($params);
    $v = $st->fetchColumn();
    return $v === false ? null : $v;
}

function db_exec(string $sql, array $params = []): int
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->rowCount();
}

function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table,
        implode(', ', $cols), implode(', ', array_map(fn($c) => ':' . $c, $cols)));
    db()->prepare($sql)->execute($data);
    return (int)db()->lastInsertId();
}

function db_update(string $table, int $id, array $data): void
{
    $set = implode(', ', array_map(fn($c) => "$c = :$c", array_keys($data)));
    $data['id'] = $id;
    db()->prepare("UPDATE $table SET $set WHERE id = :id")->execute($data);
}

/**
 * Jadval sxemasi — ikkala drayver uchun.
 */
function db_schema(string $driver): array
{
    $mysql = $driver === 'mysql';
    $pk    = $mysql ? 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $str   = $mysql ? 'VARCHAR(191)' : 'TEXT';
    $text  = $mysql ? 'MEDIUMTEXT' : 'TEXT';
    $tail  = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';

    return [
        "CREATE TABLE IF NOT EXISTS users (
            id $pk,
            username $str NOT NULL UNIQUE,
            name $str NOT NULL DEFAULT '',
            password_hash $str NOT NULL,
            created_at $str NOT NULL
        )$tail",
        "CREATE TABLE IF NOT EXISTS settings (
            setting_key $str NOT NULL PRIMARY KEY,
            setting_value $text
        )$tail",
        "CREATE TABLE IF NOT EXISTS works (
            id $pk,
            title $str NOT NULL,
            slug $str NOT NULL UNIQUE,
            category $str NOT NULL DEFAULT '',
            summary $text,
            content $text,
            cover $str NOT NULL DEFAULT '',
            gallery $text,
            client $str NOT NULL DEFAULT '',
            year $str NOT NULL DEFAULT '',
            project_url $str NOT NULL DEFAULT '',
            tags $str NOT NULL DEFAULT '',
            featured INTEGER NOT NULL DEFAULT 0,
            status $str NOT NULL DEFAULT 'draft',
            sort_order INTEGER NOT NULL DEFAULT 0,
            views INTEGER NOT NULL DEFAULT 0,
            meta_title $str NOT NULL DEFAULT '',
            meta_description $text,
            created_at $str NOT NULL,
            updated_at $str NOT NULL
        )$tail",
        "CREATE TABLE IF NOT EXISTS posts (
            id $pk,
            title $str NOT NULL,
            slug $str NOT NULL UNIQUE,
            excerpt $text,
            content $text,
            cover $str NOT NULL DEFAULT '',
            tags $str NOT NULL DEFAULT '',
            status $str NOT NULL DEFAULT 'draft',
            views INTEGER NOT NULL DEFAULT 0,
            meta_title $str NOT NULL DEFAULT '',
            meta_description $text,
            published_at $str NOT NULL,
            created_at $str NOT NULL,
            updated_at $str NOT NULL
        )$tail",
        "CREATE TABLE IF NOT EXISTS messages (
            id $pk,
            name $str NOT NULL,
            email $str NOT NULL,
            phone $str NOT NULL DEFAULT '',
            subject $str NOT NULL DEFAULT '',
            budget $str NOT NULL DEFAULT '',
            message $text,
            ip $str NOT NULL DEFAULT '',
            user_agent $str NOT NULL DEFAULT '',
            is_read INTEGER NOT NULL DEFAULT 0,
            created_at $str NOT NULL
        )$tail",
        "CREATE TABLE IF NOT EXISTS login_attempts (
            id $pk,
            ip $str NOT NULL,
            created_at $str NOT NULL
        )$tail",
    ];
}

function default_settings(): array
{
    return [
        'site_name'        => 'Bakhodirov',
        'owner_name'       => 'Bakhodirov',
        'role'             => 'Dizayner va veb-dasturchi',
        'hero_title'       => "Veb-saytlar, interfeyslar va vizual identitet yarataman — *ortiqcha bezaksiz.*",
        'hero_text'        => "Kichik bizneslar va brendlar bilan g'oyadan ishga tushirishgacha ishlayman: tuzilma, dizayn, dasturlash va SEO bir qo'lda.",
        'availability'     => "Yangi loyihalarga ochiq",
        'about_title'      => "Haqida",
        'about_text'       => "Salom! Men raqamli dizayn va veb-dasturlash bilan shug'ullanaman. Maqsadim — foydalanuvchiga qulay, biznesga esa natija beradigan mahsulotlar yaratish.\n\nIshlarim va tajribam bilan quyida tanishishingiz mumkin.",
        'services'         => "Veb-sayt dizayni va ishlab chiqish\nUI/UX dizayn\nBrending va vizual identitet\nSEO va tezlikni optimallashtirish",
        'skills'           => "Figma, UI/UX, WordPress, PHP, JavaScript, HTML/CSS, SEO, Branding",
        'stats'            => '',
        'email'            => '',
        'phone'            => '',
        'location'         => "Toshkent, O'zbekiston",
        'avatar'           => '',
        'resume_url'       => '',
        'social_links'     => json_encode([
            ['platform' => 'telegram', 'url' => 'https://t.me/'],
            ['platform' => 'instagram', 'url' => 'https://instagram.com/'],
            ['platform' => 'linkedin', 'url' => 'https://linkedin.com/in/'],
        ]),
        'meta_title'       => '',
        'meta_description' => "Portfolio: veb-saytlar, UI/UX dizayn va brending loyihalari. Ishlar, maqolalar va bog'lanish.",
        'meta_keywords'    => '',
        'og_image'         => '',
        'ga_id'            => '',
        'gsc_verification' => '',
        'yandex_verification' => '',
        'head_code'        => '',
        'body_code'        => '',
        'notify_email'     => '',
        'contact_title'    => "Loyihangiz bormi? *Yozing.*",
        'contact_text'     => "Loyiha, hamkorlik yoki savol bo'yicha — odatda bir ish kuni ichida javob beraman.",
        'footer_text'      => '',
        'posts_per_page'   => '9',
    ];
}
