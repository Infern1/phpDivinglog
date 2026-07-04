<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\StatsRepository;

final readonly class SummaryController
{
    public function __construct(private StatsRepository $stats)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function embeddable(): array
    {
        $stats = $this->stats->aggregate();

        return [
            'diveCount' => $stats->diveCount,
            'maxDepth' => $stats->maxDepth,
            'totalDurationMinutes' => $stats->totalDurationMinutes,
        ];
    }
}
