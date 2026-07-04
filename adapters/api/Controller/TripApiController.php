<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\TripRepository;

final readonly class TripApiController
{
    public function __construct(private TripRepository $trips)
    {
    }

    /**
     * @return array{data:list<object>}
     */
    public function list(): array
    {
        return ['data' => $this->trips->list()];
    }

    /**
     * @return array{data: object}|null
     */
    public function item(int $id): ?array
    {
        $trip = $this->trips->findById($id);

        return $trip === null ? null : ['data' => $trip];
    }
}
