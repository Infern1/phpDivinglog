<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class AppInfo
{
    public function __construct(
        public string $appName,
        public string $appVersion,
        public ?string $databaseProgram,
        public ?string $databaseVersion
    ) {
    }
}
