<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\DiveStatisticsFormatter;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\UnitConverter;
use PHPUnit\Framework\TestCase;

final class DiveStatisticsFormatterTest extends TestCase
{
    public function testPercentageLabelRoundsCorrectly(): void
    {
        $formatter = $this->buildFormatter();

        self::assertSame('1 (33%)', $formatter->percentageLabel(1, 3));
        self::assertSame('2 (67%)', $formatter->percentageLabel(2, 3));
        self::assertSame('0 (0%)', $formatter->percentageLabel(0, 0));
        self::assertSame('0 (0%)', $formatter->percentageLabel(null, 4));
    }

    public function testBottomTimeFormatsHoursAndMinutes(): void
    {
        $formatter = $this->buildFormatter();

        self::assertSame('00:00', $formatter->bottomTime(0));
        self::assertSame('01:35', $formatter->bottomTime(95));
        self::assertSame('-', $formatter->bottomTime(null));
    }

    public function testDepthAndTemperatureFormattingMetric(): void
    {
        $formatter = $this->buildFormatter();

        self::assertSame('18,20 m', $formatter->depth(18.2));
        self::assertSame('22,0°C', $formatter->temperature(22.0));
    }

    public function testDepthAndTemperatureFormattingImperial(): void
    {
        $formatter = $this->buildFormatter([
            'APP_CONVERT_LENGTH' => 'true',
            'APP_CONVERT_TEMP' => 'true',
            'APP_DECIMAL_SEPARATOR' => '.',
        ]);

        self::assertSame('59.71 ft', $formatter->depth(18.2));
        self::assertSame('71.6°F', $formatter->temperature(22.0));
    }

    /**
     * @param array<string, string> $overrides
     */
    private function buildFormatter(array $overrides = []): DiveStatisticsFormatter
    {
        $config = Config::fromArray(array_merge([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
            'APP_DECIMAL_SEPARATOR' => ',',
        ], $overrides));

        return new DiveStatisticsFormatter(new UnitConverter($config), new Formatter($config));
    }
}
