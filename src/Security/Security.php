<?php

declare(strict_types=1);

namespace App\Security;

interface Security
{
    public function isSecureToRun(): bool;
}
