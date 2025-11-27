<?php
// guardar_cita.php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']); exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input) || empty($input)) {
    $input = $_POST; 
}
if (!is_array($input) || empty($input)) {
    echo json_encode(['success' => false, 'message' => 'Sin datos recibidos']);
    exit;
}

$userId = $_SESSION['user_id'];
$fecha = isset($input['fecha']) ? trim($input['fecha']) : '';
$hora = isset($input['hora']) ? trim($input['hora']) : '';
$idPsicologo = isset($input['id_psicologo']) ? intval($input['id_psicologo']) : 0; // NUEVO CAMPO

// Validaciones
if (!$fecha || !$hora || !$idPsicologo) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']); exit;
}


$q = $mysqli->prepare("SELECT ID_Paciente FROM pacientes WHERE ID_Usuario = ?");
$q->bind_param('i', $userId);
$q->execute();
$res = $q->get_result();
$paciente = $res->fetch_assoc();

if (!$paciente) {
    echo json_encode(['success' => false, 'message' => 'Perfil de paciente no encontrado']); exit;
}

$idPaciente = $paciente['ID_Paciente'];
$fechaCompleta = $fecha . ' ' . $hora;

// Verificar si ya existe una cita con el mismo psicólogo en la misma fecha y hora
$check = $mysqli->prepare("SELECT 1 FROM citas WHERE ID_Psicologo = ? AND DATE(Fecha_Cita) = ? AND DATE_FORMAT(Fecha_Cita, '%H:%i') = ? AND Estado <> 'cancelada' LIMIT 1");
$check->bind_param('iss', $idPsicologo, $fecha, $hora);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El horario ya no está disponible para este psicólogo']);
    exit;
}
$check->close();


$stmt = $mysqli->prepare("INSERT INTO citas (ID_Paciente, ID_Psicologo, Fecha_Cita, Estado) VALUES (?, ?, ?, 'pendiente')");
$stmt->bind_param('iis', $idPaciente, $idPsicologo, $fechaCompleta);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error BD: ' . $mysqli->error]);
}
?>
