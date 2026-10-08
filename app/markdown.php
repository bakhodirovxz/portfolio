<?php
declare(strict_types=1);

/**
 * Yengil va xavfsiz Markdown konvertori (HTML teglar escape qilinadi).
 * Qo'llab-quvvatlanadi: # sarlavhalar, **qalin**, *kursiv*, ~~o'chirilgan~~, `kod`, ```kod bloki```,
 * > iqtibos, - ro'yxat, 1. raqamli ro'yxat, --- chiziq, [havola](url), ![rasm](url "izoh"),
 * YouTube havolasi alohida qatorda bo'lsa — video sifatida joylanadi.
 */
function markdown(?string $src): string
{
    $src = str_replace(["\r\n", "\r"], "\n", (string)$src);
    $lines = explode("\n", $src);
    $out = [];
    $para = [];
    $n = count($lines);

    $flush = function () use (&$para, &$out) {
        if ($para) {
            $out[] = '<p>' . md_inline(implode("\n", $para)) . '</p>';
            $para = [];
        }
    };

    for ($i = 0; $i < $n; $i++) {
        $line = $lines[$i];
        $trim = trim($line);

        if ($trim === '') {
            $flush();
            continue;
        }

        // Kod bloki
        if (preg_match('/^```\s*([\w+-]*)\s*$/', $trim, $m)) {
            $flush();
            $code = [];
            for ($i++; $i < $n && !preg_match('/^```\s*$/', trim($lines[$i])); $i++) {
                $code[] = $lines[$i];
            }
            $lang = $m[1] ? ' class="language-' . e($m[1]) . '"' : '';
            $out[] = '<pre><code' . $lang . '>' . e(implode("\n", $code)) . '</code></pre>';
            continue;
        }

        // Sarlavha
        if (preg_match('/^(#{1,6})\s+(.+?)\s*#*$/', $trim, $m)) {
            $flush();
            $lvl = max(2, strlen($m[1])); // h1 sahifa sarlavhasi uchun band
            $id = slugify(strip_tags(md_inline($m[2])));
            $out[] = "<h$lvl id=\"" . e($id) . "\">" . md_inline($m[2]) . "</h$lvl>";
            continue;
        }

        // Gorizontal chiziq
        if (preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trim)) {
            $flush();
            $out[] = '<hr>';
            continue;
        }

        // YouTube
        if (preg_match('#^https?://(?:www\.)?(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]{11})\S*$#', $trim, $m)) {
            $flush();
            $out[] = '<div class="embed"><iframe src="https://www.youtube-nocookie.com/embed/' . e($m[1]) . '" title="YouTube video" loading="lazy" allowfullscreen></iframe></div>';
            continue;
        }

        // Alohida qatordagi rasm -> figure
        if (preg_match('/^!\[([^\]]*)\]\(([^)\s]+)(?:\s+"([^"]*)")?\)$/', $trim, $m)) {
            $flush();
            $src = md_safe_url($m[2]);
            $cap = $m[3] ?? '';
            $out[] = '<figure><img src="' . e($src) . '" alt="' . e($m[1]) . '" loading="lazy">'
                . ($cap !== '' ? '<figcaption>' . e($cap) . '</figcaption>' : '') . '</figure>';
            continue;
        }

        // Iqtibos
        if (str_starts_with($trim, '>')) {
            $flush();
            $q = [];
            while ($i < $n && str_starts_with(trim($lines[$i]), '>')) {
                $q[] = preg_replace('/^>\s?/', '', trim($lines[$i]));
                $i++;
            }
            $i--;
            $out[] = '<blockquote>' . markdown(implode("\n", $q)) . '</blockquote>';
            continue;
        }

        // Ro'yxatlar
        if (preg_match('/^([-*+]|\d+[.)])\s+/', $trim, $m)) {
            $flush();
            $ordered = ctype_digit($m[1][0]);
            $items = [];
            while ($i < $n && preg_match('/^\s*([-*+]|\d+[.)])\s+(.*)$/', $lines[$i], $mm)) {
                $items[] = '<li>' . md_inline($mm[2]) . '</li>';
                $i++;
            }
            $i--;
            $tag = $ordered ? 'ol' : 'ul';
            $out[] = "<$tag>" . implode('', $items) . "</$tag>";
            continue;
        }

        $para[] = $trim;
    }
    $flush();
    return implode("\n", $out);
}

function md_safe_url(string $url): string
{
    $url = trim($url);
    if (preg_match('#^(https?:|mailto:|tel:|/|\#|uploads/)#i', $url)) {
        return str_starts_with($url, 'uploads/') ? url($url) : $url;
    }
    if (!preg_match('#^[a-z][a-z0-9+.-]*:#i', $url)) {
        return $url; // nisbiy havola
    }
    return '#';
}

function md_inline(string $text): string
{
    $codes = [];
    // Inline kodni vaqtincha ajratamiz
    $text = preg_replace_callback('/`([^`]+)`/', function ($m) use (&$codes) {
        $codes[] = '<code>' . e($m[1]) . '</code>';
        return "\x01" . (count($codes) - 1) . "\x01";
    }, $text);

    $text = e($text);

    // Rasm
    $text = preg_replace_callback('/!\[([^\]]*)\]\(([^)\s]+)\)/', fn($m) =>
        '<img src="' . e(md_safe_url(html_entity_decode($m[2], ENT_QUOTES))) . '" alt="' . $m[1] . '" loading="lazy">', $text);
    // Havola
    $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/', function ($m) {
        $href = md_safe_url(html_entity_decode($m[2], ENT_QUOTES));
        $ext = preg_match('#^https?://#i', $href) && !str_contains($href, $_SERVER['HTTP_HOST'] ?? '___');
        return '<a href="' . e($href) . '"' . ($ext ? ' target="_blank" rel="noopener"' : '') . '>' . $m[1] . '</a>';
    }, $text);
    // Avtomatik havola
    $text = preg_replace('#(?<!["=>])\b(https?://[^\s<]+[^\s<.,;:!?)])#i', '<a href="$1" target="_blank" rel="noopener">$1</a>', $text);

    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/s', '<em>$1</em>', $text);
    $text = preg_replace('/~~(.+?)~~/s', '<del>$1</del>', $text);
    $text = nl2br($text, false);

    return preg_replace_callback("/\x01(\d+)\x01/", fn($m) => $codes[(int)$m[1]], $text);
}
