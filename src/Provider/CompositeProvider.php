<?php

declare(strict_types=1);

namespace App\Provider;

class CompositeProvider implements CharProvider
{
    /**
     * @var CharProvider[]
     */
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function getChars(): array
    {
        $chars = [];

        foreach ($this->providers as $provider) {
            $chars[] = $provider->getChars();
        }

        return array_merge(...$chars);
    }
}
