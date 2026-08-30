<?php
$envPaths = [__DIR__ . '/../.env', __DIR__ . '/.env', dirname(__DIR__) . '/.env'];
$env = [];
foreach ($envPaths as $p) {
    if (is_readable($p)) {
        foreach (file($p, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim(trim($v), "\"'");
        }
        break;
    }
}
$servername = $env['HOST'] ?? getenv('HOST') ?: 'localhost';
$username   = $env['USERNAME'] ?? getenv('USERNAME') ?: 'root';
$password   = $env['PASSWORD'] ?? getenv('PASSWORD') ?: '';
$dbname     = $env['DATABASE'] ?? getenv('DATABASE') ?: 'country';
$port       = (int)($env['PORT'] ?? getenv('PORT') ?: 4000);

$conn = mysqli_init();
mysqli_ssl_set($conn, null, null, null, null, null);
if (!mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port, null, MYSQLI_CLIENT_SSL)) {
    die("Connection Failed: " . mysqli_connect_error());
}
$conn->set_charset("utf8mb4");
?>
