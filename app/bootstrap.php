<?php
declare(strict_types=1);
session_start();
$CFG = require __DIR__ . '/../config.php';
$DATA = require __DIR__ . '/data.php';

function cfg(string $k) { global $CFG; return $CFG[$k]; }
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money(float $n): string { return cfg('currency') . number_format($n, 2); }
function slugify(string $s): string { return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($s)), '-'); }
function redirect(string $to): never { header('Location: ' . $to); exit; }
function flash(?string $msg = null): ?string {
    if ($msg !== null) { $_SESSION['flash'] = $msg; return null; }
    $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m;
}
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . csrf() . '">'; }
function check_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) { http_response_code(419); exit('Session expired. Go back and try again.'); }
}

// The signed-in customer, or null. Cached per request.
function current_user(): ?array {
    static $u = null;
    if ($u !== null) return $u;
    $id = (int)($_SESSION['user_id'] ?? 0);
    if (!$id) return null;
    $st = db()->prepare('SELECT id, name, email, created_at FROM users WHERE id=?');
    $st->execute([$id]);
    $u = $st->fetch() ?: null;
    if (!$u) unset($_SESSION['user_id']);
    return $u;
}
// Only allow redirecting to same-site paths (guards against open redirects).
function safe_next(string $fallback = '/account'): string {
    $n = $_POST['next'] ?? $_GET['next'] ?? '';
    return (is_string($n) && $n !== '' && $n[0] === '/' && ($n[1] ?? '') !== '/') ? $n : $fallback;
}

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = 'mysql:host=' . cfg('db_host') . ';port=' . cfg('db_port') . ';dbname=' . cfg('db_name') . ';charset=utf8mb4';
    $pdo = new PDO($dsn, cfg('db_user'), cfg('db_pass'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec('CREATE TABLE IF NOT EXISTS categories (slug VARCHAR(50) PRIMARY KEY, name VARCHAR(255) NOT NULL, color VARCHAR(20) NOT NULL)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(100) UNIQUE, name VARCHAR(255) NOT NULL, category VARCHAR(50) NOT NULL, price DECIMAL(10,2) NOT NULL, badge VARCHAR(100), in_stock TINYINT(1) DEFAULT 1, description TEXT, filename VARCHAR(255) DEFAULT "", sort_order INT NOT NULL DEFAULT 0, hero TINYINT(1) NOT NULL DEFAULT 0, hero_order INT NOT NULL DEFAULT 0)');
    ensure_column($pdo, 'products', 'filename', 'VARCHAR(255) DEFAULT ""');
    ensure_column($pdo, 'products', 'sort_order', 'INT NOT NULL DEFAULT 0');
    ensure_column($pdo, 'products', 'hero', 'TINYINT(1) NOT NULL DEFAULT 0');
    ensure_column($pdo, 'products', 'hero_order', 'INT NOT NULL DEFAULT 0');
    $pdo->exec('CREATE TABLE IF NOT EXISTS orders (id INT AUTO_INCREMENT PRIMARY KEY, number VARCHAR(20) UNIQUE, name VARCHAR(255), email VARCHAR(255), phone VARCHAR(50), address TEXT, city VARCHAR(100), zip VARCHAR(20), country VARCHAR(100), notes TEXT, subtotal DECIMAL(10,2), shipping DECIMAL(10,2), total DECIMAL(10,2), status VARCHAR(50) DEFAULT "Processing", tracking VARCHAR(100), created_at DATETIME)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS order_items (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT, product_id INT, name VARCHAR(255), qty INT, unit_price DECIMAL(10,2))');
    $pdo->exec('CREATE TABLE IF NOT EXISTS messages (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), email VARCHAR(255), message TEXT, created_at DATETIME)');
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL, created_at DATETIME)');
    ensure_column($pdo, 'orders', 'user_id', 'INT NULL');
    seed($pdo);
    return $pdo;
}
function ensure_column(PDO $pdo, string $table, string $col, string $def): void {
    $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=? AND COLUMN_NAME=?');
    $st->execute([cfg('db_name'), $table, $col]);
    if (!$st->fetchColumn()) $pdo->exec("ALTER TABLE $table ADD COLUMN $col $def");
}
// Ensure a slug is unique within the catalog (two names can slugify to the same value).
function unique_slug(string $slug, array &$used): string {
    $base = $slug; $n = 2;
    while (isset($used[$slug])) $slug = $base . '-' . $n++;
    $used[$slug] = true;
    return $slug;
}

// Image filenames whose derived product name is machine garbage (hashes, scrape
// artifacts, dimensions). Such products are shown as "Product #<id>" instead.
function junk_image_files(): array {
    static $files = [
        '020892224148_1.png',
        '029465064709_1.png',
        '177-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        '20230522-160650-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        '240_F_405405863_6gapPNdeyQ8RH5HadPtMys2eoaiLhz3e-300x240.jpg',
        '249e9ac2-197b-4926-b8d3-4c13be2c1014-drugs1-300x300.webp.bv_resized_mobile.webp.bv.webp',
        '5-3-ab-chmfuppyca57059823316-1-1-300x300.webp.bv_resized_mobile.webp.bv.webp',
        '5008431101705824995_121-300x300.webp.bv_resized_mobile.webp.bv.webp',
        '5d3f697e3bdf7.image_-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        '604544618174_1.png',
        '65baad5696123a5ce12956f95fcab94f933f83d34595c.jpg',
        '664b8f118c5d36eb2ebc448cf346546b6d439dda5450c.jpg',
        '6JxhwC6CiHJvYwfO-300x300.jpeg',
        '71FmmqFlscL-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        '810742026400_1.png',
        '819ms9AI_L._SL1500_large-300x300.jpg',
        '8967422_1-1-300x300.jpg',
        'FGYdLBZzoyh-600x600.jpg',
        'GettyImages-1022570688-300x300.jpg',
        'IMG_0876_520e0c02-16c8-44ba-9c7f-43dc3fb9eafa.jpg',
        'Kzx4QqbVHLg3cAK7.jpg',
        'OIP-1lo-300x300.jpg',
        'R53056532487015f5e2600d633681bb1-300x300.jpg',
        'add-600x600.jpg',
        'artworks-000587481044-4jbq3u-t500x500-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        'chF01unksIgsllfD-300x300(1).jpg',
        'dax-300x300.jpg',
        'dexedri-300x300.jpeg',
        'download-3kj-600x600.jpg',
        'eti.jpg',
        'ho-300x300.jpg',
        'images.jpeg.bv.webp',
        'img_x500_65522a7e397a0-2-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        'metshyhylonshye-crystshyal-u-208926659661933-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        'open-uri20120802-30307-ktrznb-300x300.jpeg',
        'product-jpeg-300x300.jpg.bv_resized_mobile.jpg.bv.webp',
        'pu.jpg',
        'qtModot8xmGdfIYm.jpg',
        'r95qFpIBsZTQQKUP-2.jpg',
        'uuuu-300x300.png',
        'uuuuuuuuuuuussss.png',
        'change-the-blue-top-of-all-the-vials-and-make-it-white-and-make-white-backgroun-1-600x600.png',
    ];
    return $files;
}
function is_junk_image(string $filename): bool {
    return $filename !== '' && in_array($filename, junk_image_files(), true);
}
// Display name for a product: gibberish image-based names become "Product #<id>".
function display_product_name(string $name, string $filename, int $id): string {
    return is_junk_image($filename) ? 'Product #' . $id : $name;
}
function seed(PDO $pdo): void {
    global $DATA;
    $c = $pdo->prepare('INSERT INTO categories (slug,name,color) VALUES (?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), color=VALUES(color)');
    foreach ($DATA['categories'] as $slug => [$name, $color]) $c->execute([$slug, $name, $color]);

    $count = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    if ($count === 0) {
        // First run: seed the full catalog from data.php. Afterwards the catalog is
        // managed from the admin panel, so we never overwrite or prune it again.
        $p = $pdo->prepare('INSERT INTO products (slug,name,category,price,badge,in_stock,description,filename,sort_order) VALUES (?,?,?,?,?,?,?,?,?)');
        $ren = $pdo->prepare('UPDATE products SET name=? WHERE id=?');
        $i = 0; $used = [];
        foreach ($DATA['products'] as $prod) {
            [$n, $cat, $price, $badge, $stock, $desc] = $prod;
            $file = $prod[6] ?? '';
            $slug = unique_slug(slugify($n), $used);
            $p->execute([$slug, $n, $cat, $price, $badge, $stock, $desc, $file, $i++]);
            $id = (int)$pdo->lastInsertId();
            if (is_junk_image($file)) $ren->execute(['Product #' . $id, $id]);
        }
        return;
    }

    // Existing catalog is admin-owned. Only backfill the image filename / initial
    // order for legacy rows that predate those columns (runs once).
    $upd = $pdo->prepare('UPDATE products SET filename=?, sort_order=? WHERE slug=? AND (filename IS NULL OR filename="")');
    $i = 0;
    foreach ($DATA['products'] as $prod) {
        $upd->execute([$prod[6] ?? '', $i, slugify($prod[0])]);
        $i++;
    }
}

// Swap a product with its neighbour in the shop ordering (dir = up|down).
function reorder_product(PDO $pdo, int $id, string $dir): void {
    $st = $pdo->prepare('SELECT id, sort_order FROM products WHERE id=?');
    $st->execute([$id]);
    $cur = $st->fetch();
    if (!$cur) return;
    $so = (int)$cur['sort_order'];
    if ($dir === 'up') {
        $n = $pdo->prepare('SELECT id, sort_order FROM products WHERE (sort_order < ?) OR (sort_order = ? AND id < ?) ORDER BY sort_order DESC, id DESC LIMIT 1');
    } else {
        $n = $pdo->prepare('SELECT id, sort_order FROM products WHERE (sort_order > ?) OR (sort_order = ? AND id > ?) ORDER BY sort_order ASC, id ASC LIMIT 1');
    }
    $n->execute([$so, $so, $id]);
    $nb = $n->fetch();
    if (!$nb) return; // already at the edge
    $pdo->beginTransaction();
    $u = $pdo->prepare('UPDATE products SET sort_order=? WHERE id=?');
    $u->execute([$nb['sort_order'], $cur['id']]);
    $u->execute([$cur['sort_order'], $nb['id']]);
    $pdo->commit();
}

// Move a product up/down within the hero carousel (only products flagged hero=1).
function reorder_hero(PDO $pdo, int $id, string $dir): void {
    $st = $pdo->prepare('SELECT id, hero_order FROM products WHERE id=? AND hero=1');
    $st->execute([$id]);
    $cur = $st->fetch();
    if (!$cur) return;
    $o = (int)$cur['hero_order'];
    if ($dir === 'up') {
        $n = $pdo->prepare('SELECT id, hero_order FROM products WHERE hero=1 AND ((hero_order < ?) OR (hero_order = ? AND id < ?)) ORDER BY hero_order DESC, id DESC LIMIT 1');
    } else {
        $n = $pdo->prepare('SELECT id, hero_order FROM products WHERE hero=1 AND ((hero_order > ?) OR (hero_order = ? AND id > ?)) ORDER BY hero_order ASC, id ASC LIMIT 1');
    }
    $n->execute([$o, $o, $id]);
    $nb = $n->fetch();
    if (!$nb) return;
    $pdo->beginTransaction();
    $u = $pdo->prepare('UPDATE products SET hero_order=? WHERE id=?');
    $u->execute([$nb['hero_order'], $cur['id']]);
    $u->execute([$cur['hero_order'], $nb['id']]);
    $pdo->commit();
}

// ---------- tiered pricing ----------
function tiers(float $base): array {
    return [
        ['label' => 'Buy 1 - 2 pieces',             'min' => 1, 'price' => $base],
        ['label' => 'Buy 3 - 4 pieces and save 5%', 'min' => 3, 'price' => round($base * 0.95, 2)],
        ['label' => 'Buy 5+ pieces and save 10%',   'min' => 5, 'price' => round($base * 0.90, 2)],
    ];
}
function unit_price(float $base, int $qty): float {
    $price = $base;
    foreach (tiers($base) as $t) if ($qty >= $t['min']) $price = $t['price'];
    return $price;
}

// ---------- cart (session) ----------
function cart_items(): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $in = implode(',', array_fill(0, count($cart), '?'));
    $st = db()->prepare("SELECT * FROM products WHERE id IN ($in)");
    $st->execute(array_map('intval', array_keys($cart)));
    $out = [];
    foreach ($st->fetchAll() as $p) {
        $q = (int)$cart[$p['id']];
        $p['qty'] = $q; $p['unit'] = unit_price((float)$p['price'], $q); $p['line'] = $p['unit'] * $q;
        $out[] = $p;
    }
    return $out;
}
function cart_count(): int { return array_sum($_SESSION['cart'] ?? []); }
function cart_totals(array $items): array {
    $sub = array_sum(array_column($items, 'line'));
    $ship = $sub == 0 ? 0 : ($sub >= cfg('free_ship_over') ? 0 : cfg('shipping_flat'));
    return ['subtotal' => $sub, 'shipping' => $ship, 'total' => $sub + $ship];
}
function wishlist(): array { return $_SESSION['wish'] ?? []; }

// ---------- rendering ----------
function categories(): array { return db()->query('SELECT * FROM categories ORDER BY name')->fetchAll(); }
function icon(string $n): string {
    static $i = [
     'search'=>'<path d="M11 4a7 7 0 105 11.9l4.6 4.6 1.4-1.4-4.6-4.6A7 7 0 0011 4zm0 2a5 5 0 110 10 5 5 0 010-10z"/>',
     'heart'=>'<path d="M12 21s-8-5.4-8-11a4.5 4.5 0 018-2.8A4.5 4.5 0 0120 10c0 5.6-8 11-8 11z"/>',
     'user'=>'<path d="M12 12a4.5 4.5 0 100-9 4.5 4.5 0 000 9zm0 2c-4.4 0-8 2.2-8 5v2h16v-2c0-2.8-3.6-5-8-5z"/>',
     'cart'=>'<path d="M3 3h2.6l.6 3H21l-2 9H8L5.4 5H3V3zm6 15a1.6 1.6 0 110 3.2A1.6 1.6 0 019 18zm8 0a1.6 1.6 0 110 3.2 1.6 1.6 0 010-3.2z"/>',
     'pin'=>'<path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 119.5 9 2.5 2.5 0 0112 11.5z"/>',
     'help'=>'<path d="M4 4h16v12H9l-5 4V4z"/>',
     'truck'=>'<path d="M2 6h12v9h-1.2a2.5 2.5 0 00-4.6 0H2V6zm13 3h4l3 3v3h-1.2a2.5 2.5 0 00-4.6 0H15V9z"/>',
     'shield'=>'<path d="M12 2l8 3v6c0 5-3.4 9.4-8 11-4.6-1.6-8-6-8-11V5l8-3z"/>',
     'gift'=>'<path d="M3 8h18v4H3V8zm1 5h7v8H4v-8zm9 0h7v8h-7v-8z"/>',
     'mail'=>'<path d="M3 5h18v14H3V5zm2 2v.5l7 5 7-5V7H5z"/>',
     'chev'=>'<path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/>',
     'filter'=>'<path d="M3 6h10M17 6h4M3 12h4M11 12h10M3 18h12M19 18h2" stroke="currentColor" stroke-width="2" fill="none"/>',
     'menu'=>'<path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" fill="none"/>',
     'lab'=>'<path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 14h-3v3h-2v-3H8v-2h3v-3h2v3h3v2z"/>',
     'bulk'=>'<path d="M3 3h18v5H3zM4 9h16v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9z"/>',
     'facebook'=>'<path d="M22 12a10 10 0 10-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.7-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.7l-.4 2.9h-2.3v7A10 10 0 0022 12z"/>',
     'telegram'=>'<path d="M21.9 4.6l-3.1 14.6c-.2 1-.8 1.3-1.7.8l-4.6-3.4-2.2 2.1c-.2.2-.5.5-.9.5l.3-4.6 8.4-7.6c.4-.3-.1-.5-.6-.2L7.2 13 3 11.7c-1-.3-1-1 .2-1.4l17.1-6.6c.8-.3 1.6.2 1.5 1.3z"/>',
    ];
    return '<svg class="ic" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">' . ($i[$n] ?? '') . '</svg>';
}
function view(string $name, array $vars = []): void {
    extract($vars, EXTR_SKIP);
    require __DIR__ . "/views/$name.php";
}
function page(string $name, string $title, array $vars = [], string $bodyClass = ''): void {
    $vars['title'] = $title; $vars['bodyClass'] = $bodyClass;
    ob_start(); view($name, $vars); $content = ob_get_clean();
    $vars['content'] = $content; $vars['flash'] = flash();
    view('layout', $vars);
}
function product_card(array $p): string { ob_start(); view('product_card', ['p' => $p]); return ob_get_clean(); }
function img_url(array $p): string {
    $drugsDir = __DIR__ . '/../public/assets/DRUGS/';
    $imgDir = __DIR__ . '/../public/assets/img/';
    // The stored filename wins: check the DRUGS folder, then the img folder.
    if (!empty($p['filename'])) {
        if (is_file($drugsDir . $p['filename'])) return '/assets/DRUGS/' . rawurlencode($p['filename']);
        if (is_file($imgDir . $p['filename'])) return '/assets/img/' . rawurlencode($p['filename']);
    }
    // Legacy rows without a filename: guess the name inside DRUGS.
    if (!empty($p['name'])) foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $ext) {
        $f = $p['name'] . '.' . $ext;
        if (is_file($drugsDir . $f)) return '/assets/DRUGS/' . rawurlencode($f);
    }
    // Real image matching the slug in the legacy img folder
    foreach (['jpg', 'jpeg', 'png'] as $ext) {
        if (is_file($imgDir . $p['slug'] . '.' . $ext)) return '/assets/img/' . $p['slug'] . '.' . $ext;
    }
    // Fallback to generated SVG (legacy)
    return '/img/' . $p['slug'] . '.svg';
}
function not_found(): never { http_response_code(404); page('404', 'Page not found'); exit; }
