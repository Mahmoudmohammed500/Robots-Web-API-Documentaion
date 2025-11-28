<?php
// ---- CORS ----
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// File: api/projects.php
require_once __DIR__ . '/../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$parts = explode('/', trim($requestUri, '/'));
$idCandidate = end($parts);
$projectId = is_numeric($idCandidate) ? intval($idCandidate) : null;

// Determine if request has JSON body (PUT via fetch) or multipart/form-data (POST with image)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

try {
    switch ($method) {

        // ---------- GET (All projects or by ID) ----------
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

        // ---------- POST (Create new project with optional image) ----------
        case 'POST':
            // Use $_POST for form-data
            $projectName = $_POST['ProjectName'] ?? null;
            $location = $_POST['Location'] ?? null;
            $description = $_POST['Description'] ?? null;

            if (!$projectName || !$location || !$description) {
                http_response_code(400);
                echo json_encode(['message' => 'Missing required fields']);
                exit;
            }

            $imageName = null;
            if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $imageName = time() . '-' . basename($_FILES['Image']['name']);
                move_uploaded_file($_FILES['Image']['tmp_name'], $uploadDir . $imageName);
            }

            $stmt = $pdo->prepare("INSERT INTO projects (ProjectName, Description, Location, Image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$projectName, $description, $location, $imageName]);

            http_response_code(201);
            echo json_encode(['message' => 'Project created successfully']);
            break;

        // ---------- PUT (Update project by ID with optional new image) ----------
        case 'PUT':
            if (!$projectId) {
                http_response_code(400);
                echo json_encode(['message' => 'projectId required for update']);
                exit;
            }

            // Check if project exists
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE projectId = ?");
            $stmt->execute([$projectId]);
            $existingProject = $stmt->fetch();
            if (!$existingProject) {
                http_response_code(404);
                echo json_encode(['message' => 'Project not found']);
                exit;
            }

            // Handle PUT data (assume JSON)
            $projectName = $data['ProjectName'] ?? $existingProject['ProjectName'];
            $location = $data['Location'] ?? $existingProject['Location'];
            $description = $data['Description'] ?? $existingProject['Description'];
            $imageName = $existingProject['Image']; // keep old image by default

            // Optional: handle new uploaded image (if sending multipart/form-data via PUT)
            if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $imageName = time() . '-' . basename($_FILES['Image']['name']);
                move_uploaded_file($_FILES['Image']['tmp_name'], $uploadDir . $imageName);
            }

            $stmt = $pdo->prepare("UPDATE projects SET ProjectName = ?, Description = ?, Location = ?, Image = ? WHERE projectId = ?");
            $stmt->execute([$projectName, $description, $location, $imageName, $projectId]);

            echo json_encode(['message' => 'Project updated successfully']);
            break;

        // ---------- DELETE (Delete one or all) ----------
        case 'DELETE':
            if ($projectId) {
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
                $stmt = $pdo->query("DELETE FROM projects");
                echo json_encode(['message' => 'All projects deleted successfully']);
            }
            break;

        // ---------- Default ----------
        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}
