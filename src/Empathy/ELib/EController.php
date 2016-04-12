<?php

namespace Empathy\ELib;

use Empathy\ELib\User\CurrentUser;
use Empathy\MVC\Controller\CustomController;
use Empathy\MVC\Config;


class EController extends CustomController
{
    protected $elib_tpl_dirs;

    public function __construct($boot)
    {        
        parent::__construct($boot);


        CurrentUser::detectUser($this);
        $this->elib_tpl_dirs = Util\Libs::detect();
        if (sizeof($this->elib_tpl_dirs) > 1) {
            $this->assign('elibtpl_arr', $this->elib_tpl_dirs);
        } else {
            $this->assign('elibtpl', $this->elib_tpl_dirs[0]);
        }

        if (Config::get('SUBDOMAIN')) {

            $this->assign('elibtplsub', Config::get('SUBDOMAIN'));
        }

        if (Util\Libs::getStoreActive()) {
            CurrentUser::detectUser($this, true);
        }
    }
}
