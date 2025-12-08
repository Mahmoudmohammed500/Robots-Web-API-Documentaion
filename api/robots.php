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

// ---------- Override method for FormData ----------
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parts = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));
$idCandidate = end($parts);
$id = is_numeric($idCandidate) ? intval($idCandidate) : null;

// ---------- Helper: File upload ----------
function handleImageUpload($fileField) {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (isset($_FILES[$fileField]) && $_FILES[$fileField]['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES[$fileField]['tmp_name'];
        $originalName = basename($_FILES[$fileField]['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);

        $newName = uniqid('robot_', true) . '.' . $ext;
        $destPath = $uploadDir . $newName;

        if (move_uploaded_file($tmpName, $destPath)) return $newName;
    }
    return null;
}

// ---------- POST / JSON INPUT ----------
$rawInput = file_get_contents("php://input");
$jsonInput = json_decode($rawInput, true);
$data = !empty($_POST) ? $_POST : $jsonInput;

// Decode Sections if string (from FormData)
if (isset($data['Sections']) && is_string($data['Sections'])) {
    $data['Sections'] = json_decode($data['Sections'], true);
}

// Debug log (optional)
file_put_contents(__DIR__ . "/debug_log.txt",
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nID: " . ($id ?? 'null') .
    "\nPOST:\n" . print_r($_POST, true) .
    "\nFILES:\n" . print_r($_FILES, true) .
    "\n\n", FILE_APPEND);


try {
    switch ($method) {

        // ================== GET ==================
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM robots WHERE id = ?");
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
                $stmt = $pdo->query("SELECT * FROM robots ORDER BY id DESC");
                $robots = $stmt->fetchAll();
                foreach ($robots as &$r) {
                    $r['Sections'] = json_decode($r['Sections'], true);
                }
                echo json_encode($robots);
            }
            break;

        // ================== CREATE (POST) ==================
        case 'POST':
            // If this POST is actually update
            if (isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
                goto updateRobot;
            }

            if (!isset($data['RobotName'], $data['projectId'], $data['Sections'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            // Validate project
            $stmt = $pdo->prepare("SELECT projectId FROM Projects WHERE projectId = ?");
            $stmt->execute([intval($data['projectId'])]);
            if ($stmt->rowCount() === 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid projectId']);
                exit;
            }

            // Upload Image
            $imageName = handleImageUpload('Image') ?? trim($data['Image'] ?? '');

            // Insert
            $stmt = $pdo->prepare("INSERT INTO robots (RobotName, Image, projectId, isTrolley, Sections)
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($data['RobotName']),
                $imageName,
                intval($data['projectId']),
                !empty($data['isTrolley']) ? 1 : 0,
                json_encode($data['Sections'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Robot created successfully']);
            break;

        // ================== UPDATE (PUT via POST) ==================
        updateRobot:
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'ID required for update']);
                exit;
            }

            // Get existing robot
            $stmt = $pdo->prepare("SELECT * FROM robots WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['message' => 'Robot not found']);
                exit;
            }

            // Handle image
            if (!empty($_FILES['Image'])) {
                $imageName = handleImageUpload('Image');
            } else {
                $imageName = $existing['Image'];
            }

            // Update
            $stmt = $pdo->prepare("
                UPDATE robots 
                SET 
                    RobotName = ?,
                    Image = ?,
                    projectId = ?,
                    isTrolley = ?,
                    Sections = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $data['RobotName'] ?? $existing['RobotName'],
                $imageName,
                intval($data['projectId'] ?? $existing['projectId']),
                isset($data['isTrolley']) ? intval($data['isTrolley']) : $existing['isTrolley'],
                isset($data['Sections']) ? json_encode($data['Sections']) : $existing['Sections'],
                $id
            ]);

            echo json_encode(['message' => 'Robot updated successfully']);
            break;

        // ================== DELETE ==================
        case 'DELETE':
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM robots WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['message' => 'Robot deleted successfully']);
            } else {
                $pdo->exec("DELETE FROM robots");
                echo json_encode(['message' => 'All robots deleted successfully']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
?>
