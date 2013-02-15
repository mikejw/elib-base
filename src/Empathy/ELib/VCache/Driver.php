<?php

namespace Empathy\ELib\VCache;

abstract class Driver
{
    const DEF_D = 'memcached';
    protected $host;
    protected $port;
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    abstract public function load($h, $p);

    abstract public function get($key);

    abstract public function set($key, $value);

    abstract public function delete($key);


    /*
      abstract public function put($job);

      abstract public function getNext($tube);

      abstract public function clear();

      abstract public function info();
    */

}
