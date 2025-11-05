<?php
$mysqli = new mysqli('localhost', 'root', '', 'Clinica_VisionDB');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la BD: ' . $mysqli->connect_error]);
    exit;
}
