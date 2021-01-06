<?php

declare(strict_types=1);

namespace App\Security;

class DisabledSecurity implements Security
{
    public function isSecureToRun(): bool
    {
        return true;
    }
}
