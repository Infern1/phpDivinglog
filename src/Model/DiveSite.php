<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class DiveSite
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $countryId,
        public ?int $cityId,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $mapImage,
        public ?string $comment
    ) {
    }
}
