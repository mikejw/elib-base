<?php

declare(strict_types=1);

namespace Empathy\ELib\Queue;

abstract class Driver
{
    public const DEF_D = 'pheanstalk';
    protected $host;
    protected $name;
    protected $d;

    public function __construct($name)
    {
        $this->name = $name;
    }

    abstract public function load($h);

    abstract public function put($job);

    abstract public function getNext($tube);

    abstract public function clear();

    abstract public function info();

}
