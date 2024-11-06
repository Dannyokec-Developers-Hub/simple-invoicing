<?php
require 'Invoice.php';
require 'PDFGenerator.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice = new Invoice();
    $pdf = new PDFGenerator();
    $items = $_POST['items'];

    // Calculate total
    $total = 0;
    foreach ($items as &$item) {
        $item['qty'] = (int) $item['qty'];
        $item['price'] = (float) $item['price'];
        $total += $item['qty'] * $item['price'];
    }

    // Save invoice data
    $invoiceData = ['items' => $items, 'total' => $total, 'date' => date('Y-m-d')];
    $savedInvoice = $invoice->saveInvoice($invoiceData);

    // Generate PDF
    $pdf->AddPage();
    $pdf->watermark('images/logo.png'); // Invoice Logo. It's optional tho
    $pdf->addInvoiceData($savedInvoice);
    $pdfFilename = "invoices/invoice_{$savedInvoice['id']}.pdf";
    $pdf->outputPDF($pdfFilename);

    // Generate download btn
    echo "<p>Invoice generated successfully!</p>";
    echo "<a href='$pdfFilename' download class='btn btn-info btn-sm'>Download Invoice</a>";
}
?>
