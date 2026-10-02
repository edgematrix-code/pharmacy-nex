<?php
require __DIR__ . '/../app/bootstrap.php';
$pdo = db();
echo 'server: ', $pdo->getAttribute(PDO::ATTR_SERVER_VERSION), "\n";
$before = $pdo->query("SELECT id, slug, price, name FROM products WHERE slug='oxandrolone'")->fetchAll();
echo "before: ", json_encode($before), "\n";
$sql = 'INSERT INTO products (slug,name,category,price,badge,in_stock,description) VALUES (?,?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price=VALUES(price), badge=VALUES(badge), in_stock=VALUES(in_stock), description=VALUES(description)';
$st = $pdo->prepare($sql);
$st->execute(['oxandrolone', 'Oxandrolone', 'steroids', 90.00, '10mg Tablets', 1, 'oxandrolone test']);
echo "affected rows: ", $st->rowCount(), "\n";
$after = $pdo->query("SELECT id, slug, price, name FROM products WHERE slug='oxandrolone'")->fetchAll();
echo "after: ", json_encode($after), "\n";
$cnt = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
echo "total products: $cnt\n";
