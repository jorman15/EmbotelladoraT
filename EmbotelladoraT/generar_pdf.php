<?php
require('vendor/autoload.php'); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "botellones";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$sql = "SELECT nombre_cliente, fecha_llenado, hora_llenado, cantidad, ubicacion FROM botellones";
$result = $conn->query($sql);

$pdf = new TCPDF();
$pdf->SetMargins(10,2,10);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'EMBOTELLADORA THOMSO', 0, 1, 'C');

$tbl_width = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];
$cell_width = $tbl_width / 5; 

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(220, 220, 220); 
$pdf->SetTextColor(0, 0, 0); 

$pdf->Cell($cell_width, 10, 'Fecha', 1, 0, 'C', true);
$pdf->Cell($cell_width, 10, 'Hora', 1, 0, 'C', true);
$pdf->Cell($cell_width, 10, 'Cantidad Botellas', 1, 0, 'C', true);
$pdf->Cell($cell_width, 10, 'Nombre del Cliente', 1, 0, 'C', true);
$pdf->Cell($cell_width, 10, 'Ubicación', 1, 1, 'C', true);

$pdf->SetFont('helvetica', '', 10);
$pdf->SetFillColor(255, 255, 255); 
$pdf->SetTextColor(0, 0, 0); 

$fill = false; 

while ($row = $result->fetch_assoc()) {
    $pdf->Cell($cell_width, 10, $row['fecha_llenado'], 1, 0, 'C', $fill);
    $pdf->Cell($cell_width, 10, $row['hora_llenado'], 1, 0, 'C', $fill);
    $pdf->Cell($cell_width, 10, $row['cantidad'], 1, 0, 'C', $fill);
    $pdf->Cell($cell_width, 10, $row['nombre_cliente'], 1, 0, 'C', $fill);

    $pdf->MultiCell($cell_width, 10, $row['ubicacion'], 1, 'C', $fill);

    $fill = !$fill; 
}

$tbl_width = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];

$result->data_seek(0);

$pdf->Output('historial_registros.pdf', 'D');