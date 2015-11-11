<?php

namespace Empathy\ELib\Util;

use Empathy\ELib\YAML;
use Empathy\MVC\Config;

class SQLLog
{
    public static function log($data)
    {
        $queries = YAML::load(Config::get('DOC_ROOT').'/logs/sql_log');
        $queries[] = $data;
        YAML::save($queries, Config::get('DOC_ROOT').'/logs/sql_log');
    }

}
