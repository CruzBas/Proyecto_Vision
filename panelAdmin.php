<?php 
/**
 * panelAdmin.php
 * 
 * Este archivo maneja todas la operaciones:
 * - Listado de usuarios (paciente y psicologos).
 * - Cambio de rol (paciente, Doctor, Administrador).
 * - Eliminacion de usuarios.
 * - Validacion de sesion y permisos (solo administradores pueden acceder).
 * - Resgistro en logs de cambios de rol.
 */

session_start();

// Conexion centralizada a la dn
require_once "db.php";
//Middleware: validar si el usuario es administrador
$userID = $_SESSION['user_id']?? null;
if(!$userID) {
    http_response_code(401);
    exit(json_encode(['error' => 'No autenticado']));
}

$stmt = $mysqli->prepare("SELECT ID_Role FROM users WHERE id=?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if((int)$role !== 3) {
    http_response_code(403);
    exit(json_encode(['error' => 'Acceso denegado']));
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;

if($action === 'list') {
    //Listado de usuarios
    $nombre = $_GET['nombre'] ?? '';
    $correo = $_GET['correo'] ?? '';
    $rol = $_GET['rol'] ?? '';
    $where = [];
    $params = [];
    $types = '';

    if($nombre !== '') {
        $where[] = "(p.Nombre_Paciente LIKE ? OR p.Apellido_Paciente LIKE ? OR ps.Nombre_Psicologo LIKE ? OR ps.Apellido_Psicologo LIKE ?)";
        $like = "%$nombre%";
        $params = array_merge($params, [$like, $like, $like, $like]);
        $types .= 'ssss';
    }
    if ($correo !== ''){
        $where[] = "u.email=?";
        $params[] = "%$correo%";
        $types .= 's';
    }
    if($rol !== '') {
        $where[] = "u.ID_Role=?";
        $params[] = (int)$rol;
        $types .= 'i';
    }

    $sqlWhere = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    $sql = "SELECT u.id AS ID_Usuario, u.email, u.ID_Role, r.Role, p.ID_Paciente,
    p.Nombre_Paciente, p.Apellido_Paciente, p.Telefono_Paciente, ps.ID_Psicologo,
    ps.Nombre_Psicologo, ps.Apellido_Psicologo, ps.Psicologo_Telefono
    FROM users u JOIN Roles r ON u.ID_Role = r.ID_Role
    LEFT JOIN pacientes p ON u.id = p.ID_Usuario
    LEFT JOIN psicologos ps ON u.id = ps.ID_Usuario
    $sqlWhere 
    ORDER BY u.id ASC";

    $stmt = $mysqli->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    header("Content-Type: application/json");
    echo json_encode(['items' => $items]);
    exit;
}
elseif($action === 'updateRol') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuarioID = (int)($data['usuario_id'] ?? 0);
    $rolNuevo = (int)($data['rol_nuevo'] ?? 0);

    $stmt = $mysqli->prepare("SELECT ID_Role FROM users WHERE id=?");
    $stmt->bind_param("i", $usuarioID);
    $stmt->execute();
    $stmt->bind_result($rolAnterior);
    $stmt->fetch();
    $stmt->close();

    if($rolAnterior === null) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }
    if ((int)$rolAnterior === $rolNuevo) {
        http_response_code(409);
        echo json_encode(['error' => 'Ya tiene ese rol']);
        exit;
    }

    $mysqli->begin_transaction();
    try {
        $stmt = $mysqli->prepare("UPDATE users SET ID_Role=? WHERE id=?");
        $stmt->bind_param("ii", $rolNuevo, $usuarioID);
        $stmt->execute();
        $stmt->close();

        if($rolNuevo === 2) { //Psicologo
            $check = $mysqli->prepare("SELECT ID_Psicologo FROM psicologos WHERE ID_Usuario=?");
            $check->bind_param("i", $usuarioID);
            $check->execute();
            $check->store_result();
            if ($check->num_rows === 0){
                $check->close();
                $insert = $mysqli->prepare("INSERT INTO psicologos (ID_Usuario, ID_Especialidad, Nombre_Psicologo, Apellido_Psicologo) VALUES (?,?,?,?)");
                $idEspecialidad = 1;
                $nombrePend = 'Pendiente';
                $apellidoPend = 'Pendiente';
                $insert->bind_param('iiss', $usuarioID, $idEspecialidad, $nombrePend, $apellidoPend);
                $insert->execute();
                $insert->close();
            } else {
                $check->close();
            }
        }
        $mysqli->commit();
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Rol actualizado correctamente']);
    } catch (Throwable $e) {
        $mysqli->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Error interno']);
    }
    exit;
}
elseif ($action === 'deleteUser') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)$data['id'];

    $stmt = $mysqli->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['message' => 'Usuario eliminado']);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Accion invalida']);