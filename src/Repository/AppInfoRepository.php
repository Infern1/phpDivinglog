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
        $sql = sprintf('SELECT PrgName, Version FROM %sDBInfo LIMIT 1', $this->tablePrefix);
        $row = $this->pdo->query($sql)->fetch();

        return new AppInfo(
            $this->config->appName(),
            $this->config->appVersion(),
            is_array($row) && isset($row['PrgName']) ? (string) $row['PrgName'] : null,
            is_array($row) && isset($row['Version']) ? (string) $row['Version'] : null
        );
    }
}
