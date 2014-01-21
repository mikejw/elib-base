<?php

namespace Empathy\ELib;

use Empathy\MVC\Model as EmpModel;

class Model extends EmpModel
{
    private static $elib_model_prefix = 'Empathy\ELib\Storage\\';
    private static $app_model_prefix = 'Empathy\MVC\Model\\';

    
    public static function load($model, $id=null, $params=null, $host=null)
    {
        if($params === null || !is_array($params)) {
            $params = array();
        }

        $storage_object = null;
        $file = $model.'.php';
        $app_file = DOC_ROOT.'/storage/'.$file;

        
        if(!file_exists($app_file)) {
            $class = self::$elib_model_prefix.$model;
        } else {

            require_once($app_file);
            $class = self::$app_model_prefix.$model;
        }

        $reflect  = new \ReflectionClass($class);
        $storage_object = $reflect->newInstanceArgs($params);

         if(get_parent_class($storage_object) == 'Empathy\MVC\Entity') {
            self::connectModel($storage_object, $host);
            $storage_object->init();
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
