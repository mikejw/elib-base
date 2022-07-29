<?php

namespace Empathy\ELib\User;

use Empathy\ELib\Model;
use Empathy\MVC\Session;


class CurrentUser
{
    private $u;
    private $user_id;
    private $checked = false;

    public function detectUser($c = NULL, $store_active = false)
    {
        if (!$this->checked) {
            $this->u = Model::load('UserItem');
            $this->user_id = Session::get('user_id');

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
            $this->checked = true;
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
}
