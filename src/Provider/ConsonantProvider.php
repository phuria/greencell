<?php

declare(strict_types=1);

namespace App\Provider;

class ConsonantProvider implements CharProvider
{
    public function getChars(): array
    {
        return ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'w', 'z'];
    }
}
