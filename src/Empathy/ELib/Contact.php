<?php

namespace Empathy\ELib;

use Empathy\ELib\Mailer;
use Empathy\MVC\Model;
use Empathy\ELib\Storage\Contact as ContactItem;
use Empathy\MVC\Entity;
use Empathy\ELib\Config;


class Contact
{
    private $entity;
    private $controller;
    
    public function __construct()
    {
        $this->entity = Model::load(ContactItem::class);
    }
    
    public function signUp()
    {
        $this->entity->assignFromPost(array('submitted', 'message', 'subject', 'body', 'user_id'));
        $this->entity->message = 0;
        $this->entity->submitted = 'MYSQLTIME';
        $this->entity->validates();
        return !$this->entity->hasValErrors();
    }
    
    public function getErrors()
    {
        return $this->entity->getValErrors();
    }
    
    public function persist()
    {
        $this->entity->id = $this->entity->insert();
        return $this->entity->id;
    }
    
    public function email()
    {
        $this->entity->assignFromPost(array('message', 'submitted', 'user_id'));
        $this->entity->message = 1;
        $this->entity->submitted = 'MYSQLTIME';
        $this->entity->validates();
        return !$this->entity->hasValErrors();
    }
    
    public function sendEmail()
    {
        $r = array();
        if (
            Config::get('EMAIL_ORGANISATION') &&
            Config::get('EMAIL_FROM') &&
            Config::get('EMAIL_RECIPIENT')
        ) {
            $r[0]['alias'] = Config::get('EMAIL_RECIPIENT');
            $r[0]['address'] = Config::get('EMAIL_RECIPIENT');

            $messageOut = "Message sent from: "
                .$this->entity->first_name. ' ' .$this->entity->last_name
                ." - ".$this->entity->email."\n\n\n\n"
                .$this->entity->body;

            $m = new Mailer($r, $this->entity->subject, $messageOut, null);
        } else {
            throw new \Exception('Email service config not set in elib.yml');
        }
    }

    public function prepareDispatch($user_id, $html = false) {
        $this->entity->assignFromPost(array('submitted', 'message', 'user_id'));
        $this->entity->message = 1;
        $this->entity->submitted = 'MYSQLTIME';
        $this->entity->user_id = $user_id;
        $this->entity->validates($html);
        return !$this->entity->hasValErrors();
    }

    public function dispatchEmail($name, $html = false) {
        if (
            Config::get('EMAIL_ORGANISATION') &&
            Config::get('EMAIL_FROM')
        ) {        
            $r[0]['alias'] = $name;
            $r[0]['address'] = $this->entity->email;
            $m = new Mailer($r, $this->entity->subject, $this->entity->body, null, $html);
        } else {
            throw new \Exception('Email service config not set in elib.yml');
        }
    }

    public function getContact()
    {
        return $this->entity;
    }
}
