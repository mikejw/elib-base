<?php

namespace Empathy\ELib\Util;

class Libs
{
    private static $installed_libs;
    private static $store_active = false;

    public static function detect()
    {        
        $tpl_dirs = array();
        $composer_installed = DOC_ROOT.'/vendor/composer/installed.json';
        if(file_exists($composer_installed)) {
            $installed = json_decode(file_get_contents($composer_installed));
            foreach($installed as $i) { 
                if(strpos($i->name, 'mikejw/elib') === 0) {
                    $tpl_dirs[] = DOC_ROOT.'/vendor/'.$i->name.'/tpl';
                    if (self::$store_active == false && strpos($i->name, 'elib-store') !== false) {
                        self::$store_active = true;
                    }
                    self::$installed_libs[] = $i->name;
                }
            }
        } else {
            // support for older monolithic 'system mode' elib directory
            $tpl_dirs[] = Empathy\ELib\Util::getLocation().'/tpl';            
        }
        return $tpl_dirs;
    }


    public static function getInstalled()
    {
        return self::$installed_libs;
    }

    public static function getStoreActive()
    {
        return self::$store_active;
    }
}
