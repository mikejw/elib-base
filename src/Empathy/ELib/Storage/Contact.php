<?php

declare(strict_types=1);

namespace Empathy\ELib\Storage;

use Empathy\MVC\Entity as Entity;
use Empathy\MVC\Validate;

class Contact extends Entity
{
    public int $id;

    public mixed $user_id = null;

    public mixed $message = null;

    public mixed $subject = null;

    public mixed $body = null;

    public mixed $email = null;

    public mixed $first_name = null;

    public mixed $last_name = null;

    public mixed $submitted = null;

    public const TABLE = 'contact';

    public function validates(bool $html = false): void
    {
        if (!isset($this->message) || !($this->message === 0 || $this->message === 1)) {
            $this->addValError('Message field should be boolean, 1 or 0.');
        }
        $this->doValType(Validate::TEXT, 'first_name', $this->first_name, false);
        $this->doValType(Validate::TEXT, 'last_name', $this->last_name, false);
        $this->doValtype(Validate::EMAIL, 'email', $this->email, false);

        if ($this->message === 1) {
            $this->doValtype(Validate::TEXT, 'subject', $this->subject, false);
            if ($this->body === '') {
                $this->addValError('Please provide a message body.', 'body');
            }

            if (!$html) {
                $this->body = htmlspecialchars($this->body);
            }
        }
        $this->email = strtolower($this->email);
    }
}
