<?php

declare(strict_types=1);

namespace Empathy\ELib;

class DateTime
{
    private $time;
    private $mysql_time;
    private $day;
    private $month;
    private $year;

    private $hour;
    private $minute;
    private $second;
    private $last_day;
    private $dow;
    private $valid;

    private static $length = [31, 28, 31, 30, 31, 30,
                                   31, 31, 30, 31, 30, 31];

    public function __construct($time = [], $do_init = true)
    {
        if (sizeof($time) === 0) {
            $this->time = time();
        } elseif (sizeof($time) === 1) {
            $this->time = $time[0];
        } else {
            $this->valid = checkdate($time['month'], $time['day'], $time['year']);
            $this->time = mktime(
                $time['hour'],
                $time['minute'],
                $time['second'],
                $time['month'],
                $time['day'],
                $time['year']
            );
        }

        if ($do_init) {
            $this->init();
        }
    }

    public function getValid()
    {
        return $this->valid;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function init()
    {
        $this->mysql_time = date('Y:m:d H:i:s', $this->time);
        list($date, $time) = explode(' ', $this->mysql_time);
        list($this->year, $this->month, $this->day) = explode(':', $date);
        list($this->hour, $this->minute, $this->second) = explode(':', $time);

        $this->day = (int) $this->day;

        $this->setLastDay();
        $this->dow = date('N', $this->time);
    }

    public function setLastDay()
    {
        $last_day = self::$length[$this->month - 1];
        if ($this->month === 2 && ($this->year % 400 === 0 || ($this->year % 4 === 0 && $this->year % 100 !== 0))) {
            $last_day++;
        }
        $this->last_day = $last_day;
    }

    public function getLastDay()
    {
        return $this->last_day;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function getMonthText()
    {
        return date('F', $this->time);
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getHour()
    {
        return $this->hour;
    }

    public function getMinute()
    {
        return $this->minute;
    }

    public function getSecond()
    {
        return $this->second;
    }

    public function getDayOfWeek()
    {
        return $this->dow;
    }

    public function getMySQLTime()
    {
        return $this->mysql_time;
    }

    public function resetToFirst()
    {
        $this->time = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            $this->month,
            1,
            $this->year
        );
        $this->init();
    }

    public function resetToLast()
    {
        $this->time = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            $this->month,
            $this->getLastDay(),
            $this->year
        );
        $this->init();
    }

    public function adjustMonth($offset)
    {
        $this->time = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            ($this->month + $offset),
            $this->day,
            $this->year
        );
        $this->init();
    }

    public function adjustDay($offset)
    {
        $this->time = mktime(
            $this->hour,
            $this->minute,
            $this->second,
            $this->month,
            ($this->day + $offset),
            $this->year
        );
        $this->init();
    }

    public function adjustMinute($offset)
    {
        $this->time = mktime(
            $this->hour,
            $this->minute + $offset,
            $this->second,
            $this->month,
            ($this->day),
            $this->year
        );
        $this->init();
    }

}
