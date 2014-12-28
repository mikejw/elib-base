<?php

namespace Empathy\ELib\User;

use Empathy\ELib\Model,
    Empathy\MVC\Session;


class CurrentUser
{
    private static $u;
    private static $user_id;

    public static function detectUser($c=null)
    {
        self::$u = Model::load('UserItem');
        self::$user_id = Session::get('user_id');

        if (is_numeric(self::$user_id) && self::$user_id > 0) {
            self::$u->id = self::$user_id;
            self::$u->load();

            if($c !== null) {
                $c->assign('current_user', self::$u->username);
                $c->assign('user_id', self::$u->id);
            }
            //$c->assign('user_is_vendor', (self::$u->auth == \Empathy\ELib\Store\Access::VENDOR));
        }
    }

    public static function assertAdmin($c)
    {
        $ua = Model::load('UserAccess');
        if (self::$u->id < 1 || self::$u->getAuth(self::$u->id) < $ua->getLevel('admin')) {
            Session::down();
            $c->redirect("user/login");
	    exit();
        }
    }

    public static function getUserID()
    {
        return self::$u->id;
    }

    public static function loggedIn()
    {
        return (self::getUserID() > 0);
    }

    public static function getProfileID()
    {
        return self::$u->user_profile_id;
    }

    public static function getUser()
    {
        return self::$u;
    }

    public static function isAuthLevel($level)
    {
        return (self::$u->auth >= $level);
    }

}
