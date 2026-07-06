<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use DateTimeImmutable;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\EquipmentRepository;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\MediaResolver;
use PhpDivingLog\Support\UnitConverter;

final readonly class EquipmentController
{
    public function __construct(
        private EquipmentRepository $equipment,
        private DiveRepository $dives,
        private Config $config,
        private Formatter $formatter,
        private UnitConverter $converter,
        private MediaResolver $media,
    ) {
    }

    /**
     * @return array{equipment:list<array<string, mixed>>}
     */
    public function overview(): array
    {
        $rows = $this->equipment->listWithDiveCounts();

        return [
            'equipment' => array_map(function (array $row): array {
                $mapped = $this->mapEquipment($row['item']);
                $mapped['diveCount'] = $row['diveCount'];
                return $mapped;
            }, $rows),
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

        $diveRows = $this->dives->listOverviewByEquipment($id);
        $dives = $this->mapDiveRows($diveRows ?? []);

        return [
            'item' => $this->mapEquipment($item),
            'dives' => $dives,
            'showDiveLinking' => $diveRows !== null,
        ];
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
            'pictureUrl' => $item->picture !== null && $item->picture !== '' ? $this->media->equipmentUrl($item->picture) : null,
        ];
    }

    /**
     * @param list<array{number:int,date_time:\DateTimeImmutable,depth:float,duration:int,location:string}> $rows
     * @return list<array{number:int,date:string,depth:string,duration:int,location:string,url:string}>
     */
    private function mapDiveRows(array $rows): array
    {
        return array_map(function (array $row): array {
            return [
                'number' => $row['number'],
                'date' => $this->formatter->formatDate($row['date_time']),
                'depth' => $this->formatter->formatDecimal($this->converter->depthToDisplay($row['depth']), 1) . ' ' . $this->converter->depthLabel(),
                'duration' => $row['duration'],
                'location' => $row['location'],
                'url' => '/dives/' . $row['number'],
            ];
        }, $rows);
    }
}
