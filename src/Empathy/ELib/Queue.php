<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\ELib\Queue\Driver;
use Empathy\ELib\Queue\DriverManager;
use Empathy\ELib\Queue\Job;
use Empathy\ELib\Queue\Stats;

class Queue
{
    public const DEFAULT_DRIVER = 'pheanstalk';

    private Driver $driver;

    private ?string $tube = null;

    public function __construct(string $host, ?string $tube = null, ?string $driver_name = null)
    {
        $this->tube = $tube;
        $driver = DriverManager::load($host, $driver_name);
        if ($driver === null) {
            throw new \RuntimeException('Queue driver could not be loaded');
        }
        $this->driver = $driver;
    }

    public function setTube(string $tube): self
    {
        $this->tube = $tube;

        return $this;
    }

    public function put(mixed $job_data): void
    {
        $j = new Job([$job_data, $this->tube]);
        $this->driver->put($j);
    }

    public static function getStats(): mixed
    {
        return Stats::retrieve('stats');
    }

}
