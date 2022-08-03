<?php

namespace Empathy\ELib\User;


use Empathy\ELib\EController;
use Empathy\ELib\Model;
use Empathy\ELib\Country\Country;
use Empathy\ELib\Mailer;
use Empathy\MVC\Session;
use Empathy\MVC\Config;
use Empathy\ELib\Config as ELibConfig;
use Empathy\MVC\DI;


class Controller extends EController
{
    private $currentUser;

    public function __construct($boot) 
    {
        parent::__construct($boot);
        $this->currentUser = DI::getContainer()->get('CurrentUser');
    }

    public function default_event()
    {
        $this->redirect('');
    }


    public function login()
    {
        $this->assign('centerpage', true);
        $this->setTemplate('elib:/login.tpl');
        $errors = array();

        if (isset($_POST['login'])) {
            list($errors, $user) = $this->currentUser->doLogin($_POST['username'], $_POST['password']);

            if (!sizeof($errors)) {
                $ua = Model::load('UserAccess');
                if (!($user->auth < $ua->getLevel('admin'))) {
                    $this->redirect('admin');
                } else {
                    $this->redirect('');
                }    
            } else {
                $this->presenter->assign('errors', $user->getValErrors());
                $this->presenter->assign("username", $_POST['username']);
                $this->presenter->assign("password", $_POST['password']);    
            }
        }
    }

    public function logout()
    {
        if (!$this->currentUser->doLogout()) {
            throw new \Exception('Could not logout');
        } else {
            $this->redirect('');
            return false;    
        }
    }

    private function nullify(&$var) {
        if (isset($var) && $var === '') {
            $var = null;
        }
    }

    public function register()
    {   
        $errors = array();
        $submitted = false;

        if (isset($_POST['submit'])) {
            $this->nullify($_POST['first_name']);
            $this->nullify($_POST['last_name']);
            $_POST['first_name'] = $_POST['first_name'] ?? 'Not provided';
            $_POST['last_name'] = $_POST['last_name'] ?? 'Not provided';

            $submitted = true;                
            list($errors, $user, $address) = $this->currentUser->doRegister(
                $_POST['supply_address'],
                $_POST['username'],
                $_POST['email'],
                $_POST['fullname'] ?? '',
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['address1'],
                $_POST['address2'],
                $_POST['city'],
                $_POST['state'],
                $_POST['zip'],
                $_POST['country']
            );            

            if (!sizeof($errors)) {
               $this->redirect('user/thanks/1'); 
            } else {
                $address->first_name = (
                    isset($address->first_name) && 
                    $address->first_name === 'Not provided'
                ) ? '' : $address->first_name ?? '';
                $address->last_name = (
                    isset($address->last_name) && 
                    $address->last_name === 'Not provided'
                ) ? '' : $address->last_name ?? '';

                $this->assign('user', $user);
                $this->assign('address', $address);       
            }
        }

        $titles = array('Mr', 'Mrs', 'Miss', 'Ms', 'Dr');
        $countries = Country::build();
        $this->assign('errors', $errors);
        $this->assign('titles', $titles);
        $this->assign('countries', $countries);
        $this->assign('sc', 'GB');
        $this->setTemplate('elib://register.tpl');
        $this->assign('submitted', $submitted);
    }

    public function confirm_reg()
    {
        $reg_code = $_GET['code'];
        if ($this->currentUser->doConfirmReg($reg_code)) {
            $this->redirect('user/thanks/2');
        } else {
            throw new \Exception('Unable to activate user.');
        }
    }

    public function thanks()
    {
        $this->presenter->assign('id', $_GET['id']);
        $this->setTemplate('elib://thanks.tpl');
    }

}
