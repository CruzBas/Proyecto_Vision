<?php
/**
 * actualizarPerfil.php
 * 
 * Maneja la obtención y actualización/creación del perfil del paciente.
 */
session_start();

// Incluir conexión a BD
require_once 'db.php';

// ============================================
// ENDPOINT GET - OBTENER DATOS DEL PERFIL
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_profile') {
    header('Content-Type: application/json; charset=utf-8');
    
    // Validar sesión
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado. Por favor inicia sesión.']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    /**
     * Consulta SQL para obtener datos del paciente
     * Usa LEFT JOIN para incluir usuarios sin perfil de paciente
     * Si el paciente no existe, los campos serán NULL.
     */
    $stmt = $mysqli->prepare("
        SELECT 
            p.ID_Paciente, 
            u.id AS ID_Usuario, 
            p.Nombre_Paciente, 
            p.Apellido_Paciente, 
            p.Edad, 
            p.Telefono_Paciente, 
            p.Fecha_Registro,
            u.email
        FROM users u
        LEFT JOIN pacientes p ON p.ID_Usuario = u.id
        WHERE u.id = ?
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
    
    // Retornar datos en formato JSON
    echo json_encode(['success' => true, 'message' => 'Datos obtenidos correctamente', 'data' => $paciente]);
    exit;
}

// ============================================
// PROCESAR ACTUALIZACIÓN O CREACIÓN VÍA AJAX (JSON)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar si la solicitud es AJAX o JSON
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax || strpos($_SERVER['CONTENT_TYPE'] ?? '' , 'application/json') !== false) {
        header('Content-Type: application/json; charset=utf-8');

        // Validar sesión
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No autorizado. Por favor inicia sesión.']);
            exit;
        }

        // Leer y decodificar JSON
        $raw_input = file_get_contents('php://input');
        $input = json_decode($raw_input, true);

        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            exit;
        }

        $id_usuario = intval($input['id_usuario'] ?? 0);
        $nombre = trim($input['nombre'] ?? '');
        $apellido = trim($input['apellido'] ?? '');
        $edad = intval($input['edad'] ?? 0);
        $telefono = trim($input['telefono'] ?? '');

        // Verificar que el ID de usuario coincida con la sesión
        if ($id_usuario !== $_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No autorizado.']);
            exit;
        }

        // Valdaciones
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

        // Verificar si el paciente ya existe
        $check = $mysqli->prepare("SELECT ID_Paciente FROM pacientes WHERE ID_Usuario = ? LIMIT 1");
        $check->bind_param('i', $id_usuario);
        $check->execute();
        $res = $check->get_result();
        
        if ($res->num_rows === 0) {
            // INSERT si no existe
            $insert = $mysqli->prepare("INSERT INTO pacientes (ID_Usuario, Nombre_Paciente, Apellido_Paciente, Edad, Telefono_Paciente, Fecha_Registro) VALUES (?, ?, ?, ?, ?, NOW())");
            if (!$insert) {
                error_log("Error preparando INSERT: " . $mysqli->error);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error preparando INSERT.']);
                exit;
            }
            $insert->bind_param('issis', $id_usuario, $nombre, $apellido, $edad, $telefono);
            
            if ($insert->execute()) {
                echo json_encode(['success' => true, 'message' => 'Perfil creado correctamente.']);
            } else {
                error_log("Error ejecutando INSERT: " . $insert->error);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al crear perfil.']);
            }
            $insert->close();
        } else {
            // UPDATE si ya existe
            $paciente = $res->fetch_assoc();
            $id_paciente = $paciente['ID_Paciente'];
            
            $update = $mysqli->prepare("UPDATE pacientes SET Nombre_Paciente = ?, Apellido_Paciente = ?, Edad = ?, Telefono_Paciente = ? WHERE ID_Paciente = ? AND ID_Usuario = ?");
            if (!$update) {
                error_log("Error preparando UPDATE: " . $mysqli->error);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
                exit;
            }
            
            $update->bind_param('ssisii', $nombre, $apellido, $edad, $telefono, $id_paciente, $id_usuario);
            
            if (!$update->execute()) {
                error_log("Error ejecutando UPDATE: " . $update->error);
                http_response_code(500);
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
        }
        $check->close();
        exit;
    }
}
// ============================================
// VERIFICAR SESIÓN PARA PÁGINA NORMAL
// ============================================
if (!isset($_SESSION['user_id'])) {
    // Redirigir a login si no hay sesión
    header('Location: login.html');
    exit;
}

// Obtener datos del paciente para la página
$resultado = obtenerPacientePorUsuario($_SESSION['user_id']);
$paciente = $resultado['success'] ? $resultado['data'] : null;

// ============================================
// FUNCIÓN PARA OBTENER PACIENTE
// ============================================
function obtenerPacientePorUsuario($user_id) {
    global $mysqli;
    
    /**
     * Consulta SQL para obtener datos del paciente
     * Usa LEFT JOIN para incluir usuarios sin perfil de paciente
     * Si el paciente no existe, los campos serán NULL.
     */
    $stmt = $mysqli->prepare("
        SELECT 
            p.ID_Paciente, 
            u.id AS ID_Usuario, 
            p.Nombre_Paciente, 
            p.Apellido_Paciente, 
            p.Edad, 
            p.Telefono_Paciente, 
            p.Fecha_Registro,
            u.email
        FROM users u
        LEFT JOIN pacientes p ON p.ID_Usuario = u.id
        WHERE u.id = ?
        LIMIT 1
    ");
    
    // Error en la preparación de la consulta
    if (!$stmt) {
        return ['success' => false, 'message' => 'Error en la consulta', 'data' => null];
    }
    
    // Vincular parámetros y ejecutar
    $stmt->bind_param('i', $user_id);
    
    if (!$stmt->execute()) {
        $stmt->close();
        return ['success' => false, 'message' => 'Error al ejecutar consulta', 'data' => null];
    }
    
    // Obtener resultado
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // No se encontró el usuario
        $stmt->close();
        return ['success' => false, 'message' => 'No se encontró paciente para este usuario', 'data' => null];
    }
    
    // Obtener datos del paciente si la consulta fue exitosa
    $paciente = $result->fetch_assoc();
    $stmt->close();
    
    return ['success' => true, 'message' => 'Paciente encontrado', 'data' => $paciente];
}

