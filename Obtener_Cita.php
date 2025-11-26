<?php

session_start();
header('Content-Type: application/json');

require_once 'db.php';

$response = [];

if (empty($_SESSION['user_id'])) {
    echo json_encode([]); 
    exit;
}

$userId = $_SESSION['user_id'];


$query = "SELECT 
            c.ID_Cita,
            DATE(c.Fecha_Cita) as date, 
            DATE_FORMAT(c.Fecha_Cita, '%H:%i') as time, 
            e.Nombre_Especialidad as type,
            CONCAT(psi.Nombre_Psicologo, ' ', psi.Apellido_Psicologo) as nombre_completo_psicologo, 
            c.Estado 
         FROM citas c
          INNER JOIN pacientes p ON c.ID_Paciente = p.ID_Paciente
          INNER JOIN psicologos psi ON c.ID_Psicologo = psi.ID_Psicologo
          LEFT JOIN especialidades e ON psi.ID_Especialidad = e.ID_Especialidad
          WHERE p.ID_Usuario = ? 
          ORDER BY c.Fecha_Cita ASC";

if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $citas = [];
    while ($row = $result->fetch_assoc()) {
        $citas[] = $row;
    }
    
    echo json_encode($citas);
    $stmt->close();
} else {

    echo json_encode([]);
}
?>