<?php
declare(strict_types=1);

function admin_view(string $view, array $data = []): never
{
    header('X-Robots-Tag: noindex, nofollow');
    header('Cache-Control: no-store');
    view('admin/' . $view, $data + ['unread' => (int)db_val('SELECT COUNT(*) FROM messages WHERE is_read = 0')], 'admin/layout');
}

function admin_route(array $seg): never
{
    start_session();
    $a = $seg[0] ?? '';
    $id = isset($seg[1]) && ctype_digit($seg[1]) ? (int)$seg[1] : 0;
    $act = $seg[2] ?? ($id ? '' : ($seg[1] ?? ''));

    if ($a === 'login') {
        admin_login();
    }
    if ($a === 'logout' && is_post()) {
        csrf_check();
        $_SESSION = [];
        session_destroy();
        redirect('admin/login');
    }

    require_admin();
    if (is_post()) {
        csrf_check();
    }

    switch ($a) {
        case '':
            admin_dashboard();
        case 'works':
        case 'posts':
            if ($act === 'new' || $act === 'edit') {
                is_post() ? admin_content_save($a, $id) : admin_content_form($a, $id);
            }
            if ($act === 'delete' && is_post()) {
                admin_content_delete($a, $id);
            }
            admin_content_list($a);
        case 'messages':
            if ($id && is_post()) {
                admin_message_action($id, $act);
            }
            $id ? admin_message($id) : admin_messages();
        case 'settings':
            is_post() ? admin_settings_save() : admin_settings();
        case 'account':
            is_post() ? admin_account_save() : admin_view('account', ['title' => 'Hisob']);
        case 'upload':
            admin_upload();
        case 'preview':
            header('Content-Type: text/html; charset=UTF-8');
            echo markdown((string)($_POST['content'] ?? ''));
            exit;
    }
    not_found();
}

/* ---------- Kirish ---------- */

function admin_login(): never
{
    if (current_user()) {
        redirect('admin');
    }
    $error = '';
    if (is_post()) {
        csrf_check();
        $ip = client_ip();
        $since = date('Y-m-d H:i:s', time() - 900);
        db_exec('DELETE FROM login_attempts WHERE created_at < ?', [date('Y-m-d H:i:s', time() - 86400)]);
        $attempts = (int)db_val('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND created_at > ?', [$ip, $since]);
        if ($attempts >= 5) {
            $error = "Juda ko'p urinish. 15 daqiqadan so'ng qayta urinib ko'ring.";
        } else {
            $user = db_one('SELECT * FROM users WHERE username = ?', [(string)input('username')]);
            if ($user && password_verify((string)($_POST['password'] ?? ''), $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['uid'] = (int)$user['id'];
                db_exec('DELETE FROM login_attempts WHERE ip = ?', [$ip]);
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    db_update('users', (int)$user['id'], ['password_hash' => password_hash((string)$_POST['password'], PASSWORD_DEFAULT)]);
                }
                redirect('admin');
            }
            db_insert('login_attempts', ['ip' => $ip, 'created_at' => now()]);
            usleep(400000);
            $error = "Login yoki parol noto'g'ri.";
        }
    }
    header('X-Robots-Tag: noindex, nofollow');
    echo render('admin/login', ['error' => $error]);
    exit;
}

/* ---------- Boshqaruv paneli ---------- */

function admin_dashboard(): never
{
    $stats = [
        'works'    => (int)db_val('SELECT COUNT(*) FROM works'),
        'posts'    => (int)db_val('SELECT COUNT(*) FROM posts'),
        'messages' => (int)db_val('SELECT COUNT(*) FROM messages'),
        'views'    => (int)db_val('SELECT COALESCE(SUM(views),0) FROM works') + (int)db_val('SELECT COALESCE(SUM(views),0) FROM posts'),
    ];
    $checklist = [
        ['Ism, kasb va bosh sahifa matni', setting('owner_name') !== 'Bakhodirov' || setting('email') !== '', 'settings#umumiy'],
        ['Email manzil', setting('email') !== '', 'settings#aloqa'],
        ['Ijtimoiy tarmoqlar', (bool)social_links(), 'settings#tarmoqlar'],
        ['Google Search Console tasdiqlash kodi', setting('gsc_verification') !== '', 'settings#seo'],
        ['Google Analytics (GA4) ID', setting('ga_id') !== '', 'settings#seo'],
        ['Birinchi ish qo\'shildi', $stats['works'] > 0, 'works/new'],
        ['Birinchi maqola yozildi', $stats['posts'] > 0, 'posts/new'],
    ];
    admin_view('dashboard', [
        'title' => 'Boshqaruv paneli',
        'stats' => $stats,
        'checklist' => $checklist,
        'messages' => db_all('SELECT * FROM messages ORDER BY id DESC LIMIT 5'),
        'topWorks' => db_all('SELECT id, title, slug, views FROM works ORDER BY views DESC LIMIT 5'),
        'topPosts' => db_all('SELECT id, title, slug, views FROM posts ORDER BY views DESC LIMIT 5'),
    ]);
}

/* ---------- Ishlar va maqolalar ---------- */

function admin_content_list(string $type): never
{
    $q = (string)input('q');
    $params = [];
    $where = '1=1';
    if ($q !== '') {
        $where = 'title LIKE ?';
        $params[] = '%' . $q . '%';
    }
    $order = $type === 'works' ? 'sort_order ASC, id DESC' : 'published_at DESC';
    admin_view('content-list', [
        'type' => $type,
        'title' => $type === 'works' ? 'Ishlar' : 'Maqolalar',
        'items' => db_all("SELECT * FROM $type WHERE $where ORDER BY $order", $params),
        'q' => $q,
    ]);
}

function admin_content_form(string $type, int $id, ?array $item = null, array $errors = []): never
{
    if ($item === null) {
        $item = $id ? db_one("SELECT * FROM $type WHERE id = ?", [$id]) : null;
        if ($id && !$item) {
            not_found();
        }
        $item ??= [
            'id' => 0, 'title' => '', 'slug' => '', 'content' => '', 'cover' => '', 'tags' => '', 'status' => 'published',
            'meta_title' => '', 'meta_description' => '',
            // works
            'category' => '', 'summary' => '', 'gallery' => '[]', 'client' => '', 'year' => date('Y'), 'project_url' => '', 'featured' => 0,
            'sort_order' => 0,
            // posts
            'excerpt' => '', 'published_at' => now(),
        ];
    }
    admin_view('content-form', [
        'type' => $type,
        'title' => $type === 'works' ? ($id ? 'Ishni tahrirlash' : 'Yangi ish') : ($id ? 'Maqolani tahrirlash' : 'Yangi maqola'),
        'item' => $item,
        'errors' => $errors,
        'categories' => $type === 'works' ? array_column(db_all("SELECT DISTINCT category FROM works WHERE category <> ''"), 'category') : [],
    ]);
}

function admin_content_save(string $type, int $id): never
{
    $existing = $id ? db_one("SELECT * FROM $type WHERE id = ?", [$id]) : null;
    if ($id && !$existing) {
        not_found();
    }
    $title = (string)input('title');
    $slug = slugify((string)input('slug') ?: $title);
    $data = [
        'title' => mb_substr($title, 0, 190),
        'slug' => unique_slug($type, $slug, $id),
        'content' => (string)($_POST['content'] ?? ''),
        'tags' => mb_substr(implode(', ', tags_list((string)input('tags'))), 0, 190),
        'status' => input('status') === 'draft' ? 'draft' : 'published',
        'meta_title' => mb_substr((string)input('meta_title'), 0, 190),
        'meta_description' => mb_substr((string)input('meta_description'), 0, 320),
        'cover' => $existing['cover'] ?? '',
        'updated_at' => now(),
    ];
    if ($type === 'works') {
        $data += [
            'category' => mb_substr((string)input('category'), 0, 80),
            'summary' => mb_substr((string)input('summary'), 0, 500),
            'client' => mb_substr((string)input('client'), 0, 120),
            'year' => mb_substr((string)input('year'), 0, 20),
            'project_url' => preg_match('#^https?://#i', (string)input('project_url')) ? mb_substr((string)input('project_url'), 0, 190) : '',
            'featured' => input('featured') ? 1 : 0,
            'sort_order' => (int)input('sort_order', 0),
        ];
    } else {
        $pub = strtotime((string)input('published_at')) ?: time();
        $data += [
            'excerpt' => mb_substr((string)input('excerpt'), 0, 500),
            'published_at' => date('Y-m-d H:i:s', $pub),
        ];
    }

    $errors = [];
    if (mb_strlen($data['title']) < 2) {
        $errors[] = 'Sarlavha kiritilishi shart.';
    }

    // Muqova
    try {
        if (input('remove_cover')) {
            delete_upload($data['cover']);
            $data['cover'] = '';
        }
        if ($f = files_array('cover')) {
            $new = store_upload($f[0]);
            delete_upload($data['cover']);
            $data['cover'] = $new;
        }
    } catch (RuntimeException $ex) {
        $errors[] = 'Muqova: ' . $ex->getMessage();
    }

    // Galereya (faqat ishlar)
    if ($type === 'works') {
        $gallery = json_decode((string)($existing['gallery'] ?? '[]'), true) ?: [];
        $keep = (array)($_POST['gallery_keep'] ?? []);
        foreach ($gallery as $img) {
            if (!in_array($img, $keep, true)) {
                delete_upload($img);
            }
        }
        // Tartibni formadagi kabi saqlaymiz
        $gallery = array_values(array_filter($keep, fn($k) => in_array($k, $gallery, true)));
        foreach (files_array('gallery') as $f) {
            try {
                $gallery[] = store_upload($f);
            } catch (RuntimeException $ex) {
                $errors[] = 'Galereya (' . e($f['name']) . '): ' . $ex->getMessage();
            }
        }
        $data['gallery'] = json_encode($gallery, JSON_UNESCAPED_SLASHES);
    }

    if ($errors) {
        admin_content_form($type, $id, array_merge($existing ?? ['id' => 0], $data), $errors);
    }

    if ($id) {
        db_update($type, $id, $data);
        flash('success', 'O\'zgarishlar saqlandi.');
    } else {
        $data['created_at'] = now();
        $id = db_insert($type, $data);
        flash('success', $type === 'works' ? 'Ish qo\'shildi.' : 'Maqola qo\'shildi.');
    }
    redirect("admin/$type/$id/edit");
}

function admin_content_delete(string $type, int $id): never
{
    $item = db_one("SELECT * FROM $type WHERE id = ?", [$id]);
    if ($item) {
        delete_upload($item['cover']);
        foreach (json_decode((string)($item['gallery'] ?? '[]'), true) ?: [] as $img) {
            delete_upload($img);
        }
        db_exec("DELETE FROM $type WHERE id = ?", [$id]);
        flash('success', '«' . $item['title'] . '» o\'chirildi.');
    }
    redirect("admin/$type");
}

/* ---------- Xabarlar ---------- */

function admin_messages(): never
{
    $filter = (string)input('f');
    $where = $filter === 'unread' ? 'WHERE is_read = 0' : '';
    $total = (int)db_val("SELECT COUNT(*) FROM messages $where");
    $pg = paginate($total, 30, (int)input('p', 1));
    admin_view('messages', [
        'title' => 'So\'rovlar',
        'items' => db_all("SELECT * FROM messages $where ORDER BY id DESC LIMIT {$pg['limit']} OFFSET {$pg['offset']}"),
        'filter' => $filter,
        'pg' => $pg,
    ]);
}

function admin_message(int $id): never
{
    $m = db_one('SELECT * FROM messages WHERE id = ?', [$id]);
    if (!$m) {
        not_found();
    }
    if (!$m['is_read']) {
        db_exec('UPDATE messages SET is_read = 1 WHERE id = ?', [$id]);
    }
    admin_view('message', ['title' => 'So\'rov #' . $id, 'm' => $m]);
}

function admin_message_action(int $id, string $act): never
{
    if ($act === 'delete') {
        db_exec('DELETE FROM messages WHERE id = ?', [$id]);
        flash('success', 'So\'rov o\'chirildi.');
        redirect('admin/messages');
    }
    if ($act === 'unread') {
        db_exec('UPDATE messages SET is_read = 0 WHERE id = ?', [$id]);
        flash('success', 'O\'qilmagan deb belgilandi.');
        redirect('admin/messages');
    }
    redirect('admin/messages/' . $id);
}

/* ---------- Sozlamalar ---------- */

function settings_fields(): array
{
    return [
        'site_name', 'owner_name', 'role', 'hero_title', 'hero_text', 'availability', 'about_title', 'about_text',
        'services', 'skills', 'stats', 'email', 'phone', 'location', 'resume_url',
        'meta_title', 'meta_description', 'meta_keywords', 'ga_id', 'gsc_verification', 'yandex_verification',
        'head_code', 'body_code', 'notify_email', 'contact_title', 'contact_text', 'footer_text', 'posts_per_page',
    ];
}

function admin_settings(): never
{
    admin_view('settings', ['title' => 'Sozlamalar', 's' => settings()]);
}

function admin_settings_save(): never
{
    foreach (settings_fields() as $k) {
        if (array_key_exists($k, $_POST)) {
            save_setting($k, trim((string)$_POST[$k]));
        }
    }
    // GSC: agar butun meta teg joylashtirilgan bo'lsa, faqat content qiymatini olamiz
    foreach (['gsc_verification', 'yandex_verification'] as $k) {
        if (preg_match('/content=["\']([^"\']+)["\']/', (string)($_POST[$k] ?? ''), $m)) {
            save_setting($k, $m[1]);
        }
    }

    // Ijtimoiy tarmoqlar
    $links = [];
    $platforms = (array)($_POST['social_platform'] ?? []);
    $urls = (array)($_POST['social_url'] ?? []);
    foreach ($platforms as $i => $p) {
        $u = trim((string)($urls[$i] ?? ''));
        if ($u === '') {
            continue;
        }
        if ($p === 'email' && !str_starts_with($u, 'mailto:')) {
            $u = 'mailto:' . $u;
        } elseif ($p !== 'email' && !preg_match('#^https?://#i', $u)) {
            $u = 'https://' . ltrim($u, '/');
        }
        $links[] = ['platform' => array_key_exists($p, social_platforms()) ? $p : 'website', 'url' => $u];
    }
    save_setting('social_links', json_encode($links, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Rasmlar
    foreach (['avatar', 'og_image'] as $k) {
        try {
            if (input('remove_' . $k)) {
                delete_upload(setting($k));
                save_setting($k, '');
            }
            if ($f = files_array($k)) {
                $path = store_upload($f[0]);
                delete_upload(setting($k));
                save_setting($k, $path);
            }
        } catch (RuntimeException $ex) {
            flash('error', $ex->getMessage());
        }
    }

    flash('success', 'Sozlamalar saqlandi.');
    redirect('admin/settings' . (preg_match('/^[a-z]+$/', (string)input('_tab')) ? '#' . input('_tab') : ''));
}

function admin_account_save(): never
{
    $u = current_user();
    $user = db_one('SELECT * FROM users WHERE id = ?', [$u['id']]);
    if (!password_verify((string)($_POST['current'] ?? ''), $user['password_hash'])) {
        flash('error', 'Joriy parol noto\'g\'ri.');
        redirect('admin/account');
    }
    $data = [];
    $username = (string)input('username');
    if ($username !== '' && $username !== $user['username']) {
        if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $username)) {
            flash('error', 'Login 3–40 ta lotin harf, raqam yoki _ . - dan iborat bo\'lsin.');
            redirect('admin/account');
        }
        $data['username'] = $username;
    }
    $new = (string)($_POST['password'] ?? '');
    if ($new !== '') {
        if (mb_strlen($new) < 8) {
            flash('error', 'Yangi parol kamida 8 ta belgidan iborat bo\'lsin.');
            redirect('admin/account');
        }
        if ($new !== (string)($_POST['password2'] ?? '')) {
            flash('error', 'Parollar mos kelmadi.');
            redirect('admin/account');
        }
        $data['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
    }
    if ($data) {
        db_update('users', (int)$user['id'], $data);
        session_regenerate_id(true);
        flash('success', 'Hisob ma\'lumotlari yangilandi.');
    }
    redirect('admin/account');
}

/* ---------- Muharrir uchun rasm yuklash (AJAX) ---------- */

function admin_upload(): never
{
    header('Content-Type: application/json; charset=UTF-8');
    if (!is_post()) {
        http_response_code(405);
        exit('{"error":"POST kerak"}');
    }
    try {
        $f = files_array('file');
        if (!$f) {
            throw new RuntimeException('Fayl tanlanmagan.');
        }
        $path = store_upload($f[0]);
        echo json_encode(['path' => $path, 'url' => url($path)]);
    } catch (RuntimeException $ex) {
        http_response_code(422);
        echo json_encode(['error' => $ex->getMessage()]);
    }
    exit;
}
