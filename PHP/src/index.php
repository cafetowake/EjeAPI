<?php
header('Content-Type: application/json');

$DB_HOST = 'localhost';
$DB_NAME = 'ejeapi';
$DB_USER = 'paula';
$DB_PASS = '1806';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos: " . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$cleanPath = substr($uri, strlen($scriptDir));
$pathSegments = explode('/', trim($cleanPath, '/'));

try {
    switch ($method) {
        case 'POST':
            if ($pathSegments[0] === 'incidents' && count($pathSegments) === 1) {
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(["error" => "JSON inválido"]);
                    exit;
                }
                
                if (empty($data['reporter'])) {
                    http_response_code(400);
                    echo json_encode(["error" => "El campo 'reporter' es obligatorio"]);
                    exit;
                }
                if (empty($data['description']) || strlen($data['description']) < 10) {
                    http_response_code(400);
                    echo json_encode(["error" => "La descripción debe tener al menos 10 caracteres"]);
                    exit;
                }
                
                $stmt = $pdo->prepare("INSERT INTO incidents (reporter, description, status, created_at) VALUES (?, ?, 'pendiente', NOW())");
                $stmt->execute([$data['reporter'], $data['description']]);
                $newId = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
                $stmt->execute([$newId]);
                $newIncident = $stmt->fetch(PDO::FETCH_ASSOC);
                
                http_response_code(201);
                echo json_encode($newIncident);
                exit;
            }
            break;

        case 'GET':
            if ($pathSegments[0] === 'incidents' && count($pathSegments) === 1) {
                $stmt = $pdo->query("SELECT * FROM incidents ORDER BY created_at DESC");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                exit;
            } elseif ($pathSegments[0] === 'incidents' && count($pathSegments) === 2) {
                $incidentId = $pathSegments[1];
                if (!ctype_digit($incidentId)) {
                    http_response_code(400);
                    echo json_encode(["error" => "ID debe ser un número entero"]);
                    exit;
                }
                
                $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
                $stmt->execute([$incidentId]);
                $incident = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($incident) {
                    echo json_encode($incident);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Incidente no encontrado"]);
                }
                exit;
            }
            break;

        case 'PUT':
            if ($pathSegments[0] === 'incidents' && count($pathSegments) === 2) {
                $incidentId = $pathSegments[1];
                if (!ctype_digit($incidentId)) {
                    http_response_code(400);
                    echo json_encode(["error" => "ID debe ser un número entero"]);
                    exit;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(["error" => "JSON inválido"]);
                    exit;
                }
                
                if (!isset($data['status'])) {
                    http_response_code(400);
                    echo json_encode(["error" => "El campo 'status' es obligatorio"]);
                    exit;
                }
                
                $allowedStatuses = ['pendiente', 'en proceso', 'resuelto'];
                if (!in_array($data['status'], $allowedStatuses)) {
                    http_response_code(400);
                    echo json_encode(["error" => "Estado no válido"]);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
                $stmt->execute([$data['status'], $incidentId]);
                
                $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
                $stmt->execute([$incidentId]);
                $updatedIncident = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($updatedIncident) {
                    echo json_encode($updatedIncident);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Incidente no encontrado"]);
                }
                exit;
            }
            break;

        case 'DELETE':
            if ($pathSegments[0] === 'incidents' && count($pathSegments) === 2) {
                $incidentId = $pathSegments[1];
                if (!ctype_digit($incidentId)) {
                    http_response_code(400);
                    echo json_encode(["error" => "ID debe ser un número entero"]);
                    exit;
                }
                
                $stmt = $pdo->prepare("SELECT id FROM incidents WHERE id = ?");
                $stmt->execute([$incidentId]);
                if (!$stmt->fetch()) {
                    http_response_code(404);
                    echo json_encode(["error" => "Incidente no encontrado"]);
                    exit;
                }
                
                $stmt = $pdo->prepare("DELETE FROM incidents WHERE id = ?");
                $stmt->execute([$incidentId]);
                echo json_encode(["success" => "Incidente eliminado"]);
                exit;
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
            exit;
    }
    
    http_response_code(404);
    echo json_encode(["error" => "Endpoint no encontrado"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de base de datos: " . $e->getMessage()]);
    exit;
}
?>
