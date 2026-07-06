<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\PictureRepository;
use PhpDivingLog\Support\MediaResolver;

final readonly class GalleryController
{
    public function __construct(private PictureRepository $pictures, private MediaResolver $media)
    {
    }

    /**
     * @return array{pictures:list<array<string, mixed>>}
     */
    public function forDive(int $logId): array
    {
        $pictures = array_map(
            fn ($picture) => [
                'id' => $picture->id,
                'url' => $this->media->pictureUrl($picture->filename),
                'thumb' => $this->media->thumbUrl($picture->filename),
                'description' => $picture->description,
            ],
            $this->pictures->findByLogId($logId)
        );

        return ['pictures' => $pictures];
    }
}
