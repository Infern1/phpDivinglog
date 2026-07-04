<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\CountryRepository;
use PhpDivingLog\Support\MediaResolver;

final readonly class CountryController
{
    public function __construct(private CountryRepository $countries, private MediaResolver $media)
    {
    }

    /**
     * @return array{countries:list<array<string, mixed>>}
     */
    public function overview(): array
    {
        return [
            'countries' => array_map([$this, 'mapCountry'], $this->countries->list()),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function detail(int $id): ?array
    {
        $country = $this->countries->findById($id);
        if ($country === null) {
            return null;
        }

        return ['country' => $this->mapCountry($country)];
    }

    /**
     * @param object $country
     * @return array<string, mixed>
     */
    private function mapCountry(object $country): array
    {
        return [
            'id' => $country->id,
            'name' => $country->name,
            'flagUrl' => $country->flagImage !== null ? $this->media->flagUrl($country->flagImage) : null,
        ];
    }
}
