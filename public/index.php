<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function db_status() {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $db = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($path === '/health') {
    header('Content-Type: application/json');
    $dbOk = db_status();
    echo json_encode([
        'status' => 'ok',
        'db' => $dbOk ? 'ok' : 'fail'
    ]);
    exit;
}

// Default homepage
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hello Render</title>
</head>
<body>
<h1>Hello Render</h1>
<?php if (db_status()): ?>
<p>DB connection successful!</p>
<?php else: ?>
<p>DB connection failed!</p>
<?php endif; ?>
</body>
</html>
