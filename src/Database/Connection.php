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
        try {
            $pdo = new PDO(
                $config->dsn(),
                $config->databaseUser(),
                $config->databasePassword(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            if (str_starts_with(strtolower($config->dsn()), 'mysql:')) {
                $pdo->exec('SET NAMES utf8mb4');
            }

            return $pdo;
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to establish database connection.', 0, $exception);
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
