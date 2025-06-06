<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Database;

use PhpOffice\PhpSpreadsheet\Calculation\Database\DVarP;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PHPUnit\Framework\Attributes\DataProvider;

class DVarPTest extends SetupTeardownDatabases
{
    /**
     * @param mixed[] $database
     * @param mixed[][] $criteria
     */
    #[DataProvider('providerDVarP')]
    public function testDirectCallToDVarP(float|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $result = DVarP::evaluate($database, $field, $criteria);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    /**
     * @param mixed[] $database
     * @param mixed[][] $criteria
     */
    #[DataProvider('providerDVarP')]
    public function testDVarPAsWorksheetFormula(float|string $expectedResult, array $database, ?string $field, array $criteria): void
    {
        $this->prepareWorksheetWithFormula('DVARP', $database, $field, $criteria);

        $result = $this->getSheet()->getCell(self::RESULT_CELL)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerDVarP(): array
    {
        return [
            [
                7.04,
                self::database1(),
                'Yield',
                [
                    ['Tree'],
                    ['=Apple'],
                    ['=Pear'],
                ],
            ],
            [
                0.025622222222,
                self::database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Gender'],
                    ['Math', 'Male'],
                ],
            ],
            [
                0.011622222222,
                self::database3FilledIn(),
                'Score',
                [
                    ['Subject', 'Age'],
                    ['Science', '>8'],
                ],
            ],
            'Omitted field name' => [
                ExcelError::VALUE(),
                self::database1(),
                null,
                self::database1(),
            ],
        ];
    }
}
