<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Difference;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\FormulaArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateDifTest extends TestCase
{
    /** @param array<mixed>|int|string $expectedResult */
    #[DataProvider('providerDATEDIF')]
    public function testDirectCallToDATEDIF(array|int|string $expectedResult, string ...$args): void
    {
        $result = Difference::interval(...$args);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDATEDIF')]
    public function testDATEDIFAsFormula(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $calculation = Calculation::getInstance();
        $formula = "=DATEDIF({$arguments})";

        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    #[DataProvider('providerDATEDIF')]
    public function testDATEDIFInWorksheet(mixed $expectedResult, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DATEDIF({$argumentCells})";

        $result = $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();
        self::assertSame($expectedResult, $result);

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerDATEDIF(): array
    {
        return require 'tests/data/Calculation/DateTime/DATEDIF.php';
    }

    #[DataProvider('providerUnhappyDATEDIF')]
    public function testDATEDIFUnhappyPath(string $expectedException, mixed ...$args): void
    {
        $arguments = new FormulaArguments(...$args);

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $argumentCells = $arguments->populateWorksheet($worksheet);
        $formula = "=DATEDIF({$argumentCells})";

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage($expectedException);
        $worksheet->setCellValue('A1', $formula)
            ->getCell('A1')
            ->getCalculatedValue();

        $spreadsheet->disconnectWorksheets();
    }

    public static function providerUnhappyDATEDIF(): array
    {
        return [
            ['Formula Error: Wrong number of arguments for DATEDIF() function', '2023-03-1'],
        ];
    }

    public function testDateObject(): void
    {
        $obj1 = new DateTime('2000-3-31');
        $obj2 = new DateTimeImmutable('2000-2-29');
        self::assertSame(31, Days::between($obj1, $obj2));
    }

    /** @param array<mixed> $expectedResult */
    #[DataProvider('providerDateDifArray')]
    public function testDateDifArray(array $expectedResult, string $startDate, string $endDate, ?string $methods): void
    {
        $calculation = Calculation::getInstance();

        if ($methods === null) {
            $formula = "=DATEDIF({$startDate}, {$endDate})";
        } else {
            $formula = "=DATEDIF({$startDate}, {$endDate}, {$methods})";
        }
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertSame($expectedResult, $result);
    }

    public static function providerDateDifArray(): array
    {
        return [
            'row vector #1' => [[[364, 202, '#NUM!']], '{"2022-01-01", "2022-06-12", "2023-07-22"}', '"2022-12-31"', null],
            'row vector #2' => [[['#NUM!', '#NUM!', 203]], '"2022-12-31"', '{"2022-01-01", "2022-06-12", "2023-07-22"}', null],
            'column vector #1' => [[[364], [362], [359]], '{"2022-01-01"; "2022-01-03"; "2022-01-06"}', '"2022-12-31"', null],
            'matrix #1' => [[[365, 266], [139, 1]], '{"2022-01-01", "2022-04-10"; "2022-08-15", "2022-12-31"}', '"2023-01-01"', null],
            'column vector with methods' => [[[364, 11], [242, 7], [173, 5]], '{"2022-01-01"; "2022-05-03"; "2022-07-11"}', '"2022-12-31"', '{"D", "M"}'],
        ];
    }
}
