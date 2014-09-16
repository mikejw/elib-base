<?php

namespace Empathy\ELib;

class JSONRPC extends Curl
{

    // based on REST
    public function configure()
    {        
        if ($this->auth) {
            $auth_string = $this->user.':'.$this->pass;
            curl_setopt($this->ch, CURLOPT_USERPWD, $auth_string);
            curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }
        
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->post_fields);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
    }
}
