<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class City
{
    public function __construct(
        public int $id,
        public int $countryId,
        public string $name,
        public ?string $comment
    ) {
    }
}
