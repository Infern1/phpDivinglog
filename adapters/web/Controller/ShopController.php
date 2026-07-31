<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\ShopRepository;
use PhpDivingLog\Support\Seo\DescriptionTruncator;

final readonly class ShopController
{
    public function __construct(
        private ShopRepository $shops,
        private DescriptionTruncator $descriptionTruncator
    ) {
    }

    /**
     * @return array{shops:list<object>}
     */
    public function overview(): array
    {
        $shops = $this->shops->list();
        $total = count($shops);

        return [
            'shops' => $shops,
            'title' => 'Dive Shops',
            'meta_description' => sprintf('Browse %d dive %s logged in this logbook.', $total, $total === 1 ? 'shop' : 'shops'),
        ];
    }

    /**
     * @return array{shop: object, title: string, meta_description: string}|null
     */
    public function detail(int $id): ?array
    {
        $shop = $this->shops->findById($id);
        if ($shop === null) {
            return null;
        }

        $city = $shop->city !== null ? trim($shop->city) : '';
        $shopType = $shop->shopType !== null ? trim($shop->shopType) : '';
        $comment = $shop->comment !== null ? trim($shop->comment) : '';

        $descriptionParts = [$city !== ''
            ? sprintf('%s is a dive shop in %s.', $shop->name, $city)
            : sprintf('%s is a dive shop featured in this dive log.', $shop->name)];
        if ($shopType !== '') {
            $descriptionParts[] = sprintf('Type: %s.', $shopType);
        }
        if ($comment !== '') {
            $descriptionParts[] = $comment;
        }

        return [
            'shop' => $shop,
            'title' => sprintf('%s — Dive Shop', $shop->name),
            'meta_description' => $this->descriptionTruncator->truncate(implode(' ', $descriptionParts)),
        ];
    }
}
