<?php
session_start();

// Incluir conexión a BD
require_once 'db.php';

// ============================================
// ENDPOINT GET - OBTENER DATOS DEL PERFIL
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_profile') {
    header('Content-Type: application/json; charset=utf-8');
    
    // Verificar autenticación
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado. Por favor inicia sesión.']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Obtener datos del paciente
    $stmt = $mysqli->prepare("
        SELECT 
            p.ID_Paciente, 
            p.ID_Usuario, 
            p.Nombre_Paciente, 
            p.Apellido_Paciente, 
            p.Edad, 
            p.Telefono_Paciente, 
            p.Fecha_Registro,
            u.email
        FROM pacientes p
        INNER JOIN users u ON p.ID_Usuario = u.id
        WHERE p.ID_Usuario = ?
        LIMIT 1
    ");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
        exit;
    }
    
    $stmt->bind_param('i', $user_id);
    
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta']);
        $stmt->close();
        exit;
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No se encontró paciente para este usuario']);
        $stmt->close();
        exit;
    }
    
    $paciente = $result->fetch_assoc();
    $stmt->close();
    
    echo json_encode(['success' => true, 'message' => 'Datos obtenidos correctamente', 'data' => $paciente]);
    exit;
}

// ============================================
// PROCESAR ACTUALIZACIÓN VÍA AJAX (JSON)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si es una petición AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    // Si es AJAX o el Content-Type es JSON, procesamos como API
    if ($isAjax || strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json; charset=utf-8');
        
        // Log de debug
        error_log("=== POST REQUEST ===");
        error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'no definido'));
        error_log("X-Requested-With: " . ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'no definido'));
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'no existe'));
        
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autorizado.']);
            exit;
        }
        
        // Obtener datos JSON
        $raw_input = file_get_contents('php://input');
        error_log("Raw input: " . $raw_input);
        
        $input = json_decode($raw_input, true);
        error_log("Parsed input: " . json_encode($input));
        
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            exit;
        }
        
        // Extraer y validar datos
        $id_paciente = intval($input['id_paciente'] ?? 0);
        $id_usuario = intval($input['id_usuario'] ?? 0);
        $nombre = trim($input['nombre'] ?? '');
        $apellido = trim($input['apellido'] ?? '');
        $edad = intval($input['edad'] ?? 0);
        $telefono = trim($input['telefono'] ?? '');
        
        // Validar seguridad
        if ($id_usuario !== $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No autorizado.']);
            exit;
        }
        
        // Validar campos
        if (empty($nombre) || empty($apellido)) {
            echo json_encode(['success' => false, 'message' => 'Nombre y apellido obligatorios.']);
            exit;
        }
        
        if ($edad < 18 || $edad > 120) {
            echo json_encode(['success' => false, 'message' => 'Edad inválida. Debe ser mayor de 18 años.']);
            exit;
        }
        
        if (empty($telefono) || strlen($telefono) < 6) {
            echo json_encode(['success' => false, 'message' => 'Teléfono inválido (mínimo 6 caracteres).']);
            exit;
        }
        
        // Actualizar
        $update = $mysqli->prepare("UPDATE pacientes SET Nombre_Paciente = ?, Apellido_Paciente = ?, Edad = ?, Telefono_Paciente = ? WHERE ID_Paciente = ? AND ID_Usuario = ?");
        
        if (!$update) {
            http_response_code(500);
            error_log("Error preparando consulta: " . $mysqli->error);
            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
            exit;
        }
        
        // Correción: Los tipos son string, string, integer, string, integer, integer
        $update->bind_param('ssisii', $nombre, $apellido, $edad, $telefono, $id_paciente, $id_usuario);
        
        if (!$update->execute()) {
            http_response_code(500);
            error_log("Error ejecutando consulta: " . $update->error);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar los datos']);
            $update->close();
            exit;
        }
        
        if ($update->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se realizaron cambios.']);
        }
        
        $update->close();
        exit;
    }
}

// ============================================
// VERIFICAR SESIÓN PARA PÁGINA NORMAL
// ============================================
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Obtener datos del paciente para la página
$resultado = obtenerPacientePorUsuario($_SESSION['user_id']);

if ($resultado['success']) {
    $paciente = $resultado['data'];
} else {
    $paciente = null;
}

// ============================================
// FUNCIÓN PARA OBTENER PACIENTE
// ============================================
function obtenerPacientePorUsuario($user_id) {
    global $mysqli;
    
    $stmt = $mysqli->prepare("
        SELECT 
            p.ID_Paciente, 
            p.ID_Usuario, 
            p.Nombre_Paciente, 
            p.Apellido_Paciente, 
            p.Edad, 
            p.Telefono_Paciente, 
            p.Fecha_Registro,
            u.email
        FROM pacientes p
        INNER JOIN users u ON p.ID_Usuario = u.id
        WHERE p.ID_Usuario = ?
        LIMIT 1
    ");
    
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error en la consulta', 'data' => null];
    }
    
    $stmt->bind_param('i', $user_id);
    
    if (!$stmt->execute()) {
        $stmt->close();
        return ['success' => false, 'message' => 'Error al ejecutar consulta', 'data' => null];
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'No se encontró paciente para este usuario', 'data' => null];
    }
    
    $paciente = $result->fetch_assoc();
    $stmt->close();
    
    return ['success' => true, 'message' => 'Paciente encontrado', 'data' => $paciente];
}
?>
