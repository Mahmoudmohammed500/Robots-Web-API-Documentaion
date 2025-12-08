<?php
// File: config.php

// ---------- Database config via ENV ----------
$dbUrl = getenv('DB_URL'); // اقرأ URL قاعدة البيانات من Environment Variable

if ($dbUrl) {
    $dbParts = parse_url($dbUrl);

    $DB_HOST = $dbParts['host'];
    $DB_PORT = $dbParts['port'] ?? 3306;
    $DB_NAME = ltrim($dbParts['path'], '/');
    $DB_USER = $dbParts['user'];
    $DB_PASS = $dbParts['pass'];
} else {
    // fallback للـ local dev
    $DB_HOST = 'localhost';
    $DB_NAME = 'railway';
    $DB_USER = 'root';
    $DB_PASS = '';
    $DB_PORT = 3306;
}

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// ---------- CORS Headers ----------
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control");

// ---------- Handle preflight OPTIONS request ----------
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---------- Encryption key (APP_ENC_KEY) ----------
$secretFilePath = __DIR__ . '/../app_secret.key';
if (is_readable($secretFilePath)) {
    $APP_ENC_KEY = trim(file_get_contents($secretFilePath));
} else {
    $APP_ENC_KEY = '282f1b1fe064d7cc3d6bf22b8d2ba6c9ba568fff12873c5dff1db5345f6874c1';
    file_put_contents(__DIR__ . "/debug_log.txt", "WARNING: APP_ENC_KEY fallback used\n", FILE_APPEND);
}

if (strlen($APP_ENC_KEY) !== 64) {
    file_put_contents(__DIR__ . "/debug_log.txt", "ERROR: Invalid APP_ENC_KEY length\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Server misconfiguration: invalid encryption key']);
    exit;
}

if (!defined('APP_ENC_KEY')) {
    define('APP_ENC_KEY', $APP_ENC_KEY);
}

file_put_contents(__DIR__ . "/debug_log.txt", "APP_ENC_KEY loaded successfully\n", FILE_APPEND);
