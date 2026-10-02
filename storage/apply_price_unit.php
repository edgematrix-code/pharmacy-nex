<?php
// One-time catalog pricing + unit pass.
//
//   * Every product gets a deterministic price spread across $300 - $800.
//   * Products whose image lives in public/assets/DRUGS/ are sold by the gram
//     (badge "1 g"); products whose image lives in public/assets/img/ are sold
//     by the millilitre (badge "1 ml").
//
// Updates both app/data.php (the seed source) and the live products table.
//
// Usage: php storage/apply_price_unit.php [--apply]
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';

$apply = in_array('--apply', $argv, true);
$pdo = db();

$drugsDir = __DIR__ . '/../public/assets/DRUGS/';
$imgDir = __DIR__ . '/../public/assets/img/';

// Deterministic, stable price in 300..800 (step 5) derived from the filename.
function priced(string $key): float {
    $h = crc32($key);
    return 300.0 + (float)(($h % 101) * 5);
}
// Unit badge: gram for DRUGS images, millilitre for img images.
function unit_badge(string $file, string $drugsDir, string $imgDir): string {
    if ($file !== '' && is_file($drugsDir . $file)) return '1 g';
    if ($file !== '' && is_file($imgDir . $file)) return '1 ml';
    return '';
}
function price_str(float $n): string {
    return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
}

$DATA = require __DIR__ . '/../app/data.php';
$rows = $DATA['products'];

// ---- 1. Rewrite app/data.php -------------------------------------------------
$out = "<?php\n// Seed catalog: pharmaceutical products.\n// [name, category, price, pack badge, in_stock, description, filename]\nreturn [\n 'categories' => [\n";
foreach ($DATA['categories'] as $slug => [$name, $color]) {
    $out .= "   " . var_export($slug, true) . " => [" . var_export($name, true) . ", " . var_export($color, true) . "],\n";
}
$out .= " ],\n 'products' => [\n";

$gram = 0; $ml = 0; $min = 1e9; $max = 0; $updated = [];
$imgMarker = "  // Products whose image lives in public/assets/img/ (name = filename without extension).\n";
$imgIndex = null;
foreach ($rows as $i => $r) {
    $file = $r[6] ?? '';
    if (is_file($imgDir . $file)) { $imgIndex = $imgIndex ?? $i; }
}

$upd = $pdo->prepare('UPDATE products SET price=?, badge=? WHERE filename=? AND filename<>""');
if ($apply) {
    $backup = __DIR__ . '/products_backup_' . date('Ymd-His') . '.json';
    file_put_contents($backup, json_encode($pdo->query('SELECT * FROM products ORDER BY id')->fetchAll(), JSON_PRETTY_PRINT));
    echo "Backup written: $backup\n";
    $pdo->beginTransaction();
}

foreach ($rows as $i => $r) {
    $file = $r[6] ?? '';
    $price = priced($file !== '' ? $file : $r[0]);
    $badge = unit_badge($file, $drugsDir, $imgDir);
    if ($badge === '1 g') $gram++; elseif ($badge === '1 ml') $ml++;
    $min = min($min, $price); $max = max($max, $price);

    if ($apply && $file !== '') {
        $upd->execute([$price, $badge, $file]);
        $updated[] = $file;
    }

    if ($imgIndex !== null && $i === $imgIndex) $out .= $imgMarker;
    $out .= "  [" . implode(', ', array_map(fn($v) => var_export($v, true), [
        $r[0], $r[1], (float)$price, $badge, (int)($r[4] ?? 1), $r[5] ?? '', $file,
    ])) . "],\n";
}
$out .= "],\n];\n";

echo "Products:       " . count($rows) . "\n";
echo "By gram (1 g):  $gram\n";
echo "By ml (1 ml):   $ml\n";
echo "Price range:    \$" . price_str($min) . " - \$" . price_str($max) . "\n";

if (!$apply) {
    echo "\nDry run. Re-run with --apply to write data.php and update the DB.\n";
    exit;
}

if (file_put_contents(__DIR__ . '/../app/data.php', $out) === false) {
    $pdo->rollBack();
    exit("Failed to write app/data.php\n");
}
$pdo->commit();
echo "\nUpdated DB rows: " . count($updated) . "\n";
echo "Wrote app/data.php\n";
