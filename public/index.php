<?php
echo "<h1>Hello Render</h1>";
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "<p>DB connection successful!</p>";
} catch (Exception $e) {
    echo "<p>DB connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
