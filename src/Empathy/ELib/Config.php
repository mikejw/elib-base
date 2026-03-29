<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\MVC\DI;
use Empathy\MVC\FileContentsCache;

/*
 * Modified to have the same "static interface"
 * as Empathy\MVC\Config
 */
class Config
{
    /**
     * Initialise empty config;
     */
    private static $items = [];

    /**
     * Return a piece of config.
     *
     * @param string $key The config key.
     * @return mixed Config value, or false when the key is not set.
     */
    public static function get($key): mixed
    {
        if (!isset(self::$items[$key])) {
            return false;
        }

        return self::$items[$key];
    }

    /**
     * Store some config.
     *
     * @param string $key The config key.
     * @param mixed $data Data to store against key.
     */
    public static function store($key, $data): void
    {
        self::$items[$key] = $data;
    }

    /**
     * Simple dump of config.
     */
    public static function dump(): void
    {
        print_r(self::$items);
    }


    public static function load($config_dir)
    {
        $config_file = $config_dir.'/elib.yml';

        $config = FileContentsCache::cachedCallback($config_file, function ($data) {
            return DI::getContainer()->get('Spyc')->YamlLoadString($data);
        });

        foreach ($config as $index => $item) {
            self::store(strtoupper($index), $item);
            $index = 'ELIB_'.$index;
            if (!defined(strtoupper($index))) {
                define(strtoupper($index), $item);
            }
        }
    }

}
