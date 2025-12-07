<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
action:
$action = isset($_GET['action']) ? $_GET['action'] : '';

function json_error($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
}

function listarNotas($mysqli) {
    // Permitir filtrar por paciente usando ?paciente_id=
    if (isset($_GET['paciente_id']) && intval($_GET['paciente_id'])>0) {
        $pid = intval($_GET['paciente_id']);
        $sql = "SELECT n.ID_Nota, n.ID_Paciente, n.ID_Psicologo, n.ID_Cita, n.Fecha_Sesion, n.Contenido, n.Resumen, n.Fecha_Creacion, n.Fecha_Actualizacion,
                    CONCAT(p.Nombre_Paciente, ' ', p.Apellido_Paciente) AS Nombre_Paciente,
                    CONCAT(ps.Nombre_Psicologo, ' ', ps.Apellido_Psicologo) AS Nombre_Psicologo
                FROM notas_clinicas n
                INNER JOIN pacientes p ON n.ID_Paciente = p.ID_Paciente
                INNER JOIN psicologos ps ON n.ID_Psicologo = ps.ID_Psicologo
                WHERE n.ID_Paciente = ?
                ORDER BY n.Fecha_Sesion DESC, n.Fecha_Actualizacion DESC";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $pid);
        if (!$stmt->execute()) return json_error('Error al obtener notas: '.$mysqli->error, 500);
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode(['success' => true, 'data' => $rows]);
        return;
    }

    $sql = "SELECT n.ID_Nota, n.ID_Paciente, n.ID_Psicologo, n.ID_Cita, n.Fecha_Sesion, n.Contenido, n.Resumen, n.Fecha_Creacion, n.Fecha_Actualizacion,
                CONCAT(p.Nombre_Paciente, ' ', p.Apellido_Paciente) AS Nombre_Paciente,
                CONCAT(ps.Nombre_Psicologo, ' ', ps.Apellido_Psicologo) AS Nombre_Psicologo
            FROM notas_clinicas n
            INNER JOIN pacientes p ON n.ID_Paciente = p.ID_Paciente
            INNER JOIN psicologos ps ON n.ID_Psicologo = ps.ID_Psicologo
            ORDER BY n.Fecha_Sesion DESC, n.Fecha_Actualizacion DESC";

    $res = $mysqli->query($sql);
    if (!$res) return json_error('Error al obtener notas: ' . $mysqli->error, 500);

    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function obtenerNota($mysqli, $id) {
    $id = intval($id);
    $sql = "SELECT n.ID_Nota, n.ID_Paciente, n.ID_Psicologo, n.ID_Cita, n.Fecha_Sesion, n.Contenido, n.Resumen, n.Fecha_Creacion, n.Fecha_Actualizacion,
                CONCAT(p.Nombre_Paciente, ' ', p.Apellido_Paciente) AS Nombre_Paciente,
                CONCAT(ps.Nombre_Psicologo, ' ', ps.Apellido_Psicologo) AS Nombre_Psicologo
            FROM notas_clinicas n
            INNER JOIN pacientes p ON n.ID_Paciente = p.ID_Paciente
            INNER JOIN psicologos ps ON n.ID_Psicologo = ps.ID_Psicologo
            WHERE n.ID_Nota = ? LIMIT 1";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        json_error('Nota no encontrada', 404);
    }
}

function buscarNotas($mysqli, $q) {
    $qLike = "%" . $q . "%";
    $sql = "SELECT n.ID_Nota, n.ID_Paciente, n.ID_Psicologo, n.ID_Cita, n.Fecha_Sesion, n.Contenido, n.Resumen, n.Fecha_Creacion, n.Fecha_Actualizacion,
                CONCAT(p.Nombre_Paciente, ' ', p.Apellido_Paciente) AS Nombre_Paciente,
                CONCAT(ps.Nombre_Psicologo, ' ', ps.Apellido_Psicologo) AS Nombre_Psicologo
            FROM notas_clinicas n
            INNER JOIN pacientes p ON n.ID_Paciente = p.ID_Paciente
            INNER JOIN psicologos ps ON n.ID_Psicologo = ps.ID_Psicologo
            WHERE CONCAT(p.Nombre_Paciente, ' ', p.Apellido_Paciente) LIKE ?
               OR n.Resumen LIKE ?
               OR n.Contenido LIKE ?
            ORDER BY n.Fecha_Sesion DESC, n.Fecha_Actualizacion DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $qLike, $qLike, $qLike);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function crearNota($mysqli) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) return json_error('Datos inválidos', 400);
    if (empty($data['ID_Paciente']) || empty($data['ID_Psicologo']) || empty($data['Fecha_Sesion']) || empty($data['Contenido'])) {
        return json_error('Faltan campos requeridos', 400);
    }

    $idPaciente = intval($data['ID_Paciente']);
    $idPsicologo = intval($data['ID_Psicologo']);
    $idCita = isset($data['ID_Cita']) && $data['ID_Cita'] !== '' ? intval($data['ID_Cita']) : null;
    $fechaSesion = $data['Fecha_Sesion'];
    $contenido = $data['Contenido'];
    $resumen = isset($data['Resumen']) ? $data['Resumen'] : mb_substr($contenido, 0, 255);

    $sql = "INSERT INTO notas_clinicas (ID_Paciente, ID_Psicologo, ID_Cita, Fecha_Sesion, Contenido, Resumen) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if ($idCita === null) {
        // bind null as i? Use s and pass null? easiest: use separate query
        $sql2 = "INSERT INTO notas_clinicas (ID_Paciente, ID_Psicologo, Fecha_Sesion, Contenido, Resumen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql2);
        $stmt->bind_param('iisss', $idPaciente, $idPsicologo, $fechaSesion, $contenido, $resumen);
    } else {
        $stmt->bind_param('iiisss', $idPaciente, $idPsicologo, $idCita, $fechaSesion, $contenido, $resumen);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $mysqli->insert_id]);
    } else {
        json_error('Error al crear nota: ' . $mysqli->error, 500);
    }
}

function actualizarNota($mysqli, $id) {
    $id = intval($id);
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['Contenido'])) return json_error('Contenido requerido', 400);

    $contenido = $data['Contenido'];
    $resumen = isset($data['Resumen']) ? $data['Resumen'] : mb_substr($contenido, 0, 255);
    $fechaSesion = isset($data['Fecha_Sesion']) && $data['Fecha_Sesion'] !== '' ? $data['Fecha_Sesion'] : null;

    if ($fechaSesion) {
        $sql = "UPDATE notas_clinicas SET Contenido = ?, Resumen = ?, Fecha_Sesion = ?, Fecha_Actualizacion = CURRENT_TIMESTAMP WHERE ID_Nota = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sssi', $contenido, $resumen, $fechaSesion, $id);
    } else {
        $sql = "UPDATE notas_clinicas SET Contenido = ?, Resumen = ?, Fecha_Actualizacion = CURRENT_TIMESTAMP WHERE ID_Nota = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssi', $contenido, $resumen, $id);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) echo json_encode(['success' => true, 'message' => 'Nota actualizada']);
        else json_error('Nota no encontrada o sin cambios', 404);
    } else json_error('Error al actualizar: ' . $mysqli->error, 500);
}

function eliminarNota($mysqli, $id) {
    $id = intval($id);
    $stmt = $mysqli->prepare("DELETE FROM notas_clinicas WHERE ID_Nota = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) echo json_encode(['success' => true, 'message' => 'Eliminado']);
        else json_error('Nota no encontrada', 404);
    } else json_error('Error al eliminar: ' . $mysqli->error, 500);
}

switch ($action) {
    case 'listar': listarNotas($mysqli); break;
    case 'obtener': if (isset($_GET['id'])) obtenerNota($mysqli, $_GET['id']); else json_error('ID requerido', 400); break;
    case 'buscar': if (isset($_GET['q'])) buscarNotas($mysqli, $_GET['q']); else listarNotas($mysqli); break;
    case 'crear': if ($method === 'POST') crearNota($mysqli); else json_error('Método no permitido', 405); break;
    case 'actualizar': if ($method === 'POST' || $method === 'PUT') { if (isset($_GET['id'])) actualizarNota($mysqli, $_GET['id']); else json_error('ID requerido', 400);} else json_error('Método no permitido', 405); break;
    case 'eliminar': if ($method === 'POST' || $method === 'DELETE') { if (isset($_GET['id'])) eliminarNota($mysqli, $_GET['id']); else json_error('ID requerido', 400);} else json_error('Método no permitido', 405); break;
    default: json_error('Acción no válida', 400); break;
}

$mysqli->close();

?>