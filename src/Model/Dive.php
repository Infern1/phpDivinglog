<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

use DateTimeImmutable;

final readonly class Dive
{
    /**
     * @param list<int> $buddyIds
     * @param array<string, mixed> $extra
     */
    public function __construct(
        public int $number,
        public int $logId,
        public int $placeId,
        public DateTimeImmutable $dateTime,
        public float $depthMax,
        public int $durationMinutes,
        public ?float $waterTemp,
        public ?float $airTemp,
        public ?float $weight,
        public ?float $pressureStart,
        public ?float $pressureEnd,
        public ?string $commentRtf,
        public array $buddyIds,
        public array $extra = []
    ) {
    }
}
