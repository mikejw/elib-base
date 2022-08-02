<?php

namespace Empathy\ELib\Storage;

use Empathy\ELib\Model;
use Empathy\MVC\Entity;
use Empathy\MVC\Validate;


class UserItem extends Entity
{
    const TABLE = 'e_user';

    public $id;
    public $user_profile_id;
    public $email;
    public $auth;
    public $username;
    public $password;
    public $reg_code;
    public $active;
    public $registered;
    public $activated;

    public function validates($email_check=true)
    {
        if ($this->doValType(Validate::USERNAME, 'username', $this->username, false)) {
            if ($this->usernameExists()) {
                $this->addValError('Username is already taken', 'username');
            }
        }
        if ($this->doValType(Validate::EMAIL, 'email', $this->email, false)) {
            if ($email_check) {
                if ($this->activeUser()) {
                    $this->addValError('That email address can\'t be used', 'email');
                }
            }
        }
    }

    public function validateLogin()
    {
        $this->doValType(Validate::USERNAME, 'username', $this->username, false);
        $this->doValType(Validate::TEXT, 'password', $this->password, false);
    }

    public function getUsername($id)
    {
        $sql = "SELECT username FROM ".Model::getTable('UserItem')." WHERE id = $id";
        $error = "Could not get username.";
        $result = $this->query($sql, $error);
        $row = $result->fetch();

        return $row['username'];
    }

    public function buildInvalid($username, $password)
    {
        $this->id = 0;
        $this->username = $username;
        $this->password = $password;
    }

    public function getID($username, $password=null)
    {
        $sql = "SELECT id FROM ".Model::getTable('UserItem')
            ." WHERE username = '$username'";
        if ($password !== null) {
            $sql .= " AND password = '$password'";
        }
        //." AND password = '".md5($this->password)."'";
        $error = "Could not verify user.";
        $result = $this->query($sql, $error);
        if (1 == $result->rowCount()) {
            $row =  $result->fetch();

            return $row['id'];
        } else {
            return 0;
        }
    }

    public function oAuthSignIn($username, $name, $image)
    {
        $user_id = 0;
        if (($this->id = $this->getID($username))) {
            $this->load();
            $user_id = $this->id;
        } else {
            unset($this->id);
            $this->email = 'twitter_user@example.com';
            $this->auth = 'DEFAULT';
            $this->username = $username;
            $this->password = 'password';
            $this->reg_code = '1';
            $this->active = 1;
            $this->registered = 'MYSQLTIME';
            $this->activated = 'MYSQLTIME';
            $this->validates(false);
            if (!$this->hasValErrors()) {
                $up = Model::load('UserProfile');
                $up->fullname = $name;
                $up->picture = $image;
                $up->about = '';
                $this->user_profile_id = $up->insert(Model::getTable('UserProfile'), true, array(), 0);
                $this->id = $this->insert(self::TABLE, true, array(), 0);
                $user_id = $this->id;
            } else {
                $errors = $this->getValErrors();
                throw new \Exception('Could not validate new user: '.implode(' - ', $errors));
            }
        }

        return $user_id;
    }

    /**
     * Do login.
     * User should not need to know exact casing of username (like twitter).
     * This is handled by DB.  
     */
    public function login()
    {
        $user_id = 0;
        $sql = 'SELECT id, password FROM '.Model::getTable('UserItem')
            .' WHERE username = \''.$this->username.'\''
            .' AND active = 1';
        $error = "Could not get user for login.";
        $result = $this->query($sql, $error);
        $rows = $result->rowCount();
        if ($rows == 1) {
            $row = $result->fetch();
            if (password_verify($this->password, $row['password'])) {
                $user_id = $row['id'];    
            }
        }
        return $user_id;
    }

    public function getAuth($id)
    {
        $auth = 0;
        $sql = "SELECT auth FROM ".Model::getTable('UserItem')." WHERE id = $id";
        $error = "Could not get auth code.";
        $result = $this->query($sql, $error);
        if ($result->rowCount() == 1) {
            $row = $result->fetch();
            $auth = $row['auth'];
        }

        return $auth;
    }

    public function findUserForActivation($reg_code)
    {
        $user_id = 0;
        //    $sql = 'SELECT id FROM '.Model::getTable('UserItem').' WHERE reg_code = \''.md5($reg_code).'\''
        $sql = 'SELECT id FROM '.Model::getTable('UserItem').' WHERE reg_code = \''.md5($reg_code).'\''
            .' AND active = 0';
        $error = 'Could not get user based on registation code.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() == 1) {
            $row = $result->fetch();
            $user_id = $row['id'];
        }

        return $user_id;
    }

    protected function activeUser()
    {
        $active = 0;
        $sql = 'SELECT id FROM '.Model::getTable('UserItem').' WHERE email = \''.$this->email.'\''
            .' AND active = 1';
        if (isset($this->id)) {
            $sql .= ' AND id != '.$this->id;
        }
        $error = 'Could not check for existing email address.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            $active = 1;
        }

        return $active;
    }

    protected function usernameExists()
    {
        $exists = 0;
        $sql = 'SELECT id FROM '.Model::getTable('UserItem').' WHERE username = \''.$this->username.'\'';
        if (isset($this->id)) {
            $sql .= ' AND id != '.$this->id;
        }
        $error = 'Could not check for existing username.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            $exists = 1;
        }

        return $exists;
    }

}
