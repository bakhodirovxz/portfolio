<?php
declare(strict_types=1);

/** Kategoriya => ishlar soni */
function work_categories(): array
{
    $out = [];
    foreach (db_all("SELECT category, COUNT(*) c FROM works WHERE status = 'published' AND category <> '' GROUP BY category ORDER BY c DESC, category") as $r) {
        $out[$r['category']] = (int)$r['c'];
    }
    return $out;
}

function person_jsonld(): array
{
    $sameAs = array_values(array_filter(array_column(social_links(), 'url'), fn($u) => preg_match('#^https?://#', $u)));
    return array_filter([
        '@type'    => 'Person',
        '@id'      => abs_url('#person'),
        'name'     => setting('owner_name'),
        'jobTitle' => setting('role'),
        'url'      => abs_url(),
        'image'    => setting('avatar') ? abs_url(setting('avatar')) : null,
        'email'    => setting('email') ? 'mailto:' . setting('email') : null,
        'sameAs'   => $sameAs ?: null,
    ]);
}

function site_home(): never
{
    $works = db_all("SELECT * FROM works WHERE status = 'published' ORDER BY featured DESC, sort_order ASC, id DESC LIMIT 6");
    $posts = db_all("SELECT * FROM posts WHERE status = 'published' AND published_at <= ? ORDER BY published_at DESC LIMIT 3", [now()]);
    $worksTotal = (int)db_val("SELECT COUNT(*) FROM works WHERE status = 'published'");

    view('site/home', [
        'works' => $works,
        'worksTotal' => $worksTotal,
        'posts' => $posts,
        'nav' => 'home',
        'meta' => [
            'title' => setting('meta_title', setting('owner_name') . ' — ' . setting('role')),
            'raw_title' => true,
            'description' => setting('meta_description'),
            'jsonld' => [
                ['@type' => 'WebSite', '@id' => abs_url('#website'), 'url' => abs_url(), 'name' => setting('site_name'), 'inLanguage' => 'uz'],
                ['@type' => 'ProfilePage', 'url' => abs_url(), 'mainEntity' => ['@id' => abs_url('#person')]],
                person_jsonld(),
            ],
        ],
    ]);
}

function site_works(): never
{
    $cat = (string)input('kategoriya');
    $params = [];
    $sql = "SELECT * FROM works WHERE status = 'published'";
    if ($cat !== '') {
        $sql .= ' AND category = ?';
        $params[] = $cat;
    }
    $works = db_all($sql . ' ORDER BY featured DESC, sort_order ASC, id DESC', $params);
    view('site/works', [
        'works' => $works,
        'categories' => work_categories(),
        'total' => (int)db_val("SELECT COUNT(*) FROM works WHERE status = 'published'"),
        'active' => $cat,
        'mode' => input('korinish') === 'tor' ? 'grid' : 'list',
        'nav' => 'works',
        'meta' => [
            'title' => $cat !== '' ? $cat . ' — Ishlar' : 'Ishlar',
            'description' => 'Tanlangan loyihalar va ishlar: ' . setting('meta_description'),
            'canonical' => abs_url('ishlar'),
            'jsonld' => [[
                '@type' => 'CollectionPage', 'name' => 'Ishlar', 'url' => abs_url('ishlar'),
                'hasPart' => array_map(fn($w) => ['@type' => 'CreativeWork', 'name' => $w['title'], 'url' => abs_url('ishlar/' . $w['slug'])], $works),
            ]],
        ],
    ]);
}

function site_work(string $slug): never
{
    $work = db_one("SELECT * FROM works WHERE slug = ? AND status = 'published'", [$slug]);
    if (!$work) {
        not_found();
    }
    db_exec('UPDATE works SET views = views + 1 WHERE id = ?', [$work['id']]);

    $next = db_one("SELECT title, slug, cover, category FROM works WHERE status = 'published' AND id <> ? ORDER BY CASE WHEN sort_order > ? OR (sort_order = ? AND id < ?) THEN 0 ELSE 1 END, sort_order ASC, id DESC LIMIT 1",
        [$work['id'], $work['sort_order'], $work['sort_order'], $work['id']]);

    $desc = $work['meta_description'] ?: ($work['summary'] ?: excerpt($work['content']));
    view('site/work', [
        'work' => $work,
        'gallery' => json_decode((string)$work['gallery'], true) ?: [],
        'next' => $next,
        'nav' => 'works',
        'meta' => [
            'title' => $work['meta_title'] ?: $work['title'],
            'description' => $desc,
            'image' => $work['cover'],
            'type' => 'article',
            'canonical' => abs_url('ishlar/' . $work['slug']),
            'jsonld' => [
                array_filter([
                    '@type' => 'CreativeWork',
                    'name' => $work['title'],
                    'description' => $desc,
                    'image' => $work['cover'] ? abs_url($work['cover']) : null,
                    'url' => abs_url('ishlar/' . $work['slug']),
                    'dateCreated' => $work['year'] ?: null,
                    'genre' => $work['category'] ?: null,
                    'keywords' => $work['tags'] ?: null,
                    'creator' => ['@id' => abs_url('#person')],
                ]),
                breadcrumbs([['Ishlar', 'ishlar'], [$work['title'], 'ishlar/' . $work['slug']]]),
                person_jsonld(),
            ],
        ],
    ]);
}

function site_blog(): never
{
    $tag = (string)input('teg');
    $where = "status = 'published' AND published_at <= ?";
    $params = [now()];
    if ($tag !== '') {
        $where .= ' AND tags LIKE ?';
        $params[] = '%' . $tag . '%';
    }
    $total = (int)db_val("SELECT COUNT(*) FROM posts WHERE $where", $params);
    $pg = paginate($total, max(1, (int)setting('posts_per_page', '9')), (int)input('sahifa', 1));
    $posts = db_all("SELECT * FROM posts WHERE $where ORDER BY published_at DESC LIMIT {$pg['limit']} OFFSET {$pg['offset']}", $params);

    view('site/blog', [
        'posts' => $posts,
        'pg' => $pg,
        'tag' => $tag,
        'nav' => 'blog',
        'meta' => [
            'title' => 'Blog' . ($pg['page'] > 1 ? ' — ' . $pg['page'] . '-sahifa' : ''),
            'description' => 'Maqolalar, fikrlar va tajribalar — ' . setting('owner_name'),
            'canonical' => abs_url('blog' . ($pg['page'] > 1 ? '?sahifa=' . $pg['page'] : '')),
            'robots' => $tag !== '' ? 'noindex, follow' : null,
        ],
    ]);
}

function site_post(string $slug): never
{
    $post = db_one("SELECT * FROM posts WHERE slug = ? AND status = 'published' AND published_at <= ?", [$slug, now()]);
    if (!$post) {
        not_found();
    }
    db_exec('UPDATE posts SET views = views + 1 WHERE id = ?', [$post['id']]);
    $related = db_all("SELECT * FROM posts WHERE status = 'published' AND published_at <= ? AND id <> ? ORDER BY published_at DESC LIMIT 2", [now(), $post['id']]);

    $desc = $post['meta_description'] ?: ($post['excerpt'] ?: excerpt($post['content']));
    view('site/post', [
        'post' => $post,
        'related' => $related,
        'nav' => 'blog',
        'meta' => [
            'title' => $post['meta_title'] ?: $post['title'],
            'description' => $desc,
            'image' => $post['cover'],
            'type' => 'article',
            'published' => $post['published_at'],
            'modified' => $post['updated_at'],
            'canonical' => abs_url('blog/' . $post['slug']),
            'jsonld' => [
                array_filter([
                    '@type' => 'BlogPosting',
                    'headline' => $post['title'],
                    'description' => $desc,
                    'image' => $post['cover'] ? abs_url($post['cover']) : null,
                    'datePublished' => date('c', strtotime($post['published_at'])),
                    'dateModified' => date('c', strtotime($post['updated_at'])),
                    'mainEntityOfPage' => abs_url('blog/' . $post['slug']),
                    'author' => ['@id' => abs_url('#person')],
                    'keywords' => $post['tags'] ?: null,
                    'inLanguage' => 'uz',
                ]),
                breadcrumbs([['Blog', 'blog'], [$post['title'], 'blog/' . $post['slug']]]),
                person_jsonld(),
            ],
        ],
    ]);
}

function breadcrumbs(array $items): array
{
    $list = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Bosh sahifa', 'item' => abs_url()]];
    foreach ($items as $i => [$name, $path]) {
        $list[] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $name, 'item' => abs_url($path)];
    }
    return ['@type' => 'BreadcrumbList', 'itemListElement' => $list];
}

function site_contact(array $errors = [], array $old = []): never
{
    view('site/contact', [
        'errors' => $errors,
        'old' => $old,
        'sent' => isset($_GET['yuborildi']),
        'nav' => 'contact',
        'meta' => [
            'title' => 'Aloqa',
            'description' => setting('contact_text'),
            'canonical' => abs_url('aloqa'),
        ],
    ]);
}

function site_contact_submit(): never
{
    $d = [
        'name'    => mb_substr((string)input('name'), 0, 120),
        'email'   => mb_substr((string)input('email'), 0, 160),
        'phone'   => mb_substr((string)input('phone'), 0, 40),
        'subject' => mb_substr((string)input('subject'), 0, 160),
        'budget'  => mb_substr((string)input('budget'), 0, 60),
        'message' => mb_substr((string)input('message'), 0, 5000),
    ];

    // Bot himoyasi: yashirin maydon to'ldirilgan yoki forma juda tez yuborilgan
    if ((string)input('website') !== '') {
        redirect('aloqa?yuborildi=1#forma');
    }

    $errors = [];
    if (!form_token_valid((string)input('_t'))) {
        $errors['form'] = "Forma eskirgan yoki juda tez yuborildi. Iltimos, qayta yuboring.";
    }
    if (mb_strlen($d['name']) < 2) {
        $errors['name'] = 'Ismingizni kiriting.';
    }
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "To'g'ri email manzil kiriting.";
    }
    if (mb_strlen($d['message']) < 10) {
        $errors['message'] = 'Xabar kamida 10 ta belgidan iborat bo\'lsin.';
    }
    $recent = (int)db_val('SELECT COUNT(*) FROM messages WHERE ip = ? AND created_at > ?', [client_ip(), date('Y-m-d H:i:s', time() - 600)]);
    if ($recent >= 3) {
        $errors['form'] = "Juda ko'p so'rov yuborildi. Birozdan so'ng qayta urinib ko'ring.";
    }
    if ($errors) {
        http_response_code(422);
        site_contact($errors, $d);
    }

    $d['ip'] = client_ip();
    $d['user_agent'] = mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 180);
    $d['created_at'] = now();
    db_insert('messages', $d);

    $to = setting('notify_email', setting('email'));
    if ($to !== '' && function_exists('mail')) {
        $host = parse_url(site_origin(), PHP_URL_HOST) ?: 'localhost';
        $subject = '=?UTF-8?B?' . base64_encode('Yangi so\'rov: ' . ($d['subject'] ?: $d['name'])) . '?=';
        $body = "Ism: {$d['name']}\nEmail: {$d['email']}\nTelefon: {$d['phone']}\nMavzu: {$d['subject']}\nByudjet: {$d['budget']}\n\n{$d['message']}\n\n— " . abs_url('admin/messages');
        $headers = "From: noreply@$host\r\nReply-To: " . str_replace(["\r", "\n"], '', $d['email']) . "\r\nContent-Type: text/plain; charset=UTF-8";
        @mail($to, $subject, $body, $headers);
    }

    redirect('aloqa?yuborildi=1#forma');
}

function site_sitemap(): never
{
    header('Content-Type: application/xml; charset=UTF-8');
    $urls = [[abs_url(), null, '1.0'], [abs_url('ishlar'), null, '0.9'], [abs_url('blog'), null, '0.8'], [abs_url('aloqa'), null, '0.5']];
    foreach (db_all("SELECT slug, updated_at FROM works WHERE status = 'published'") as $w) {
        $urls[] = [abs_url('ishlar/' . $w['slug']), $w['updated_at'], '0.8'];
    }
    foreach (db_all("SELECT slug, updated_at FROM posts WHERE status = 'published' AND published_at <= ?", [now()]) as $p) {
        $urls[] = [abs_url('blog/' . $p['slug']), $p['updated_at'], '0.7'];
    }
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as [$loc, $mod, $prio]) {
        echo '  <url><loc>' . e($loc) . '</loc>' . ($mod ? '<lastmod>' . date('Y-m-d', strtotime($mod)) . '</lastmod>' : '') . '<priority>' . $prio . '</priority></url>' . "\n";
    }
    echo '</urlset>';
    exit;
}

function site_robots(): never
{
    header('Content-Type: text/plain; charset=UTF-8');
    echo "User-agent: *\nDisallow: " . url('admin') . "\nDisallow: " . url('install.php') . "\n\nSitemap: " . abs_url('sitemap.xml') . "\n";
    exit;
}

function site_feed(): never
{
    header('Content-Type: application/rss+xml; charset=UTF-8');
    $posts = db_all("SELECT * FROM posts WHERE status = 'published' AND published_at <= ? ORDER BY published_at DESC LIMIT 20", [now()]);
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel>';
    echo '<title>' . e(setting('site_name')) . ' — Blog</title><link>' . e(abs_url('blog')) . '</link>';
    echo '<description>' . e(setting('meta_description')) . '</description><language>uz</language>';
    echo '<atom:link href="' . e(abs_url('feed.xml')) . '" rel="self" type="application/rss+xml"/>';
    foreach ($posts as $p) {
        echo '<item><title>' . e($p['title']) . '</title><link>' . e(abs_url('blog/' . $p['slug'])) . '</link>';
        echo '<guid>' . e(abs_url('blog/' . $p['slug'])) . '</guid><pubDate>' . date(DATE_RSS, strtotime($p['published_at'])) . '</pubDate>';
        echo '<description>' . e($p['excerpt'] ?: excerpt($p['content'], 300)) . '</description></item>';
    }
    echo '</channel></rss>';
    exit;
}
