<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-7.0.1\PHPMailer-7.0.1\src\Exception.php';
require 'PHPMailer-7.0.1\PHPMailer-7.0.1\src\PHPMailer.php';
require 'PHPMailer-7.0.1/PHPMailer-7.0.1/src/SMTP.php';

header("Content-Type: application/json");

// Validar POST
if(!isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['message'])){
    echo json_encode(['ok' => false, 'error' => 'Faltan campos']);
    exit;
}

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];

$mail = new PHPMailer(true);
$mail->SMTPDebug = 3;
$mail->Debugoutput = 'html';
try {
    // Config SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'warioregon123@gmail.com';  
    $mail->Password   = 'uyhv odfb csit upag';   
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('warioregon123@gmail.com', 'FORMULARIO WEB');
    $mail->addAddress('warioregon123@gmail.com');

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = "Nuevo mensaje de contacto";
    $mail->Body    = "
        <h3>Nuevo mensaje enviado desde tu página</h3>
        <p><b>Nombre:</b> $name</p>
        <p><b>Email:</b> $email</p>
        <p><b>Teléfono:</b> $phone</p>
        <p><b>Mensaje:</b><br>$message</p>
    ";

    $mail->send();

    echo json_encode(['ok' => true, 'message' => 'Enviado correctamente']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $mail->ErrorInfo]);
}
