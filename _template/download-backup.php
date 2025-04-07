<?php
require __DIR__ . '/../vendor/autoload.php'; // adjust path if needed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
    $filename = basename($_POST['file']);
    $filePath = dirname(__DIR__) . "/_backups/$filename";

    if (file_exists($filePath)) {
        $xml = simplexml_load_file($filePath);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Backup Data');

        // Header
        $sheet->setCellValue('A1', 'Product Name');
        $sheet->setCellValue('B1', 'Stock Quantity');

        $row = 2;

        foreach ($xml->BODY->IMPORTDATA->REQUESTDATA->TALLYMESSAGE as $msg) {
            $name = (string)$msg->STOCKITEM->NAME;
            $stock = (string)$msg->STOCKITEM->OPENINGBALANCE;

            $sheet->setCellValue("A$row", $name);
            $sheet->setCellValue("B$row", $stock);
            $row++;
        }

        // Output Excel File for Download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . pathinfo($filename, PATHINFO_FILENAME) . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } else {
        echo "❌ File not found.";
    }
} else {
    echo "❌ Invalid request.";
}
