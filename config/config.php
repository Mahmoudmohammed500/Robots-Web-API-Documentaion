<?php
// File: config.php

// ---------- Database config ----------
$DB_HOST = 'nozomi.proxy.rlwy.net';
$DB_NAME = 'railway';
$DB_USER = 'root';
$DB_PASS = 'icRgAivWFhUlJbjNuFfOgqQgcJvIUsgm'; // الباسورد من Railway
$DB_PORT = 16187;


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
// سنستخدم ملف app_secret.key بدل الملفات المخفية لتجنب مشاكل InfinityFree
$secretFilePath = __DIR__ . '/../app_secret.key';

if (is_readable($secretFilePath)) {
    $APP_ENC_KEY = trim(file_get_contents($secretFilePath));
} else {
    // fallback لمفتاح ثابت مؤقت (لـ local dev فقط)
    $APP_ENC_KEY = '282f1b1fe064d7cc3d6bf22b8d2ba6c9ba568fff12873c5dff1db5345f6874c1';
    file_put_contents(__DIR__ . "/debug_log.txt", "WARNING: APP_ENC_KEY fallback used\n", FILE_APPEND);
}

// التحقق من طول المفتاح (32 bytes بعد التحويل من hex)
if (strlen($APP_ENC_KEY) !== 64) {
    file_put_contents(__DIR__ . "/debug_log.txt", "ERROR: Invalid APP_ENC_KEY length\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Server misconfiguration: invalid encryption key']);
    exit;
}

// تعريف الثابت للاستخدام في بقية الكود
if (!defined('APP_ENC_KEY')) {
    define('APP_ENC_KEY', $APP_ENC_KEY);
}

// ---------- Debug log ----------
file_put_contents(__DIR__ . "/debug_log.txt", "APP_ENC_KEY loaded successfully\n", FILE_APPEND);
