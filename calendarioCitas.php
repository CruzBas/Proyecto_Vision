<?php
//calendarioCitas.php
session_start();
require_once 'db.php';

// Validar que se reciba el parámetro idDoctor
if (!isset($_GET['idDoctor'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parámetro idDoctor faltante']);
    exit;
}

$idDoctor = (int)$_GET['idDoctor'];

// Consulta SQL: obtener citas y datos del paciente
$sql = "SELECT 
            c.ID_Cita, c.Fecha_Cita, c.Estado, c.Duracion, c.Motivo,
            p.Nombre_Paciente, p.Apellido_Paciente
        FROM citas c
        JOIN pacientes p ON c.ID_Paciente = p.ID_Paciente
        WHERE c.ID_Psicologo = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idDoctor);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($cita = $result->fetch_assoc()) {
    // Construir nombre completo del paciente
    $pacienteNombreCompleto = trim($cita['Nombre_Paciente'] . ' ' . $cita['Apellido_Paciente']);
    // Hora de inicio formateada
    $hora = date('H:i', strtotime($cita['Fecha_Cita']));
    // Texto que se mostrará en el bloque del calendario
    $titulo = $pacienteNombreCompleto;

    // Armar evento para FullCalendar
    $events[] = [
        'id' => $cita['ID_Cita'],
        'title' => $titulo,
        'start' => $cita['Fecha_Cita'], // inicio
        'end' => date('Y-m-d H:i:s', strtotime($cita['Fecha_Cita'].' +'.$cita['Duracion'].' minutes')), // fin calculado
        'estado' => $cita['Estado'],
        'paciente' => $pacienteNombreCompleto,
        'motivo' => $cita['Motivo'],
        'fecha' => date('d/m/Y', strtotime($cita['Fecha_Cita'])),
        'hora' => $hora
    ];
}
$stmt->close();

// Devolver JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($events);


