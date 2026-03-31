<?php

declare(strict_types=1);

namespace Empathy\ELib;

class Curl
{
    private mixed $response = null;

    protected \CurlHandle $ch;

    /** @var non-empty-string */
    protected string $url;

    protected string $user;

    protected string $pass;

    protected mixed $header;

    protected mixed $post_fields;

    protected bool $auth;

    protected bool $success = false;

    public function getResponse(): mixed
    {
        return $this->response;
    }

    public function __construct(string $url, mixed $header, mixed $post_fields, string $user, string $pass, bool $auth)
    {
        if ($url === '') {
            throw new \InvalidArgumentException('CURL URL must not be empty');
        }
        $this->ch = curl_init();
        $this->url = $url;
        $this->header = $header;
        $this->user = $user;
        $this->pass = $pass;
        $this->post_fields = $post_fields;
        $this->auth = $auth;
        $this->configure();
    }

    public function configure(): void
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);



        //    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array());

        //    curl_setopt($this->ch, CURLOPT_USERPWD, $auth);
        //curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->user.':'.$this->pass);

        //curl_setopt($this->ch, CURLOPT_POST, 0);

        //curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($this->ch, CURLOPT_POSTFIELDS, $b);
    }

    public function fetch(bool $disconnect = true): bool
    {
        $this->response = curl_exec($this->ch);
        $code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $this->success = ($code === 200);
        if ($disconnect === true) {
            curl_close($this->ch);
        }
        return $this->success;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

}
