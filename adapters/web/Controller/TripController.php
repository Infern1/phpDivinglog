<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\TripRepository;

final readonly class TripController
{
    public function __construct(private TripRepository $trips)
    {
    }

    /**
     * @return array{trips:list<object>}
     */
    public function overview(): array
    {
        return ['trips' => $this->trips->list()];
    }

    /**
     * @return array{trip: object}|null
     */
    public function detail(int $id): ?array
    {
        $trip = $this->trips->findById($id);
        return $trip === null ? null : ['trip' => $trip];
    }
}
