<?php

namespace Empathy\ELib\VCache;

class DriverMemcached extends Driver
{
    private $m;

    public function load($h, $p)
    {
        $this->m = new \Memcached();
        $this->m->setOption(\Memcached::OPT_COMPRESSION, false);
        $this->m->setOption(\Memcached::OPT_PREFIX_KEY, 'default');
        $this->m->addServer($h, $p);
    }

    public function set($key, $value, $timeout=0)
    {
        $this->m->set($key, $value, $timeout);
        $this->m->set($key . '_json', json_encode($value), $timeout);
    }

    public function get($key)
    {
        return $this->m->get($key);
    }

    public function delete($key)
    {
        return $this->m->delete($key);
    }
}
