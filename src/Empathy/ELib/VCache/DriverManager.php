<?php

namespace ELib\VCache;

class DriverManager
{  


  public static function load($h, $p, $name = null)
  {
    $driver = null;

    if($name === null)
      {
	$name = \ELib\VCache::DEFAULT_DRIVER;
      }

    switch($name)
      {
      case 'memcached':
	$driver_name = 'ELib\VCache\Driver'.ucfirst($name);
	$driver = new $driver_name($driver_name);
	
	$driver->load($h, $p);

	break;
      default:
	break;
      }

    return $driver;
  }

}
?>