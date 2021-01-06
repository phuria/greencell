<?php

declare(strict_types=1);

namespace App;

use App\Provider\CharProvider;

class EndingChecker
{
    /**
     * @var CharProvider
     */
    private $provider;

    public function __construct(CharProvider $provider)
    {
        $this->provider = $provider;
    }

    public function hasEndingChars(string $word, int $endingChars = 2): bool
    {
        $pattern = implode(',', $this->provider->getChars());
        preg_match("/[{$pattern}]{{$endingChars}}$/", $word, $matches);

        return (bool) count($matches);
    }
}
