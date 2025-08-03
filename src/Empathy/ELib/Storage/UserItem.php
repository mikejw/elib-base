<?php

namespace Empathy\ELib\Storage;

use Empathy\MVC\Model;
use Empathy\MVC\Entity;
use Empathy\MVC\Validate;


class UserItem extends Entity
{
    const TABLE = 'user';

    public $id;
    public $email;
    public $auth;
    public $username;
    public $password;
    public $reg_code;
    public $active;
    public $registered;
    public $activated;
    public $fullname;
    public $picture;
    public $about;

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
        if ($this->doValType(Validate::TEXT, 'fullname', $this->fullname, false)) {
            if (!strpos($this->fullname, ' ')) {
                $this->addValError('Must have space(s)', 'fullname');
            }
        }
        $this->doValType(Validate::TEXT, 'picture', $this->picture, true);
        $this->doValType(Validate::TEXT, 'about', $this->about, true);
    }

    public function validateLogin()
    {
        $this->doValType(Validate::USERNAME, 'username', $this->username, false);
        $this->validatePassword();
    }


    public function validatePassword()
    {
        $this->doValType(Validate::TEXT, 'password', $this->password, false);
    }


    public function getUsername($id)
    {
        $table = $this::TABLE;
        $params = [];
        $sql = "SELECT username FROM $table WHERE id = ?";
        $params[] = $id;
        $error = 'Could not get username.';
        $result = $this->query($sql, $error, $params);
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
        $table = $this::TABLE;
        $params = [];
        $sql = "SELECT id FROM $table WHERE username = ?";
        $params[] = $username;
        if ($password !== null) {
            $sql .= ' AND password = ?';
            $params[] = $password;
        }
        $error = 'Could not verify user.';
        $result = $this->query($sql, $error, $params);
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
            $this->load($this->id);
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
            $this->fullname = $name;
            $this->picture = $image;
            $this->about = '';
            $this->validates(false);
            if (!$this->hasValErrors()) {
                $this->id = $this->insert();
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
        $table = $this::TABLE;
        $user_id = 0;
        $params = [];
        $sql = "SELECT id, password FROM $table"
            .' WHERE (username = ? or email = ?)'
            .' AND active = 1';
        $params[] = $this->username;
        $params[] = $this->username;

        $error = 'Could not get user for login.';
        $result = $this->query($sql, $error, $params);
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
        $table = $this::TABLE;
        $params = [];
        $auth = 0;
        $sql = "SELECT auth FROM $table WHERE id = ?";
        $params[] = $id;
        $error = 'Could not get auth code.';
        $result = $this->query($sql, $error, $params);
        if ($result->rowCount() == 1) {
            $row = $result->fetch();
            $auth = $row['auth'];
        }

        return $auth;
    }

    public function findUserForActivation($reg_code)
    {
        $table = $this::TABLE;
        $params = [];
        $user_id = 0;
        $sql = "SELECT id FROM $table WHERE reg_code = ?"
            .' AND active = 0';
        $params[] = md5($reg_code);
        $error = 'Could not get user based on registation code.';
        
        $result = $this->query($sql, $error, $params);
        if ($result->rowCount() == 1) {
            $row = $result->fetch();
            $user_id = $row['id'];
        }

        return $user_id;
    }

    protected function activeUser()
    {
        $table = $this::TABLE;
        $params = [];
        $active = 0;
        $sql = "SELECT id FROM $table WHERE email = ?"
            .' AND active = 1';
        $params[] = $this->email;
        if (isset($this->id)) {
            $sql .= ' AND id != ?';
            $params[] = $this->id;
        }
        $error = 'Could not check for existing email address.';
        $result = $this->query($sql, $error, $params);
        if ($result->rowCount() > 0) {
            $active = 1;
        }

        return $active;
    }

    protected function usernameExists()
    {
        $table = $this::TABLE;
        $params = [];
        $exists = 0;
        $sql = "SELECT id FROM $table WHERE username = ?";
        $params[] = $this->username;
        if (isset($this->id)) {
            $sql .= ' AND id != ?';
            $params[] = $this->id;
        }
        $error = 'Could not check for existing username.';
        $result = $this->query($sql, $error, $params);
        if ($result->rowCount() > 0) {
            $exists = 1;
        }

        return $exists;
    }
}
