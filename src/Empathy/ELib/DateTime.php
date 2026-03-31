<?php

declare(strict_types=1);

namespace Empathy\ELib;

class DateTime
{
    private int $time = 0;

    private string $mysql_time = '';

    private int $day = 0;

    private int $month = 0;

    private int $year = 0;

    private int $hour = 0;

    private int $minute = 0;

    private int $second = 0;

    private int $last_day = 0;

    private int $dow = 0;

    private ?bool $valid = null;

    /** @var list<int> */
    private static array $length = [31, 28, 31, 30, 31, 30,
        31, 31, 30, 31, 30, 31];

    /**
     * @param array<int|string, mixed> $time
     */
    public function __construct(array $time = [], bool $do_init = true)
    {
        if (sizeof($time) === 0) {
            $this->time = time();
        } elseif (sizeof($time) === 1) {
            $this->time = (int) $time[0];
        } else {
            $this->valid = checkdate((int) $time['month'], (int) $time['day'], (int) $time['year']);
            $t = mktime(
                (int) $time['hour'],
                (int) $time['minute'],
                (int) $time['second'],
                (int) $time['month'],
                (int) $time['day'],
                (int) $time['year']
            );
            $this->time = $t !== false ? $t : time();
        }

        if ($do_init) {
            $this->init();
        }
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function init(): void
    {
        $this->mysql_time = date('Y:m:d H:i:s', $this->time);
        list($date, $clock) = explode(' ', $this->mysql_time);
        list($y, $mo, $dayStr) = explode(':', $date);
        $this->year = (int) $y;
        $this->month = (int) $mo;
        $this->day = (int) $dayStr;
        list($h, $mi, $s) = explode(':', $clock);
        $this->hour = (int) $h;
        $this->minute = (int) $mi;
        $this->second = (int) $s;

        $this->setLastDay();
        $this->dow = (int) date('N', $this->time);
    }

    public function setLastDay(): void
    {
        $last_day = self::$length[$this->month - 1];
        if ($this->month === 2 && ($this->year % 400 === 0 || ($this->year % 4 === 0 && $this->year % 100 !== 0))) {
            $last_day++;
        }
        $this->last_day = $last_day;
    }

    public function getLastDay(): int
    {
        return $this->last_day;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getMonthText(): string
    {
        return date('F', $this->time);
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getHour(): int
    {
        return $this->hour;
    }

    public function getMinute(): int
    {
        return $this->minute;
    }

    public function getSecond(): int
    {
        return $this->second;
    }

    public function getDayOfWeek(): int
    {
        return $this->dow;
    }

    public function getMySQLTime(): string
    {
        return $this->mysql_time;
    }

    public function resetToFirst(): void
    {
        $t = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            $this->month,
            1,
            $this->year
        );
        $this->time = $t !== false ? $t : $this->time;
        $this->init();
    }

    public function resetToLast(): void
    {
        $t = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            $this->month,
            $this->getLastDay(),
            $this->year
        );
        $this->time = $t !== false ? $t : $this->time;
        $this->init();
    }

    public function adjustMonth(int $offset): void
    {
        $t = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            $this->month + $offset,
            $this->day,
            $this->year
        );
        $this->time = $t !== false ? $t : $this->time;
        $this->init();
    }

    public function adjustDay(int $offset): void
    {
        $t = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            $this->month,
            $this->day + $offset,
            $this->year
        );
        $this->time = $t !== false ? $t : $this->time;
        $this->init();
    }

    public function adjustMinute(int $offset): void
    {
        $t = mktime(
            $this->hour,
            $this->minute + $offset,
            $this->second,
            $this->month,
            $this->day,
            $this->year
        );
        $this->time = $t !== false ? $t : $this->time;
        $this->init();
    }

}
