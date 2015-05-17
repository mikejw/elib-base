<?php

namespace Empathy\ELib;

use Empathy\ELib\User\CurrentUser;
use Empathy\MVC\Controller\CustomController;
use Empathy\MVC\Config as EmpConfig;


class EController extends CustomController
{
    protected $elib_tpl_dirs = array();


    public function __construct($boot)
    {        
        parent::__construct($boot);
        CurrentUser::detectUser($this);
        $this->assignELibTemplateDir();
    }

    private function assignELibTemplateDir()
    {
        // assuming 'non-system' mode
        $this->elib_tpl_dirs = array();
        $composer_installed = EmpConfig::get('DOC_ROOT').'/vendor/composer/installed.json';
        if(file_exists($composer_installed)) {
            
            $installed = json_decode(file_get_contents($composer_installed));
            foreach($installed as $i) { 
                if(strpos($i->name, 'mikejw/elib') === 0) {
                    $this->elib_tpl_dirs[] = EmpConfig::get('DOC_ROOT').'/vendor/'.$i->name.'/tpl';
                }
            }
            $this->assign('elibtpl_arr', $this->elib_tpl_dirs);
        } else {   
            $tpl_loc = Util::getLocation().'/tpl';       
            $this->assign('elibtpl', $tpl_loc);
        }
    }
}
