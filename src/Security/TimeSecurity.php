<?php

declare(strict_types=1);

namespace App\Security;

use DateTime;

class TimeSecurity implements Security
{
    /**
     * @var int
     */
    private $now;

    /**
     * @var array
     */
    private $disabledPeriods;

    public function __construct(DateTime $now)
    {
        $this->now = $this->dateTimeToMinutesOfWeek($now);
    }

    public function addDisabledPeriod(DateTime $from, DateTime $to): void
    {
        $this->disabledPeriods[] = [$this->dateTimeToMinutesOfWeek($from), $this->dateTimeToMinutesOfWeek($to)];
    }

    public function isSecureToRun(): bool
    {
        foreach ($this->disabledPeriods as $period) {
            [$from, $to] = $period;

            if ($this->isBetween($this->now, $from, $to)) {
                return false;
            }
        }

        return true;
    }

    private function dateTimeToMinutesOfWeek(DateTime $dateTime): int
    {
        $minutesFromDays = $dateTime->format('w') * 24 * 60;
        $minutesFromHours = $dateTime->format('H') * 60;

        return $minutesFromDays + $minutesFromHours + (int) $dateTime->format('i');
    }

    private function isBetween(int $current, int $from, int $to): bool
    {
        return false !== filter_var(
            $current,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => $from,
                    'max_range' => $to,
                ],
            ]
        );
    }
}
