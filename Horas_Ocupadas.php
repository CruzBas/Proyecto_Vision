<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$idPsicologo = isset($_GET['id_psicologo']) ? intval($_GET['id_psicologo']) : 0;
$fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : '';

if (!$idPsicologo || !$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    echo json_encode(['success' => false, 'message' => 'Parametros inválidos']);
    exit;
}

$inicioDia = $fecha . ' 00:00:00';
$finDia = $fecha . ' 23:59:59';

$stmt = $mysqli->prepare("SELECT DATE_FORMAT(Fecha_Cita, '%H:%i') AS hora FROM citas WHERE ID_Psicologo = ? AND Fecha_Cita BETWEEN ? AND ? AND Estado <> 'cancelada'");
$stmt->bind_param('iss', $idPsicologo, $inicioDia, $finDia);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en consulta: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$horas = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row['hora'])) {
        $horas[] = $row['hora'];
    }
}
$stmt->close();

echo json_encode(['success' => true, 'horas' => $horas]);
