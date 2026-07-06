<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use DateTimeImmutable;
use PhpDivingLog\Model\Dive;
use PhpDivingLog\Model\Tank;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\DiveMetricsCalculator;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\UnitConverter;
use PHPUnit\Framework\TestCase;

final class DiveMetricsCalculatorTest extends TestCase
{
    public function testCalculateUsesAllTankSetsForSac(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
            'APP_CONVERT_LENGTH' => 'false',
            'APP_CONVERT_VOLUME' => 'false',
            'APP_DECIMAL_SEPARATOR' => '.',
        ]);

        $calculator = new DiveMetricsCalculator(new UnitConverter($config), new Formatter($config));

        $dive = new Dive(
            1,
            100,
            10,
            new DateTimeImmutable('2026-01-01 12:00:00'),
            18.2,
            40,
            null,
            null,
            null,
            null,
            null,
            null,
            [],
            [
                'profile' => '010000000000015000000000020000000000',
                'profile_interval_seconds' => 60,
            ]
        );

        $tanks = [
            new Tank(1, 1, 12.0, 200.0, 70.0, 32.0),
            new Tank(2, 1, 11.0, 210.0, 90.0, 21.0),
        ];

        $result = $calculator->calculate($dive, $tanks);

        self::assertSame('15.00 m', $result['averageDepthDisplay']);
        self::assertSame('28.80 l/min', $result['sacDisplay']);
    }

    public function testCalculateReturnsDashWhenProfileMissing(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
        ]);

        $calculator = new DiveMetricsCalculator(new UnitConverter($config), new Formatter($config));

        $dive = new Dive(
            1,
            100,
            10,
            new DateTimeImmutable('2026-01-01 12:00:00'),
            18.2,
            40,
            null,
            null,
            null,
            null,
            null,
            null,
            []
        );

        $result = $calculator->calculate($dive, [new Tank(1, 1, 12.0, 200.0, 70.0, 32.0)]);

        self::assertSame('-', $result['averageDepthDisplay']);
        self::assertSame('-', $result['sacDisplay']);
    }
}
