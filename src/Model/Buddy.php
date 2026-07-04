<?php

declare(strict_types=1);

namespace PhpDivingLog\Model;

final readonly class Buddy
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public ?string $email,
        public ?string $comment,
        public ?string $picture
    ) {
    }
}
