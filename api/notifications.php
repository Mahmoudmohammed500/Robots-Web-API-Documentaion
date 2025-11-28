<?php
// ---- CORS ----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// File: notifications.php
require_once __DIR__ . '/../config/config.php';
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

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$idCandidate = end($parts);
$notificationId = is_numeric($idCandidate) ? intval($idCandidate) : null;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// ---------- Debug log ----------
file_put_contents(__DIR__ . "/debug_log.txt", 
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nnotificationId: " . ($notificationId ?? 'null') . 
    "\nRAW:\n$input\nDecoded:\n" . print_r($data, true) . "\n\n", FILE_APPEND);

try {
    switch ($method) {

        // GET (All notifications or by ID)
        case 'GET':
            if ($notificationId) {
                $stmt = $pdo->prepare("SELECT * FROM notifications WHERE notificationId = ?");
                $stmt->execute([$notificationId]);
                $notification = $stmt->fetch();
                if ($notification) {
                    echo json_encode($notification);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Notification not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM notifications ORDER BY notificationId DESC");
                $notifications = $stmt->fetchAll();
                echo json_encode($notifications);
            }
            break;

        // POST (Create new notification)
        case 'POST':
            if (!isset($data['topic_main'], $data['message'], $data['type'], $data['date'], $data['time'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO notifications (topic_main, message, type, date, time) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($data['topic_main']),
                trim($data['message']),
                trim($data['type']),
                trim($data['date']),
                trim($data['time'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Notification created successfully']);
            break;

        // PUT (Update notification by ID)
        case 'PUT':
            if (!$notificationId) {
                http_response_code(400);
                echo json_encode(['message' => 'notificationId required for update']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT notificationId FROM notifications WHERE notificationId = ?");
            $stmt->execute([$notificationId]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Notification not found']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE notifications 
                SET topic_main = ?, message = ?, type = ?, date = ?, time = ?
                WHERE notificationId = ?
            ");
            $stmt->execute([
                trim($data['topic_main']),
                trim($data['message']),
                trim($data['type']),
                trim($data['date']),
                trim($data['time']),
                $notificationId
            ]);

            echo json_encode(['message' => 'Notification updated successfully']);
            break;

        // DELETE (Delete one or all)
        case 'DELETE':
            if ($notificationId) {
                $stmt = $pdo->prepare("SELECT notificationId FROM notifications WHERE notificationId = ?");
                $stmt->execute([$notificationId]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Notification not found']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM notifications WHERE notificationId = ?");
                $stmt->execute([$notificationId]);

                echo json_encode(['message' => 'Notification deleted successfully']);
            } else {
                $stmt = $pdo->query("DELETE FROM notifications");
                echo json_encode(['message' => 'All notifications deleted successfully']);
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
