<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Database;

use PDO;
use PhpDivingLog\Database\Connection;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\ConfigException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ConnectionTest extends TestCase
{
    public function testValidatedTablePrefixAllowsExpectedCharacters(): void
    {
        self::assertSame('DL_42', Connection::validatedTablePrefix('DL_42'));
    }

    public function testValidatedTablePrefixRejectsInvalidCharacters(): void
    {
        $this->expectException(ConfigException::class);

        Connection::validatedTablePrefix('DL-42;DROP');
    }

    public function testFromConfigOpensSqliteDsnReadOnly(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $path = (string) tempnam(sys_get_temp_dir(), 'dl_test_');

        try {
            $seed = new PDO('sqlite:' . $path);
            $seed->exec('CREATE TABLE t (id INTEGER PRIMARY KEY)');
            unset($seed);

            $config = Config::fromArray([
                'DB_DSN' => 'sqlite:' . $path,
                'TABLE_PREFIX' => '',
            ]);

            $pdo = Connection::fromConfig($config);

            self::assertSame('sqlite', $pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
            self::assertSame(0, (int) $pdo->query('SELECT COUNT(*) FROM t')->fetchColumn());
        } finally {
            @unlink($path);
        }
    }

    public function testFromConfigThrowsWithPathWhenSqliteFileMissing(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $path = sys_get_temp_dir() . '/dl_test_missing_' . uniqid() . '.sqlite';

        $config = Config::fromArray([
            'DB_DSN' => 'sqlite:' . $path,
            'TABLE_PREFIX' => '',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($path);

        Connection::fromConfig($config);
    }
}
