<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class HiddenWorksheetTest extends TestCase
{
    public function testPageSetup(): void
    {
        $filename = 'tests/data/Reader/Gnumeric/HiddenSheet.gnumeric';
        $reader = new Gnumeric();
        $spreadsheet = $reader->load($filename);
        $assertions = $this->worksheetAssertions();
        $sheetCount = 0;

        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                self::fail('Unexpected worksheet ' . $worksheet->getTitle());
            }

            ++$sheetCount;
            $sheetAssertions = $assertions[$worksheet->getTitle()];
            foreach ($sheetAssertions as $test => $expectedResult) {
                $actualResult = $worksheet->getSheetState();
                self::assertSame(
                    $expectedResult,
                    $actualResult,
                    "Failed asserting sheet state {$expectedResult} for Worksheet '{$worksheet->getTitle()}' {$test}"
                );
            }
        }
        self::assertCount($sheetCount, $assertions);
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<string, string[]> */
    private function worksheetAssertions(): array
    {
        return [
            'Sheet1' => [
                'sheetState' => Worksheet::SHEETSTATE_VISIBLE,
            ],
            'Sheet2' => [
                'sheetState' => Worksheet::SHEETSTATE_HIDDEN,
            ],
        ];
    }

    public function testListWorksheetInfo(): void
    {
        $filename = 'tests/data/Reader/Gnumeric/HiddenSheet.gnumeric';
        $reader = new Gnumeric();
        $expected = [
            [
                'worksheetName' => 'Sheet1',
                'lastColumnLetter' => 'A',
                'lastColumnIndex' => 0,
                'totalRows' => 1,
                'totalColumns' => 1,
                'sheetState' => 'visible',
            ],
            [
                'worksheetName' => 'Sheet2',
                'lastColumnLetter' => 'A',
                'lastColumnIndex' => 0,
                'totalRows' => 1,
                'totalColumns' => 1,
                'sheetState' => 'hidden',
            ],
        ];
        self::assertSame($expected, $reader->listWorksheetInfo($filename));
    }
}
