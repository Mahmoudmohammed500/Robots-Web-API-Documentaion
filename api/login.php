<?php
// ---- CORS ----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/config.php';

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

// Accept either Username/Password or username/password keys
$username = $data['Username'] ?? $data['username'] ?? null;
$password = $data['Password'] ?? $data['password'] ?? null;

if ($username === null || $password === null) {
    http_response_code(400);
    echo json_encode(['message' => 'Username and Password are required']);
    exit();
}

// ---------- Encryption key ----------
$encKey = hex2bin(APP_ENC_KEY);
if ($encKey === false || strlen($encKey) !== 32) {
    http_response_code(500);
    echo json_encode(['message' => 'Server misconfiguration: encryption key missing']);
    exit;
}

// ---------- Decrypt function ----------
function decrypt_password(string $b64, string $key): ?string {
    $cipher = 'aes-256-cbc';
    $raw = base64_decode($b64, true);
    if ($raw === false) return null;
    $ivlen = openssl_cipher_iv_length($cipher);
    if (strlen($raw) <= $ivlen) return null;
    $iv = substr($raw, 0, $ivlen);
    $ciphertext = substr($raw, $ivlen);
    $plain = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return $plain === false ? null : $plain;
}

try {
    // Fetch user by exact Username match (case-sensitive)
    $stmt = $pdo->prepare(
        "SELECT id, Username, Password, TelephoneNumber, ProjectName
         FROM Users
         WHERE BINARY Username = BINARY ?
         LIMIT 1"
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid username or password']);
        exit();
    }

    // Decrypt password from DB
    $decryptedPassword = decrypt_password($user['Password'], $encKey);

    if ($decryptedPassword === null || $decryptedPassword !== $password) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid username or password']);
        exit();
    }

    // Successful login — don't send the password back
    unset($user['Password']);
    echo json_encode([
        'message' => 'Login successful',
        'user' => $user
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
