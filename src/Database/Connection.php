<?php

declare(strict_types=1);

namespace PhpDivingLog\Database;

use PDO;
use PDOException;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\ConfigException;
use RuntimeException;

final class Connection
{
    public static function fromConfig(Config $config): PDO
    {
        $dsn = $config->dsn();
        $isSqlite = str_starts_with(strtolower($dsn), 'sqlite:');

        if ($isSqlite) {
            self::assertSqliteFileIsReadable($dsn);
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        if ($isSqlite) {
            // PHP 8.4+ deprecates the generic PDO::SQLITE_* constants in favor of Pdo\Sqlite::*;
            // PHP 8.3 (the minimum supported version) does not have the Pdo\Sqlite class yet.
            // Resolved dynamically so PHPStan (targeting 8.3 stubs) doesn't flag the newer
            // constant as undefined.
            if (defined('Pdo\\Sqlite::ATTR_OPEN_FLAGS') && defined('Pdo\\Sqlite::OPEN_READONLY')) {
                $options[constant('Pdo\\Sqlite::ATTR_OPEN_FLAGS')] = constant('Pdo\\Sqlite::OPEN_READONLY');
            } else {
                $options[PDO::SQLITE_ATTR_OPEN_FLAGS] = PDO::SQLITE_OPEN_READONLY;
            }
        }

        try {
            $pdo = new PDO($dsn, $config->databaseUser(), $config->databasePassword(), $options);

            if (str_starts_with(strtolower($dsn), 'mysql:')) {
                $pdo->exec('SET NAMES utf8mb4');
            }

            return $pdo;
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to establish database connection.', 0, $exception);
        }
    }

    private static function assertSqliteFileIsReadable(string $dsn): void
    {
        $path = substr($dsn, strlen('sqlite:'));

        if ($path === '' || $path === ':memory:' || str_starts_with($path, 'file::memory:')) {
            return;
        }

        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('SQLite database file not readable: %s', $path));
        }
    }

    public static function validatedTablePrefix(string $prefix): string
    {
        if (preg_match('/^[A-Za-z0-9_]*$/', $prefix) !== 1) {
            throw new ConfigException('Invalid table prefix. Only letters, numbers, and underscores are allowed.');
        }

        return $prefix;
    }
}
