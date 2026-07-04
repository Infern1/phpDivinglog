<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\DiveSiteRepository;

final readonly class DiveSiteApiController
{
    public function __construct(private DiveSiteRepository $sites)
    {
    }

    /**
     * @return array{data:list<object>}
     */
    public function list(): array
    {
        return ['data' => $this->sites->list()];
    }

    /**
     * @return array{data: object}|null
     */
    public function item(int $id): ?array
    {
        $site = $this->sites->findById($id);

        return $site === null ? null : ['data' => $site];
    }
}
