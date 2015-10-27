<?php

namespace Empathy\ELib;

class REST extends Curl
{

    public function configure()
    {
        if ($this->auth) {
            $auth_string = $this->user.':'.$this->pass;
            curl_setopt($this->ch, CURLOPT_USERPWD, $auth_string);
        }

        curl_setopt($this->ch, CURLOPT_POST, 0);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
    }

    private function assign_post_params()
    {
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->post_fields));
    }


    public function configure_post()
    {
        curl_setopt($this->ch, CURLOPT_POST, 1);
        $this->assign_post_params();
    }


    public function configure_put()
    {    
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->assign_post_params();
    }

    public function configure_delete()
    {    
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->assign_post_params();
    }



}


