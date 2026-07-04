<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

use DateTimeImmutable;

final readonly class Trip
{
    public function __construct(
        public int $id,
        public string $name,
        public ?DateTimeImmutable $dateFrom,
        public ?DateTimeImmutable $dateTo,
        public ?int $countryId,
        public ?int $shopId,
        public ?string $comment
    ) {
    }
}
