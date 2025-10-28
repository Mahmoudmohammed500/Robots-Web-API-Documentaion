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

file_put_contents(__DIR__ . "/debug_log.txt", 
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nID: " . ($id ?? 'null') . 
    "\nRAW:\n$input\nDecoded:\n" . print_r($data, true) . "\n\n", FILE_APPEND);

try {
    switch ($method) {

        //  Get all buttons or get one by BtnID
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM Buttons WHERE BtnID = ?");
                $stmt->execute([$id]);
                $button = $stmt->fetch();

                if ($button) {
                    echo json_encode($button);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Button not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM Buttons ORDER BY BtnID DESC");
                $buttons = $stmt->fetchAll();
                echo json_encode($buttons);
            }
            break;

        //  Create new button
        case 'POST':
            if (!isset($data['BtnName'], $data['RobotId'], $data['Color'], $data['Operation'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $btnName = trim($data['BtnName']);
            $robotId = intval($data['RobotId']);
            $color = trim($data['Color']);
            $operation = trim($data['Operation']);

            $stmt = $pdo->prepare("INSERT INTO Buttons (BtnName, RobotId, Color, Operation) VALUES (?, ?, ?, ?)");
            $stmt->execute([$btnName, $robotId, $color, $operation]);

            http_response_code(201);
            echo json_encode(['message' => 'Button created successfully']);
            break;

        //  Update button by BtnID
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'BtnID required for update']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT BtnID FROM Buttons WHERE BtnID = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Button not found']);
                exit;
            }

            $btnName = $data['BtnName'] ?? null;
            $robotId = isset($data['RobotId']) ? intval($data['RobotId']) : null;
            $color = $data['Color'] ?? null;
            $operation = $data['Operation'] ?? null;

            $stmt = $pdo->prepare("
                UPDATE Buttons
                SET 
                    BtnName = COALESCE(?, BtnName),
                    RobotId = COALESCE(?, RobotId),
                    Color = COALESCE(?, Color),
                    Operation = COALESCE(?, Operation)
                WHERE BtnID = ?
            ");
            $stmt->execute([$btnName, $robotId, $color, $operation, $id]);

            echo json_encode(['message' => 'Button updated successfully']);
            break;

        //  Delete one button or all
        case 'DELETE':
            if ($id) {
                //  Delete by ID
                $stmt = $pdo->prepare("SELECT BtnID FROM Buttons WHERE BtnID = ?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Button not found']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM Buttons WHERE BtnID = ?");
                $stmt->execute([$id]);

                echo json_encode(['message' => 'Button deleted successfully']);
            } else {
                //  Delete all buttons
                $stmt = $pdo->query("DELETE FROM Buttons");
                echo json_encode(['message' => 'All buttons deleted successfully']);
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
