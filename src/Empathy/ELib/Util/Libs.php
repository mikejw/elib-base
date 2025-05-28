<?php

namespace Empathy\ELib\Util;

use Empathy\ELib\Util as UtilClass;
use Empathy\MVC\Config;
use Empathy\MVC\FileContentsCache;

class Libs
{
    private static $installed_libs = [];
    private static $store_active = false;

    private static function testE($name)
    {
        return (
            strpos($name, 'mikejw/elib-') === 0 ||
            strpos($name, 'mikejw/empathy') === 0
        );
    }

    public static function findAll($doc_root = null) 
    {
        self::$installed_libs = [];
        if ($doc_root === null) {
            $doc_root = Config::get('DOC_ROOT');
        }

        $composer_installed = $doc_root.'/vendor/composer/installed.json';

        if (file_exists($composer_installed)) {
            $installed = FileContentsCache::cachedCallback($composer_installed, function ($data) {
                return json_decode($data);
            });
            
            if (isset($installed->packages)) {
                $installed = $installed->packages;
            }
            foreach ($installed as $i) {
                if (self::testE($i->name)) {
                    $dir = Config::get('DOC_ROOT') . '/vendor/' . $i->name;
                    if (self::$store_active == false && strpos($i->name, 'elib-store') !== false) {
                        self::$store_active = true;
                    }

                    $deps = [];
                    foreach ($i->require as $lib => $version) {
                        if (self::testE($lib)) {
                            $deps[] = $lib;
                        }
                    }

                    self::$installed_libs[] = ['name' => $i->name, 'deps' => $deps, 'score' => 0, 'dir' => $dir];
                }
            }

            foreach (self::$installed_libs as &$lib) {
                if (count($lib['deps']) === 0) {
                    $lib['score'] = 0;
                } elseif ($lib['name'] === 'mikejw/empathy') {
                    $lib['score'] = 1;
                } elseif (in_array('mikejw/empathy', $lib['deps'])) {
                    $lib['score'] = 2;

                } elseif (in_array('mikejw/elib-base', $lib['deps'])) {
                    $lib['score'] = 3;
    
                } else {
                    // @todo 
                    // recursively look for chain of deps that 
                    // stem from elib-base as root dependency
                    // and count each "jump".
                    // if not found 
                    // do the following calculation plus biggest "jump".
                    $lib['score'] = count($lib['deps']) + 3;
                }
            }

            $score = [];
            foreach (self::$installed_libs as $i => $row)
            {
                $score[$i] = $row['score'];
            }
            array_multisort($score, SORT_ASC, self::$installed_libs);
        }
        return array_map(function ($lib) {
            return $lib['dir'];
        }, self::$installed_libs);
    }

    public static function detect()
    {               
        self::findAll();
        return array_reverse(
            array_map(function($lib) {
                return $lib['dir'] . '/tpl/';
            }, self::$installed_libs)
        );
    }

    public static function getInstalled()
    {
        $names = [];
        foreach (self::$installed_libs as $item) {
            $names[] = $item['name'];
        }
        return $names;
    }

    public static function getStoreActive()
    {
        return self::$store_active;
    }

    public static function getMappedLibNames() {

        $mapped = [];
        foreach (self::$installed_libs as $lib)  {
            switch ($lib['name']) {
                case 'mikejw/elib-cms':
                    $mapped['dsection'] = 'CMS';
                    break;
                case 'mikejw/elib-siteinfo':
                    $mapped['settings'] = 'SEO Settings';
                    break;
                case 'mikejw/elib-blog':
                    $mapped['blog'] = 'Blog';
                    break;
                case 'mikejw/elib-events':
                    $mapped['events'] = 'Events';
                    break;
                case 'mikejw/elib-store':
                    $mapped['store'] = 'Store';
                default:
                    break;
            }
        } 
        return $mapped;
    }
}
