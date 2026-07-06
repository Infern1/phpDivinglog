<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class Picture
{
    public function __construct(
        public int $id,
        public int $logId,
        public string $filename,
        public ?string $description
    ) {
    }
}
