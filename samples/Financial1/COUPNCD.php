<?php

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Helpers as DateHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Returns the next coupon date, after the settlement date.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Settlement Date', DateHelper::getDateValue('01-Jan-2011')],
    ['Maturity Date', DateHelper::getDateValue('25-Oct-2012')],
    ['Frequency', 4],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B2')->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

// Now the formula
$worksheet->setCellValue('B6', '=COUPNCD(B1, B2, B3)');
$worksheet->getStyle('B6')->getNumberFormat()->setFormatCode('dd-mmm-yyyy');

$helper->log($worksheet->getCell('B6')->getValue());
$helper->log('COUPNCD() Result is ' . $worksheet->getCell('B6')->getFormattedValue());
