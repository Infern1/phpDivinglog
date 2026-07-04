<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use DateTimeImmutable;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\Formatter;
use PHPUnit\Framework\TestCase;

final class FormatterTest extends TestCase
{
    public function testDateAndDecimalAndCoordinatesFormatting(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
            'APP_DATE_FORMAT' => 'date:dmy:-',
            'APP_DECIMAL_SEPARATOR' => ',',
            'APP_COORD_FORMAT' => 'dm',
        ]);

        $formatter = new Formatter($config);

        self::assertSame('31-12-2026', $formatter->formatDate(new DateTimeImmutable('2026-12-31')));
        self::assertSame('12,35', $formatter->formatDecimal(12.345, 2));
        self::assertStringContainsString('10 ', $formatter->formatCoordinate(10.5));
    }
}
