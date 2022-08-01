<?php

namespace Empathy\ELib;
use Empathy\MVC\FileContentsCache;
use Empathy\MVC\DI;


/*
 * Modified to have the same "static interface"
 * as Empathy\MVC\Config
 */
class Config
{
    /**
     * Initialise empty config;
     */
    private static $items = array();

    /**
     * Return a piece of config.
     *
     * @param string $key The config key.
     * @return string Config.
     */
    public static function get($key)
    {
        if (!isset(self::$items[$key])) {
            return false;
        } else {
            return self::$items[$key];
        }
    }

    /**
     * Store some config.
     *
     * @param string $key The config key.
     * @param mixed Data to store against key.
     * @return null
     */
    public static function store($key, $data)
    {
        self::$items[$key] = $data;
    }

    /**
     * Simple dump of config.
     * @return null
     */
    public static function dump()
    {
        print_r(self::$items);
    }


    public static function load($config_dir)
    {
        $config_file = $config_dir.'/elib.yml';
        
        $config = FileContentsCache::cachedCallback($config_file, function($data) {
            return DI::getContainer()->get('Spyc')->YamlLoadString($data);
        });

        foreach ($config as $index => $item) {
            if (!is_array($item)) {
                self::store(strtoupper($index), $item);
                $index = 'ELIB_'.$index;
                if (!defined(strtoupper($index))) {
                    define(strtoupper($index), $item);    
                }
            }
        }
    }

}
