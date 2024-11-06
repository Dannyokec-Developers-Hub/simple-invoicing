<?php
// This section Read invoices from our JSON file. so we can easily re-visit old invoices.
//You have't explained what the invoice system will be used for thus i designd this when i have access to a small pc here
$invoiceFile = 'invoices.json';
$invoices = file_exists($invoiceFile) ? json_decode(file_get_contents($invoiceFile), true) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>Create Invoice</h2>
            <form id="invoiceForm">
                <div id="invoiceItems">
                    <div class="invoice-item mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="items[0][name]" placeholder="Item Name">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="items[0][qty]" placeholder="Quantity">
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" class="form-control" name="items[0][price]" placeholder="Price">
                            </div>
                            <div class="col-md-2">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addItemBtn" class="btn btn-success">+</button>
   
                <button type="submit" class="btn btn-primary">Generate Invoice</button>
            </form>
            <div id="message"></div>
        </div>
        <div class="col-md-4">
            <h2>Previous Invoices</h2>
            <ul class="list-group" id="invoiceList">
                <?php foreach ($invoices as $invoice): ?>
                    <li class="list-group-item">
                        Invoice #<?= $invoice['id'] ?> 
                        <a href="#" class="btn btn-info btn-sm float-end downloadBtn" data-id="<?= $invoice['id'] ?>">Download</a>
                        <a href="#" class="btn btn-warning btn-sm float-end me-2 editBtn" data-id="<?= $invoice['id'] ?>">Edit</a>
                        <a href="#" class="btn btn-danger btn-sm float-end me-2 deleteBtn" data-id="<?= $invoice['id'] ?>">X</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    // Section that add Item to Invoice Form
    let itemCount = 1;
    $('#addItemBtn').click(function () {
        const newItem = `
            <div class="invoice-item mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="items[${itemCount}][name]" placeholder="Item Name">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="items[${itemCount}][qty]" placeholder="Quantity">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control" name="items[${itemCount}][price]" placeholder="Price">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger removeItemBtn">X</button>
                    </div>
                </div>
            </div>
        `;
        $('#invoiceItems').append(newItem);
        itemCount++;
    });

    // This section remove Itm from our json file db
    $(document).on('click', '.removeItemBtn', function () {
        $(this).closest('.invoice-item').remove();
        loadInvoiceList(); 
    });

    // Submit Form with AJAX
    $('#invoiceForm').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: 'process.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                $('#message').html(response);
             
                loadInvoiceList();
            },
            error: function () {
                $('#message').html('<div class="alert alert-danger">There was an error generating the invoice. Please try again.</div>');
            }
        });
        loadInvoiceList(); 
    });


// Handle Delete btn
$(document).on('click', '.deleteBtn', function (e) {
    e.preventDefault();
    let invoiceId = $(this).data('id');
        $.post('delete_invoice.php', { id: invoiceId }, function (response) {
        $('#message').html('<div class="alert alert-success">Invoice deleted successfully.</div>');
    }).fail(function () {
        $('#message').html('<div class="alert alert-danger">Failed to delete the invoice. Please try again.</div>');
    });
    loadInvoiceList(); 
});



    // Load Invoice List from our json db
    function loadInvoiceList() {
        $.get('invoices.json', function (data) {
            let invoices = JSON.parse(data);
            let list = '';
            invoices.forEach(function (invoice) {
                list += `<li class="list-group-item">
                            Invoice #${invoice.id} 
                            <a href="#" class="btn btn-info btn-sm float-end downloadBtn" data-id="${invoice.id}">Download</a>
                            <a href="#" class="btn btn-warning btn-sm float-end me-2 editBtn" data-id="${invoice.id}">Edit</a>
                            <a href="#" class="btn btn-danger btn-sm float-end me-2 deleteBtn" data-id="${invoice.id}">Delete</a>
                        </li>`;
            });
            $('#invoiceList').html(list);
        });
    }

    // Handle Download
    $(document).on('click', '.downloadBtn', function (e) {
        e.preventDefault();
        let invoiceId = $(this).data('id');
        window.location.href = `invoices/invoice_${invoiceId}.pdf`;
    });
</script>
</body>
</html>
