<?php

declare(strict_types=1);

namespace Empathy\ELib;

class Util
{
    /**
     * @return string|false
     */
    public static function getLocation(): string|false
    {
        //return dirname(__FILE__);


        // fix for composer - how to deal with 'system install' templates?
        return realpath(__DIR__.'/../../../');
    }
}
