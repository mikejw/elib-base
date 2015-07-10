<?php

namespace Empathy\ELib;

use Empathy\ELib\User\CurrentUser;
use Empathy\MVC\Controller\CustomController;


class EController extends CustomController
{

    public function __construct($boot)
    {
        parent::__construct($boot);

        CurrentUser::detectUser($this);
        $elib_tpl_dirs = Util\Libs::detetct();
        if (sizeof($elib_tpl_dirs) > 1) {
            $this->assign('elibtpl_arr', $elib_tpl_dirs);
        } else {
            $this->assign('elibtpl', $elib_tpl_dirs[0]);
        }
        if (Util\Libs::getStoreActive()) {
            CurrentUser::detectUser($this, true);
        }
    }
}
