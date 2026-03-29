<?php

declare(strict_types=1);

namespace Empathy\ELib;

class Util
{
    public static function getLocation()
    {
        //return dirname(__FILE__);


        // fix for composer - how to deal with 'system install' templates?
        return realpath(dirname(realpath(__FILE__)).'/../../../');
    }
}
