<?php

namespace Empathy\ELib;


class Config
{

    public static function load($config_dir)
    {
        $config_file = $config_dir.'/elib.yml';
        if (!file_exists($config_file)) {
            die('Config error: '.$config_file.' does not exist');
        }

        $config = YAML::load($config_file);
        foreach ($config as $index => $item) {
            if (!is_array($item)) {
                $index = 'ELIB_'.$index;
                if (!defined(strtoupper($index))) {
                    define(strtoupper($index), $item);    
                }
                
            }
        }
    }

}
