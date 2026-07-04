<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class Tank
{
    public function __construct(
        public int $id,
        public int $diveNumber,
        public ?float $volume,
        public ?float $pressureStart,
        public ?float $pressureEnd,
        public ?float $o2
    ) {
    }
}
