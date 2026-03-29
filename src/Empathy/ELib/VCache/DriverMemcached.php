<?php

declare(strict_types=1);

namespace Empathy\ELib\VCache;

class DriverMemcached extends Driver
{
    private \Memcached $m;

    public function load(string $h, int $p): void
    {
        $this->m = new \Memcached();
        $this->m->setOption(\Memcached::OPT_COMPRESSION, false);
        $this->m->setOption(\Memcached::OPT_PREFIX_KEY, 'default');
        $this->m->addServer($h, $p);
    }

    public function set(string $key, mixed $value, int $timeout = 0): bool
    {
        $ok = $this->m->set($key, $value, $timeout);
        $this->m->set($key . '_json', json_encode($value), $timeout);

        return $ok;
    }

    public function get(string $key): mixed
    {
        return $this->m->get($key);
    }

    public function delete(string $key): mixed
    {
        return $this->m->delete($key);
    }

    public function init(): void
    {
    }
}
