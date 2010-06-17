<?php

namespace ELib\Storage;
use Empathy\Entity;

define('PUBLISHED', 2);

class BlogItem extends Entity
{
  public $id;
  public $blog_category_id;
  public $status;
  public $user_id;
  public $stamp;
  public $heading;
  public $body;

  public static $table = 'blog';
  

  public function validates()
  {
    if($this->heading == '' || !ctype_alnum(str_replace(' ', '', $this->heading)))
      {
	$this->addValError('Invalid heading');	
      }       
    if($this->body == '')
      {
	$this->addValError('Invalid body');	
      }
  }

  
  public function getFeed()
  {
    $entry = array();
    $sql = 'SELECT *, UNIX_TIMESTAMP(stamp) AS stamp FROM '.BlogItem::$table
      .' WHERE status = '.PUBLISHED.' ORDER BY stamp DESC LIMIT 0, 5';
    $error = 'Could not get blog feed.';
    $result = $this->query($sql, $error);
    $i = 0;
    foreach($result as $row)
      {
	$entry[$i] = $row;
	$i++;
      }      
    return $entry;
  }
  
  public function checkForDuplicates($input)
  {
    $temp = '';
    $error = 0;
    foreach($input as $item)
      {
	$temp = array_pop($input);
	if(in_array($temp, $input))
	  {
	    $error = 1;
	  }
	array_push($input, $temp);
      }
    if($error)
      {
	$this->addValError('Duplicate tags submitted');
      }    
  }

  public function buildTags()
  {
    $tags = array();
    if($_POST['tags'] != '')
      {	
	if(ctype_alnum(str_replace(',', '', str_replace(' ', '', $_POST['tags']))))
	  {
	    $tags = explode(',', str_replace(' ', '', $_POST['tags']));
	  }
	else
	  {
	    $this->addValError('Invalid tags submitted');
	  }
      }
    return $tags;
  }   

  public function getStamp()
  {
    $stamp = 0;
    $sql = 'SELECT UNIX_TIMESTAMP(stamp) AS stamp FROM '.BlogItem::$table
      .' WHERE id = '.$this->id;
    $error = 'Could not get stamp.';
    $result = $this->query($sql, $error);
    if($result->rowCount() > 0)
      {
	$row = $result->fetch();
	$stamp = $row['stamp'];
      }
    return $stamp;
  }


  public function getRecentlyModified()
  {
    $stamp = 0;
    $sql = 'SELECT UNIX_TIMESTAMP(stamp) AS stamp FROM '.BlogItem::$table
      .' ORDER BY stamp DESC LIMIT 0,1';
    $error = 'Could not get recently modified blogs';
    $result = $this->query($sql, $error);
    if($result->rowCount() > 0)
      {
	$row = $result->fetch();
	$stamp = $row['stamp'];
      }
    return $stamp;
  }

  public function getAllForSiteMap()
  {
    $blogs = array();
    $sql = 'SELECT *, UNIX_TIMESTAMP(stamp) AS stamp FROM '.BlogItem::$table.' b'
      .' WHERE status = 2';
    $error = 'Could not get blogs for sitemap';
    $result = $this->query($sql, $error);
    if($result->rowCount() > 0)
      {
	foreach($result as $row)
	  {
	    //	    $row['slug'] = $this->urlSlug($row['name']);
	    array_push($blogs, $row);
	  }
      }
    return $blogs;
  }  

  public function getArchive()
  {
    $archive = array();
    /*
    $sql = 'SELECT MAX(UNIX_TIMESTAMP(stamp)) AS max,'
      .' MIN(UNIX_TIMESTAMP(stamp)) AS min'
      .' FROM '.BlogItem::$table;
    */

    $sql = 'SELECT id, YEAR(stamp) AS year, MONTH(stamp) AS month,'
      .' MONTHNAME(stamp) AS monthname, heading FROM '.BlogItem::$table
      .' WHERE status = 2 ORDER BY stamp DESC';
    $error = 'Could not get blog archive.';
    $result = $this->query($sql, $error);
    
    foreach($result as $row)
      {
	$year = $row['year'];
	$month = $row['monthname'];
	$id = $row['id'];
	$archive[$year][$month][$id] = ucwords($row['heading']);	
      }

    return $archive;
    //    print_r($archive);

    //    $max = $row['stamp'];

    /*
    $sql = 'SELECT MIN(UNIX_TIMESTAMP(stamp)) AS stamp FROM '.BlogItem::$table;
    $result = $this->query($sql, $error);
    $row = $result->fetch();
    $max = $row['stamp'];
    */

  }
}
?>