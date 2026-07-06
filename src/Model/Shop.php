<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class Shop
{
    public function __construct(
        public int $id,
        public int $countryId,
        public string $name,
        public ?string $shopType,
        public ?string $city,
        public ?string $comment
    ) {
    }
}
