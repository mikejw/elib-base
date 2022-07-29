<?php

namespace Empathy\ELib;
use Empathy\ELib\Config;
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
    private $html;
    public $result;


    public function __construct($r, $s, $m, $f=null, $html=false)
    {
        $this->recipients = $r;
        $this->subject = $s;
        $this->message = $m;
        $this->result = 1;
        $this->from = $f;
        $this->html = $html;
        $this->send();
    }

    private function init()
    {
        $this->mailer = new PHPMailer(true); // allow exceptions
        $this->mailer->IsSMTP();
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Host = Config::get('EMAIL_HOST');
        $this->mailer->Username = Config::get('EMAIL_USER');
        $this->mailer->Password = Config::get('EMAIL_PASSWORD');
        $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Port = 25;
        $this->mailer->isHTML($this->html);
        $this->mailer->Subject = $this->subject;
        $this->mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if ($this->from === null) {
            $this->mailer->setFrom(Config::get('EMAIL_FROM'), 'Mike Whiting');
            $this->mailer->addReplyTo(Config::get('EMAIL_FROM'), 'Mike whiting');
        } else {
            $this->mailer->setFrom($this->from['address'], $this->from['alias']);
            $this->mailer->addReplyTo($this->from['address'], $this->from['alias']);
        }
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
            throw new \Exception("{$this->mailer->ErrorInfo}");
            $this->result = 0;
        }
    }
}
