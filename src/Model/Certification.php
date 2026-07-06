<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

use DateTimeImmutable;

final readonly class Certification
{
    public function __construct(
        public ?string $organisation,
        public ?string $certification,
        public ?DateTimeImmutable $date,
        public ?string $certNumber,
        public ?string $instructor,
        public ?string $frontImage,
        public ?string $backImage,
    ) {
    }
}
