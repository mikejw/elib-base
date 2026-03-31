<?php

declare(strict_types=1);

namespace Empathy\ELib\Gen;

class Admin extends \Empathy\MVC\Util\ControllerGen
{
    protected string $name = 'admin';
    protected string $module = 'admin';
    protected string $parent = '\Empathy\ELib\AdminController';
}
