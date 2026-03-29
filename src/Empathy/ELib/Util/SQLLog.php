<?php

declare(strict_types=1);

namespace Empathy\ELib\Util;

use Empathy\ELib\YAML;
use Empathy\MVC\Config;

class SQLLog
{
    public static function log(mixed $data): void
    {
        $queries = YAML::load(Config::get('DOC_ROOT').'/logs/sql_log');
        $queries[] = $data;
        YAML::save($queries, Config::get('DOC_ROOT').'/logs/sql_log');
    }

}
