<?php

declare(strict_types=1);

namespace Empathy\ELib\Queue;

use Empathy\MVC\Config;

class Worker
{
    public const DEF_MEM_LIMIT = 10000000;
    public const DEF_SLEEP_INTERVAL = 10;

    private ?string $tube = null;

    private Driver $driver;

    private int $memory_limit;

    private int $sleep_interval;

    private bool $display_log;

    public function __construct(
        string $host,
        ?string $tube = null,
        ?bool $display_log = null,
        ?int $sleep_interval = null,
        ?int $memory_limit = null,
        ?string $driver_name = null
    ) {
        $this->tube = $tube;
        $this->display_log = ($display_log === true) ? true : false;
        $this->memory_limit = ($memory_limit === null) ? self::DEF_MEM_LIMIT : $memory_limit;
        $this->sleep_interval = ($sleep_interval === null) ? self::DEF_SLEEP_INTERVAL : $sleep_interval;
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

    public function log(string $txt): void
    {
        if ($this->display_log) {
            echo $txt."\n";
        } else {
            file_put_contents(Config::get('DOC_ROOT').'/logs/worker_'.$this->tube.'.txt', $txt."\n", FILE_APPEND);
        }
    }

    public function nextJob(): Job
    {
        $job = $this->driver->getNext($this->tube);
        $this->updateStats();

        return $job;
    }

    public function removeJob(): void
    {
        $this->driver->clear();
        $this->checkMemory();
        $this->sleep();
    }

    public function sleep(): void
    {
        usleep($this->sleep_interval * 1000000);
    }

    public function checkMemory(): void
    {
        $memory = memory_get_usage();
        if ($memory > $this->memory_limit) {
            exit();
        }
    }

    public function updateStats(): void
    {
        $stats = $this->driver->info();
        //Stats::store('stats', $stats);
    }

}
