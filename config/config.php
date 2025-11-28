<?php
// File: config.php

// ---------- Database config ----------
$DB_HOST = '127.0.0.1';
$DB_PORT = 3307; 
$DB_NAME = 'robotswebdb';
$DB_USER = 'root';
$DB_PASS = '';

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
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Cache-Control");

// ---------- Handle preflight OPTIONS request ----------
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---------- Encryption key (APP_ENC_KEY) ----------
$secretFromEnv = getenv('APP_ENC_KEY') ?: (isset($_SERVER['APP_ENC_KEY']) ? $_SERVER['APP_ENC_KEY'] : null);
$secretFilePath = __DIR__ . '/../.app_secret';

if (!empty($secretFromEnv)) {
    $APP_ENC_KEY = trim($secretFromEnv);
} elseif (is_readable($secretFilePath)) {
    $APP_ENC_KEY = trim(file_get_contents($secretFilePath));
} else {
    // Generate a key for local development
    try {
        $generated = bin2hex(random_bytes(32)); // 64 hex chars
        @file_put_contents($secretFilePath, $generated, LOCK_EX);
        @chmod($secretFilePath, 0600);
        $APP_ENC_KEY = $generated;
        file_put_contents(__DIR__ . "/debug_log.txt", "WARNING: APP_ENC_KEY generated for local dev\n", FILE_APPEND);
    } catch (Exception $e) {
        $APP_ENC_KEY = null;
    }
}

// Define constant for backward compatibility if not already defined
if (!defined('APP_ENC_KEY')) {
    if ($APP_ENC_KEY !== null) {
        define('APP_ENC_KEY', $APP_ENC_KEY);
    } else {
        // fallback safe key (local dev only)
        $fallback = bin2hex(random_bytes(32));
        define('APP_ENC_KEY', $fallback);
        file_put_contents(__DIR__ . "/debug_log.txt", "ERROR: APP_ENC_KEY missing, fallback key used\n", FILE_APPEND);
    }
}

