<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\StatsRepository;

final readonly class StatsApiController
{
    public function __construct(private StatsRepository $stats)
    {
    }

    /**
     * @return array{data: object}
     */
    public function view(): array
    {
        return ['data' => $this->stats->aggregate()];
    }
}
