<?php
require_once __DIR__ . '/../config/config.php';

// ---------- CORS Headers ----------
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Cache-Control");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$idCandidate = end($parts);
$id = is_numeric($idCandidate) ? intval($idCandidate) : null;

// ---------- Helper function to handle image upload ----------
function handleImageUpload($fileField) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (isset($_FILES[$fileField]) && $_FILES[$fileField]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileField]['tmp_name'];
        $originalName = basename($_FILES[$fileField]['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $newName = uniqid('robot_', true) . '.' . $ext;
        $destPath = $uploadDir . $newName;

        if (move_uploaded_file($tmpName, $destPath)) {
            return $newName;
        }
    }
    return null;
}

// ---------- Fetch input ----------
$data = $_POST; // FormData will be here
if (isset($data['Sections'])) {
    $data['Sections'] = json_decode($data['Sections'], true);
}

// ---------- Debug log ----------
file_put_contents(__DIR__ . "/debug_log.txt",
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nID: " . ($id ?? 'null') .
    "\nPOST:\n" . print_r($_POST, true) .
    "\nFILES:\n" . print_r($_FILES, true) .
    "\n\n", FILE_APPEND);

try {
    switch ($method) {
        // GET all robots or one by ID
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM Robots WHERE id = ?");
                $stmt->execute([$id]);
                $robot = $stmt->fetch();
                if ($robot) {
                    $robot['Sections'] = json_decode($robot['Sections'], true);
                    echo json_encode($robot);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Robot not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM Robots ORDER BY id DESC");
                $robots = $stmt->fetchAll();
                foreach ($robots as &$r) {
                    $r['Sections'] = json_decode($r['Sections'], true);
                }
                echo json_encode($robots);
            }
            break;

        // POST create new robot
        case 'POST':
            if (!isset($data['RobotName'], $data['projectId'], $data['mqttUrl'], $data['Sections'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            // تحقق من وجود المشروع
            $stmt = $pdo->prepare("SELECT projectId FROM Projects WHERE projectId = ?");
            $stmt->execute([intval($data['projectId'])]);
            if ($stmt->rowCount() === 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid projectId: project not found']);
                exit;
            }

            // رفع الصورة
            $imageName = handleImageUpload('Image') ?? trim($data['Image'] ?? '');

            // إدخال الروبوت
            $stmt = $pdo->prepare("INSERT INTO Robots (RobotName, Image, projectId, mqttUrl, isTrolley, Sections)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($data['RobotName']),
                $imageName,
                intval($data['projectId']),
                trim($data['mqttUrl']),
                !empty($data['isTrolley']) ? 1 : 0,
                json_encode($data['Sections'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Robot created successfully']);
            break;

        // PUT update robot
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'ID required for update']);
                exit;
            }

            // تحقق من وجود الروبوت
            $stmt = $pdo->prepare("SELECT * FROM Robots WHERE id = ?");
            $stmt->execute([$id]);
            $existingRobot = $stmt->fetch();
            if (!$existingRobot) {
                http_response_code(404);
                echo json_encode(['message' => 'Robot not found']);
                exit;
            }

            // PUT من FormData أو JSON
            if (!empty($_FILES['Image'])) {
                $imageName = handleImageUpload('Image');
            } else {
                $input = file_get_contents("php://input");
                $putData = json_decode($input, true);
                $imageName = $putData['Image'] ?? $existingRobot['Image'];
                $data = array_merge($putData ?? [], $data ?? []);
            }

            if (isset($data['projectId'])) {
                $stmt = $pdo->prepare("SELECT projectId FROM Projects WHERE projectId = ?");
                $stmt->execute([intval($data['projectId'])]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid projectId: project not found']);
                    exit;
                }
            }

            $stmt = $pdo->prepare("
                UPDATE Robots 
                SET 
                    RobotName = COALESCE(?, RobotName),
                    Image = COALESCE(?, Image),
                    projectId = COALESCE(?, projectId),
                    mqttUrl = COALESCE(?, mqttUrl),
                    isTrolley = COALESCE(?, isTrolley),
                    Sections = COALESCE(?, Sections)
                WHERE id = ?
            ");
            $stmt->execute([
                $data['RobotName'] ?? $existingRobot['RobotName'],
                $imageName ?? $existingRobot['Image'],
                isset($data['projectId']) ? intval($data['projectId']) : $existingRobot['projectId'],
                $data['mqttUrl'] ?? $existingRobot['mqttUrl'],
                isset($data['isTrolley']) ? (int)$data['isTrolley'] : $existingRobot['isTrolley'],
                isset($data['Sections']) ? json_encode($data['Sections']) : $existingRobot['Sections'],
                $id
            ]);

            echo json_encode(['message' => 'Robot updated successfully']);
            break;

        // DELETE robot
        case 'DELETE':
            if ($id) {
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
