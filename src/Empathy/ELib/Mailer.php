<?php

declare(strict_types=1);

namespace Empathy\ELib;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    /** @var list<array{alias: string, address: string}> */
    private array $recipients;

    private string $subject;

    private string $message;

    /** @var array{address: string, alias: string}|null */
    private ?array $from = null;

    private ?PHPMailer $mailer = null;

    private bool $html = false;

    public int $result = 1;

    /**
     * @param list<array{alias: string, address: string}> $r
     * @param array{address: string, alias: string}|null  $f
     */
    public function __construct(array $r, string $s, string $m, ?array $f = null, bool $html = false)
    {
        $this->recipients = $r;
        $this->subject = $s;
        $this->message = $m;
        $this->result = 1;
        $this->from = $f;
        $this->html = $html;
        $this->send();
    }

    private function init(): void
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

    public function send(): void
    {
        try {
            foreach ($this->recipients as $r) {
                $this->init();
                $mailer = $this->mailer;
                if (!$mailer instanceof PHPMailer) {
                    throw new \RuntimeException('PHPMailer failed to initialise');
                }
                $mailer->Body = str_replace('___', $r['alias'], $this->message);
                $mailer->addAddress($r['address'], $r['alias']);
                $mailer->send();
            }
        } catch (Exception $e) {
            $this->result = 0;
            $errorInfo = $this->mailer->ErrorInfo;
            throw new \Exception($errorInfo !== '' ? $errorInfo : $e->getMessage());
        }
    }
}
