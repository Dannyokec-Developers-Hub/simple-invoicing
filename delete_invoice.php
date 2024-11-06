<?php

   // This script find and delete invoice data from our json db then delete the specific invoice , update the json however this will not delete the invoice PDF saved in data directory!
   // It's made this way to avoid accidental delete.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $invoiceFile = 'invoices.json';
    $invoices = file_exists($invoiceFile) ? json_decode(file_get_contents($invoiceFile), true) : [];
    $invoiceId = $_POST['id'];

 
    foreach ($invoices as $key => $invoice) {
        if ($invoice['id'] == $invoiceId) {
            unset($invoices[$key]);
            break;
        }
    }

    file_put_contents($invoiceFile, json_encode(array_values($invoices)));

}
?>
