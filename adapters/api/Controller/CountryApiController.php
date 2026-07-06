<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\CountryRepository;

final readonly class CountryApiController
{
    public function __construct(private CountryRepository $countries)
    {
    }

    /**
     * @return array{data:list<object>}
     */
    public function list(): array
    {
        return ['data' => $this->countries->list()];
    }

    /**
     * @return array{data: object}|null
     */
    public function item(int $id): ?array
    {
        $country = $this->countries->findById($id);

        return $country === null ? null : ['data' => $country];
    }
}
