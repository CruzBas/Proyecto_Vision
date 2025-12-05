<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $message = htmlspecialchars($_POST["message"]);

    $to = "Warioregon123@gmail.com";
    $subject = "Nuevo mensaje desde el formulario de contacto";
    
    $body = "
    Nombre: $name\n
    Correo: $email\n
    Teléfono: $phone\n
    Mensaje:\n$message
    ";

    $headers = "From: $email\r\nReply-To: $email\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
