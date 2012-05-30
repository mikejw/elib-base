<?php

namespace ELib;

class YAML
{ 
  public static function save($data, $file, $append=false)
  {
    $s = new \spyc();    
    $yaml = self::dump($data);
    $mode = 'w';
    
    if($append)
      {
	$mode = 'a';
      }

    $fh = fopen($file, $mode);
    fwrite($fh, $yaml);
    fclose($fh);   
  }

  public static function load($file)
  {
    $s = new \spyc();
    return $s->YAMLLoad($file);
  }

  
  public static function dump($data)
  {
    $s = new \spyc();
    return $s->YAMLDump($data, 4, 60);
  }


  public static function loadString($data)
  {
    $s = new \spyc();
    return $s->YAMLLoadString($data);
  }


  public static function objectToArray($object)
  {
    if(!is_object( $object ) && !is_array($object))
      {
	return $object;
      }
    if(is_object($object))
      {
	$object = get_object_vars($object);
      }
    return array_map(array('ELib\YAML', 'objectToArray'), $object);
  }






}
?>
