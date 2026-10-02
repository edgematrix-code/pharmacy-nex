<?php
declare(strict_types=1);
$path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/') ?: '/';
if (PHP_SAPI === 'cli-server' && $path !== '/' && is_file(__DIR__ . $path)) return false; // built-in server static files
require __DIR__ . '/../app/bootstrap.php';
$method = $_SERVER['REQUEST_METHOD'];
$seg = explode('/', trim($path, '/'));

// ---- product image (generated SVG, no external assets) ----
if ($seg[0] === 'img' && isset($seg[1])) {
    $st = db()->prepare('SELECT p.name, p.badge, c.color FROM products p JOIN categories c ON c.slug=p.category WHERE p.slug=?');
    $st->execute([basename($seg[1], '.svg')]);
    $r = $st->fetch() ?: not_found();
    header('Content-Type: image/svg+xml'); header('Cache-Control: public, max-age=86400');
    $c = e($r['color']); $words = explode(' ', wordwrap($r['name'], 14, "\n", true)); $lines = explode("\n", wordwrap($r['name'], 14, "\n", true));
    $t = ''; foreach (array_slice($lines, 0, 3) as $i => $l) $t .= '<text x="300" y="' . (330 + $i * 34) . '" text-anchor="middle" font-family="Outfit,Arial,sans-serif" font-size="28" font-weight="600" fill="#fff">' . e($l) . '</text>';
    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600"><rect width="600" height="600" fill="#fff"/>'
       . '<ellipse cx="300" cy="548" rx="150" ry="14" fill="#000" opacity=".08"/>'
       . '<rect x="250" y="70" width="100" height="60" rx="10" fill="#1d2128"/><rect x="238" y="120" width="124" height="24" rx="6" fill="#32373c"/>'
       . '<rect x="190" y="140" width="220" height="396" rx="34" fill="' . $c . '"/>'
       . '<rect x="190" y="140" width="60" height="396" rx="30" fill="#fff" opacity=".13"/>'
       . '<rect x="205" y="250" width="190" height="170" rx="12" fill="#000" opacity=".22"/>' . $t
       . '<text x="300" y="480" text-anchor="middle" font-family="Outfit,Arial,sans-serif" font-size="20" fill="#fff" opacity=".9">' . e($r['badge'] ?: cfg('site_name')) . '</text></svg>';
    exit;
}

switch (true) {
    case $path === '/':
        $feat = db()->query('SELECT * FROM products WHERE in_stock=1 ORDER BY sort_order, id LIMIT 8')->fetchAll();
        // Hero carousel: the products the admin chose (hero=1), in their chosen order.
        $hero = db()->query('SELECT * FROM products WHERE hero=1 ORDER BY hero_order, id LIMIT 12')->fetchAll();
        if (!$hero) $hero = array_slice($feat, 0, 6); // fallback until the admin picks images
        $topSlugs = ['anavar-50mg-300x300', 'cialis', 'fishscale-cocaine'];
        $topSt = db()->prepare('SELECT * FROM products WHERE in_stock=1 AND slug IN (' . implode(',', array_fill(0, count($topSlugs), '?')) . ')'); $topSt->execute($topSlugs);
        $top = $topSt->fetchAll(); usort($top, fn($a, $b) => array_search($a['slug'], $topSlugs) <=> array_search($b['slug'], $topSlugs));
        if (count($top) < 3) $top = array_slice($feat, 0, 3);
        page('home', 'Home', compact('feat', 'top', 'hero'), 'home'); break;

    case $path === '/shop' || $seg[0] === 'product-category':
        $cat = $seg[0] === 'product-category' ? ($seg[1] ?? '') : ($_GET['cat'] ?? '');
        $q = trim($_GET['s'] ?? ''); $sort = $_GET['sort'] ?? 'default'; $pg = max(1, (int)($_GET['page'] ?? 1));
        $where = ['1=1']; $args = [];
        if ($cat !== '' && $cat !== '0') { $where[] = 'category=?'; $args[] = $cat; }
        if ($q !== '') { $where[] = '(name LIKE ? OR description LIKE ?)'; $args[] = "%$q%"; $args[] = "%$q%"; }
        $order = ['price-asc' => 'price ASC', 'price-desc' => 'price DESC', 'name' => 'name ASC'][$sort] ?? 'sort_order ASC, id ASC';
        $cnt = db()->prepare('SELECT COUNT(*) FROM products WHERE ' . implode(' AND ', $where)); $cnt->execute($args); $total = (int)$cnt->fetchColumn();
        $shown = min($total, $pg * cfg('per_page'));
        $st = db()->prepare('SELECT * FROM products WHERE ' . implode(' AND ', $where) . " ORDER BY $order LIMIT $shown"); $st->execute($args);
        $products = $st->fetchAll();
        
        // Check if this is an AJAX request for loading more products
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // Return only the product grid HTML
            ob_start();
            ?>
            <ul class="grid cols-4" id="product-grid" data-page="<?= $pg ?>" data-cat="<?= e($cat) ?>" data-sort="<?= e($sort) ?>" data-q="<?= e($q) ?>">
            <?php foreach ($products as $p) echo product_card($p); ?>
            </ul>
            <div class="loadmore">
              <p>Showing <?= $shown ?> of <?= $total ?> items</p>
              <?php if ($shown < $total): ?>
                <button class="btn ghost load-more-btn" data-next-page="<?= $pg + 1 ?>">Load More Products</button>
              <?php else: ?>
                <p>All products loaded!</p>
              <?php endif; ?>
            </div>
            <?php
            $html = ob_get_clean();
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            exit;
        }
        
        page('shop', 'Shop', compact('products', 'total', 'shown', 'cat', 'q', 'sort', 'pg'), 'shop'); break;

    case $seg[0] === 'product' && isset($seg[1]):
        $st = db()->prepare('SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON c.slug=p.category WHERE p.slug=?'); $st->execute([$seg[1]]);
        $p = $st->fetch() ?: not_found();
        $rel = db()->prepare('SELECT * FROM products WHERE category=? AND id<>? LIMIT 4'); $rel->execute([$p['category'], $p['id']]);
        page('product', $p['name'], ['p' => $p, 'related' => $rel->fetchAll()], 'single-product'); break;

    case $path === '/cart':
        page('cart', 'Cart', ['items' => cart_items()], 'cart'); break;

    case $path === '/cart/add' && $method === 'POST':
        check_csrf(); $id = (int)($_POST['id'] ?? 0); $qty = max(1, min(99, (int)($_POST['qty'] ?? 1)));
        $ok = db()->prepare('SELECT in_stock FROM products WHERE id=?'); $ok->execute([$id]);
        if ($ok->fetchColumn()) { $_SESSION['cart'][$id] = min(99, ($_SESSION['cart'][$id] ?? 0) + $qty); flash('Successfully added to your cart'); }
        redirect($_POST['back'] ?? '/cart');

    case $path === '/cart/update' && $method === 'POST':
        check_csrf();
        foreach (($_POST['qty'] ?? []) as $id => $q) { $q = (int)$q; if ($q <= 0) unset($_SESSION['cart'][(int)$id]); else $_SESSION['cart'][(int)$id] = min(99, $q); }
        if (isset($_POST['remove'])) unset($_SESSION['cart'][(int)$_POST['remove']]);
        redirect('/cart');

    case $path === '/wishlist/toggle' && $method === 'POST':
        check_csrf(); $id = (int)$_POST['id'];
        if (in_array($id, wishlist())) $_SESSION['wish'] = array_values(array_diff(wishlist(), [$id])); else $_SESSION['wish'][] = $id;
        redirect($_POST['back'] ?? '/shop');

    case $path === '/wishlist':
        $w = wishlist(); $products = [];
        if ($w) { $st = db()->prepare('SELECT * FROM products WHERE id IN (' . implode(',', array_map('intval', $w)) . ')'); $st->execute(); $products = $st->fetchAll(); }
        page('wishlist', 'Wishlist', compact('products')); break;

    // ---- customer accounts ----
    case $path === '/signup':
        $err = ''; $name = ''; $email = ''; $next = safe_next();
        if ($method === 'POST') {
            check_csrf();
            $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? '');
            $pw = $_POST['password'] ?? ''; $pw2 = $_POST['password2'] ?? '';
            if ($name === '') $err = 'Please enter your name.';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Enter a valid email address.';
            elseif (strlen($pw) < 8) $err = 'Password must be at least 8 characters.';
            elseif ($pw !== $pw2) $err = 'Passwords do not match.';
            else {
                $chk = db()->prepare('SELECT id FROM users WHERE email=?'); $chk->execute([$email]);
                if ($chk->fetchColumn()) $err = 'An account with that email already exists.';
                else {
                    db()->prepare('INSERT INTO users (name,email,password_hash,created_at) VALUES (?,?,?,?)')
                        ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT), date('c')]);
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int)db()->lastInsertId();
                    flash('Welcome, ' . $name . '! Your account is ready.');
                    redirect($next);
                }
            }
        }
        page('signup', 'Create account', compact('err', 'name', 'email', 'next')); break;

    case $path === '/login':
        $err = ''; $email = ''; $next = safe_next();
        if ($method === 'POST') {
            check_csrf();
            $email = trim($_POST['email'] ?? '');
            $st = db()->prepare('SELECT * FROM users WHERE email=?'); $st->execute([$email]);
            $u = $st->fetch();
            if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$u['id'];
                flash('Signed in as ' . $u['name'] . '.');
                redirect($next);
            }
            $err = 'Email or password is incorrect.';
        }
        page('login', 'Sign in', compact('err', 'email', 'next')); break;

    case $path === '/logout':
        unset($_SESSION['user_id']);
        flash('You have been signed out.');
        redirect('/');

    case $path === '/account':
        $me = current_user();
        if (!$me) redirect('/login?next=/account');
        $st = db()->prepare('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC'); $st->execute([$me['id']]);
        page('account', 'My account', ['me' => $me, 'orders' => $st->fetchAll()]); break;

    case $path === '/checkout':
        $items = cart_items(); if (!$items) redirect('/cart');
        $errors = []; $old = $_POST;
        if ($method !== 'POST' && ($u = current_user())) $old = ['name' => $u['name'], 'email' => $u['email']];
        if ($method === 'POST') {
            check_csrf();
            foreach (['name' => 'Name', 'email' => 'Email', 'address' => 'Address', 'city' => 'City', 'zip' => 'ZIP', 'country' => 'Country'] as $k => $l)
                if (trim($_POST[$k] ?? '') === '') $errors[] = "$l is required.";
            if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
            if (!$errors) {
                $t = cart_totals($items);                $num = 'NX-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));
                // Capture the crypto payment details alongside the customer notes.
                $payBits = [];
                if (trim($_POST['pay_method'] ?? '') !== '') $payBits[] = 'Method: ' . trim($_POST['pay_method']);
                if (trim($_POST['amount_sent'] ?? '') !== '') $payBits[] = 'Amount sent: ' . trim($_POST['amount_sent']);
                if (trim($_POST['tx_hash'] ?? '') !== '') $payBits[] = 'TXID: ' . trim($_POST['tx_hash']);
                $notes = trim($_POST['notes'] ?? '');
                if ($payBits) $notes = trim($notes . "\n" . '[Crypto payment] ' . implode(' | ', $payBits));
                $pdo = db(); $pdo->beginTransaction();
                $pdo->prepare('INSERT INTO orders (number,name,email,phone,address,city,zip,country,notes,subtotal,shipping,total,created_at,user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)')
                    ->execute([$num, $_POST['name'], $_POST['email'], $_POST['phone'] ?? '', $_POST['address'], $_POST['city'], $_POST['zip'], $_POST['country'], $notes, $t['subtotal'], $t['shipping'], $t['total'], date('c'), current_user()['id'] ?? null]);
                $oid = (int)$pdo->lastInsertId(); $ins = $pdo->prepare('INSERT INTO order_items (order_id,product_id,name,qty,unit_price) VALUES (?,?,?,?,?)');
                foreach ($items as $i) $ins->execute([$oid, $i['id'], $i['name'], $i['qty'], $i['unit']]);
                $pdo->commit(); unset($_SESSION['cart']); $_SESSION['last_order'] = $num;
                redirect('/order/' . $num);
            }
        }
        page('checkout', 'Checkout', compact('items', 'errors', 'old')); break;

    case $seg[0] === 'order' && isset($seg[1]):
        if (($_SESSION['last_order'] ?? '') !== $seg[1]) redirect('/tracking-order');
        $o = db()->prepare('SELECT * FROM orders WHERE number=?'); $o->execute([$seg[1]]); $order = $o->fetch() ?: not_found();
        page('order', 'Order received', ['order' => $order, 'lines' => db()->query('SELECT * FROM order_items WHERE order_id=' . (int)$order['id'])->fetchAll()]); break;

    case $path === '/tracking-order':
        $order = null; $lines = []; $err = '';
        if ($method === 'POST') {
            check_csrf();
            $o = db()->prepare('SELECT * FROM orders WHERE number=? AND lower(email)=lower(?)'); $o->execute([trim($_POST['number'] ?? ''), trim($_POST['email'] ?? '')]);
            $order = $o->fetch();
            if ($order) $lines = db()->query('SELECT * FROM order_items WHERE order_id=' . (int)$order['id'])->fetchAll(); else $err = 'No order found with those details.';
        }
        page('tracking', 'Track Order', compact('order', 'lines', 'err')); break;

    case $path === '/faq':
        page('faq', 'FAQ', ['faq' => $DATA['faq']], 'page-faq'); break;

    case $path === '/contact-us':
        $sent = false; $err = '';
        if ($method === 'POST') {
            check_csrf();
            if (trim($_POST['name'] ?? '') && filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) && trim($_POST['message'] ?? '')) {
                db()->prepare('INSERT INTO messages (name,email,message,created_at) VALUES (?,?,?,?)')->execute([$_POST['name'], $_POST['email'], $_POST['message'], date('c')]); $sent = true;
            } else $err = 'Please fill in all fields with a valid email.';
        }
        page('contact', 'Contact Us', compact('sent', 'err')); break;

    case $path === '/about-us': page('about', 'About Us'); break;
    case $path === '/laboratory-tests': page('laboratory-tests', 'Laboratory Tests'); break;

    case $seg[0] === 'admin':
        if (isset($_POST['logout'])) { unset($_SESSION['admin']); redirect('/admin'); }
        if ($method === 'POST' && isset($_POST['user'])) {
            check_csrf();
            if (hash_equals(cfg('admin_user'), $_POST['user']) && hash_equals(cfg('admin_pass'), $_POST['pass'] ?? '')) $_SESSION['admin'] = true; else flash('Wrong credentials.');
            redirect('/admin');
        }
        if (empty($_SESSION['admin'])) { page('admin_login', 'Admin'); break; }
        // ---- product management ----
        if ($method === 'POST' && isset($_POST['product_action'])) {
            check_csrf();
            $act = $_POST['product_action'];
            if ($act === 'save') {
                $id = (int)($_POST['id'] ?? 0); $name = trim($_POST['name'] ?? '');
                $cat = $_POST['category'] ?? 'other'; $price = (float)($_POST['price'] ?? 0);
                $badge = trim($_POST['badge'] ?? ''); $stock = isset($_POST['in_stock']) ? 1 : 0;
                $desc = trim($_POST['description'] ?? ''); $file = trim($_POST['filename'] ?? '');
                if ($name === '') {
                    flash('Product name is required.');
                } elseif ($id > 0) {
                    db()->prepare('UPDATE products SET name=?, category=?, price=?, badge=?, in_stock=?, description=?, filename=? WHERE id=?')
                        ->execute([$name, $cat, $price, $badge, $stock, $desc, $file, $id]);
                    flash('Product updated.');
                } else {
                    $slug = slugify($name) ?: 'product'; $base = $slug; $k = 2;
                    $chk = db()->prepare('SELECT COUNT(*) FROM products WHERE slug=?');
                    do { $chk->execute([$slug]); $taken = (bool)$chk->fetchColumn(); if ($taken) $slug = $base . '-' . $k++; } while ($taken);
                    $next = (int)db()->query('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM products')->fetchColumn();
                    db()->prepare('INSERT INTO products (slug,name,category,price,badge,in_stock,description,filename,sort_order) VALUES (?,?,?,?,?,?,?,?,?)')
                        ->execute([$slug, $name, $cat, $price, $badge, $stock, $desc, $file, $next]);
                    flash('Product added.');
                }
                redirect('/admin#products');
            } elseif ($act === 'delete') {
                db()->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['id']]);
                flash('Product deleted.'); redirect('/admin#products');
            } elseif ($act === 'move') {
                reorder_product(db(), (int)$_POST['id'], ($_POST['dir'] ?? 'up') === 'down' ? 'down' : 'up');
                redirect('/admin#products');
            } elseif ($act === 'hero_add') {
                $pid = (int)($_POST['product_id'] ?? 0);
                if ($pid) {
                    $next = (int)db()->query('SELECT COALESCE(MAX(hero_order), -1) + 1 FROM products WHERE hero=1')->fetchColumn();
                    db()->prepare('UPDATE products SET hero=1, hero_order=? WHERE id=?')->execute([$next, $pid]);
                    flash('Added to the hero carousel.');
                }
                redirect('/admin#carousel');
            } elseif ($act === 'hero_remove') {
                db()->prepare('UPDATE products SET hero=0, hero_order=0 WHERE id=?')->execute([(int)$_POST['id']]);
                flash('Removed from the hero carousel.');
                redirect('/admin#carousel');
            } elseif ($act === 'hero_move') {
                reorder_hero(db(), (int)$_POST['id'], ($_POST['dir'] ?? 'up') === 'down' ? 'down' : 'up');
                redirect('/admin#carousel');
            }
        }
        if ($method === 'POST' && isset($_POST['order_id'])) {
            check_csrf();
            db()->prepare('UPDATE orders SET status=?, tracking=? WHERE id=?')->execute([$_POST['status'], $_POST['tracking'], (int)$_POST['order_id']]); flash('Order updated.'); redirect('/admin');
        }
        $products = db()->query('SELECT * FROM products ORDER BY sort_order, id')->fetchAll();
        $edit = null;
        if (isset($_GET['edit'])) { $st = db()->prepare('SELECT * FROM products WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null; }
        $drugFiles = array_values(array_diff(scandir(__DIR__ . '/../public/assets/DRUGS'), ['.', '..']));
        $heroProducts = db()->query('SELECT * FROM products WHERE hero=1 ORDER BY hero_order, id')->fetchAll();
        page('admin', 'Admin', [
            'orders' => db()->query('SELECT * FROM orders ORDER BY id DESC')->fetchAll(),
            'msgs' => db()->query('SELECT * FROM messages ORDER BY id DESC LIMIT 20')->fetchAll(),
            'products' => $products, 'edit' => $edit, 'catList' => $DATA['categories'], 'drugFiles' => $drugFiles,
            'heroProducts' => $heroProducts,
        ]); break;

    default: not_found();
}
