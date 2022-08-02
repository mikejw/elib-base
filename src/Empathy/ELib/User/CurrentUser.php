<?php

namespace Empathy\ELib\User;

use Empathy\ELib\Model;
use Empathy\MVC\Session;
use Empathy\MVC\DI;
use Empathy\ELib\Config as ELibConfig;
use Empathy\MVC\Config;


class CurrentUser
{
    private $u;
    private $user_id;


    // must return true
    // to carry on with default behaviour
    protected function loginSuccess($u)
    {
        return true;
    }

    protected function logoutSuccess($u)
    {
        return true; 
    }

    protected function postRegister($u)
    {
        return true;
    }

    public function detectUser($c = NULL, $store_active = false, $user_id = null)
    {
        if ($user_id === null) {
            $user_id = Session::get('user_id');
        }

        $this->u = Model::load('UserItem');
        $this->user_id = $user_id;

        if (is_numeric($this->user_id) && $this->user_id > 0) {
            $this->u->id = $this->user_id;
            $this->u->load();

            if($c !== null) {
                $c->assign('current_user', $this->u->username);
                $c->assign('user_id', $this->u->id);
            }

            if ($store_active) {
                $c->assign('user_is_vendor', ($this->u->auth == \Empathy\ELib\Store\Access::VENDOR));
            }
        }
    }

    public function assertAdmin($c)
    {        
        $ua = Model::load('UserAccess');
        if ($this->u->id < 1 || $this->u->getAuth($this->u->id) < $ua->getLevel('admin')) {
            Session::down();
            $c->redirect("user/login");
        }
    }

    public function getUserID()
    {
        return $this->u->id;
    }

    public function loggedIn()
    {
        return ($this->getUserID() > 0);
    }

    public function getProfileID()
    {
        return $this->u->user_profile_id;
    }

    public function getUser()
    {
        return $this->u;
    }

    public static function isAuthLevel($level)
    {
        return ($this->u->auth >= $level);
    }

    public static function setUserID($id)
    {
        $this->u->id = $id;
    }

    public function doLogin($username, $password, $initSession = true)
    {
        $user = Model::load('UserItem');
        $user->username = $username;
        $user->password = $password;

        $user->validateLogin();
        $errors = array();

        if (!$user->hasValErrors()) {
            $user_id = $user->login();
            if ($user_id > 0) {

                if ($initSession) {
                    session_regenerate_id();
                    Session::set('user_id', $user_id);                    
                }
                $user->id = $user_id;
                $user->load();

                if (!$this->loginSuccess($user)) {
                    throw new \Exception('Could not process post login');
                } else {
                    $this->u = $user;
                }
            } else {
                $user->addValError('Wrong username/password combination.', 'success');
            }
        }

        if ($user->hasValErrors() || $user_id < 1) {
            $errors = $user->getValErrors();
        }
        return array($errors, $user);
    }

    public function doLogout()
    {
        $user = $this->getUser();
        Session::down();
        if ($this->logoutSuccess($user)) {
            return true;
        } else {
            return false;
        }
    }


    public function doRegister(
        $supply_address,
        $username,
        $email,
        $fullname,
        $first_name,
        $last_name,
        $address1,
        $address2,
        $city,
        $state,
        $zip,
        $country
    ) {
        $u = Model::load('UserItem');
        $u->username = $username;
        $u->email = $email;
        $u->validates();
        $errors = array();

        $p = Model::load('UserProfile');

        $supply_address = (isset($supply_address) && $supply_address == 1) ? 1 : 0;

        if ($supply_address == 1) {
            $s = Model::load('ShippingAddress');
            if ($p->fullname != '') {
                $fullname_arr = explode(' ', $p->fullname);
                if (sizeof($fullname_arr) > 1) {
                    $s->last_name = $fullname_arr[sizeof($fullname_arr) - 1];
                    array_pop($fullname_arr);
                    $s->first_name = implode(' ', $fullname_arr);
                }
            } else {
                $s->first_name = $first_name;
                $s->last_name = $last_name;
                $p->fullname = $s->first_name.' '.$s->last_name;
            }

            $s->address1 = $address1;
            $s->address2 = $address2;
            $s->city = $city;
            $s->state = $state;
            $s->zip = strtoupper($zip);
            $s->country = $country;
            $s->default_address = 1;
            $s->validates();
        } else {
            $p->fullname = $first_name.' '.$last_name;
        }

        $p->validates();

        if ($u->hasValErrors() || $p->hasValErrors() || (isset($s) && $s->hasValErrors())) {

            $errors = array_merge($u->getValErrors(), $p->getValErrors());
            if (isset($s)) {
                $errors = array_merge($errors, $s->getValErrors());
            }

        } else {
            $password = exec(MAKEPASSWD . ' --chars=8');
            $reg_code = exec(MAKEPASSWD . ' --chars=16');
            $u->password = $password;
            $u->reg_code = md5($reg_code);
            $u->auth = 0;
            $u->active = 0;
            $u->registered = 'MYSQLTIME';
            $u->popups = 'DEFAULT';
            $u->user_profile_id = $p->insert(Model::getTable('UserProfile'), 1, array(), 0);
            $u->id = $u->insert(Model::getTable('UserItem'), 1, array(), 0);

            if (isset($s)) {
                $s->user_id = $u->id;
                $s->insert(Model::getTable('ShippingAddress'), 1, array(), 0);

                // vendor stuff disabled - see elib-store for more
                // $v = Model::load('Vendor');
                // $v->user_id = $s->user_id;
                // $v->insert(Model::getTable('Vendor'), 1, array(), 0);
            }

            if ($this->postRegister($u)) {
                if (
                    ELibConfig::get('EMAIL_ORGANISATION') &&
                    ELibConfig::get('EMAIL_FROM')
                ) {
                    $_POST['body'] = "\nHi ___,\n\n"
                        . "Thanks for registering with " . ELibConfig::get('EMAIL_ORGANISATION') . "\n\nBefore we can let you"
                        . " know your password for using the site, please confirm your email address"
                        . " by clicking the following link:\n\n"
                        . "http://" . Config::get('WEB_ROOT') . Config::get('PUBLIC_DIR') . "/user/confirm_reg/?code=" . $reg_code
                        . "\n\nCheers\n\n";
                    if ($p->fullname === 'Not provided Not provided') {
                        $_POST['body'] = str_replace('Hi ___,', 'Hi,', $_POST['body']);
                        $p->fullname = $u->email;
                    }

                    $_POST['subject'] = "Registration with ".ELibConfig::get('EMAIL_ORGANISATION');        
                    $service =  DI::getContainer()->get('Contact');
                    $service->prepareDispatch($u->id);
                    $service->dispatchEmail($p->fullname);
                    $service->persist();                              
                }   
            } else {
                throw new \Exception('Could not complete registration');
            }
        }
        return array($errors, $u, $p, $s ?? new \stdClass()) ;
    }


    public function doConfirmReg($reg_code)
    {
        $u = Model::load('UserItem');
        $id = $u->findUserForActivation($reg_code);

        if ($id > 0) {
            $u->id = $id;
            $u->load();
            $password = $u->password;
            $u->password = password_hash($password, PASSWORD_DEFAULT);
            $u->active = 1;
            $u->activated = 'MYSQLTIME';
            $u->save(Model::getTable('UserItem'), array(), 0);

            $p = Model::load('UserProfile');
            $p->id = $u->user_profile_id;
            $p->load();

            Session::set('user_id',$u->id);

            $_POST['body'] = "\nHi ___,\n\n"
                ."Thanks for confirming your registration. You can now log in to the ".ELibConfig::get('EMAIL_ORGANISATION')." website using your username "
                ." '___' and the password '".$password."'.\n\nCheers\n\n";
            if ($p->fullname === 'Not provided Not provided') {
                $_POST['body'] = str_replace('Hi ___,', 'Hi,', $_POST['body']);
                $p->fullname = $u->email;
            }

            $_POST['body'] = str_replace('___', $u->username, $_POST['body']);

            $_POST['subject'] = 'Welcome to '.ELibConfig::get('EMAIL_ORGANISATION');
            $_POST['email'] = $u->email;

            $service =  DI::getContainer()->get('Contact');
            if ($service->prepareDispatch($u->id)) {
                $service->dispatchEmail($p->fullname);
                return true;
            } else {
                return false;
            }
        }
    }
}

