<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    #[DataProvider('providerYEAR')]
    public function testDirectCallToYEAR(mixed $expectedResultExcel, mixed ...$args): void
    {
        $result = DateParts::year(...$args);
        self::assertSame($expectedResultExcel, $result);
    }

    #[DataProvider('providerYEAR')]
    public function testYEARAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=YEAR({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerYEAR')]
    public function testYEARInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=YEAR({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerYEAR(): array
    {
        return require 'tests/data/Calculation/DateTime/YEAR.php';
    }

    #[DataProvider('providerUnhappyYEAR')]
    public function testYEARUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=YEAR({$argumentCells})";

        $this->expectException(\PhpOffice\PhpSpreadsheet\Calculation\Exception::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyYEAR(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for YEAR() function'],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerYearArray')]
    public function testYearArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=YEAR({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerYearArray(): array
    {
        return [
            'row vector' => [[[2021, 2022, 2023]], '{"2021-01-01", "2022-01-01", "2023-01-01"}'],
            'column vector' => [[[2021], [2022], [2023]], '{"2021-01-01"; "2022-01-01"; "2023-01-01"}'],
            'matrix' => [[[2021, 2022], [2023, 1999]], '{"2021-01-01", "2022-01-01"; "2023-01-01", "1999-12-31"}'],
        ];
    }
}
