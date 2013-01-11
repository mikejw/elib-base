<?php

namespace Empathy\ELib;

use Empathy\MVC\Model as EmpModel;

class Model extends EmpModel
{
    private static $elib_model_prefix = 'Empathy\ELib\Storage\\';
    private static $app_model_prefix = 'Empathy\MVC\Model\\';


    public static function load($model, $host = null, $connect=true)
    {
        $storage_object = null;
        $file = $model.'.php';
        $app_file = DOC_ROOT.'/storage/'.$file;

        
        if(!file_exists($app_file)) {
            $class = self::$elib_model_prefix.$model;
        } else {

            require_once($app_file);
            $class = self::$app_model_prefix.$model;
        }

        $storage_object = new $class();

        if ($connect) {
            self::connectModel($storage_object, $host);
        }
         
        return $storage_object;
    }

    public static function getTable($model)
    {
        $file = $model.'.php';
        $app_file = DOC_ROOT.'/storage/'.$file;

        if (!file_exists($app_file)) {
            $class = self::$elib_model_prefix.$model;
        } else {
            $class = self::$app_model_prefix.$model;
        }

        return $class::TABLE;
    }

}
