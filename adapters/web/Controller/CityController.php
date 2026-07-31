<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\CityRepository;
use PhpDivingLog\Support\Seo\DescriptionTruncator;

final readonly class CityController
{
    public function __construct(
        private CityRepository $cities,
        private DescriptionTruncator $descriptionTruncator
    ) {
    }

    /**
     * @return array{cities:list<object>}
     */
    public function overview(): array
    {
        $cities = $this->cities->list();
        $total = count($cities);

        return [
            'cities' => $cities,
            'title' => 'Cities',
            'meta_description' => $this->descriptionTruncator->truncate(sprintf(
                'Browse %d %s featured in this dive log, each a logged diving destination.',
                $total,
                $total === 1 ? 'city' : 'cities'
            )),
        ];
    }

    /**
     * @return array{city: object, title: string, meta_description: string}|null
     */
    public function detail(int $id): ?array
    {
        $city = $this->cities->findById($id);
        if ($city === null) {
            return null;
        }

        $comment = $city->comment !== null ? trim($city->comment) : '';
        $description = $comment !== ''
            ? sprintf('%s. %s', $city->name, $comment)
            : sprintf('%s is a city featured in this dive log.', $city->name);

        return [
            'city' => $city,
            'title' => sprintf('%s — Diving Destination', $city->name),
            'meta_description' => $this->descriptionTruncator->truncate($description),
        ];
    }
}
