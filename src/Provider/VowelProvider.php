<?php

declare(strict_types=1);

namespace App\Provider;

class VowelProvider implements CharProvider
{
    public function getChars(): array
    {
        return ['a', 'e', 'i', 'o', 'u', 'y'];
    }
}
