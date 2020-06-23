<?php

namespace Empathy\ELib;

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
        if (!file_exists($config_file)) {
            die('Config error: '.$config_file.' does not exist');
        }

        $config = YAML::load($config_file);
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
