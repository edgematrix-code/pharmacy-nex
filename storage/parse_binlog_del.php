<?php
// Temporary recovery helper: pull the before-image rows for `nexus`.`products`
// out of the mysqlbinlog DECODE-ROWS dump.
$file = $argv[1] ?? __DIR__ . '/binlog000007.txt';

$unescape = function (string $v): ?string {
    if ($v === 'NULL') return null;
    if (strlen($v) >= 2 && $v[0] === "'" && substr($v, -1) === "'") $v = substr($v, 1, -1);
    return str_replace(["\\'", '\\\\', '\\n', '\\r', '\\0'], ["'", '\\', "\n", "\r", "\0"], $v);
};

const MARK = '### DELETE FROM `nexus`.`products`';
$handle = fopen($file, 'r');
$inDel = false;
$cur = [];
$rows = [];

$flush = function () use (&$cur, &$rows) {
    if ($cur) { $rows[] = $cur; $cur = []; }
};

while (($ln = fgets($handle)) !== false) {
    $ln = rtrim($ln, "\r\n");
    if ($ln === MARK) {
        $flush();
        $inDel = true;
        continue;
    }
    if (!$inDel) continue;
    if ($ln === '### WHERE') continue;
    if (preg_match('/^###\s+@(\d+)=(.*)$/', $ln, $m)) {
        $cur[(int)$m[1]] = $unescape($m[2]);
        continue;
    }
    // Any other line ends the current row's image.
    $flush();
    $inDel = false;
}
$flush();
fclose($handle);

$byId = [];
foreach ($rows as $r) if (isset($r[1])) $byId[(int)$r[1]] = $r;
fwrite(STDERR, "delete images: " . count($rows) . ", distinct ids: " . count($byId) . "\n");
echo json_encode(array_values($byId), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
