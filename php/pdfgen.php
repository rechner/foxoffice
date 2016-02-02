<?php
require('pdf_lib.php');
require('barcode.php');

function PutLink($pdf, $text, $URL) {
    $pdf->SetFont('', 'U');
    $pdf->SetTextColor(0, 0, 255);
    $pdf->Write(5, $text, $URL);
    $pdf->SetFont('', '');
    $pdf->SetTextColor(0);
}

$pdf = new PDF_Generator();

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->SetY(25);
#$pdf->SetX(25);
#$pdf->Cell(15);
$pdf->SetLeftMargin(25);


$pdf->Image('images/alamo-30.png', 30, 60, 70, 0);

$pdf->Write(5, "Present this ticket at the registration table to receive your $15 Alamo food gift card.\n");
$pdf->Ln();

#$pdf->SetX(25);
$pdf->SetFont('', 'B');
$pdf->Write(5, 'Where: ');
PutLink($pdf, 'One Loudoun, 20575 Easthampton Plaza, Ashburn, VA 20147', 'https://www.google.com/maps/place/Alamo+Drafthouse+Cinema/@39.0477854,-77.4656295,14z/data=!4m2!3m1!1s0x0:0x3fda98f8c48cb5aa');
$pdf->Ln();

$pdf->SetFont('', 'B');
$pdf->Write(5, 'When: ');
$pdf->SetFont('', '');
$pdf->Write(5, "Saturday, March 5th @ 11:00\n\n");

$pdf->SetFont('', '', 10);
$pdf->Write(5, "Please arrive 15 minutes before the showtime to
select a seat and take a look at the menu.  You
might not be seated if you arrive after the showtime.
Alamo has a strict no talking/texting policy. Noisy
tables get one warning before being ejected from
the theatre.

Fursuits have been approved inside the Alamo,
however was not approved by the property owners.
As such we ask that fursuits be worn indoors only,
but note that there is no dedicated changing
areas.
");

$pdf->Image('images/map.png', 110, 50, 80, 0, '', 'https://www.google.com/maps/place/Alamo+Drafthouse+Cinema/@39.0477854,-77.4656295,14z/data=!4m2!3m1!1s0x0:0x3fda98f8c48cb5aa');

$ticket = generate_ticket($_GET['name'], $_GET['code'], 1, $_GET['seats'], '');

$pdf->GDImage($ticket, 35, 120, 140);
$pdf->Line(25, 120, 190, 120);

$ticket = generate_ticket($_GET['name'], $_GET['code'], 2, $_GET['seats'], '');

$pdf->GDImage($ticket, 35, 190, 140);
$pdf->Line(30, 190, 180, 190);


$pdf->AddPage();
$ticket = generate_ticket($_GET['name'], $_GET['code'], 2, $_GET['seats'], '');
$pdf->GDImage($ticket, 35, 30, 140);
$pdf->Line(30, 100, 180, 100);

$ticket = generate_ticket($_GET['name'], $_GET['code'], 2, $_GET['seats'], '');
$pdf->GDImage($ticket, 35, 100, 140);
$pdf->Line(30, 170, 180, 170);

$ticket = generate_ticket($_GET['name'], $_GET['code'], 2, $_GET['seats'], '');
$pdf->GDImage($ticket, 35, 170, 140);


imagedestroy($ticket);
$pdf->Output('', "Zootopia-tickets.pdf");
?>
