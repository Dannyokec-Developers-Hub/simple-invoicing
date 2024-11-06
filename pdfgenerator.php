<?php
//The script that generates the invoice from our form data. This can be updated to include logo, other writeups, watermark etc

require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

class PDFGenerator extends Fpdi {
    private $extgstates = [];

    // Header function - add logo centered in header
    function header() {
        // Get the width of the page
        $pageWidth = $this->GetPageWidth();
        
        // Image path for the logo
        $logoPath = 'https://okecbot.com/images/logo.png';
        
        // Width and height of the logo
        $logoWidth = 50;  // Adjust width as needed
        $logoHeight = 20; // Adjust height as needed
        
        // Calculate the X position to center the image
        $x = ($pageWidth - $logoWidth) / 2;

        // Set font and write the invoice title
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Invoice', 0, 1, 'C');
        
        // Add the logo centered at the top of the page
        $this->Image($logoPath, $x, 15, $logoWidth, $logoHeight); // Adjust position and size as needed

        $this->Ln(20); // Add some space after the logo
    }

    // Footer function - page number at the bottom
    function footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Watermark function
    function watermark($imagePath) {
        $this->SetAlpha(0.1);
        $this->Image($imagePath, 30, 60, 150, 0, 'PNG');
        $this->SetAlpha(1);
    }

    // Function to add invoice data to the PDF
    function addInvoiceData($data) {
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0);
        
        foreach ($data['items'] as $item) {
            $this->Cell(90, 10, $item['name'], 1);
            $this->Cell(30, 10, $item['qty'], 1, 0, 'C');
            $this->Cell(30, 10, $item['price'], 1, 0, 'R');
            $this->Cell(30, 10, $item['qty'] * $item['price'], 1, 1, 'R');
        }

        $this->Cell(150, 10, 'Total:', 1, 0, 'R');
        $this->Cell(30, 10, $data['total'], 1, 1, 'R');
    }

    // Function to output the PDF
    function outputPDF($filename) {
        $this->Output('F', $filename);
    }

    // SetAlpha function to control transparency
    private function SetAlpha($alpha, $blendMode = 'Normal') {
        $gs = $this->AddExtGState(['ca' => $alpha, 'CA' => $alpha, 'BM' => '/' . $blendMode]);
        $this->SetExtGState($gs);
    }

    // AddExtGState function to add an extended graphics state
    private function AddExtGState($parms) {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n] = $parms;
        return $n;
    }

    // SetExtGState function to set the extended graphics state
    private function SetExtGState($gs) {
        $this->_out(sprintf('/GS%d gs', $gs));
    }
}
?>
