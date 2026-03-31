<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Empathy\ELib\Storage\Contact as ContactItem;
use Empathy\MVC\Model;

class Contact
{
    private ContactItem $entity;

    public function __construct()
    {
        $this->entity = Model::load(ContactItem::class);
    }

    public function signUp(): bool
    {
        $this->entity->assignFromPost(['submitted', 'message', 'subject', 'body', 'user_id']);
        $this->entity->message = 0;
        $this->entity->submitted = 'MYSQLTIME';
        $this->entity->validates();
        return !$this->entity->hasValErrors();
    }

    /**
     * @return array<int|string, string>
     */
    public function getErrors(): array
    {
        return $this->entity->getValErrors();
    }

    public function persist(): int
    {
        $this->entity->id = $this->entity->insert();
        return $this->entity->id;
    }

    public function email(): bool
    {
        $this->entity->assignFromPost(['message', 'submitted', 'user_id']);
        $this->entity->message = 1;
        $this->entity->submitted = 'MYSQLTIME';
        $this->entity->validates();
        return !$this->entity->hasValErrors();
    }

    public function sendEmail(): void
    {
        $r = [];
        if (
            Config::get('EMAIL_ORGANISATION') &&
            Config::get('EMAIL_FROM') &&
            Config::get('EMAIL_RECIPIENT')
        ) {
            $r[0]['alias'] = Config::get('EMAIL_RECIPIENT');
            $r[0]['address'] = Config::get('EMAIL_RECIPIENT');

            $messageOut = 'Message sent from: '
                .$this->entity->first_name. ' ' .$this->entity->last_name
                .' - '.$this->entity->email."\n\n\n\n"
                .$this->entity->body;

            $m = new Mailer($r, $this->entity->subject, $messageOut, null);
        } else {
            throw new \Exception('Email service config not set in elib.yml');
        }
    }

    public function prepareDispatch(mixed $user_id, bool $html = false): bool
    {
        $this->entity->assignFromPost(['submitted', 'message', 'user_id']);
        $this->entity->message = 1;
        $this->entity->submitted = 'MYSQLTIME';
        $this->entity->user_id = $user_id;
        $this->entity->validates($html);
        return !$this->entity->hasValErrors();
    }

    public function dispatchEmail(string $name, bool $html = false): void
    {
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

    public function getContact(): ContactItem
    {
        return $this->entity;
    }
}
