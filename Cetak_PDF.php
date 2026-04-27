<?php
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// tandai ini mode PDF
$_GET['pdf'] = true;

// ambil tampilan hasil.php
ob_start();
include "Hasil.php";
$html = ob_get_clean();

// setting dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// download
$dompdf->stream("hasil_analisa.pdf", ["Attachment" => true]);
?>