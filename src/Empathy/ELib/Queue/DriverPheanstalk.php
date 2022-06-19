<?php

namespace Empathy\ELib\Queue;

use Empathy\ELib\Queue\Job;

class DriverPheanstalk extends Driver
{
    protected static $job;

    public function load($h)
    {
        $this->d = new \Pheanstalk\Pheanstalk($h);
    }

    public function put($job)
    {
        $tube = $job->getTube();
        if ($tube === null) {
            $tube = 'default';
        }
        $this->d->useTube($tube)->put($job->getSerialized());
    }

    public function getNext($tube)
    {
        self::$job = $this->d->watch($tube)->ignore('default')->reserve();
        $j = new Job(array(self::$job->getData()));

        return $j;
    }

    public function clear()
    {
        $this->d->delete(self::$job);
    }

    public function info()
    {
        return $this->d->stats();
    }

}
