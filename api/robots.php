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

// Debug log
file_put_contents(__DIR__ . "/debug_log.txt",
    "=== $method " . date("Y-m-d H:i:s") . " ===\n" .
    "URI: $requestUri\n" .
    "ID: " . ($id ?? 'null') . "\n" .
    "RAW:\n$input\n" .
    "Decoded:\n" . print_r($data, true) . "\n\n",
    FILE_APPEND
);

try {
    switch ($method) {

        //  Get all robots or get one by ID
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM Robots WHERE id = ?");
                $stmt->execute([$id]);
                $robot = $stmt->fetch();

                if ($robot) {
                    $robot['ActiveBtns'] = json_decode($robot['ActiveBtns'], true);
                    echo json_encode($robot);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Robot not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM Robots ORDER BY id DESC");
                $robots = $stmt->fetchAll();

                foreach ($robots as &$r) {
                    $r['ActiveBtns'] = json_decode($r['ActiveBtns'], true);
                }

                echo json_encode($robots);
            }
            break;

        //  Create new robot
        case 'POST':
            if (!isset($data['RobotName'], $data['Image'], $data['projectId'], $data['Voltage'], $data['Cycles'], $data['Status'], $data['ActiveBtns'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO Robots (RobotName, Image, projectId, Voltage, Cycles, Status, ActiveBtns) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($data['RobotName']),
                trim($data['Image']),
                intval($data['projectId']),
                intval($data['Voltage']),
                intval($data['Cycles']),
                in_array($data['Status'], ['Running', 'Stop']) ? $data['Status'] : 'Stop',
                json_encode($data['ActiveBtns'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Robot created successfully']);
            break;

        //  Update robot by ID
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'ID required for update']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT id FROM Robots WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Robot not found']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE Robots 
                SET 
                    RobotName = COALESCE(?, RobotName),
                    Image = COALESCE(?, Image),
                    projectId = COALESCE(?, projectId),
                    Voltage = COALESCE(?, Voltage),
                    Cycles = COALESCE(?, Cycles),
                    Status = COALESCE(?, Status),
                    ActiveBtns = COALESCE(?, ActiveBtns)
                WHERE id = ?
            ");
            $stmt->execute([
                $data['RobotName'] ?? null,
                $data['Image'] ?? null,
                isset($data['projectId']) ? intval($data['projectId']) : null,
                isset($data['Voltage']) ? intval($data['Voltage']) : null,
                isset($data['Cycles']) ? intval($data['Cycles']) : null,
                isset($data['Status']) && in_array($data['Status'], ['Running', 'Stop']) ? $data['Status'] : null,
                isset($data['ActiveBtns']) ? json_encode($data['ActiveBtns']) : null,
                $id
            ]);

            echo json_encode(['message' => 'Robot updated successfully']);
            break;

        //  Delete robot by ID or ALL if no ID
        case 'DELETE':
            if ($id) {
                $stmt = $pdo->prepare("SELECT id FROM Robots WHERE id = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Robot not found']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM Robots WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['message' => 'Robot deleted successfully']);
            } else {
                $pdo->exec("DELETE FROM Robots");
                echo json_encode(['message' => 'All robots deleted successfully']);
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
