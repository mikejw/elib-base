<?php

namespace Empathy\ELib\Storage;
use Empathy\MVC\Entity as Entity;
use Empathy\MVC\Validate;


class Contact extends Entity
{
    public $id;
    public $user_id;
    public $message;
    public $subject;
    public $body;
    public $email;
    public $first_name;
    public $last_name;
    public $submitted;

    const TABLE = "contact";

    /**
     * Validate and filter input.
     */
    public function validates()
    {
        if (!isset($this->message) || !($this->message === 0 || $this->message === 1)) {
            $this->addValError("Message field should be boolean, 1 or 0.");
        }
        $this->doValType(Validate::TEXT, 'first_name', $this->first_name, false);
        $this->doValType(Validate::TEXT, 'last_name', $this->last_name, false);
        $this->doValtype(Validate::EMAIL, 'email', $this->email, false);

        if ($this->message === 1) {
            $this->doValtype(Validate::TEXT, 'subject', $this->subject, true);
            if ($this->body == '') {
                $this->addValError('Please provide a message body.', 'body');
            }

            $this->body = htmlspecialchars($this->body);
            $this->body = str_replace(array("\r\n", "\r", "\n"), "<br />", $this->body);
        }
        $this->email = strtolower($this->email);
    }
}
