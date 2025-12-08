<?php
// ===============================================
// BLOQUE 1: INICIALIZACIÓN Y AUTENTICACIÓN
// ===============================================

// Permitir solicitudes JSON
header('Content-Type: application/json');
session_start();

// Incluir conexión a la base de datos
require_once 'db.php';

// Asignar la conexión
$conn = $mysqli;

// Verificar conexión a la BD
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
error_log("DEBUG: user_id = " . $user_id);


// ===============================================
// BLOQUE 2: GET - OBTENER CITAS DEL PSICÓLOGO
// ===============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_citas') {
    try {
        // Obtener ID del psicólogo relacionado con el usuario autenticado
        $sql_psicologo = "SELECT ID_Psicologo FROM psicologos WHERE ID_Usuario = ? LIMIT 1";
        
        error_log("DEBUG: Buscando psicólogo con ID_Usuario = " . $user_id);
        
        $stmt_psicologo = $conn->prepare($sql_psicologo);
        if (!$stmt_psicologo) {
            error_log("ERROR: Prepare failed: " . $conn->error);
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!$stmt_psicologo->bind_param('i', $user_id)) {
            error_log("ERROR: Bind param failed: " . $stmt_psicologo->error);
            throw new Exception("Bind param failed: " . $stmt_psicologo->error);
        }
        
        if (!$stmt_psicologo->execute()) {
            error_log("ERROR: Execute failed: " . $stmt_psicologo->error);
            throw new Exception("Execute failed: " . $stmt_psicologo->error);
        }
        
        $result_psicologo = $stmt_psicologo->get_result();
        
        error_log("DEBUG: Resultado num_rows = " . $result_psicologo->num_rows);
        
        if ($result_psicologo->num_rows === 0) {
            error_log("ERROR: No se encontró psicólogo para user_id = " . $user_id);
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No eres un psicólogo registrado']);
            $stmt_psicologo->close();
            exit;
        }
        
        $psicologo = $result_psicologo->fetch_assoc();
        $id_psicologo = $psicologo['ID_Psicologo'];
        $stmt_psicologo->close();
        
        error_log("DEBUG: Psicólogo encontrado: ID_Psicologo = " . $id_psicologo);
        
        error_log("DEBUG: id_psicologo = " . $id_psicologo);
        
        // Obtener todas las citas del psicólogo con información del paciente
        $sql = "SELECT 
                    c.ID_Cita,
                    c.ID_Paciente,
                    c.ID_Psicologo,
                    c.Fecha_Cita,
                    c.Motivo,
                    c.Estado,
                    c.Duracion,
                    p.Nombre_Paciente,
                    p.Apellido_Paciente,
                    p.Telefono_Paciente,
                    u.email as Correo_Paciente
                FROM citas c
                JOIN pacientes p ON c.ID_Paciente = p.ID_Paciente
                JOIN users u ON p.ID_Usuario = u.id
                WHERE c.ID_Psicologo = ? 
                ORDER BY c.Fecha_Cita ASC";
        
        error_log("DEBUG: SQL Query = " . $sql);
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("ERROR: Prepare failed: " . $conn->error);
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        if (!$stmt->bind_param('i', $id_psicologo)) {
            error_log("ERROR: Bind param failed: " . $stmt->error);
            throw new Exception("Bind param failed: " . $stmt->error);
        }
        
        if (!$stmt->execute()) {
            error_log("ERROR: Execute failed: " . $stmt->error);
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        $citas = [];
        while ($row = $result->fetch_assoc()) {
            $citas[] = $row;
        }
        
        $stmt->close();
        
        error_log("DEBUG: Citas encontradas = " . count($citas));
        
        echo json_encode([
            'success' => true,
            'data' => $citas,
            'count' => count($citas),
            'id_psicologo' => $id_psicologo
        ]);
        
    } catch (Exception $e) {
        error_log("ERROR: Exception caught: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al cargar las citas: ' . $e->getMessage()
        ]);
    }
    exit;
}


// ===============================================
// BLOQUE 3: POST - ACTUALIZAR ESTADO DE CITA
// ===============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id_cita']) || !isset($input['estado'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        
        $id_cita = intval($input['id_cita']);
        $estado = trim($input['estado']);
        
        // Validar que el estado sea válido
        $estados_validos = ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'];
        if (!in_array($estado, $estados_validos)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Estado inválido']);
            exit;
        }
        
        // Obtener ID del psicólogo
        $sql_psicologo = "SELECT ID_Psicologo FROM psicologos WHERE ID_Usuario = ?";
        $stmt_psicologo = $conn->prepare($sql_psicologo);
        $stmt_psicologo->bind_param('i', $user_id);
        $stmt_psicologo->execute();
        $result_psicologo = $stmt_psicologo->get_result();
        
        if ($result_psicologo->num_rows === 0) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No eres un psicólogo registrado']);
            $stmt_psicologo->close();
            exit;
        }
        
        $psicologo = $result_psicologo->fetch_assoc();
        $id_psicologo = $psicologo['ID_Psicologo'];
        $stmt_psicologo->close();
        
        // Verificar que la cita pertenezca al psicólogo autenticado
        $sql_check = "SELECT c.ID_Cita FROM citas c 
                      WHERE c.ID_Cita = ? AND c.ID_Psicologo = ?";
        
        $stmt_check = $conn->prepare($sql_check);
        if (!$stmt_check) {
            throw new Exception($conn->error);
        }
        
        $stmt_check->bind_param('ii', $id_cita, $id_psicologo);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows === 0) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para actualizar esta cita']);
            $stmt_check->close();
            exit;
        }
        
        $stmt_check->close();
        
        // Actualizar el estado de la cita
        $sql_update = "UPDATE citas SET Estado = ?, updated_at = CURRENT_TIMESTAMP WHERE ID_Cita = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        if (!$stmt_update) {
            throw new Exception($conn->error);
        }
        
        $stmt_update->bind_param('si', $estado, $id_cita);
        $stmt_update->execute();
        
        if ($stmt_update->affected_rows === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No se realizaron cambios'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Estado de cita actualizado correctamente',
                'id_cita' => $id_cita,
                'nuevo_estado' => $estado
            ]);
        }
        
        $stmt_update->close();
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Si llega aquí, método no permitido
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>
