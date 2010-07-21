<?php

namespace ELib\Queue;
use ELib\YAML;


class Job
{  
  private $id;
  private $queued_at;
  private $body;

  /*
  private $time_to_run;
  private $priority;
  private $delay;
  */

  private $data;
  private $data_s;

  private $tube;
  private $serialized_vars;


  public function __construct($args)
  {
    switch(sizeof($args))
      {
      case 2:
	$this->init($args[0], $args[1]);
	break;
      case 1:	
	$this->initEmpty($args[0]);
	break;
      }
  }

  public function init($body, $tube)
  {
    $this->serialized_vars = array(
				   'id', 'queued_at', 'body');
    $this->tube = $tube;
    $this->id = uniqid();
    $this->queued_at = time();
    $this->body = $body;   
    $this->serialize();   
  }

  public function initEmpty($data)
  {
    $this->setData($data);
    $this->deserialize();
  }

  public function setData($data)
  {
    $this->data_s = $data;
  }

  public function getBody()
  {
    return $this->body;
  }
  
  public function getID()
  {
    return $this->id;
  }


  public function serialize()
  {
    $data = array();
    foreach($this->serialized_vars as $v)
      {
	$data[$v] = $this->$v;
      }
    $this->data_s = YAML::dump($data);
  }

  public function getSerialized()
  {
    return $this->data_s;
  }

  public function deserialize()
  {
    $data = YAML::loadString($this->data_s);   
    foreach($this->serialized_vars as $v)
      {
	$this->$v = $data[$v];
      }
  }

}
?>