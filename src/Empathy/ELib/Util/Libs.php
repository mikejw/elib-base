<?php

namespace Empathy\ELib\Util;

use Empathy\ELib\Util as UtilClass;
use Empathy\MVC\Config;

class Libs
{
    private static $installed_libs;
    private static $store_active = false;

    public static function detect()
    {                
        if (Config::get('DOC_ROOT') === false) {
            die("Initialise new app (directory structure) before running this command.\n");
        }

        $tpl_dirs = array();
        $composer_installed = Config::get('DOC_ROOT').'/vendor/composer/installed.json';
        if(file_exists($composer_installed)) {
            $installed = json_decode(file_get_contents($composer_installed));
            foreach($installed as $i) { 
                if(strpos($i->name, 'mikejw/elib') === 0) {
                    $tpl_dirs[] = Config::get('DOC_ROOT').'/vendor/'.$i->name.'/tpl';
                    if (self::$store_active == false && strpos($i->name, 'elib-store') !== false) {
                        self::$store_active = true;
                    }
                    self::$installed_libs[] = $i->name;
                }
            }
        } else {
            // support for older monolithic 'system mode' elib directory
            $tpl_dirs[] = UtilClass::getLocation().'/tpl';            
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


    public static function getMappedLibNames() {

        $mapped = array();
        foreach (self::$installed_libs as $lib)  {
            switch ($lib) {
                case 'mikejw/elib-cms':#
                    $mapped['dsection'] = 'Generic Sections';
                    break;
                case 'mikejw/elib-siteinfo':
                    $mapped['settings'] = 'Settings';
                    break;
                case 'mikejw/elib-blog':
                    $mapped['blog'] = 'Blog';
                    break;
                default:
                    break;
            }
        } 
        return $mapped;
    }


}
