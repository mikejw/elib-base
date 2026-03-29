<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\ELib\VCache\Driver;
use Empathy\ELib\VCache\DriverManager;

class VCache
{
    public const DEFAULT_DRIVER = 'memcached';

    private Driver $driver;

    private bool $enabled;

    /** @var list<string> */
    private array $keys = [];

    private string $keys_key = 'keys';

    public function __construct(string $host, int $port, ?string $driver_name = null, bool $enabled = true)
    {
        $this->enabled = $enabled;
        $driver = DriverManager::load($host, $port, $driver_name);
        if ($driver === null) {
            throw new \RuntimeException('VCache driver could not be loaded');
        }
        $this->driver = $driver;
        $this->keys = [];
        $this->keys_key = 'keys';
    }

    public function clear(): void
    {
        $this->loadKeys();
        $toDelete = $this->keys;
        foreach ($toDelete as $key) {
            $this->delete($key);
        }
        $this->keys = [];
        $this->updateKeys();
    }


    public function delete(string $key): void
    {
        $this->driver->delete($key);
    }

    /**
     * @param callable $callback
     * @param list<mixed> $callback_params
     */
    public function cachedCallback(string $key, callable $callback, array $callback_params = [], bool $setOnFail = true, bool $setOnFalse = false): mixed
    {
        $data = false;
        if ($this->enabled && (false !== ($data = $this->get($key)))) {

            // received cached
        } else {
            $data = call_user_func_array($callback, $callback_params);
            if ($setOnFail) {
                if ($data !== false || $setOnFalse) {
                    $this->set($key, $data);
                }
            }
        }
        return $data;
    }


    /**
     * @return array<string, mixed>
     */
    public function getAllCacheData(): array
    {
        $this->loadKeys();
        sort($this->keys);

        $data = [];
        foreach ($this->keys as $key) {
            $item = $this->get($key);
            if (!is_scalar($item)) {
                $item = print_r($item, true);
            }
            $data[$key] = $item;
        }
        return $data;
    }


    public function get(string $key): mixed
    {
        return $this->driver->get($key);
    }

    public function set(string $key, mixed $value): bool
    {
        if (!$this->enabled) {
            return true;
        }

        $success = false;
        try {
            $success = $this->driver->set($key, $value);
        } catch (\Throwable $e) {
            // don't catch here
        }

        $this->updateKeys($key);
        return $success;
    }

    private function loadKeys(): void
    {
        $cached = $this->get($this->keys_key);
        if (is_array($cached)) {
            /** @var list<string> $keys */
            $keys = array_values(array_map(static fn (mixed $k): string => (string) $k, $cached));
            $this->keys = $keys;
        } else {
            $this->keys = [];
        }
    }


    private function updateKeys(?string $key = null): void
    {
        if ($key !== null) {
            $this->loadKeys();
            if (!in_array($key, $this->keys, true)) {
                $this->keys[] = $key;
            }
        }

        $this->driver->set($this->keys_key, $this->keys);
    }

    public function init(): void
    {
        $this->driver->init();
    }
}
