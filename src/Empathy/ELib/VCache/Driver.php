<?php

declare(strict_types=1);

namespace Empathy\ELib\VCache;

abstract class Driver
{
    public const DEF_D = 'memcached';

    protected string $host = '';

    protected int $port = 0;

    protected string $name = '';

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract public function load(string $h, int $p): void;

    abstract public function get(string $key): mixed;

    abstract public function set(string $key, mixed $value): bool;

    abstract public function delete(string $key): mixed;

    abstract public function init(): void;


    /*
      abstract public function put($job);

      abstract public function getNext($tube);

      abstract public function clear();

      abstract public function info();
    */

}
