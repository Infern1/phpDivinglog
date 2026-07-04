<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\CityRepository;

final readonly class CityController
{
    public function __construct(private CityRepository $cities)
    {
    }

    /**
     * @return array{cities:list<object>}
     */
    public function overview(): array
    {
        return ['cities' => $this->cities->list()];
    }

    /**
     * @return array{city: object}|null
     */
    public function detail(int $id): ?array
    {
        $city = $this->cities->findById($id);
        return $city === null ? null : ['city' => $city];
    }
}
