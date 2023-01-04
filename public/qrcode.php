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
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => true,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false,
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);

$pdf->Cell(0, 0, $id, 0, 1);
$pdf->write1DBarcode($id, 'C128', '', '', '', 24, 0.4, $style, 'N');

$pdf->Output('code_barre.png', 'I');