<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\ConfigException;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testThrowsForMissingDatabaseSettings(): void
    {
        $this->expectException(ConfigException::class);

        Config::fromArray([]);
    }

    public function testAcceptsHostStyleDatabaseSettings(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'divelog',
            'DB_PASSWORD' => 'secret',
            'TABLE_PREFIX' => 'DL_',
            'APP_DEBUG' => 'true',
            'APP_MAX_LIST' => '40',
        ]);

        self::assertSame('mysql:host=localhost;port=3306;dbname=divelog;charset=utf8mb4', $config->dsn());
        self::assertTrue($config->appDebug());
        self::assertSame(40, $config->maxList());
        self::assertSame('DL_', $config->tablePrefix());
    }

    public function testAcceptsDsnStyleDatabaseSettings(): void
    {
        $config = Config::fromArray([
            'DB_DSN' => 'mysql:host=db;port=3307;dbname=divelog;charset=utf8mb4',
            'DB_USER' => 'divelog',
            'DB_PASSWORD' => 'secret',
        ]);

        self::assertSame('mysql:host=db;port=3307;dbname=divelog;charset=utf8mb4', $config->dsn());
        self::assertSame('divelog', $config->databaseUser());
    }
}
