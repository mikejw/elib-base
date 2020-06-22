<?php

namespace Empathy\ELib;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private $recipients;
    private $subject;
    private $message;
    private $from;
    private $mailer;
    public $result;

    public function __construct($r, $s, $m, $f)
    {
        $this->recipients = $r;
        $this->subject = $s;
        $this->message = $m;
        $this->result = 1;
        $this->from = $f;
        $this->send();
    }

    private function init()
    {
        $this->mailer = new PHPMailer(true); // allow exceptions
        $this->mailer->IsSMTP();
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Host = ELIB_EMAIL_HOST;
        $this->mailer->Username = ELIB_EMAIL_USER;
        $this->mailer->Password = ELIB_EMAIL_PASSWORD;
        $this->mailer->setFrom(ELIB_EMAIL_FROM, 'Mike Whiting');
        $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Port = 25;
        $this->mailer->isHTML(false);
        $this->mailer->Subject = $this->subject;
        $this->mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $this->mailer->addReplyTo(ELIB_EMAIL_FROM, 'Mike whiting');
    }

    public function send()
    {
        try {
            foreach ($this->recipients as $index => $r) {
                $this->init();
                $this->mailer->Body = str_replace('___', $r['alias'], $this->message);
                $this->mailer->addAddress($r['address'], $r['alias']);
                $this->mailer->send();
            }
        } catch(Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
            $this->result = 0;
        }
    }
}
