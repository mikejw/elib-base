<?php

namespace Empathy\ELib;

use Empathy\ELib\Mailer;
use Empathy\ELib\Model;
use Empathy\MVC\Entity;
use Empathy\ELib\Config;


class Contact
{
    private $entity;
    private $controller;
    
    public function __construct()
    {
        $this->entity = Model::load('Contact');
    }
    
    public function signUp()
    {
        $this->entity->assignFromPost(array('submitted', 'message', 'subject', 'body'));
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
        $this->entity->id = $this->entity->insert(Model::getTable('Contact'), true, array(''), Entity::SANITIZE);
        return $this->entity->id;
    }
    
    public function email()
    {
        $this->entity->assignFromPost(array('message', 'submitted'));
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
                ." - ".$this->entity->email."<br /><br />"
                .$this->entity->body;

            $m = new Mailer($r, $this->entity->subject, $messageOut, null, true);
        } else {
            throw new \Exception('Email service config not set in elib.yml');
        }
    }
    
    public function getContact()
    {
        return $this->entity;
    }
}