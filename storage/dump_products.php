<?php
require __DIR__ . '/../app/bootstrap.php';
$rows = db()->query('SELECT id, slug, name, category, price FROM products ORDER BY name')->fetchAll();
echo count($rows), " products in DB\n";
foreach ($rows as $r) {
    $img = img_url($r);
    $missing = strpos($img, '.svg') !== false ? '  <-- NO IMAGE FILE' : '';
    printf("%-4s %-36s %-34s %8s  %s%s\n", $r['id'], $r['slug'], substr($r['name'], 0, 34), $r['price'], $img, $missing);
}
