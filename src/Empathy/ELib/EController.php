<?php

namespace Empathy\ELib;

use Empathy\ELib\User\CurrentUser;
use Empathy\MVC\Controller\CustomController;


class EController extends CustomController
{
    protected $elib_tpl_dirs = array();
    private $store_active = false;


    public function __construct($boot)
    {
        parent::__construct($boot);
        $this->assignELibTemplateDir();
        if ($this->store_active) {
            CurrentUser::detectUser($this, $this->store_active);
        }
    }

    private function assignELibTemplateDir()
    {
        // assuming 'non-system' mode
        $this->elib_tpl_dirs = array();
        $composer_installed = DOC_ROOT.'/vendor/composer/installed.json';
        if(file_exists($composer_installed)) {
            
            $installed = json_decode(file_get_contents($composer_installed));
            foreach($installed as $i) { 
                if(strpos($i->name, 'mikejw/elib') === 0) {
                    $this->elib_tpl_dirs[] = DOC_ROOT.'/vendor/'.$i->name.'/tpl';

                    if ($this->store_active == false && strpos($i->name, 'elib-store') !== false) {
                        $this->store_active = true;
                    }
                }
            }
            $this->assign('elibtpl_arr', $this->elib_tpl_dirs);
        } else {   
            $tpl_loc = Util::getLocation().'/tpl';       
            $this->assign('elibtpl', $tpl_loc);
        }
    }
}
