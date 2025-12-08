<?php
// ---- CORS ----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
// File: api/users.php
require_once __DIR__ . '/../config/config.php';


// ---------- Handle preflight OPTIONS request ----------
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parts = explode('/', trim($requestUri, '/'));
$idCandidate = end($parts);
$id = is_numeric($idCandidate) ? intval($idCandidate) : null;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// ---------- Debug log ----------
file_put_contents(__DIR__ . "/debug_log.txt",
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nID: " . ($id ?? 'null') .
    "\nRAW:\n$input\nDecoded:\n" . print_r($data, true) . "\n\n",
    FILE_APPEND
);

// ---------- Encryption key ----------
$encKey = hex2bin(APP_ENC_KEY);
if ($encKey === false || strlen($encKey) !== 32) {
    file_put_contents(__DIR__ . "/debug_log.txt", "ERROR: Invalid APP_ENC_KEY\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['message' => 'Server misconfiguration: encryption key missing']);
    exit;
}

function encrypt_password(string $plaintext, string $key): string {
    $cipher = 'aes-256-cbc';
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = random_bytes($ivlen);
    $raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $raw);
}

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
    switch($method) {

        // ---------- GET ----------
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT id, Username, Password, TelephoneNumber, Email, ProjectName FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $user['Password'] = decrypt_password($user['Password'], $encKey);
                    echo json_encode($user);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'User not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT id, Username, Password, TelephoneNumber, Email, ProjectName FROM users ORDER BY id DESC");
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($users as &$u) {
                    $u['Password'] = decrypt_password($u['Password'], $encKey);
                }
                echo json_encode($users);
            }
            break;

        // ---------- POST ----------
        case 'POST':
            if (!isset($data['Username'], $data['Password'], $data['TelephoneNumber'], $data['ProjectName'], $data['Email'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO users (Username, Password, TelephoneNumber, Email, ProjectName) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($data['Username']),
                encrypt_password($data['Password'], $encKey),
                trim($data['TelephoneNumber']),
                trim($data['Email']),
                trim($data['ProjectName'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'User created successfully']);
            break;

        // ---------- PUT ----------
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'ID required for update']);
                exit;
            }
            if (!isset($data['Username'], $data['Password'], $data['TelephoneNumber'], $data['ProjectName'], $data['Email'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users 
                SET Username = ?, Password = ?, TelephoneNumber = ?, Email = ?, ProjectName = ? 
                WHERE id = ?");
            $stmt->execute([
                $data['Username'],
                encrypt_password($data['Password'], $encKey),
                $data['TelephoneNumber'],
                $data['Email'],
                $data['ProjectName'],
                $id
            ]);

            echo json_encode(['message' => 'User updated successfully']);
            break;

        // ---------- DELETE ----------
        case 'DELETE':
            if ($id) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['message' => 'User not found']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['message' => 'User deleted successfully']);
            } else {
                $stmt = $pdo->query("DELETE FROM users");
                echo json_encode(['message' => 'All users deleted successfully']);
            }
            break;

        // ---------- Default ----------
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            break;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
