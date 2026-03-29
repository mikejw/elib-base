<?php

declare(strict_types=1);

namespace Empathy\ELib\Storage;

class UserAccess
{
    public const REGULAR = 0;
    public const LOGGED_IN = 1;
    public const ADMIN = 2;
    public const SUPER_ADMIN = 3;

    public function getLevel($name)
    {
        $c = get_class($this);
        $level = @constant($c.'::'.strtoupper($name));
        if ($level === null) {
            throw new \Exception('Use of invalid access level');
        }

        return $level;
    }

}
