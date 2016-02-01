<?php
require('pdf_lib.php');
require('barcode.php');

$pdf = new PDF_Generator();

$ticket = generate_ticket($_GET['name'], $_GET['code'], $_GET['current'], $_GET['seats'], '');

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(20, 10, 'Present this ticket at the registration table to receive your $15 Alamo food gift card');
$pdf->GDImage($ticket, 20, 20, 170);
$pdf->GDImage($ticket, 20, 105, 170);
$pdf->GDImage($ticket, 20, 190, 170);

$pdf->Line(20, 105, 190, 105);
$pdf->Line(20, 190, 190, 190);

imagedestroy($ticket);
$pdf->Output();
?>
