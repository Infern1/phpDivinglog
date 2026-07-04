<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\DiveRepository;

final readonly class DiveApiController
{
    public function __construct(private DiveRepository $dives)
    {
    }

    /**
     * @return array{data:list<int>}
     */
    public function list(): array
    {
        return ['data' => $this->dives->listNumbers(100, 0)];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function item(int $number): ?array
    {
        $dive = $this->dives->findByNumber($number);
        if ($dive === null) {
            return null;
        }

        return [
            'data' => [
                'number' => $dive->number,
                'logId' => $dive->logId,
                'placeId' => $dive->placeId,
                'dateTime' => $dive->dateTime->format(DATE_ATOM),
                'depthMax' => $dive->depthMax,
            ],
        ];
    }
}
