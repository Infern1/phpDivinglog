<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\StatsRepository;

final readonly class StatsController
{
    public function __construct(private StatsRepository $stats)
    {
    }

    /**
     * @return array{stats: object}
     */
    public function view(): array
    {
        return ['stats' => $this->stats->aggregate()];
    }
}
