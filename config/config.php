<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// ---------- Database config (unchanged) ----------
$DB_HOST = '127.0.0.1';
$DB_NAME = 'RobotsWebDB';
$DB_USER = 'root';
$DB_PASS = '';

// ---------- Encryption key (APP_ENC_KEY) ----------
/*
 Recommended:
  - In production set APP_ENC_KEY as an environment variable (64 hex chars, i.e. 32 bytes).
  - For local/dev, this file will attempt to read ../.app_secret or generate one automatically.
*/
$secretFromEnv = getenv('APP_ENC_KEY') ?: (isset($_SERVER['APP_ENC_KEY']) ? $_SERVER['APP_ENC_KEY'] : null);
$secretFilePath = __DIR__ . '/../.app_secret';

if (!empty($secretFromEnv)) {
    $APP_ENC_KEY = trim($secretFromEnv);
} elseif (is_readable($secretFilePath)) {
    $APP_ENC_KEY = trim(file_get_contents($secretFilePath));
} else {
    // Generate a key for local development and save it for subsequent runs.
    // WARNING: This is convenient for local testing but NOT recommended for production.
    try {
        $generated = bin2hex(random_bytes(32)); // 64 hex chars
        // attempt to write; ignore failure but keep generated in memory
        @file_put_contents($secretFilePath, $generated, LOCK_EX);
        // try to restrict permissions (may be ignored on Windows)
        @chmod($secretFilePath, 0600);
        $APP_ENC_KEY = $generated;
    } catch (Exception $e) {
        // fallback to null (will be handled by code that requires the key)
        $APP_ENC_KEY = null;
    }
}

// Define constant for backward compatibility if not already defined
if (!defined('APP_ENC_KEY')) {
    if ($APP_ENC_KEY !== null) {
        define('APP_ENC_KEY', $APP_ENC_KEY);
    }
}

// ---------- PDO (unchanged) ----------
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
