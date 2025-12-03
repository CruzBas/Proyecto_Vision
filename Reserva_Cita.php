<?php
// Reserva_Cita.php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']); 
    exit;
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
$idPsicologo = isset($input['id_psicologo']) ? intval($input['id_psicologo']) : 0;
$motivo = isset($input['motivo']) ? trim($input['motivo']) : null; // NUEVO CAMPO
$duracion = isset($input['duracion']) ? intval($input['duracion']) : 60; // NUEVO CAMPO con default

// Validaciones
if (!$fecha || !$hora || !$idPsicologo) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']); 
    exit;
}

$q = $mysqli->prepare("SELECT ID_Paciente FROM pacientes WHERE ID_Usuario = ?");
$q->bind_param('i', $userId);
$q->execute();
$res = $q->get_result();
$paciente = $res->fetch_assoc();

if (!$paciente) {
    echo json_encode(['success' => false, 'message' => 'Perfil de paciente no encontrado']); 
    exit;
}

$idPaciente = $paciente['ID_Paciente'];
$fechaCompleta = $fecha . ' ' . $hora;

// Inserción con todos los campos relevantes
$stmt = $mysqli->prepare("INSERT INTO citas 
    (ID_Paciente, ID_Psicologo, Fecha_Cita, Motivo, Estado, Duracion) 
    VALUES (?, ?, ?, ?, 'Pendiente', ?)");
$stmt->bind_param('iissi', $idPaciente, $idPsicologo, $fechaCompleta, $motivo, $duracion);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id_cita' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error BD: ' . $mysqli->error]);
}
?>
