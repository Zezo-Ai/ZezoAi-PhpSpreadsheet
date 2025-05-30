<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\Attributes\DataProvider;

class LeftTest extends AllSetupTeardown
{
    /**
     * @param mixed $str string from which to extract
     * @param mixed $cnt number of characters to extract
     */
    #[DataProvider('providerLEFT')]
    public function testLEFT(mixed $expectedResult, mixed $str = 'omitted', mixed $cnt = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=LEFT()');
        } elseif ($cnt === 'omitted') {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=LEFT(A1)');
        } else {
            $this->setCell('A1', $str);
            $this->setCell('A2', $cnt);
            $sheet->getCell('B1')->setValue('=LEFT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLEFT(): array
    {
        return require 'tests/data/Calculation/TextData/LEFT.php';
    }

    #[DataProvider('providerLocaleLEFT')]
    public function testLowerWithLocaleBoolean(string $expectedResult, string $locale, mixed $value, mixed $characters): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $this->setCell('A2', $characters);
        $sheet->getCell('B1')->setValue('=LEFT(A1, A2)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLocaleLEFT(): array
    {
        return [
            ['VR', 'fr_FR', true, 2],
            ['WA', 'nl_NL', true, 2],
            ['TO', 'fi', true, 2],
            ['ИСТ', 'bg', true, 3],
            ['FA', 'fr_FR', false, 2],
            ['ON', 'nl_NL', false, 2],
            ['EPÄT', 'fi', false, 4],
            ['ЛОЖ', 'bg', false, 3],
        ];
    }

    #[DataProvider('providerCalculationTypeLEFTTrue')]
    public function testCalculationTypeTrue(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', true);
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=LEFT(A1, 1)');
        $this->setCell('B2', '=LEFT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerCalculationTypeLEFTTrue(): array
    {
        return [
            'Excel LEFT(true, 1) AND LEFT("hello", true)' => [
                Functions::COMPATIBILITY_EXCEL,
                'T',
                'H',
            ],
            'Gnumeric LEFT(true, 1) AND LEFT("hello", true)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'T',
                'H',
            ],
            'OpenOffice LEFT(true, 1) AND LEFT("hello", true)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '1',
                '#VALUE!',
            ],
        ];
    }

    #[DataProvider('providerCalculationTypeLEFTFalse')]
    public function testCalculationTypeFalse(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', false);
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=LEFT(A1, 1)');
        $this->setCell('B2', '=LEFT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerCalculationTypeLEFTFalse(): array
    {
        return [
            'Excel LEFT(false, 1) AND LEFT("hello", false)' => [
                Functions::COMPATIBILITY_EXCEL,
                'F',
                '',
            ],
            'Gnumeric LEFT(false, 1) AND LEFT("hello", false)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'F',
                '',
            ],
            'OpenOffice LEFT(false, 1) AND LEFT("hello", false)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '0',
                '#VALUE!',
            ],
        ];
    }

    #[DataProvider('providerCalculationTypeLEFTNull')]
    public function testCalculationTypeNull(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=LEFT(A1, 1)');
        $this->setCell('B2', '=LEFT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerCalculationTypeLEFTNull(): array
    {
        return [
            'Excel LEFT(null, 1) AND LEFT("hello", null)' => [
                Functions::COMPATIBILITY_EXCEL,
                '',
                '',
            ],
            'Gnumeric LEFT(null, 1) AND LEFT("hello", null)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                '',
                'H',
            ],
            'OpenOffice LEFT(null, 1) AND LEFT("hello", null)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '',
                '',
            ],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerLeftArray')]
    public function testLeftArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=LEFT({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerLeftArray(): array
    {
        return [
            'row vector #1' => [[['Hel', 'Wor', 'Php']], '{"Hello", "World", "PhpSpreadsheet"}', '3'],
            'column vector #1' => [[['Hel'], ['Wor'], ['Php']], '{"Hello"; "World"; "PhpSpreadsheet"}', '3'],
            'matrix #1' => [[['Hel', 'Wor'], ['Php', 'Exc']], '{"Hello", "World"; "PhpSpreadsheet", "Excel"}', '3'],
            'column vector #2' => [[['Php'], ['PhpSp']], '"PhpSpreadsheet"', '{3; 5}'],
        ];
    }
}
