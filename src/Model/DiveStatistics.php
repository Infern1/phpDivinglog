<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

use DateTimeImmutable;

final readonly class DiveStatistics
{
    /**
     * @param array{min:?int,minNumber:?int,max:?int,maxNumber:?int,avg:?float} $diveTime
     * @param array{min:?float,minNumber:?int,max:?float,maxNumber:?int,avg:?float} $depth
     * @param array{min:?float,minNumber:?int,max:?float,maxNumber:?int,avg:?float} $waterTemp
     * @param array{min:?float,minNumber:?int,max:?float,maxNumber:?int,avg:?float} $airTemp
     * @param array<string, int|null> $classifications
     * @param array{b0_18:int,b19_30:int,b31_40:int,b41_55:int,b55_plus:int} $depthBuckets
     */
    public function __construct(
        public int $totalDives,
        public ?DateTimeImmutable $firstDiveDate,
        public ?int $firstDiveNumber,
        public ?DateTimeImmutable $lastDiveDate,
        public ?int $lastDiveNumber,
        public ?int $totalBottomTimeMinutes,
        public array $diveTime,
        public array $depth,
        public array $waterTemp,
        public array $airTemp,
        public array $classifications,
        public array $depthBuckets,
    ) {
    }
}
