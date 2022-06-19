<?php

namespace Empathy\ELib\Queue;

use Empathy\MVC\Config;

class Worker
{
    const DEF_MEM_LIMIT = 10000000;
    const DEF_SLEEP_INTERVAL = 10;

    private $tube;
    private $driver;
    private $memory_limit;
    private $sleep_interval;

    public function __construct($host, $tube = null, $display_log = null,
                                $sleep_interval = null,
                                $memory_limit = null,
                                $driver_name = null)
    {
        $this->tube = $tube;
        $this->display_log = ($display_log === true)? true: false;
        $this->memory_limit = ($memory_limit === null)? self::DEF_MEM_LIMIT: $memory_limit;
        $this->sleep_interval = ($sleep_interval === null)? self::DEF_SLEEP_INTERVAL: $sleep_interval;
        $this->driver = DriverManager::load($host, $driver_name);
    }

    public function setTube($tube)
    {
        $this->tube = $tube;

        return $this;
    }

    public function log($txt)
    {
        if ($this->display_log) {
            echo $txt."\n";
        } else {
            file_put_contents(Config::get('DOC_ROOT').'/logs/worker_'.$this->tube.'.txt', $txt."\n", FILE_APPEND);
        }
    }

    public function nextJob()
    {
        $job = $this->driver->getNext($this->tube);
        $this->updateStats();

        return $job;
    }

    public function removeJob()
    {
        $this->driver->clear();
        $this->checkMemory();
        $this->sleep();
    }

    public function sleep()
    {
        usleep($this->sleep_interval * 1000000);
    }

    public function checkMemory()
    {
        $memory = memory_get_usage();
        if ($memory > $this->memory_limit) {
            exit();
        }
    }

    public function updateStats()
    {
        $stats = $this->driver->info();
        //Stats::store('stats', $stats);
    }

}
