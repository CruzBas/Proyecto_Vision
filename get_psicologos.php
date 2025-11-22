<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$idEsp = isset($_GET['especialidad']) ? intval($_GET['especialidad']) : 0;
if (!$idEsp) {
    echo json_encode(['success' => false, 'message' => 'Especialidad requerida']);
    exit;
}

if (!$stmt = $mysqli->prepare("SELECT ID_Psicologo, Nombre_Psicologo, Apellido_Psicologo FROM psicologos WHERE ID_Especialidad = ?")) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en consulta (prep): '.$mysqli->error]);
    exit;
}

$stmt->bind_param('i', $idEsp);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en consulta (exec): '.$stmt->error]);
    exit;
}

$res = $stmt->get_result();
$psicologos = [];
while ($row = $res->fetch_assoc()) {
    $psicologos[] = [
        'id' => $row['ID_Psicologo'],
        'nombre' => trim($row['Nombre_Psicologo'].' '.$row['Apellido_Psicologo'])
    ];
}

echo json_encode(['success' => true, 'psicologos' => $psicologos]);
