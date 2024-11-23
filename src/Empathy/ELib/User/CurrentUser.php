<?php

namespace Empathy\ELib\User;

use Empathy\ELib\Model;
use Empathy\MVC\Session;
use Empathy\MVC\DI;
use Empathy\ELib\Config as ELibConfig;
use Empathy\MVC\Config;
use Empathy\MVC\RequestException;


class CurrentUser
{
    protected $u;
    protected $user_id;
    protected $loaded = false;

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

    public function detectUser($controller)
    {
        if ($this->loaded) {
            return;
        }

        $user_id = Session::get('user_id');
        $this->u = Model::load('UserItem');
        $this->user_id = $user_id;

        if (is_numeric($this->user_id) && $this->user_id > 0) {
            $this->u->id = $this->user_id;
            $this->u->load();
            $controller->assign('current_user', $this->u->username);
            $controller->assign('user_id', $this->u->id);
            $this->loaded = true;
        }
    }

    public function assertAdmin($controller)
    {
        $ua = Model::load('UserAccess');
        if ($this->u->id < 1 || $this->u->getAuth($this->u->id) < $ua->getLevel('admin')) {
            Session::down();
            $controller->redirect("user/login");
        }
    }

    public function isAdmin($u)
    {
        $admin = false;
        $ua = Model::load('UserAccess');
        if (!($u->auth < $ua->getLevel('admin'))) {
            $admin = true;
        }
        return $admin;
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

    public function setUser($user)
    {
        $this->u = $user;
    }

    public function isAuthLevel($level)
    {
        return ($this->u->auth >= $level);
    }

    public function setUserID($id)
    {
        $this->u->id = $id;
    }

    public function doLogin($username, $password, $initSession = true, $model = null)
    {
        if ($model === null) {
            $model = DI::getContainer()->get('UserModel');    
        }
        $user = Model::load($model);
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
                $user->addValError('Wrong username/password combination.', 'general');
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

    public function sendConfirmationEmail($u, $reg_code)
    {
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
            if ($u->fullname === 'Not provided Not provided') {
                $_POST['body'] = str_replace('Hi ___,', 'Hi,', $_POST['body']);
                $u->fullname = $u->email;
            }

            $_POST['subject'] = "Registration with ".ELibConfig::get('EMAIL_ORGANISATION');        
            $service =  DI::getContainer()->get('Contact');
            $service->prepareDispatch($u->id);
            $service->dispatchEmail($u->fullname);
            $service->persist();                              
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
        $errors = array();
        $u = Model::load('UserItem');
        $u->username = $username;
        $u->email = $email;
        
        $supply_address = (isset($supply_address) && $supply_address == 1) ? 1 : 0;

        if ($supply_address == 1) {
            $s = Model::load('ShippingAddress');
            if ($fullname != '') {
                $fullname_arr = explode(' ', $fullname);
                if (sizeof($fullname_arr) > 1) {
                    $s->last_name = $fullname_arr[sizeof($fullname_arr) - 1];
                    array_pop($fullname_arr);
                    $s->first_name = implode(' ', $fullname_arr);
                }
            } else {
                $s->first_name = $first_name;
                $s->last_name = $last_name;                
            }
            $u->fullname = $s->first_name.' '.$s->last_name;

            $s->address1 = $address1;
            $s->address2 = $address2;
            $s->city = $city;
            $s->state = $state;
            $s->zip = strtoupper($zip);
            $s->country = $country;
            $s->default_address = 1;
            $s->validates();
        } else {
            $u->fullname = $first_name.' '.$last_name;
        }
        $u->validates();

        if ($u->hasValErrors() || (isset($s) && $s->hasValErrors())) {

            $errors = $u->getValErrors();
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
                $this->sendConfirmationEmail($u, $reg_code);
            } else {
                throw new \Exception('Could not complete registration');
            }
        }
        return array($errors, $u, $s ?? new \stdClass()) ;
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

            Session::set('user_id',$u->id);

            $_POST['body'] = "\nHi ___,\n\n"
                ."Thanks for confirming your registration. You can now log in to the ".ELibConfig::get('EMAIL_ORGANISATION')." website using your username "
                ." '___' and the password '".$password."'.\n\nCheers\n\n";
            if ($u->fullname === 'Not provided Not provided') {
                $_POST['body'] = str_replace('Hi ___,', 'Hi,', $_POST['body']);
                $u->fullname = $u->email;
            }

            $_POST['body'] = str_replace('___', $u->username, $_POST['body']);

            $_POST['subject'] = 'Welcome to '.ELibConfig::get('EMAIL_ORGANISATION');
            $_POST['email'] = $u->email;

            $service =  DI::getContainer()->get('Contact');
            if ($service->prepareDispatch($u->id)) {
                $service->dispatchEmail($u->fullname);
                return true;
            }
        }
    }


    public function doChangePassword(
        $old_password,
        $password1,
        $password2
    ) {
        $errors = array();
        $model = DI::getContainer()->get('UserModel');
        $u = Model::load($model);
        $u->id = Session::get('user_id');
        $u->load();

        if (!password_verify($old_password, $u->password)) {
            $u->addValError('The existing password you have entered is not correct', 'old_password');
        }

        if ($password2 == '') {
            $u->addValError('This is a required field', 'password2');
        } else if ($password1 != $password2) {
            $u->addValError('The new password entered does not match the confirmation', 'password1');
            $u->addValError('The new password entered does not match the confirmation', 'password2');
        }

        $u->password = $password1;
        $u->validatePassword();

        $errors = $u->getValErrors();
        if (isset($errors['password'])) {
            $errors['password1'] = $errors['password'];
            unset($errors['password']);
        }

        if (!sizeof($errors)) {
            $u->password = password_hash($password1, PASSWORD_DEFAULT);
            $u->save(Model::getTable('UserItem'), array(), 0);
        }
        return $errors;
    }

    public function denyNotAdmin()
    {
        $ua = Model::load('UserAccess');
        if ($this->u->auth < $ua->getLevel('admin')) {
            throw new RequestException('Denied', RequestException::NOT_AUTHORIZED);
        }
    }
}

