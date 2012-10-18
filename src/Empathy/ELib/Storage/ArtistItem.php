<?php

namespace ELib\Storage;
use ELib\Model;
use Empathy\Entity;

class ArtistItem extends Entity
{
  const TABLE = 'artist_item';

  public $id;
  public $artist_alias;
  public $forename;
  public $surname;
  public $bio;
  public $image;
  public $active;
  
  
  public function validates()
  {    
    if($this->forename == '' || $this->surname == '')
    //if($this->artist_alias == '' && ($this->forename == '' XOR $this->surname == ''))
      {
	//$this->addValError('Invalid artist name. Please enter an alias or full name.');	
	$this->addValError('Invalid artist name. Please enter a full name.');	
      }           
  }


  public function buildTree($current, $tree)
  {     
    $i = 0;   
    $nodes = array();
    $sql = 'SELECT id,artist_alias,forename,surname FROM '.Model::getTable('ArtistItem').' ORDER BY surname, forename';
    $error = 'Could not get artists.'; 
    $result = $this->query($sql, $error);
    if($result->rowCount() > 0)
      {	
	foreach($result as $row)
	  {	    
	    $id = $row['id'];	    
	    $nodes[$i]['id'] = $id;
	    $nodes[$i]['artist_alias'] = $row['artist_alias'];
	    $nodes[$i]['forename'] = $row['forename'];	    
	    $nodes[$i]['surname'] = $row['surname'];
	    $i++;
	  }		
      }

    return $nodes;
  }

  // produce a list of artists ordered correctly
  // not used to produce the artist tree  
  public function getArtists()
  {
    $artist = array();
    $sql = 'SELECT * FROM '.Model::getTable('ArtistItem')
      .' WHERE active = 1'
      .' ORDER BY surname, forename';
        
    $error = 'Could not get list of artists.';
    $result = $this->query($sql, $error);
    if($result->rowCount() > 0)
      {	
	foreach($result as $row)
	  {	    
	    $id = $row['id'];	    

	    if($row['artist_alias'] == '')
	      {
		//$artist[$id] = $row['surname'].', '.$row['forename'];
		$artist[$id] = $row['forename'].' '.$row['surname'];
	      }
	    else
	      {
		$artist[$id] = $row['artist_alias'];
	      }
	    
	  }		
      }
    return $artist;   
  }


  public function getBios()
  {   
    $sql = 'SELECT t1.id AS artist_id, t3.id AS product_id, t1.artist_alias,'
      .' t1.forename, t1.surname, t1.bio, t3.name, t3.image, t3.category_id, t3.price'
      .' FROM '.Model::getTable('ArtistItem').' t1'
      .' LEFT JOIN '.Model::getTable('ProductArtist').' t2 ON t2.artist_id = t1.id'
      .' LEFT JOIN '.Model::getTable('ProductItem').' t3 ON t3.id = t2.product_id'
      .' ORDER BY t1.id';    
    $error = 'Could not get bios.';
    $result = $this->query($sql, $error);
    $last_artist_id = 0;
    $bio = array();
    $bios = array();
    $book = array();
    $books = array();
    if($result->rowCount() > 0)
      {
	foreach($result as $row)
	  {
	    if($last_artist_id != $row['artist_id'])
	      {
		if(sizeof($books) > 0)
		  {
		    $bio['books'] = $books;
		    $books = array();
		  }
		if(sizeof($bio) > 0)
		  {
		    array_push($bios, $bio);
		    $bio = array();
		  }

		$last_artist_id = $row['artist_id'];
		$bio['artist_id'] = $row['artist_id'];
		$bio['artist_alias'] = $row['artist_alias'];
		$bio['forename'] = $row['forename'];
		$bio['surname'] = $row['surname'];
		if($row['artist_alias'] == '')
		  {
		    $bio['artist'] = $row['forename'].' '.$row['surname'];
		  }
		else
		  {
		    $bio['artist'] = $row['artist_alias'];
		  }
		$bio['bio'] = $row['bio'];
	      }	    

	    if(isset($row['product_id']))
	      {
		if($row['category_id'] == 14)
		  {
		    $book = array();
		    $book['id'] = $row['product_id'];
		    $book['image'] = $row['image'];
		    $book['name'] = $row['name'];
		    $book['price'] = $row['price'];
		    array_push($books, $book);
		  }
	      }
	  }

      }
    if(sizeof($books) > 0)
      {
	$bio['books'] = $books;
      }
    if(sizeof($bio) > 0)
      {
	array_push($bios, $bio);
      }	
	
    return $bios;   
  }
}
?>