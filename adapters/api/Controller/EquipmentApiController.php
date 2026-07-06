<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\EquipmentRepository;

final readonly class EquipmentApiController
{
    public function __construct(private EquipmentRepository $equipment)
    {
    }

    /**
     * @return array{data:list<object>}
     */
    public function list(): array
    {
        return ['data' => $this->equipment->list()];
    }

    /**
     * @return array{data: object}|null
     */
    public function item(int $id): ?array
    {
        $item = $this->equipment->findById($id);

        return $item === null ? null : ['data' => $item];
    }
}
