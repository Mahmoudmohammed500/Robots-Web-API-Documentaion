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
$projectId = is_numeric($idCandidate) ? intval($idCandidate) : null;

$input = file_get_contents("php://input");
$data = json_decode($input, true);

file_put_contents(__DIR__ . "/debug_log.txt", 
    "=== $method " . date("Y-m-d H:i:s") . " ===\nURI: $requestUri\nprojectId: " . ($projectId ?? 'null') . 
    "\nRAW:\n$input\nDecoded:\n" . print_r($data, true) . "\n\n", FILE_APPEND);

try {
    switch ($method) {

        //  GET (All projects or by ID)
        case 'GET':
            if ($projectId) {
                $stmt = $pdo->prepare("SELECT * FROM projects WHERE projectId = ?");
                $stmt->execute([$projectId]);
                $project = $stmt->fetch();
                if ($project) {
                    echo json_encode($project);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Project not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM projects ORDER BY projectId DESC");
                $projects = $stmt->fetchAll();
                echo json_encode($projects);
            }
            break;

        //  POST (Create new project)
        case 'POST':
            if (!isset($data['ProjectName'], $data['Description'], $data['Location'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO projects (ProjectName, Description, Location, Image) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                trim($data['ProjectName']),
                trim($data['Description']),
                trim($data['Location']),
                $data['Image'] ?? null
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Project created successfully']);
            break;

        // PUT (Update project by ID)
        case 'PUT':
            if (!$projectId) {
                http_response_code(400);
                echo json_encode(['message' => 'projectId required for update']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT projectId FROM projects WHERE projectId = ?");
            $stmt->execute([$projectId]);
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['message' => 'Project not found']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE projects 
                SET 
                    ProjectName = ?, 
                    Description = ?, 
                    Location = ?, 
                    Image = ?
                WHERE projectId = ?
            ");
            $stmt->execute([
                $data['ProjectName'],
                $data['Description'],
                $data['Location'],
                $data['Image'] ?? null,
                $projectId
            ]);

            echo json_encode(['message' => 'Project updated successfully']);
            break;

        //  DELETE (Delete one or all)
        case 'DELETE':
            if ($projectId) {
                // 🗑 Delete specific project
                $stmt = $pdo->prepare("SELECT projectId FROM projects WHERE projectId = ?");
                $stmt->execute([$projectId]);
                if ($stmt->rowCount() === 0) {
                    http_response_code(404);
                    echo json_encode(['message' => 'Project not found']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM projects WHERE projectId = ?");
                $stmt->execute([$projectId]);

                echo json_encode(['message' => 'Project deleted successfully']);
            } else {
                //  Delete all projects
                $stmt = $pdo->query("DELETE FROM projects");
                echo json_encode(['message' => 'All projects deleted successfully']);
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
