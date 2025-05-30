<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class CleanTest extends AllSetupTeardown
{
    #[DataProvider('providerCLEAN')]
    public function testCLEAN(mixed $expectedResult, mixed $value = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($value === 'omitted') {
            $sheet->getCell('B1')->setValue('=CLEAN()');
        } else {
            $this->setCell('A1', $value);
            $sheet->getCell('B1')->setValue('=CLEAN(A1)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerCLEAN(): array
    {
        return require 'tests/data/Calculation/TextData/CLEAN.php';
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerCleanArray')]
    public function testCleanArray(array $expectedResult, string $array): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CLEAN({$array})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerCleanArray(): array
    {
        return [
            'row vector' => [[['PHP', 'MS Excel', 'Open/Libre Office']], '{"PHP", "MS Excel", "Open/Libre Office"}'],
            'column vector' => [[['PHP'], ['MS Excel'], ['Open/Libre Office']], '{"PHP"; "MS Excel"; "Open/Libre Office"}'],
            'matrix' => [[['PHP', 'MS Excel'], ['PhpSpreadsheet', 'Open/Libre Office']], '{"PHP", "MS Excel"; "PhpSpreadsheet", "Open/Libre Office"}'],
        ];
    }
}
