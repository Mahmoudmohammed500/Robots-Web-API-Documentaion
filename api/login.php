<?php
require_once __DIR__ . '/../config/config.php';

// ---------- CORS Headers ----------
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control");

// ---------- Handle preflight OPTIONS ----------
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---------- Only POST ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
    exit();
}

// ---------- Read input ----------
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// ---------- Normalize keys ----------
$username = $data['Username'] ?? $data['username'] ?? null;
$password = $data['Password'] ?? $data['password'] ?? null;

// ---------- Validate ----------
if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['message' => 'Username and Password are required']);
    exit();
}

$username = trim($username);
$password = trim($password);

try {
    // ---------- Fetch user ----------
    $stmt = $pdo->prepare("SELECT id, Username, Password, TelephoneNumber, ProjectName FROM Users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['Password'] !== $password) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid username or password']);
        exit();
    }

    // ---------- Successful login ----------
    unset($user['Password']); // لا نرسل الباسورد في الرد
    echo json_encode([
        'message' => 'Login successful',
        'user' => $user
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
