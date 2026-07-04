<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class Country
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $flagImage
    ) {
    }
}
