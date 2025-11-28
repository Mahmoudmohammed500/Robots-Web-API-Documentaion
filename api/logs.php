<?php
// ---- CORS ----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
require_once __DIR__ . '/../config/config.php';

// ---------- Handle preflight OPTIONS request ----------
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$idCandidate = end($parts);
$logId = is_numeric($idCandidate) ? intval($idCandidate) : null;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// ---------- Debug log ----------
file_put_contents(__DIR__ . "/debug_log.txt", 
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nlogId: " . ($logId ?? 'null') . 
    "\nRAW:\n$input\nDecoded:\n" . print_r($data, true) . "\n\n", FILE_APPEND);

try {
    switch ($method) {

        // GET (All logs or single log)
        case 'GET':
            if ($logId) {
                $stmt = $pdo->prepare("SELECT * FROM logs WHERE logId = ?");
                $stmt->execute([$logId]);
                $log = $stmt->fetch();
                if ($log) {
                    echo json_encode($log);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Log not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM logs ORDER BY logId DESC");
                $logs = $stmt->fetchAll();
                echo json_encode($logs);
            }
            break;

        // POST (Create new log)
        case 'POST':
            if (!isset($data['topic_main'], $data['message'], $data['type'], $data['date'], $data['time'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO logs (topic_main, message, type, date, time) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($data['topic_main']),
                trim($data['message']),
                trim($data['type']),
                trim($data['date']),
                trim($data['time'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Log created successfully']);
            break;

        // PUT (Update log)
        case 'PUT':
            if (!$logId) {
                http_response_code(400);
                echo json_encode(['message' => 'logId required for update']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT logId FROM logs WHERE logId = ?");
            $stmt->execute([$logId]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Log not found']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE logs 
                SET topic_main = ?, message = ?, type = ?, date = ?, time = ?
                WHERE logId = ?
            ");
            $stmt->execute([
                trim($data['topic_main']),
                trim($data['message']),
                trim($data['type']),
                trim($data['date']),
                trim($data['time']),
                $logId
            ]);

            echo json_encode(['message' => 'Log updated successfully']);
            break;

        // DELETE (Delete single or all logs)
        case 'DELETE':
            if ($logId) {
                $stmt = $pdo->prepare("SELECT logId FROM logs WHERE logId = ?");
                $stmt->execute([$logId]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Log not found']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM logs WHERE logId = ?");
                $stmt->execute([$logId]);

                echo json_encode(['message' => 'Log deleted successfully']);
            } else {
                $stmt = $pdo->query("DELETE FROM logs");
                echo json_encode(['message' => 'All logs deleted successfully']);
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
