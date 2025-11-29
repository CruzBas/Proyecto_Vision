<?php
session_start();
header('Content-Type: application/json');


if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

require_once 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    
    $citaId = $_POST['id'];
    $userId = $_SESSION['user_id'];

   
    $queryPaciente = "SELECT ID_Paciente FROM pacientes WHERE ID_Usuario = ?";
    $stmtP = $mysqli->prepare($queryPaciente);
    $stmtP->bind_param("i", $userId);
    $stmtP->execute();
    $resP = $stmtP->get_result();
    
    if ($row = $resP->fetch_assoc()) {
        $idPaciente = $row['ID_Paciente'];

        $queryDelete = "UPDATE citas SET Fecha_Cita = NULL, Estado = 'Cancelada' WHERE  ID_Cita = ? AND ID_Paciente = ?";
        
        if ($stmt = $mysqli->prepare($queryDelete)) {
            $stmt->bind_param("ii", $citaId, $idPaciente);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró la cita o no tienes permiso para eliminarla.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al preparar consulta.']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Perfil de paciente no encontrado.']);
    }
    $stmtP->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
}
?>