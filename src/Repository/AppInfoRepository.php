<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\AppInfo;
use PhpDivingLog\Support\Config;
use PDO;

final readonly class AppInfoRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix, private Config $config)
    {
    }

    public function getInfo(): AppInfo
    {
        $row = $this->fetchRow();

        return new AppInfo(
            $this->config->appName(),
            $this->config->appVersion(),
            is_array($row) && isset($row['PrgName']) ? (string) $row['PrgName'] : null,
            is_array($row) && isset($row['Version'])
                ? (string) $row['Version']
                : (is_array($row) && isset($row['DBVersion']) ? (string) $row['DBVersion'] : null)
        );
    }

    /**
     * @return array<string, mixed>|false
     */
    private function fetchRow(): array|false
    {
        $sql = sprintf('SELECT * FROM %sDBInfo LIMIT 1', $this->tablePrefix);

        try {
            return $this->pdo->query($sql)->fetch();
        } catch (\PDOException $exception) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if ($sqlState === '42S02' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such table'))) {
                return false;
            }

            throw $exception;
        }
    }
}
