<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function checkFile($filename) {
    echo "=== File: $filename ===\n";
    if (!file_exists($filename)) {
        echo "File does not exist.\n";
        return;
    }
    try {
        $spreadsheet = IOFactory::load($filename);
        $sheetNames = $spreadsheet->getSheetNames();
        echo "Sheet Names: " . implode(", ", $sheetNames) . "\n";
        
        foreach ($sheetNames as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            echo "  Sheet '$sheetName': $highestRow rows, $highestColumn columns\n";
            if ($highestRow >= 1) {
                $row = $sheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, FALSE)[0];
                echo "    Header Row: " . implode(" | ", array_filter($row)) . "\n";
                // Print a sample row if rows exist
                if ($highestRow >= 2) {
                    $sample = $sheet->rangeToArray('A2:' . $highestColumn . '2', NULL, TRUE, FALSE)[0];
                    echo "    Sample Row: " . implode(" | ", array_slice($sample, 0, 10)) . "\n";
                }
            }
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

checkFile('test_import.xlsx');
checkFile('test_import_sektoral.xlsx');
