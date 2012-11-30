<?php

namespace Empathy\ELib;

use Empathy\ELib\User\CurrentUser,
    Empathy\MVC\Controller\CustomController;


class EController extends CustomController
{
    public function __construct($boot)
    {
        parent::__construct($boot);
        CurrentUser::detectUser($this);
        $this->assignELibTemplateDir();
    }

    private function assignELibTemplateDir()
    {
        // assuming 'non-system' mode
        $elib_tpl_dirs = array();
        $composer_installed = DOC_ROOT.'/vendor/composer/installed.json';
        if(file_exists($composer_installed)) {
            
            $installed = json_decode(file_get_contents($composer_installed));
            foreach($installed as $i) { 
                if(strpos($i->name, 'elib')) {
                    $elib_tpl_dirs[] = DOC_ROOT.'/vendor/'.$i->name.'/tpl';
                }
            }
            $this->assign('elibtpl_arr', $elib_tpl_dirs);
        } else {   
            $tpl_loc = Util::getLocation().'/tpl';       
            $this->assign('elibtpl', $tpl_loc);
        }
    }
}
