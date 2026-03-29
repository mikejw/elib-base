<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\MVC\Controller\CustomController;
use Empathy\MVC\DI;

class EController extends CustomController
{
    protected $elib_tpl_dirs;

    public function __construct($boot)
    {
        parent::__construct($boot);

        DI::getContainer()->get('CurrentUser')->detectUser($this);
        $this->elib_tpl_dirs = Util\Libs::detect();

        if (isset($this->elib_tpl_dirs) && sizeof($this->elib_tpl_dirs) > 1) {
            $this->assign('elibtpl_arr', $this->elib_tpl_dirs);
        } elseif (count($this->elib_tpl_dirs) === 1) {
            $this->assign('elibtpl', $this->elib_tpl_dirs[0]);
        }

        $this->assign('installed_libs', Util\Libs::getMappedLibNames());
    }
}
