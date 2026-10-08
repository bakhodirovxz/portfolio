# Bakhodirov.uz — portfolio sayt

Oddiy PHP + SQLite/MySQL'da yozilgan portfolio sayt va admin panel. Framework, Composer yoki Node.js shart emas — fayllarni cPanel'ga yuklab, brauzerda o'rnatish formasini to'ldirasiz.

## Imkoniyatlar

- **Sayt:** bosh sahifa (hero, ko'nikmalar lentasi, tanlangan ishlar, men haqimda, xizmatlar, blog, CTA), ishlar ro'yxati va kategoriyalar, ish sahifasi (galereya bilan), blog va maqola sahifasi, aloqa formasi, 404. Yorug' va qorong'i rejim, mobilga moslashgan.
- **Admin panel** (`/admin`):
  - Ishlar: qo'shish/tahrirlash, muqova, galereya (sudrab tartiblash), kategoriya, mijoz, yil, havola, "tanlangan", qoralama.
  - Maqolalar: Markdown muharriri (asboblar paneli, oldindan ko'rish, rasmni tanlash/joylash/sudrab tashlash), rejalashtirilgan chop etish.
  - So'rovlar: aloqa formasidan kelgan xabarlar, o'qilgan/o'qilmagan, emailga javob berish. Ixtiyoriy ravishda yangi so'rov haqida email xabarnoma.
  - Sozlamalar: matnlar, avatar, aloqa ma'lumotlari, **ijtimoiy tarmoqlar** (14 ta platforma, qo'shish/o'chirish/tartiblash), SEO, Google Search Console, Google Analytics 4, qo'shimcha kod (Metrika, Pixel va h.k.).
  - Panelda statistika, sozlash ro'yxati, ko'p ko'rilgan sahifalar.
- **SEO:** chiroyli URL'lar (`/ishlar/...`, `/blog/...`), canonical, Open Graph va Twitter teglari, JSON-LD (Person, WebSite, CreativeWork, BlogPosting, BreadcrumbList), avtomatik `sitemap.xml`, `robots.txt`, RSS (`feed.xml`), har bir sahifa uchun meta sarlavha/tavsif va Google natijasi ko'rinishi.
- **Xavfsizlik:** parollar `password_hash`, CSRF himoyasi, login urinishlarini cheklash, tayyorlangan SQL so'rovlar, rasm turini tekshirish, `uploads/` ichida skriptlarni ishga tushirish taqiqlangan, aloqa formasida bot himoyasi (honeypot + vaqt tokeni + IP limit).

## Talablar

- PHP 8.1+ (`pdo_sqlite` yoki `pdo_mysql`, `gd`)
- Apache + `mod_rewrite` (cPanel'da standart)

## cPanel'ga o'rnatish

1. **PHP versiyasi:** cPanel → *Select PHP Version* (yoki *MultiPHP Manager*) → 8.1 yoki yangiroq.
2. **Fayllarni yuklash:** repozitoriyani ZIP qilib yuklab oling (GitHub → Code → Download ZIP), cPanel → *File Manager* → `public_html` ga yuklang va **Extract** qiling. Fayllar to'g'ridan-to'g'ri `public_html` ichida bo'lishi kerak (`public_html/index.php`). Yashirin `.htaccess` fayllari ham ko'chganiga ishonch hosil qiling (File Manager → Settings → *Show Hidden Files*).
   - Eski WordPress'ni avval zaxiralab (*Backup*), keyin `public_html` dan olib tashlang.
3. **Ruxsatlar:** `data/` va `uploads/` papkalari 755 bo'lsin.
4. **O'rnatish:** brauzerda `https://bakhodirov.uz/install.php` ni oching, ismingiz, admin login/parol va baza turini kiriting.
   - **SQLite** — hech narsa sozlash shart emas (tavsiya etiladi).
   - **MySQL** — avval cPanel → *MySQL® Databases* da baza va foydalanuvchi yarating.
5. O'rnatishdan so'ng `/admin` ga kiring. Xavfsizlik uchun `install.php` ni o'chirib tashlash tavsiya etiladi (u qayta ishlamaydi, chunki `config.php` mavjud).
6. **SSL:** cPanel → *SSL/TLS Status* → AutoSSL. So'ng `.htaccess` dagi HTTPS yo'naltirish qatorlarini izohdan chiqaring.

## Google Search Console va Analytics

1. Admin → Sozlamalar → **SEO va analitika** bo'limida qadamba-qadam ko'rsatma bor.
2. Search Console: *URL prefix* usuli → *HTML tag* → tegni maydonga joylang → Verify → *Sitemaps* ga `sitemap.xml` ni qo'shing.
3. Analytics: GA4 property yarating va `G-XXXXXXXXXX` ID'ni kiriting.

## Zaxira nusxa

- SQLite: `data/site.sqlite` va `uploads/` papkasini yuklab oling.
- MySQL: cPanel → *phpMyAdmin* → Export, hamda `uploads/`.

## Lokal ishga tushirish

```bash
php -S localhost:8000 index.php
# so'ng http://localhost:8000/install.php
```

## Tuzilishi

```
index.php          front controller (marshrutlar)
install.php        bir martalik o'rnatuvchi
app/               mantiq: db, yordamchilar, markdown, sayt va admin kontrollerlari
views/site, admin  shablonlar
assets/            CSS, JS, favicon
data/              SQLite baza (veb orqali yopiq)
uploads/           yuklangan rasmlar
config.php         o'rnatishda yaratiladi (gitda saqlanmaydi)
```

Yangi so'rov xati spamga tushsa: cPanel → *Email Deliverability* → SPF/DKIM ni yoqing.
