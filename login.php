<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Preparar consulta
    $stmt = $mysqli->prepare("SELECT id, first_name, last_name, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $user = $resultado->fetch_assoc();

        // Verificar contraseña usando password_verify
        if (password_verify($password, $user['password_hash'])) {
            echo json_encode([
                "success" => true,
                "usuario" => [
                    "id" => $user['id'],
                    "nombre" => $user['first_name'] . " " . $user['last_name'],
                    "email" => $user['email']
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Contraseña incorrecta."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "El correo no está registrado."]);
    }

    $stmt->close();
}
?>
