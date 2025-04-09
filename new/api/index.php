<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: application/json; charset=UTF-8");



error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$resourceIndex = array_search('incidents', $uri);
$id = $resourceIndex !== false && isset($uri[$resourceIndex + 1]) ? $uri[$resourceIndex + 1] : null;

if ($resourceIndex === false) {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint no encontrado"]);
    exit;
}

try {
    switch ($requestMethod) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
                $stmt->execute([$id]);
                $incident = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$incident) {
                    http_response_code(404);
                    echo json_encode(["message" => "Incidente no encontrado"]);
                    exit;
                }
                echo json_encode($incident);
            } else {
                try {
                    $stmt = $pdo->query("SELECT * FROM incidents");
                    $incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($incidents)) {
                        echo json_encode(["message" => "No hay incidentes registrados"]);
                    } else {
                        echo json_encode($incidents);
                    }
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
                }
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (empty($data['reporter'])) {
                http_response_code(400);
                echo json_encode(["message" => "El campo 'reporter' es obligatorio"]);
                exit;
            }
            
            if (strlen($data['description'] ?? '') < 10) {
                http_response_code(400);
                echo json_encode(["message" => "La descripción debe tener al menos 10 caracteres"]);
                exit;
            }
            
            $sql = "INSERT INTO incidents (reporter, description) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$data['reporter'], $data['description']]);
            
            $newIncident = [
                'id' => $pdo->lastInsertId(),
                'reporter' => $data['reporter'],
                'description' => $data['description'],
                'status' => 'pendiente',
                'created_at' => date('Y-m-d H:i:s')
            ];
            http_response_code(201);
            echo json_encode($newIncident);
            break;

        case 'PUT':
            if (!$id || !is_numeric($id)) {
                http_response_code(400);
                echo json_encode(["message" => "ID inválido"]);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(["message" => "Incidente no encontrado"]);
                exit;
            }
            
            $data = json_decode(file_get_contents("php://input"), true);
            $allowedStatuses = ['pendiente', 'en proceso', 'resuelto'];
            if (!isset($data['status']) || !in_array($data['status'], $allowedStatuses)) {
                http_response_code(400);
                echo json_encode(["message" => "Estado no válido. Valores permitidos: " . implode(', ', $allowedStatuses)]);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $id]);
            
            $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            break;

        case 'DELETE':
            if (!$id || !is_numeric($id)) {
                http_response_code(400);
                echo json_encode(["message" => "ID inválido"]);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT id FROM incidents WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(["message" => "Incidente no encontrado"]);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM incidents WHERE id = ?");
            $stmt->execute([$id]);
            http_response_code(204);
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Método no permitido"]);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error en la base de datos: " . $e->getMessage()]);
}
?>