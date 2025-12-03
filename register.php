<?php
// register.php
header('Content-Type: application/json; charset=utf-8');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido. Usa POST.']);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Body inválido. Se espera JSON.']);
    exit;
}

$email = trim(strtolower($input['email'] ?? ''));
$password = $input['password'] ?? '';
$rol = 1;

// Validaciones básicas (server-side)

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ingrese un correo electrónico válido.']);
    exit;
}
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
    exit;
}

// Incluir conexión
require_once 'db.php';

// Comprobar si el email ya existe
$stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en la consulta.']);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'El correo ya está en uso.']);
    exit;
}
$stmt->close();


$password_hash = password_hash($password, PASSWORD_DEFAULT);

// insert Tabla users
$insert = $mysqli->prepare("INSERT INTO users (email, password_hash, ID_ROLE) VALUES (?, ?, ?)");
if (!$insert) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al preparar la inserción.']);
    exit;
}
$insert->bind_param('ssi', $email, $password_hash, $rol);
if ($insert->execute()) {
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Cuenta creada correctamente.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo crear la cuenta.']);
}
$insert->close();
$mysqli->close();
