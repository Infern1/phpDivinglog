<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

use DateTimeImmutable;

final readonly class Equipment
{
    public function __construct(
        public int $id,
        public string $object,
        public ?string $manufacturer,
        public ?DateTimeImmutable $purchaseDate,
        public ?DateTimeImmutable $serviceDate,
        public ?DateTimeImmutable $serviceWarningDate,
        public ?string $comment,
        public ?string $picture
    ) {
    }
}
