<?php
// ---- CORS ----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
// File: buttons.php
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
$id = is_numeric($idCandidate) ? intval($idCandidate) : null;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// ---------- Debug log ----------
file_put_contents(__DIR__ . "/debug_log.txt",
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nID: " . ($id ?? 'null') .
    "\nRAW:\n$input\nDecoded:\n" . print_r($data, true) . "\n\n", FILE_APPEND);

try {
    switch ($method) {

        // ✅ GET buttons
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM buttons WHERE BtnID = ?");
                $stmt->execute([$id]);
                $button = $stmt->fetch();

                if ($button) {
                    echo json_encode($button);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Button not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM buttons ORDER BY BtnID DESC");
                $buttons = $stmt->fetchAll();
                echo json_encode($buttons);
            }
            break;

        // ✅ POST - Create button
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
            $section = $_GET['section'] ?? null;

            if (!$section) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing section parameter in URL (e.g. ?section=main)']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM Robots WHERE id = ?");
            $stmt->execute([$robotId]);
            $robot = $stmt->fetch();

            if (!$robot) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid RobotId: robot not found']);
                exit;
            }

            $sections = json_decode($robot['Sections'], true);

            if (!isset($sections[$section]) || !is_array($sections[$section])) {
                http_response_code(400);
                echo json_encode(['message' => "Section '$section' is not active for this robot"]);
                exit;
            }

            $projectId = intval($robot['projectId']);
            $stmt = $pdo->prepare("INSERT INTO buttons (BtnName, RobotId, Color, Operation, projectId) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$btnName, $robotId, $color, $operation, $projectId]);
            $newBtnId = $pdo->lastInsertId();

            if (!isset($sections[$section]['ActiveBtns']) || !is_array($sections[$section]['ActiveBtns'])) {
                $sections[$section]['ActiveBtns'] = [];
            }

            $sections[$section]['ActiveBtns'][] = [
                'Name' => $btnName,
                'id'   => $newBtnId
            ];

            $stmt = $pdo->prepare("UPDATE Robots SET Sections = ? WHERE id = ?");
            $stmt->execute([json_encode($sections), $robotId]);

            http_response_code(201);
            echo json_encode([
                'message' => "Button added successfully to section '$section'",
                'BtnID' => $newBtnId
            ]);
            break;

        // ✅ PUT - Update button
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'BtnID required for update']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM buttons WHERE BtnID = ?");
            $stmt->execute([$id]);
            $button = $stmt->fetch();

            if (!$button) {
                http_response_code(404);
                echo json_encode(['message' => 'Button not found']);
                exit;
            }

            $btnName = $data['BtnName'] ?? $button['BtnName'];
            $robotId = isset($data['RobotId']) ? intval($data['RobotId']) : $button['RobotId'];
            $color = $data['Color'] ?? $button['Color'];
            $operation = $data['Operation'] ?? $button['Operation'];
            $section = $_GET['section'] ?? null;

            $stmt = $pdo->prepare("SELECT * FROM Robots WHERE id = ?");
            $stmt->execute([$robotId]);
            $robot = $stmt->fetch();

            if (!$robot) {
                http_response_code(400);
                echo json_encode(['message' => 'Robot not found.']);
                exit;
            }

            $sections = json_decode($robot['Sections'], true);

            if ($section) {
                if (!isset($sections[$section])) {
                    http_response_code(400);
                    echo json_encode(['message' => "Section '$section' does not exist in this robot."]);
                    exit;
                }

                $found = false;
                if (isset($sections[$section]['ActiveBtns']) && is_array($sections[$section]['ActiveBtns'])) {
                    foreach ($sections[$section]['ActiveBtns'] as &$b) {
                        if (isset($b['id']) && $b['id'] == $id) {
                            $b['Name'] = $btnName;
                            $found = true;
                        }
                    }
                }

                if (!$found) {
                    http_response_code(400);
                    echo json_encode(['message' => 'This button is not part of the specified section for this robot.']);
                    exit;
                }
            }

            $stmt = $pdo->prepare("UPDATE buttons SET BtnName = ?, RobotId = ?, Color = ?, Operation = ? WHERE BtnID = ?");
            $stmt->execute([$btnName, $robotId, $color, $operation, $id]);

            $stmt = $pdo->prepare("UPDATE Robots SET Sections = ? WHERE id = ?");
            $stmt->execute([json_encode($sections), $robotId]);

            echo json_encode(['message' => 'Button updated successfully.']);
            break;

        // ✅ DELETE button
        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'BtnID required for deletion']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM buttons WHERE BtnID = ?");
            $stmt->execute([$id]);
            $button = $stmt->fetch();

            if (!$button) {
                http_response_code(404);
                echo json_encode(['message' => 'Button not found']);
                exit;
            }

            $section = $_GET['section'] ?? null;
            $robotId = intval($button['RobotId']);
            $stmt = $pdo->prepare("SELECT * FROM Robots WHERE id = ?");
            $stmt->execute([$robotId]);
            $robot = $stmt->fetch();

            if (!$robot) {
                http_response_code(400);
                echo json_encode(['message' => 'Robot not found.']);
                exit;
            }

            $sections = json_decode($robot['Sections'], true);
            $found = false;

            if ($section && isset($sections[$section]['ActiveBtns']) && is_array($sections[$section]['ActiveBtns'])) {
                foreach ($sections[$section]['ActiveBtns'] as $b) {
                    if (isset($b['id']) && $b['id'] == $id) {
                        $found = true;
                        break;
                    }
                }
            }

            if ($section && !$found) {
                http_response_code(400);
                echo json_encode(['message' => 'This button is not part of the specified section for this robot.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM buttons WHERE BtnID = ?");
            $stmt->execute([$id]);

            foreach ($sections as &$sec) {
                if (isset($sec['ActiveBtns']) && is_array($sec['ActiveBtns'])) {
                    $sec['ActiveBtns'] = array_values(array_filter(
                        $sec['ActiveBtns'],
                        fn($b) => !isset($b['id']) || $b['id'] != $id
                    ));
                }
            }

            $stmt = $pdo->prepare("UPDATE Robots SET Sections = ? WHERE id = ?");
            $stmt->execute([json_encode($sections), $robotId]);

            echo json_encode(['message' => 'Button deleted successfully.']);
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
