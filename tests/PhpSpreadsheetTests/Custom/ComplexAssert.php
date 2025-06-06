<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Custom;

use Complex\Complex;
use PHPUnit\Framework\TestCase;

class ComplexAssert extends TestCase
{
    private string $errorMessage = '';

    private float $delta = 0.0;

    public function __construct()
    {
        // Phpstan doesn't want you to use "internal" method outside PHPunit namespace
        parent::__construct('complexAssert'); //* @phpstan-ignore-line
    }

    private function testExpectedExceptions(string|float $expected, string|float $actual): bool
    {
        //    Expecting an error, so we do a straight string comparison
        if ($expected === $actual) {
            return true;
        } elseif ($expected === INF && $actual === 'INF') {
            return true;
        }
        $this->errorMessage = 'Expected Error: ' . $actual . ' !== ' . $expected;

        return false;
    }

    private function adjustDelta(float $expected, float $actual, float $delta): float
    {
        $adjustedDelta = $delta;

        if (abs($actual) > 10 && abs($expected) > 10) {
            $variance = floor(log10(abs($expected)));
            $adjustedDelta *= 10 ** $variance;
        }

        return $adjustedDelta > 1.0 ? 1.0 : $adjustedDelta;
    }

    public function setDelta(float $delta): self
    {
        $this->delta = $delta;

        return $this;
    }

    public function assertComplexEquals(mixed $expected, mixed $actual, ?float $delta = null): bool
    {
        if ($expected === INF || (is_string($expected) && $expected[0] === '#')) {
            return $this->testExpectedExceptions($expected, (is_string($actual) || is_float($actual)) ? $actual : 'neither string nor float');
        }

        if ($delta === null) {
            $delta = $this->delta;
        }
        $expectedComplex = new Complex($expected);
        $actualComplex = new Complex($actual);

        $adjustedDelta = $this->adjustDelta($expectedComplex->getReal(), $actualComplex->getReal(), $delta);
        if (abs($actualComplex->getReal() - $expectedComplex->getReal()) > $adjustedDelta) {
            $this->errorMessage = 'Mismatched Real part: ' . $actualComplex->getReal() . ' != ' . $expectedComplex->getReal();

            return false;
        }

        $adjustedDelta = $this->adjustDelta($expectedComplex->getImaginary(), $actualComplex->getImaginary(), $delta);
        if (abs($actualComplex->getImaginary() - $expectedComplex->getImaginary()) > $adjustedDelta) {
            $this->errorMessage = 'Mismatched Imaginary part: ' . $actualComplex->getImaginary() . ' != ' . $expectedComplex->getImaginary();

            return false;
        }

        if ($actualComplex->getSuffix() !== $actualComplex->getSuffix()) {
            $this->errorMessage = 'Mismatched Suffix: ' . $actualComplex->getSuffix() . ' != ' . $expectedComplex->getSuffix();

            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /** @param array<mixed>|float|string $actual */
    public function runAssertComplexEquals(string $expected, array|float|string $actual, ?float $delta = null): void
    {
        self::assertTrue($this->assertComplexEquals($expected, $actual, $delta), $this->getErrorMessage());
    }
}
