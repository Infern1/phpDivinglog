<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\UnitConverter;
use PHPUnit\Framework\TestCase;

final class UnitConverterTest extends TestCase
{
    public function testMetricPassThroughWhenConversionDisabled(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
            'APP_CONVERT_LENGTH' => 'false',
        ]);

        $converter = new UnitConverter($config);

        self::assertSame(10.0, $converter->depthToDisplay(10.0));
        self::assertSame('m', $converter->depthLabel());
    }

    public function testImperialConversionWhenEnabled(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
            'APP_CONVERT_LENGTH' => 'true',
            'APP_CONVERT_PRESSURE' => 'true',
            'APP_CONVERT_WEIGHT' => 'true',
            'APP_CONVERT_TEMP' => 'true',
            'APP_CONVERT_VOLUME' => 'true',
        ]);

        $converter = new UnitConverter($config);

        self::assertEqualsWithDelta(32.8084, $converter->depthToDisplay(10.0), 0.0001);
        self::assertEqualsWithDelta(29.0076, $converter->pressureToDisplay(2.0), 0.0001);
        self::assertEqualsWithDelta(22.0462, $converter->weightToDisplay(10.0), 0.0001);
        self::assertEqualsWithDelta(50.0, $converter->temperatureToDisplay(10.0), 0.0001);
        self::assertEqualsWithDelta(3.53147, $converter->volumeToDisplay(100.0), 0.0001);
        self::assertSame('ft', $converter->depthLabel());
    }
}
