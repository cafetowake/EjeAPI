<?php
header('Content-Type: application/json');

$DB_HOST = '127.0.0.1';
$DB_NAME = 'ejeapi';
$DB_USER = 'paula';
$DB_PASS = '1806';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;port=1806;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión a la base de datos: " . $e->getMessage()]);
    exit;
}

if (php_sapi_name() !== 'cli') {
    echo json_encode(["error" => "Este script solo debe ejecutarse en la terminal (CLI)."]);
    exit;
}

if ($argc < 2) {
    echo json_encode(["error" => "Uso: php index.php {GET|POST|PUT|DELETE} [id]"]);
    exit;
}

$method = strtoupper($argv[1]);
$incidentId = $argc > 2 ? $argv[2] : null;

try {
    switch ($method) {
        case 'POST':

            echo "Ingrese el nombre del reportante: ";
            $reporter = trim(fgets(STDIN));
            if (empty($reporter)) {
                echo json_encode(["error" => "El campo 'reporter' es obligatorio"]);
                exit;
            }

            echo "Ingrese la descripción del incidente (mínimo 10 caracteres): ";
            $description = trim(fgets(STDIN));
            if (strlen($description) < 10) {
                echo json_encode(["error" => "La descripción debe tener al menos 10 caracteres"]);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO incidents (reporter, description, status, created_at) VALUES (?, ?, 'pendiente', NOW())");
            $stmt->execute([$reporter, $description]);
            $newId = $pdo->lastInsertId();

            echo json_encode(["success" => "Incidente creado con ID $newId"]);
            exit;

        case 'GET':
            if ($incidentId) {
                $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
                $stmt->execute([$incidentId]);
                $incident = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($incident) {
                    echo json_encode($incident);
                } else {
                    echo json_encode(["error" => "Incidente no encontrado"]);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM incidents ORDER BY created_at DESC");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            exit;

        case 'PUT':
            if (!$incidentId || !ctype_digit($incidentId)) {
                echo json_encode(["error" => "Debe proporcionar un ID válido para actualizar"]);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
            $stmt->execute([$incidentId]);
            $incident = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$incident) {
                echo json_encode(["error" => "Incidente no encontrado"]);
                exit;
            }

            echo "Ingrese el nuevo estado (pendiente, en proceso, resuelto): ";
            $status = trim(fgets(STDIN));
            $allowedStatuses = ['pendiente', 'en proceso', 'resuelto'];
            if (!in_array($status, $allowedStatuses)) {
                echo json_encode(["error" => "Estado no válido"]);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?");
            $stmt->execute([$status, $incidentId]);

            echo json_encode(["success" => "Incidente actualizado"]);
            exit;

        case 'DELETE':
            if (!$incidentId || !ctype_digit($incidentId)) {
                echo json_encode(["error" => "Debe proporcionar un ID válido para eliminar"]);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM incidents WHERE id = ?");
            $stmt->execute([$incidentId]);
            $incident = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$incident) {
                echo json_encode(["error" => "Incidente no encontrado"]);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM incidents WHERE id = ?");
            $stmt->execute([$incidentId]);

            echo json_encode(["success" => "Incidente eliminado"]);
            exit;

        default:
            echo json_encode(["error" => "Método no permitido"]);
            exit;
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de base de datos: " . $e->getMessage()]);
    exit;
}
?>
