<?php

namespace Empathy\ELib\VCache;

class DriverMemcached extends Driver
{

    private $m;

    public function load($h, $p)
    {
        $this->m = new \Memcached();
        $this->m->setOption(\Memcached::OPT_COMPRESSION, true);
        $this->m->setOption(\Memcached::OPT_PREFIX_KEY, 'default');

        $this->m->addServer($h, $p);

    }

    public function get($key)
    {
        return $this->m->get($key);
    }

    public function set($key, $value, $timeout=0)
    {
        $this->m->set($key, $value, $timeout);

    }

    public function delete($key)
    {
        return $this->m->delete($key);
    }

    /*

      public function init()
      {
      echo 'new caching object.. ready to party.';
      }
    */

}
