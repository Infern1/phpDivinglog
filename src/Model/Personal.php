<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class Personal
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $email,
        public ?string $city,
        public ?string $country,
        public ?string $comment,
        public ?string $picture
    ) {
    }
}
