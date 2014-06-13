<?php

namespace Empathy\ELib;

use Empathy\MVC\Model as EmpModel;

class Model extends EmpModel
{
    private static $elib_model_prefix = 'Empathy\ELib\Storage\\';
    private static $app_model_prefix = 'Empathy\MVC\Model\\';


    private static function getClass($model)
    {
        $file = $model.'.php';
        $app_file = DOC_ROOT.'/storage/'.$file;

        if (!file_exists($app_file)) {
            $class = self::$elib_model_prefix.$model;
        } else {
            require_once($app_file);
            $class = self::$app_model_prefix.$model;
        }
        return $class;
    }

    
    public static function load($model, $id=null, $params=array(), $host=null)
    {
        $storage_object = null;
        
        $reflect  = new \ReflectionClass(self::getClass($model));

        if(sizeof($params)) {
            $storage_object = $reflect->newInstanceArgs($params);
        } else {
            $storage_object = $reflect->newInstanceArgs();
        }

        if(in_array('Empathy\MVC\Entity', class_parents($storage_object))) {
            self::connectModel($storage_object, $host);
            $storage_object->init();
        }
         
        return $storage_object;
    }


    public static function getTable($model)
    {
        $class = self::getClass($model); 
        return $class::TABLE;
    }
}
