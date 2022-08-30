<?php

namespace Empathy\ELib;

use Empathy\ELib\Model;
use Empathy\MVC\Session;
use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\DI;

class AdminController extends EController
{
    public function __construct($boot, $assertAdmin = true)
    {
        parent::__construct($boot);
        if ($assertAdmin) {
            DI::getContainer()->get('CurrentUser')->assertAdmin($this);    
        }
        
        $this->detectHelp();
    }


    private function tplInLib($help_file) {
        $exists = false;
        $i = 0;
        while(!$exists && $i < sizeof($this->elib_tpl_dirs)) {
            $file = $this->elib_tpl_dirs[$i].'/'.$help_file;
            if(file_exists($file)) {
                $exists = true;
            }
            $i++;
        }
        return $exists;
    }


    protected function findHelp()
    {
        $help_file = 'admin_help/'.$this->class.'_'.$this->event.'.tpl';
        if (
            file_exists(
                EmpConfig::get('DOC_ROOT').'/presentation/'.$help_file
            )
            || $this->tplInLib($help_file)
        ) {
            return $help_file;
        }
        $help_file = 'admin_help/'.$this->class.'.tpl';
        if (
            file_exists(
                EmpConfig::get('DOC_ROOT').'/presentation/'.$help_file
            )
            || $this->tplInLib($help_file)
        ) {
            return $help_file;
        }
        return false;
    }


    protected function detectHelp()
    {
        $help_file = $this->findHelp();
        if ($help_file) {
            $this->presenter->assign('help_file', 'elib:/'.$help_file);
        }
    }

    public function default_event()
    {
        $this->setTemplate('elib:/admin/admin.tpl');
    }

    public function store()
    {
        $this->setTemplate('elib:/admin/store.tpl');
    }

    public function password()
    {
        $this->currentUser = DI::getContainer()->get('CurrentUser');
        $this->setTemplate('elib:/admin/password.tpl');
        if (isset($_POST['submit'])) {
            $errors = $this->currentUser->doChangePassword(
                $_POST['old_password'],
                $_POST['password1'],
                $_POST['password2']
            );

            if (sizeof($errors) < 1) {
                $this->redirect('admin');
            } else {
                $this->presenter->assign('errors', $errors);
            }
        } elseif (isset($_POST['cancel'])) {
            $this->redirect('admin');
        }
    }

    public function toggle_help()
    {
        if($this->isXMLHttpRequest()) {
            $help_shown = Session::get('help_shown');
            if ($help_shown) {
                Session::set('help_shown', false);
            } else {
                Session::set('help_shown', true);
            }           
            header('Content-type: application/json');
            echo json_encode(1);
            exit();
        }
    }

}
