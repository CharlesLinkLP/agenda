<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';
if (!class_exists('TCPDF')) {
    die('Error: clase TCPDF no encontrada. Revisa vendor/autoload.php y composer install.');
}
$pdf = new TCPDF('P','mm','A4', true, 'UTF-8', false);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, 'Prueba TCPDF OK');
$pdf->Output('test.pdf', 'I');
exit();
?>