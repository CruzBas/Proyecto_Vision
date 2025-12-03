
<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$idCita = intval($input['id'] ?? $input['id_cita'] ?? 0);

if (!$idCita) {
    echo json_encode(['success' => false, 'message' => 'ID de cita no proporcionado']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmtP = $mysqli->prepare("SELECT ID_Paciente FROM pacientes WHERE ID_Usuario = ?");
$stmtP->bind_param('i', $userId);
$stmtP->execute();
$resP = $stmtP->get_result();
$paciente = $resP->fetch_assoc();
$stmtP->close();

if (!$paciente) {
    echo json_encode(['success' => false, 'message' => 'Perfil de paciente no encontrado']);
    exit;
}
$idPaciente = $paciente['ID_Paciente'];


$stmtGet = $mysqli->prepare("SELECT ID_Psicologo, DATE(Fecha_Cita) as Fecha, DATE_FORMAT(Fecha_Cita, '%H:%i') as Hora FROM citas WHERE ID_Cita = ? AND ID_Paciente = ?");
$stmtGet->bind_param('ii', $idCita, $idPaciente);
$stmtGet->execute();
$resCita = $stmtGet->get_result();
$citaActual = $resCita->fetch_assoc();
$stmtGet->close();

if (!$citaActual) {
    echo json_encode(['success' => false, 'message' => 'Cita no encontrada o no tienes permiso para editarla']);
    exit;
}

$nuevoIdPsicologo = !empty($input['id_psicologo']) ? intval($input['id_psicologo']) : $citaActual['ID_Psicologo'];
$nuevaFecha       = !empty($input['fecha']) ? trim($input['fecha']) : $citaActual['Fecha'];
$nuevaHora        = !empty($input['hora']) ? trim($input['hora']) : $citaActual['Hora'];
$nuevaFechaCompleta = $nuevaFecha . ' ' . $nuevaHora;

$stmtCheck = $mysqli->prepare("
    SELECT 1 FROM citas 
    WHERE ID_Psicologo = ? 
      AND Fecha_Cita = ? 
      AND Estado <> 'cancelada' 
      AND ID_Cita != ? 
    LIMIT 1
");
$stmtCheck->bind_param('isi', $nuevoIdPsicologo, $nuevaFechaCompleta, $idCita);
$stmtCheck->execute();
if ($stmtCheck->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El horario seleccionado ya está ocupado.']);
    $stmtCheck->close();
    exit;
}
$stmtCheck->close();


$stmtUpdate = $mysqli->prepare("UPDATE citas SET ID_Psicologo = ?, Fecha_Cita = ? WHERE ID_Cita = ? AND ID_Paciente = ?");
$stmtUpdate->bind_param('isii', $nuevoIdPsicologo, $nuevaFechaCompleta, $idCita, $idPaciente);

if ($stmtUpdate->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cita actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar en BD: ' . $mysqli->error]);
}
$stmtUpdate->close();
?>