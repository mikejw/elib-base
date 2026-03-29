<?php

declare(strict_types=1);

namespace Empathy\ELib\Queue;

class DriverPheanstalk extends Driver
{
    protected static ?object $job = null;

    public function load(string $h): void
    {
        $this->d = new \Pheanstalk\Pheanstalk($h);
    }

    public function put(Job $job): void
    {
        $tube = $job->getTube();
        if ($tube === null) {
            $tube = 'default';
        }
        $this->d->useTube($tube)->put($job->getSerialized());
    }

    public function getNext(?string $tube): Job
    {
        if (!$this->d instanceof \Pheanstalk\Pheanstalk) {
            throw new \RuntimeException('Pheanstalk driver not initialised');
        }
        $tubeName = $tube ?? 'default';
        $reserved = $this->d->watch($tubeName)->ignore('default')->reserve();
        self::$job = $reserved;
        $j = new Job([$reserved->getData()]);

        return $j;
    }

    public function clear(): void
    {
        $this->d->delete(self::$job);
    }

    public function info(): mixed
    {
        return $this->d->stats();
    }

}
