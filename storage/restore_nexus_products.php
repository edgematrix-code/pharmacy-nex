<?php
// One-off recovery: re-insert the original curated Nexus catalog that was wiped
// when the DRUGS import re-seeded `products`. Data comes from the MySQL row-based
// binlog before-images (see parse_binlog_del.php).
declare(strict_types=1);

$apply   = in_array('--apply', $argv, true);
$jsonIn  = $argv[count($argv) - 1] ?? __DIR__ . '/deleted_products.json';
if (!is_file($jsonIn)) $jsonIn = __DIR__ . '/deleted_products.json';

$rows = json_decode((string)file_get_contents($jsonIn), true);
if (!is_array($rows) || !$rows) exit("No recovered rows in $jsonIn\n");

// [@1 id, @2 slug, @3 name, @4 category, @5 price, @6 badge, @7 in_stock, @8 description]
usort($rows, fn($a, $b) => (int)$a[1] <=> (int)$b[1]);

$cfg = require __DIR__ . '/../config.php';
$pdo = new PDO("mysql:host={$cfg['db_host']};port={$cfg['db_port']};dbname={$cfg['db_name']};charset=utf8mb4",
    $cfg['db_user'], $cfg['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

$cats = array_column($pdo->query('SELECT slug FROM categories')->fetchAll(), 'slug');
$existing = array_column($pdo->query('SELECT slug FROM products')->fetchAll(), 'slug');
$existing = array_flip($existing);

$toInsert = [];
foreach ($rows as $r) {
    $slug = (string)$r[2];
    if ($slug === '' || isset($existing[$slug])) continue;
    if (strlen($slug) > 100) { echo "SKIP (slug too long): $slug\n"; continue; }
    $cat = $r[4];
    if (!in_array($cat, $cats, true)) { echo "SKIP (unknown category '$cat'): $slug\n"; continue; }
    $toInsert[] = [
        'slug' => $slug, 'name' => (string)$r[3], 'category' => $cat,
        'price' => (float)$r[5], 'badge' => (string)($r[6] ?? ''), 'in_stock' => (int)($r[7] ?? 1),
        'description' => (string)($r[8] ?? ''),
    ];
}

echo "recovered rows:   " . count($rows) . "\n";
echo "to insert:        " . count($toInsert) . "\n";
echo "skipped/existing: " . (count($rows) - count($toInsert)) . "\n";
$shift = count($toInsert);

if (!$apply) {
    echo "\n[DRY RUN] would shift existing sort_order by +$shift and insert:\n";
    foreach (array_slice($toInsert, 0, 12) as $p) printf("  %-44s %-12s %8.2f\n", $p['slug'], $p['category'], $p['price']);
    echo "  ... (+" . max(0, count($toInsert) - 12) . " more)\n\nRe-run with --apply to write.\n";
    exit;
}

// Back up the current catalog first.
$backup = __DIR__ . '/products_backup_' . date('Ymd-His') . '.json';
file_put_contents($backup, json_encode($pdo->query('SELECT * FROM products ORDER BY sort_order, id')->fetchAll(), JSON_PRETTY_PRINT));
echo "backup written:   $backup\n";

$pdo->beginTransaction();
try {
    $shiftStmt = $pdo->prepare('UPDATE products SET sort_order = sort_order + ?');
    $shiftStmt->execute([$shift]);

    $ins = $pdo->prepare('INSERT INTO products (slug,name,category,price,badge,in_stock,description,filename,sort_order,hero,hero_order) VALUES (?,?,?,?,?,?,?,?,?,0,0)');
    $so = 0;
    foreach ($toInsert as $p) {
        $ins->execute([$p['slug'], $p['name'], $p['category'], $p['price'], $p['badge'], $p['in_stock'], $p['description'], '', $so++]);
    }
    $pdo->commit();
    echo "inserted:         $so\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    exit('FAILED, rolled back: ' . $e->getMessage() . "\n");
}

echo "total products now: " . $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() . "\n";
