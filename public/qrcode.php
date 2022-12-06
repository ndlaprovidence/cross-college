<?php 
require_once '../vendor/autoload.php';

$id = $_POST["id"];
$gender = $_POST["gender"];

$id = $gender . "-" . $id;

$pdf = new TCPDF();

// add a page
$pdf->AddPage();

// set style for barcode
$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);

// QRCODE,L : QR-CODE Low error correction
$pdf->write2DBarcode($id, 'QRCODE,L', 20, 30, 500, 500, $style, 'N');
$pdf->Text(20, 25, $id);

//Close and output PDF document
$pdf->Output('qrcode.png', 'I');