<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

$sql = "SELECT 
            p.ID_Paciente, 
            p.Nombre_Paciente, 
            p.Apellido_Paciente, 
            p.Telefono_Paciente, 
            p.Edad, 
            p.Fecha_Registro, 
            CONCAT(p.Nombre_Paciente, ' ', p.Apellido_Paciente) AS Nombre_Completo,
            -- Última sesión registrada en notas_clinicas (Fecha_Sesion)
            (SELECT MAX(n.Fecha_Sesion) FROM notas_clinicas n WHERE n.ID_Paciente = p.ID_Paciente) AS Ultima_Sesion,
            -- Última cita programada (opcional)
            (SELECT MAX(c.Fecha_Cita) FROM citas c WHERE c.ID_Paciente = p.ID_Paciente) AS Ultima_Cita
        FROM pacientes p
        ORDER BY p.Nombre_Paciente, p.Apellido_Paciente";

    $res = $mysqli->query($sql);
if (!$res) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al obtener pacientes: ' . $mysqli->error]);
    exit;
}

$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;
echo json_encode(['success' => true, 'data' => $rows]);

$mysqli->close();
?>