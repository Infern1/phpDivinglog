<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use DateTimeImmutable;
use PhpDivingLog\Repository\EquipmentRepository;
use PhpDivingLog\Support\Config;

final readonly class EquipmentController
{
    public function __construct(private EquipmentRepository $equipment, private Config $config)
    {
    }

    /**
     * @return array{equipment:list<array<string, mixed>>}
     */
    public function overview(): array
    {
        return [
            'equipment' => array_map(fn ($item) => $this->mapEquipment($item), $this->equipment->list()),
        ];
    }

    /**
     * @return array{item: array<string, mixed>}|null
     */
    public function detail(int $id): ?array
    {
        $item = $this->equipment->findById($id);
        if ($item === null) {
            return null;
        }

        return ['item' => $this->mapEquipment($item)];
    }

    /**
     * @param object $item
     * @return array<string, mixed>
     */
    private function mapEquipment(object $item): array
    {
        $now = new DateTimeImmutable();
        $warningDays = $this->config->all()['equipment_service_warning'];
        $dueSoon = $item->serviceDate !== null && $item->serviceDate <= $now->modify('+' . $warningDays . ' days');

        return [
            'id' => $item->id,
            'object' => $item->object,
            'manufacturer' => $item->manufacturer,
            'serviceDate' => $item->serviceDate,
            'serviceDueSoon' => $dueSoon,
            'comment' => $item->comment,
            'picture' => $item->picture,
        ];
    }
}
