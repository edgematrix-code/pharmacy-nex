<?php
// Rebuild the products catalog so it contains exactly the products in
// public/assets/DRUGS/, using app/data.php as the source of truth
// (each product's name = its filename minus the extension).
//
// Usage: php storage/rebuild_drugs_catalog.php [--apply]
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';

$apply = in_array('--apply', $argv, true);
$pdo = db();

$DATA = require __DIR__ . '/../app/data.php';
$drugsDir = __DIR__ . '/../public/assets/DRUGS/';
$files = array_values(array_diff(scandir($drugsDir), ['.', '..']));
$fileSet = array_flip($files);

// data.php rows keyed by filename
$catalog = [];
foreach ($DATA['products'] as $r) {
    $catalog[$r[6]] = $r;
}

echo "DRUGS files on disk: " . count($files) . "\n";
echo "data.php rows:       " . count($DATA['products']) . "\n";
echo "data.php filenames:  " . count($catalog) . "\n";

$existing = $pdo->query('SELECT id, slug, name, filename FROM products ORDER BY id')->fetchAll();
$byFilename = [];
$toDelete = [];
foreach ($existing as $e) {
    if ($e['filename'] !== '' && isset($catalog[$e['filename']])) {
        $byFilename[$e['filename']] = $e;
    } else {
        $toDelete[] = $e;
    }
}
echo "existing rows matching a data.php file: " . count($byFilename) . "\n";
echo "existing rows to DELETE:                " . count($toDelete) . "\n";

if (!$apply) {
    echo "\nDry run. Re-run with --apply to make changes.\n";
    exit;
}

$pdo->beginTransaction();

$upd = $pdo->prepare('UPDATE products SET name=?, category=?, price=?, badge=?, in_stock=?, description=?, filename=?, sort_order=? WHERE id=?');
$ins = $pdo->prepare('INSERT INTO products (slug,name,category,price,badge,in_stock,description,filename,sort_order) VALUES (?,?,?,?,?,?,?,?,?)');
$delIds = array_column($toDelete, 'id');
if ($delIds) {
    $in = implode(',', array_fill(0, count($delIds), '?'));
    $pdo->prepare("DELETE FROM products WHERE id IN ($in)")->execute($delIds);
}

$used = array_fill_keys($pdo->query('SELECT slug FROM products')->fetchAll(PDO::FETCH_COLUMN), true);
$i = 0; $updated = 0; $inserted = 0;
foreach ($DATA['products'] as $r) {
    [$name, $cat, $price, $badge, $stock, $desc] = $r;
    $file = $r[6] ?? '';
    if (isset($byFilename[$file])) {
        $rid = (int)$byFilename[$file]['id'];
        $upd->execute([display_product_name($name, $file, $rid), $cat, $price, $badge, $stock, $desc, $file, $i, $rid]);
        $updated++;
    } else {
        $ins->execute([unique_slug(slugify($name), $used), $name, $cat, $price, $badge, $stock, $desc, $file, $i]);
        $nid = (int)$pdo->lastInsertId();
        if (is_junk_image($file)) $pdo->prepare('UPDATE products SET name=? WHERE id=?')->execute(['Product #' . $nid, $nid]);
        $inserted++;
    }
    $i++;
}

$pdo->commit();

echo "\nDeleted:  " . count($delIds) . "\n";
echo "Updated:  $updated\n";
echo "Inserted: $inserted\n";
$total = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
echo "Total products now: $total\n";
