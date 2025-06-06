<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Spreadsheet */
$spreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';

/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Hide grid lines');
$spreadsheet->getActiveSheet()->setShowGridLines(false);

$helper->log('Set orientation to landscape');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

$className = PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class;
$helper->log("Write to PDF format using {$className}");
IOFactory::registerWriter('Pdf', $className);

// Save
$helper->write($spreadsheet, __FILE__, ['Pdf']);
