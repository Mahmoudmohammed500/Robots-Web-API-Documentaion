<?php
require_once __DIR__ . '/../config/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parts = explode('/', trim($requestUri, '/'));
$idCandidate = end($parts);
$id = is_numeric($idCandidate) ? intval($idCandidate) : null;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Log Debug
file_put_contents(__DIR__ . "/debug_log.txt", "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: " . $requestUri . "\nID: " . ($id ?? 'null') . "\nRAW:\n$input\nDecoded:\n" . print_r($data, true) . "\n\n", FILE_APPEND);

try {
    switch($method) {

        case 'GET':
            if ($id) {
                // GET by ID
                $stmt = $pdo->prepare("SELECT id, Username, TelephoneNumber, ProjectName FROM Users WHERE id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch();
                if ($user) {
                    echo json_encode($user);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'User not found']);
                }
            } else {
                // GET all
                $stmt = $pdo->query("SELECT id, Username, TelephoneNumber, ProjectName FROM Users ORDER BY id DESC");
                $users = $stmt->fetchAll();
                echo json_encode($users);
            }
            break;

        case 'POST':
            if (!isset($data['Username'], $data['Password'], $data['TelephoneNumber'], $data['ProjectName'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $username = trim($data['Username']);
            $password = password_hash($data['Password'], PASSWORD_DEFAULT);
            $telephone = trim($data['TelephoneNumber']);
            $project = trim($data['ProjectName']);

            $stmt = $pdo->prepare("INSERT INTO Users (Username, Password, TelephoneNumber, ProjectName) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $telephone, $project]);

            http_response_code(201);
            echo json_encode(['message' => 'User created successfully']);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'ID required for update']);
                exit;
            }
            if (!isset($data['Username'], $data['Password'], $data['TelephoneNumber'], $data['ProjectName'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id FROM Users WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
                exit;
            }

            $username = $data['Username'];
            $password = password_hash($data['Password'], PASSWORD_DEFAULT);
            $telephone = $data['TelephoneNumber'];
            $project = $data['ProjectName'];

            $stmt = $pdo->prepare("UPDATE Users SET Username = ?, Password = ?, TelephoneNumber = ?, ProjectName = ? WHERE id = ?");
            $stmt->execute([$username, $password, $telephone, $project, $id]);

            echo json_encode(['message' => 'User updated successfully']);
            break;

        case 'DELETE':
            if ($id) {
                $stmt = $pdo->prepare("SELECT id FROM Users WHERE id = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['message' => 'User not found']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM Users WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['message' => 'User deleted successfully']);
            } else {
                $stmt = $pdo->query("DELETE FROM Users");
                echo json_encode(['message' => 'All users deleted successfully']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            break;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
