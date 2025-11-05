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
$logId = is_numeric($idCandidate) ? intval($idCandidate) : null;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

try {
    switch ($method) {

        // 🟢 GET (كل اللوجات أو لوج واحد)
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

        // 🟡 POST (إضافة لوج جديد)
        case 'POST':
            if (!isset($data['projectId'], $data['robotId'], $data['message'], $data['type'], $data['date'], $data['time'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            // تأكد إن المشروع والروبوت موجودين فعلًا
            $stmt = $pdo->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM projects WHERE projectId = ?) AS project_exists,
                    (SELECT COUNT(*) FROM robots WHERE id = ?) AS robot_exists
            ");
            $stmt->execute([$data['projectId'], $data['robotId']]);
            $exists = $stmt->fetch();

            if (!$exists['project_exists'] || !$exists['robot_exists']) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid projectId or robotId']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO logs (projectId, robotId, message, type, date, time) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                intval($data['projectId']),
                intval($data['robotId']),
                trim($data['message']),
                trim($data['type']),
                trim($data['date']),
                trim($data['time'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Log created successfully']);
            break;

        // 🔵 PUT (تحديث)
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

            // تحقق من صحة projectId و robotId
            $stmt = $pdo->prepare("
                SELECT 
                    (SELECT COUNT(*) FROM projects WHERE projectId = ?) AS project_exists,
                    (SELECT COUNT(*) FROM robots WHERE id = ?) AS robot_exists
            ");
            $stmt->execute([$data['projectId'], $data['robotId']]);
            $exists = $stmt->fetch();

            if (!$exists['project_exists'] || !$exists['robot_exists']) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid projectId or robotId']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE logs 
                SET projectId = ?, robotId = ?, message = ?, type = ?, date = ?, time = ?
                WHERE logId = ?
            ");
            $stmt->execute([
                $data['projectId'],
                $data['robotId'],
                $data['message'],
                $data['type'],
                $data['date'],
                $data['time'],
                $logId
            ]);

            echo json_encode(['message' => 'Log updated successfully']);
            break;

        // 🔴 DELETE
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
