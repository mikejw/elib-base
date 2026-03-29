<?php

declare(strict_types=1);

namespace Empathy\ELib\Queue;

abstract class Driver
{
    public const DEF_D = 'pheanstalk';

    protected string $host = '';

    protected string $name = '';

    protected mixed $d = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract public function load(string $h): void;

    abstract public function put(Job $job): void;

    abstract public function getNext(?string $tube): Job;

    abstract public function clear(): void;

    /**
     * @return mixed
     */
    abstract public function info(): mixed;

}
