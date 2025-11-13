<?php
session_start();

// no enviar ninguna salida antes de headers
ini_set('display_errors',1); error_reporting(E_ALL);

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($vendorAutoload)) {
    die('Error: vendor/autoload.php no encontrado. Ejecuta "composer install" en C:\\xampp\\htdocs\\agenda');
}
require_once $vendorAutoload;

if (!class_exists('TCPDF')) {
    die('Error: clase TCPDF no encontrada. Revisa la instalación de composer.');
}

// Obtener datos desde sesión
$data = $_SESSION['pdf_patient'] ?? null;
if (!$data) {
    die('Error: no hay datos de paciente para generar el PDF.');
}

// Asignar variables
$apaterno = $data['apaterno'];
$amaterno = $data['amaterno'];
$nombres = $data['nombres'];
$dob = $data['dob'];
$edad = $data['edad'];
$sexo = $data['sexo'];
$estado_civil = $data['estado_civil'];
$ocupacion = $data['ocupacion'];
$calle = $data['calle'];
$numero = $data['numero'];
$colonia = $data['colonia'];
$cp = $data['cp'];
$ciudad = $data['ciudad'];
$estado = $data['estado'];
$telefono_cel = $data['telefono_cel'];
$telefono_fijo = $data['telefono_fijo'];
$email = $data['email'];
$curp = $data['curp'];
$nss = $data['nss'];
$tutor_nombre = $data['tutor_nombre'];
$tutor_parentesco = $data['tutor_parentesco'];
$emergencia_nombre = $data['emergencia_nombre'];
$emergencia_telefono = $data['emergencia_telefono'];

// Generar PDF en memoria
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Sistema');
$pdf->SetAuthor('Clínica');
$pdf->SetTitle('Expediente');
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

$html = '<h2>Expediente</h2>
<table cellpadding="4" border="1">
<tr><td><b>Nombre</b></td><td>'.htmlspecialchars("$nombres $apaterno $amaterno").'</td></tr>
<tr><td><b>Fecha de nacimiento</b></td><td>'.htmlspecialchars($dob).'</td></tr>
<tr><td><b>Edad</b></td><td>'.htmlspecialchars($edad).'</td></tr>
<tr><td><b>Sexo</b></td><td>'.htmlspecialchars($sexo).'</td></tr>
<tr><td><b>Estado Civil</b></td><td>'.htmlspecialchars($estado_civil).'</td></tr>
<tr><td><b>Ocupación</b></td><td>'.htmlspecialchars($ocupacion).'</td></tr>
<tr><td><b>Domicilio</b></td><td>'.htmlspecialchars("$calle $numero, $colonia, CP $cp, $ciudad, $estado").'</td></tr>
<tr><td><b>Teléfonos</b></td><td>'.htmlspecialchars("Cel: $telefono_cel  Fijo: $telefono_fijo").'</td></tr>
<tr><td><b>Email</b></td><td>'.htmlspecialchars($email).'</td></tr>
<tr><td><b>CURP</b></td><td>'.htmlspecialchars($curp).'</td></tr>
<tr><td><b>NSS</b></td><td>'.htmlspecialchars($nss).'</td></tr>
<tr><td><b>Tutor</b></td><td>'.htmlspecialchars("$tutor_nombre ($tutor_parentesco)").'</td></tr>
<tr><td><b>Contacto emergencia</b></td><td>'.htmlspecialchars("$emergencia_nombre - $emergencia_telefono").'</td></tr>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Preparar carpeta y nombre de archivo
$outputDir = __DIR__ . '/../pdf';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', trim($nombres));
$timestamp = time();
$filename = $outputDir . '/expediente_' . $safeName . '_' . $timestamp . '.pdf';

// Guardar el PDF en disco
$pdf->Output($filename, 'F');

// Opcional: limpiar datos de sesión usados
unset($_SESSION['pdf_patient']);

// Ruta accesible desde el navegador (relative path)
$relativePath = '/agenda/pdf/' . basename($filename);
$fileNameBasename = basename($filename);

// ...existing code...
?>
<script>
(function(){
    var fileUrl = <?php echo json_encode($relativePath); ?>;
    var msg = 'Generación del expediente exitosa';

    if (window.opener && !window.opener.closed) {
        try {
            // Mostrar popup (alert) en la ventana padre
            window.opener.alert(msg);
            // Abrir el PDF en una nueva pestaña para visualizarlo
            window.open(fileUrl, '_blank');
            // Redirigir la ventana padre al tablero del doctor con info del PDF
            try { window.opener.location.href = '/agenda/doctor/index.php?pdf_saved=1&file=' + encodeURIComponent(fileUrl); } catch(e){}
        } catch(e){}
        // Cerrar esta ventana (la que generó el PDF)
        try { window.close(); } catch(e){}
    } else {
        // Si no hay ventana padre: mostrar alerta aquí, abrir el PDF y luego redirigir
        try { alert(msg); } catch(e){}
        try { window.open(fileUrl, '_blank'); } catch(e){}
        window.location.href = '/agenda/doctor/index.php?pdf_saved=1&file=' + encodeURIComponent(fileUrl);
    }
})();
</script>
<?php
exit();
?>
