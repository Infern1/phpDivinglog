<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\CityRepository;

final readonly class CityApiController
{
    public function __construct(private CityRepository $cities)
    {
    }

    /**
     * @return array{data:list<object>}
     */
    public function list(): array
    {
        return ['data' => $this->cities->list()];
    }

    /**
     * @return array{data: object}|null
     */
    public function item(int $id): ?array
    {
        $city = $this->cities->findById($id);

        return $city === null ? null : ['data' => $city];
    }
}
