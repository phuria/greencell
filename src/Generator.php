<?php

namespace App;

use App\Provider\CharProvider;

class Generator
{
    /**
     * @var CharProvider
     */
    private $consonantProvider;

    /**
     * @var CharProvider
     */
    private $vowelProvider;

    /**
     * @var CharProvider
     */
    private $charProvider;

    /**
     * @var EndingChecker
     */
    private $consonantEndingChecker;

    public function __construct(
        CharProvider $consonantProvider,
        CharProvider $vowelProvider,
        CharProvider $charProvider,
        EndingChecker $consonantEndingChecker
    ) {
        $this->consonantProvider = $consonantProvider;
        $this->vowelProvider = $vowelProvider;
        $this->charProvider = $charProvider;
        $this->consonantEndingChecker = $consonantEndingChecker;
    }

    public function getWord(int $min = 2, int $max = 15): string
    {
        $word = '';
        $size = random_int($min, $max);

        while (mb_strlen($word) < $size) {
            $word .= $this->getNextChar($word);
        }

        return $word;
    }

    private function getNextChar(string $word): string
    {
        $length = mb_strlen($word);

        if (0 === $length) {
            return $this->getRandConsonant();
        }

        if (1 === $length || $this->hasEndingConsonants($word)) {
            return $this->getRandVowel();
        }

        return $this->getRandChar();
    }

    public function getWords(int $numberOfWords, int $wordMin = 2, int $wordMax = 15): array
    {
        $words = [];

        for ($i = 0; $i < $numberOfWords; $i++) {
            $words[] = $this->getWord($wordMin, $wordMax);
        }

        return $words;
    }

    private function hasEndingConsonants(string $word): bool
    {
        return $this->consonantEndingChecker->hasEndingChars($word);
    }

    private function getRandChar(): string
    {
        return $this->getRandElement($this->charProvider);
    }

    private function getRandVowel(): string
    {
        return $this->getRandElement($this->vowelProvider);
    }

    private function getRandConsonant(): string
    {
        return $this->getRandElement($this->consonantProvider);
    }

    private function getRandElement(CharProvider $provider): string
    {
        $array = $provider->getChars();

        return $array[array_rand($array)];
    }
}
