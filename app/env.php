<?php
declare(strict_types=1);

// Minimal, dependency-free .env loader.
//
// Loads KEY=VALUE pairs from the project's .env file into an internal map and
// makes them available through env(). Real environment variables (set by the
// web server / Docker / CI) always win over the .env file, so deployments can
// override without editing files. Keys are NOT pushed into getenv()/$_ENV to
// avoid leaking secrets into phpinfo() or child processes.

function env_load(?string $path = null): void {
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;

    $path ??= dirname(__DIR__) . '/.env';
    if (!is_file($path) || !is_readable($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) return;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        // Allow an optional "export " prefix (shell-style .env files).
        if (str_starts_with($line, 'export ')) $line = trim(substr($line, 7));
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === '') continue;

        // Strip matching surrounding quotes; keep everything else verbatim.
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $_ENV['__dotenv'][$key] ??= $value;
    }
}

// Read a config value: real env var first, then .env, then $default.
function env(string $key, ?string $default = null): ?string {
    env_load();

    // A real environment variable takes precedence over the .env file.
    $val = getenv($key);
    if ($val !== false && $val !== '') return $val;

    $val = $_ENV['__dotenv'][$key] ?? null;
    return ($val === null || $val === '') ? $default : (string)$val;
}

// Same as env() but coerced to int (used for ports, page sizes, thresholds).
function env_int(string $key, int $default): int {
    $v = env($key, (string)$default);
    return is_numeric($v) ? (int)$v : $default;
}

// Same as env() but coerced to float (used for prices and shipping).
function env_float(string $key, float $default): float {
    $v = env($key, (string)$default);
    return is_numeric($v) ? (float)$v : $default;
}
