<?php

declare(strict_types=1);

namespace App;

use App\Exception\InvalidInputException;
use App\Exception\SecurityException;
use App\Provider\CompositeProvider;
use App\Provider\ConsonantProvider;
use App\Provider\VowelProvider;
use App\Security\DisabledSecurity;
use App\Security\Security;
use App\Security\TimeSecurity;
use DateTime;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Application
{
    private const DEFAULT_NUMBER_OF_WORDS = 10;
    private const REAL_MIN_NUMBER_OF_WORDS = 1;
    private const REAL_MIN_WORD_LENGTH = 2;
    private const DEFAULT_MIN_WORD_LENGTH = 5;
    private const DEFAULT_MAX_WORD_LENGTH = 15;

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->doExecute($input, $output);
        } catch (InvalidInputException | SecurityException $exception) {
            $output->writeln($exception->getMessage());
            $this->logException($exception);
            return;
        } catch (Throwable $throwable) {
            $this->logException($throwable);
            throw $throwable;
        }
    }

    private function logException(Throwable $throwable): void
    {
        (new Logger('exception.log'))->log('['.get_class($throwable).']: '.$throwable->getMessage());
    }

    private function doExecute(InputInterface $input, OutputInterface $output): void
    {
        $now = $this->getNow($input->getOption('now'));
        $security = $this->getSecurity((bool) $input->getOption('force'), $now);

        if (false === $security->isSecureToRun()) {
            throw new SecurityException('Is not secure to run generator now. Try again later.');
        }

        $numberOfWords = $this->getNumberOfWords($input->getArgument('words'));
        $minWordLength = $this->getMinWordLength($input->getOption('min-word-length'));
        $maxWordLength = $this->getMaxWordLength($input->getOption('max-word-length'));
        $this->validateMinMax($minWordLength, $maxWordLength);

        $words = (new Generator(
            $consonantProvider = new ConsonantProvider(),
            $vowelProvider = new VowelProvider(),
            $charProvider = new CompositeProvider([$consonantProvider, $vowelProvider]),
            $consonantEndingChecker = new EndingChecker($consonantProvider)
        ))->getWords(
            $numberOfWords,
            $minWordLength,
            $maxWordLength
        );

        (new Logger('generator.log'))->log(implode("\n", [
            "Generated At: {$now->format('Y-m-d H:i:s')}",
            "Number of generated words: {$numberOfWords}",
            "Min / max length of word: {$minWordLength} / {$maxWordLength}",
            '---'
        ]));

        $output->writeln($list = implode("\n", $words));
        (new Logger('words.txt'))->log($list);
    }

    private function getSecurity(bool $force, DateTime $now): Security
    {
        if ($force) {
            return new DisabledSecurity();
        }

        $security = new TimeSecurity($now);
        $security->addDisabledPeriod(new DateTime('Sunday 00:00'), new DateTime('Monday 10:00'));
        $security->addDisabledPeriod(new DateTime('Friday 15:00'), new DateTime('Saturday 23:59'));

        return $security;
    }

    private function getNow(?string $now): DateTime
    {
        if ($now) {
            return new DateTime($now);
        }

        return new DateTime();
    }

    private function getNumberOfWords(?string $words): int
    {
        $intWords = $this->stringToInt($words);

        if (null === $words) {
            return self::DEFAULT_NUMBER_OF_WORDS;
        }

        if ($intWords >= self::REAL_MIN_NUMBER_OF_WORDS) {
            return $intWords;
        }

        throw new InvalidInputException("Invalid number of words [{$words}].");
    }

    private function getMinWordLength(?string $length): int
    {
        $intLength = $this->stringToInt($length);

        if (null === $length) {
            return self::DEFAULT_MIN_WORD_LENGTH;
        }

        if ($intLength >= self::REAL_MIN_WORD_LENGTH) {
            return $intLength;
        }

        throw new InvalidInputException("Invalid min length of word [{$length}].");
    }

    private function getMaxWordLength(?string $length): int
    {
        $intLength = $this->stringToInt($length);

        if (null === $length) {
            return self::DEFAULT_MAX_WORD_LENGTH;
        }

        return $intLength;
    }

    private function stringToInt(?string $length): ?int
    {
        if (null === $length || '' === $length) {
            return null;
        }

        return (int) $length;
    }

    private function validateMinMax(int $min, int $max): void
    {
        if ($min > $max) {
            throw new InvalidInputException("Min length [{$min}] of word can not be greater than max [{$max}] length.");
        }
    }
}
