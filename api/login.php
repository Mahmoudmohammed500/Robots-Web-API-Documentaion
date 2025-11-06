<?php
require_once __DIR__ . '/../config/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data['Username'], $data['Password'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Username and Password are required']);
    exit;
}

$username = trim($data['Username']);
$password = $data['Password'];

try {
    $stmt = $pdo->prepare("SELECT id, Username, Password, TelephoneNumber, ProjectName FROM Users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid username or password']);
        exit;
    }

    // decrypt stored password
    $encKey = hex2bin(APP_ENC_KEY);
    $storedEncrypted = $user['Password'] ?? '';
    $plainStored = null;
    if ($storedEncrypted !== '') {
        $ivlen = openssl_cipher_iv_length('aes-256-cbc');
        $raw = base64_decode($storedEncrypted);
        if ($raw !== false && strlen($raw) > $ivlen) {
            $iv = substr($raw, 0, $ivlen);
            $ciphertext = substr($raw, $ivlen);
            $plainStored = openssl_decrypt($ciphertext, 'aes-256-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
        }
    }

    if ($plainStored === null) {
        http_response_code(500);
        echo json_encode(['message' => 'Server error: cannot decrypt stored password']);
        exit;
    }

    if (!hash_equals($plainStored, $password)) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid username or password']);
        exit;
    }

    // successful login - don't send password back
    unset($user['Password']);

    echo json_encode([
        'message' => 'Login successful',
        'user' => $user
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
