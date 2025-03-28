<?php

header('Content-Type: application/json');

$incidents = [
    ["id" => 1, "reporter" => "Juan Pérez", "description" => "Fuga de agua en el baño", "status" => "pendiente"],
    ["id" => 2, "reporter" => "Ana López", "description" => "Corte de energía en el piso 3", "status" => "en proceso"]
];

$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($uri[0] !== 'incidents') {
    echo json_encode(["error" => "Endpoint no encontrado"]);
    exit;
}

$id = isset($uri[1]) ? (int)$uri[1] : null;

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['reporter']) || strlen($data['description']) < 10) {
            echo json_encode(["error" => "Datos inválidos: El nombre del reportero es requerido y la descripción debe tener al menos 10 caracteres."]);
            exit;
        }
        $data['id'] = count($incidents) + 1;
        $data['status'] = 'pendiente';
        $incidents[] = $data;
        echo json_encode(["success" => "Incidente creado", "data" => $data]);
        break;

    case 'GET':
        if ($id) {
            $incident = array_filter($incidents, fn($inc) => $inc['id'] === $id);
            if (empty($incident)) {
                echo json_encode(["error" => "Incidente no encontrado"]);
                exit;
            }
            echo json_encode(array_values($incident)[0]);
        } else {
            echo json_encode($incidents);
        }
        break;

    case 'PUT':
        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        foreach ($incidents as &$incident) {
            if ($incident['id'] === $id) {
                $incident['status'] = $data['status'] ?? $incident['status'];
                echo json_encode(["success" => "Incidente actualizado", "data" => $incident]);
                exit;
            }
        }
        echo json_encode(["error" => "Incidente no encontrado"]);
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            exit;
        }
        $initialCount = count($incidents);
        $incidents = array_filter($incidents, fn($inc) => $inc['id'] !== $id);
        if (count($incidents) === $initialCount) {
            echo json_encode(["error" => "Incidente no encontrado"]);
            exit;
        }
        echo json_encode(["success" => "Incidente eliminado"]);
        break;

    default:
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
