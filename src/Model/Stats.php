<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

use DateTimeImmutable;

final readonly class Stats
{
    public function __construct(
        public int $diveCount,
        public ?DateTimeImmutable $firstDive,
        public ?DateTimeImmutable $lastDive,
        public ?float $maxDepth,
        public ?float $avgDepth,
        public ?int $totalDurationMinutes
    ) {
    }
}
