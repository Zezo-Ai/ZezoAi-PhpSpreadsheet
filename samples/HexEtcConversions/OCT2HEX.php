<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$category = 'Engineering';
$functionName = 'OCT2HEX';
$description = 'Converts an octal number to hexadecimal';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$testData = [
    [3],
    [12],
    [42],
    [70],
    [72],
    [77],
    [100],
    [127],
    [177],
    [456],
    [4567],
    [7777700001],
    [7777776543],
];
$testDataCount = count($testData);

$worksheet->fromArray($testData, null, 'A1', true);

for ($row = 1; $row <= $testDataCount; ++$row) {
    $worksheet->setCellValue('B' . $row, '=OCT2HEX(A' . $row . ')');
}

// Test the formulae
for ($row = 1; $row <= $testDataCount; ++$row) {
    $helper->log(
        "(B$row): "
        . 'Octal ' . $worksheet->getCell("A$row")->getValueString()
        . ' is hexadecimal ' . $worksheet->getCell("B$row")->getCalculatedValueString()
    );
}
