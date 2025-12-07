<?php
// generate_pdf.php
// Requiere dompdf: composer require dompdf/dompdf

// Permitir solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido. Use POST.";
    exit;
}

// Cargar Dompdf al inicio
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Sanitizar entrada
function safe($s) {
    return htmlspecialchars(trim($s), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$patient_name = isset($_POST['patient_name']) ? safe($_POST['patient_name']) : '';
$patient_age = isset($_POST['patient_age']) ? safe($_POST['patient_age']) : '';
$patient_id = isset($_POST['patient_id']) ? safe($_POST['patient_id']) : '';
$session_date = isset($_POST['session_date']) ? safe($_POST['session_date']) : '';
$session_time = isset($_POST['session_time']) ? safe($_POST['session_time']) : '';
$session_notes = isset($_POST['session_notes']) ? safe($_POST['session_notes']) : '';

// Formatear fecha a formato legible (en español)
$display_date = $session_date;
if ($session_date) {
    $d = DateTime::createFromFormat('Y-m-d', $session_date);
    if ($d) {
        $meses = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];
        $mes = $meses[$d->format('n') - 1];
        $display_date = $d->format('d') . ' de ' . $mes . ' de ' . $d->format('Y');
    }
}

// Construir HTML para el PDF
$html = '<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        color: #0d171b;
        background: #fff;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 24px;
        border: 1px solid #e7eff3;
        border-radius: 12px;
    }
    .header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    .logo {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: #13a4ec;
    }
    .header-text h1 {
        font-size: 24px;
        margin: 0;
        color: #0d171b;
    }
    .header-text p {
        margin: 2px 0;
        color: #6b7280;
        font-size: 13px;
    }
    h2 {
        font-size: 16px;
        margin: 18px 0 10px;
        color: #0d171b;
        border-bottom: 2px solid #13a4ec;
        padding-bottom: 6px;
    }
    .section {
        margin: 16px 0;
    }
    .row {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin: 8px 0;
    }
    .field {
        flex: 1;
        min-width: 200px;
    }
    .label {
        font-weight: bold;
        font-size: 13px;
        color: #334155;
        display: block;
        margin-bottom: 4px;
    }
    .value {
        font-size: 14px;
        color: #0d171b;
        padding: 8px;
        background: #f8fafc;
        border-radius: 4px;
    }
    .notes {
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 13px;
        line-height: 1.5;
        padding: 12px;
        background: #f8fafc;
        border-radius: 4px;
        border-left: 3px solid #13a4ec;
    }
    .footer {
        margin-top: 24px;
        padding-top: 12px;
        border-top: 1px solid #e7eff3;
        font-size: 11px;
        color: #9ca3af;
        text-align: right;
    }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="logo"></div>
    <div class="header-text">
      <h1>Vision</h1>
      <p>Informe de sesión clínica</p>
    </div>
  </div>

  <h2>Información del paciente</h2>
  <div class="section">
    <div class="row">
      <div class="field">
        <span class="label">Nombre:</span>
        <div class="value">' . ($patient_name ?: '-') . '</div>
      </div>
      <div class="field">
        <span class="label">Edad:</span>
        <div class="value">' . ($patient_age ? $patient_age . ' años' : '-') . '</div>
      </div>
    </div>
    <div class="row">
      <div class="field">
        <span class="label">Cédula de identidad:</span>
        <div class="value">' . ($patient_id ?: '-') . '</div>
      </div>
    </div>
  </div>

  <h2>Detalles de la sesión</h2>
  <div class="section">
    <div class="row">
      <div class="field">
        <span class="label">Fecha:</span>
        <div class="value">' . ($display_date ?: '-') . '</div>
      </div>
      <div class="field">
        <span class="label">Hora:</span>
        <div class="value">' . ($session_time ?: '-') . '</div>
      </div>
    </div>
  </div>

  <h2>Notas clínicas y observaciones</h2>
  <div class="section">
    <div class="notes">' . ($session_notes ?: '<em>No hay notas registradas.</em>') . '</div>
  </div>

  <div class="footer">Documento generado el ' . date('d/m/Y \\a \\l\\a\\s H:i') . '</div>
</div>
</body>
</html>';

// Cargar Dompdf
try {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $filename = 'Informe_Sesion_' . ($session_date ?: date('Ymd')) . '.pdf';
    
    // Enviar headers para descargar el PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    
    echo $dompdf->output();
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error al generar el PDF: " . $e->getMessage();
    exit;
}
?>
