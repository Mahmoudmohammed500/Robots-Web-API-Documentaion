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

try {
    switch ($method) {

        // ✅ Get all robots or one by ID
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

        // ✅ Create new robot
        case 'POST':
            if (!isset($data['RobotName'], $data['projectId'], $data['mqttUrl'], $data['Sections'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            // 🧱 تحقق من وجود المشروع
            $stmt = $pdo->prepare("SELECT projectId FROM Projects WHERE projectId = ?");
            $stmt->execute([intval($data['projectId'])]);
            if ($stmt->rowCount() === 0) {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid projectId: project not found']);
                exit;
            }

            // ✅ إدخال الروبوت
            $stmt = $pdo->prepare("INSERT INTO Robots (RobotName, Image, projectId, mqttUrl, isTrolley, Sections)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($data['RobotName']),
                trim($data['Image'] ?? ''),
                intval($data['projectId']),
                trim($data['mqttUrl']),
                !empty($data['isTrolley']) ? 1 : 0,
                json_encode($data['Sections'])
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Robot created successfully']);
            break;

        // ✅ Update robot
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['message' => 'ID required for update']);
                exit;
            }

            // تحقق من أن الروبوت موجود
            $stmt = $pdo->prepare("SELECT id FROM Robots WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Robot not found']);
                exit;
            }

            // 🧱 تحقق من المشروع في حال تعديل projectId
            if (isset($data['projectId'])) {
                $stmt = $pdo->prepare("SELECT projectId FROM Projects WHERE projectId = ?");
                $stmt->execute([intval($data['projectId'])]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid projectId: project not found']);
                    exit;
                }
            }

            // ✅ تحديث البيانات
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
                $data['RobotName'] ?? null,
                $data['Image'] ?? null,
                isset($data['projectId']) ? intval($data['projectId']) : null,
                $data['mqttUrl'] ?? null,
                isset($data['isTrolley']) ? (int)$data['isTrolley'] : null,
                isset($data['Sections']) ? json_encode($data['Sections']) : null,
                $id
            ]);

            echo json_encode(['message' => 'Robot updated successfully']);
            break;

        // ✅ Delete
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
