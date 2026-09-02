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
mysqli_report(MYSQLI_REPORT_OFF);

// use .env first, then platform env only if HOST is set (avoid Windows USERNAME collision)
$hasPlatformEnv = getenv('HOST') !== false && getenv('HOST') !== '';
$servername = $env['HOST'] ?? ($hasPlatformEnv ? getenv('HOST') : 'localhost');
$username   = $env['USERNAME'] ?? ($hasPlatformEnv ? getenv('USERNAME') : 'root');
$password   = $env['PASSWORD'] ?? ($hasPlatformEnv ? getenv('PASSWORD') : '');
$dbname     = $env['DATABASE'] ?? ($hasPlatformEnv ? getenv('DATABASE') : 'country');
$port       = (int)($env['PORT'] ?? ($hasPlatformEnv ? getenv('PORT') : 3306));

$conn = mysqli_init();
$isTiDB = strpos($servername, 'tidbcloud.com') !== false || $port === 4000;
$isPlaceholder = $password === 'YOUR_TIDB_PASSWORD_HERE' || $password === 'CHANGE_ME';

if ($isTiDB) {
    mysqli_ssl_set($conn, null, null, null, null, null);
    $flags = MYSQLI_CLIENT_SSL;
} else {
    $flags = 0;
}

$connected = @mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port, null, $flags);

if (!$connected && $isTiDB) {
    $conn = mysqli_init();
    $servername = '127.0.0.1';
    $username = 'root';
    $password = '';
    $dbname = 'country';
    $port = 3306;
    $flags = 0;
    $connected = @mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port, null, $flags);
}

if (!$connected) {
    $err = mysqli_connect_error();
    if ($err === '' || $err === null) $err = 'Unknown connection error';
    if ($isPlaceholder) {
        die("Connection Failed: $err<br>Tip: Your .env PASSWORD is still 'YOUR_TIDB_PASSWORD_HERE'. Ask teammate for real TiDB password and update D:\\cse370_t\\.env and D:\\cse370_t\\DataExplorer\\.env . Fallback local DB also failed – ensure XAMPP MySQL is running and country DB imported.");
    }
    die("Connection Failed: " . $err);
}
$conn->set_charset("utf8mb4");
?>
