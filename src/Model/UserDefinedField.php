<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class UserDefinedField
{
    public function __construct(
        public int $id,
        public int $logId,
        public string $name,
        public ?string $value
    ) {
    }
}
