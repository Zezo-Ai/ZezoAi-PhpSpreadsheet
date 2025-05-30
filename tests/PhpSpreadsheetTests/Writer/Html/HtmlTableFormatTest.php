<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Html;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PHPUnit\Framework\TestCase;

class HtmlTableFormatTest extends TestCase
{
    private string $data = '';

    protected function setUp(): void
    {
        $file = 'samples/templates/TableFormat.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file);
        $writer = new HtmlWriter($spreadsheet);
        $writer->setTableFormats(true);
        $this->data = $writer->generateHtmlAll();
        $spreadsheet->disconnectWorksheets();
    }

    private function extractCell(string $coordinate): string
    {
        [$column, $row] = Coordinate::indexesFromString($coordinate);
        --$column;
        --$row;
        // extract row into $matches
        $match = preg_match('~<tr class="row' . $row . '".+?</tr>~s', $this->data, $matches);
        if ($match !== 1) {
            return 'unable to match row';
        }
        $rowData = $matches[0];
        // extract cell into $matches
        $match = preg_match('~<td class="column' . $column . ' .+?</td>~s', $rowData, $matches);
        if ($match !== 1) {
            return 'unable to match column';
        }

        return $matches[0];
    }

    public function testHtmlTableFormatOutput(): void
    {
        $expectedMatches = [
            ['J1', 'background-color:#145F82;">Sep<', 'table style for header row cell J1'],
            ['J2', 'background-color:#C0E4F5;">110<', 'table style for cell J2'],
            ['I3', 'background-color:#82CAEB;">70<', 'table style for cell I3'],
            ['J3', 'background-color:#82CAEB;">70<', 'table style for cell J3'], // as conditional calculations are off
            ['K3', 'background-color:#82CAEB;">70<', 'table style for cell K3'],
            ['J4', 'background-color:#C0E4F5;">1<', 'table style for cell J4'],
            ['J5', 'background-color:#82CAEB;">1<', 'table style for cell J5'],
        ];
        foreach ($expectedMatches as $expected) {
            [$coordinate, $expectedString, $message] = $expected;
            $string = $this->extractCell($coordinate);
            self::assertStringContainsString($expectedString, $string, $message);
        }
    }
}
