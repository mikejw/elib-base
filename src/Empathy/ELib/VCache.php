<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\ELib\VCache\DriverManager;

class VCache
{
    public const DEFAULT_DRIVER = 'memcached';

    private $driver;
    private $enabled;
    private $keys;
    private $keys_key;

    public function __construct($host, $port, $driver_name = null, $enabled = true)
    {
        $this->enabled = $enabled;
        $this->driver = DriverManager::load($host, $port, $driver_name);
        $this->keys = null;
        $this->keys_key = 'keys';
    }


    public function clear()
    {
        $this->loadKeys();
        foreach ($this->keys as $index => $key) {

            $this->delete($key);
            unset($this->keys[$index]);
        }
        $this->updateKeys();
    }


    public function delete($key)
    {
        $this->driver->delete($key);
    }


    public function cachedCallback($key, $callback, $callback_params = [], $setOnFail = true, $setOnFalse = false)
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


    public function getAllCacheData()
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


    public function get($key)
    {
        return $this->driver->get($key);
    }

    public function set($key, $value)
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

    private function loadKeys()
    {
        $cached = $this->get($this->keys_key);
        $this->keys = ($cached) ? $cached : [];
    }


    private function updateKeys($key = null)
    {
        if ($key !== null) {
            $this->loadKeys();
            if (!in_array($key, $this->keys, true)) {
                $this->keys[] = $key;
            }
        }

        $this->driver->set($this->keys_key, $this->keys);
    }

    public function init()
    {
        $this->driver->init();
    }
}
