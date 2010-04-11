<?php

namespace ELib;

class Curl
{ 
  private $response;
  protected $ch;

  protected $url;
  protected $user;
  protected $pass;
  protected $header;
  protected $post_fields;

  public function getResponse()
  {
    return $this->response;
  }
  
  public function __construct($url, $header, $post_fields, $user, $pass)
  {
    $this->ch = curl_init();
    $this->url = $url;
    $this->header = $header;    
    $this->user = $user;
    $this->pass = $pass;
    $this->post_fields = $post_fields;
    $this->configure();
  }

  public function configure()
  {
    curl_setopt($this->ch, CURLOPT_URL, $this->url);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);


    //    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array());
    

    //    curl_setopt($this->ch, CURLOPT_USERPWD, $auth);
    //curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->user.':'.$this->pass);
    

    //curl_setopt($this->ch, CURLOPT_POST, 0);

    //curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);        
    //curl_setopt($this->ch, CURLOPT_POSTFIELDS, $b);
  }

  public function fetch()
  {
    $this->response = curl_exec($this->ch);
    echo $this->response;
    curl_close($this->ch);
  }
}
?>