<?php

namespace ELib\Store;
use ELib\Model;

/*
use Empathy\Model\CategoryItem as CategoryItem;
use Empathy\Model\ProductItem as ProductItem;
use Empathy\Model\BrandItem as BrandItem;
use Empathy\Model\ProductVariant as ProductVariant;
*/

define('BUTTONS_PER_PAGE', 12);

class ProductsLayout
{
  private $category;
  private $product;
  private $variant;
  private $option;
  private $buttons = array();
  private $breadcrumb = array();
  private $redirect = '';
  private $controller;
  private $p_nav = array();

  public function __construct($c, $p, $v, $o, $controller)
  {
    $this->controller = $controller;
    $this->category = $c;
    $this->product = $p;
    $this->variant = $v;
    $this->option = $o;

    $this->buildBC();

    if($this->option->id != 0)
      {
	$this->buildByOption();
      }
    elseif($this->variant->id != 0)
      {
	//
      }
    elseif($this->product->id == 0)
      {
	$this->buildByCategory();
      }
    else
      {
	$variant_id = $this->product->hasOneVariant();
	if($variant_id)
	  {	    
	    //$this->redirect = 'products/?variant_id='.$variant_id;
	  }
	else
	  {
	    //$this->buildByProduct();
	  }
      }
  }

  public function getRedirect()
  {
    return $this->redirect;
  }

  public function getBreadCrumb()
  {
    return $this->breadcrumb;
  }

  public function getProduct()
  {
    return $this->product;
  }

  public function randomImage($c_id)
  {
    $image = '';
    $descendants = array();

    $this->category->buildDescendantIDs($c_id, $descendants);
    
    $d = '('.implode(',', $descendants).')';
   
    //    $products = $this->product->getAllCustom(Model::getTable('ProductItem'), ' WHERE category_id IN'.$d);
    if(sizeof($products) > 0)
      {
	shuffle($products);
	$p = $products[0];
	$image = $p['image'];
      }    
    /*
    $sql = ' WHERE t2.product_id = t1.id AND t1.category_id IN'.$d;
    $variants = $this->variant->getAllCustomPaginateSimpleJoin('*', ProductItem::$table, ProductVariant::$table, $sql, 1, 100);
    //shuffle($variants);
    $v = $variants[0];
    return $v['image'];    
    */
    return $image;
  }


  public function getButtons()
  {
    return $this->buttons;
  }

  public function buildByCategory()
  {    
    $button = array();

    if(!$this->category->getChildren($this->category->id))
      {
	$sql = ' WHERE category_id = '.$this->category->id
	  .' AND t1.sold_in_store = 1'
	  .' AND t1.brand_id = t2.id'
	  .' AND t3.product_id = t1.id';

	if(isset($_GET['page']) && is_numeric($_GET['page']))
	  {
	    $page = $_GET['page'];
	  }
	else
	  {
	    $page = 1;
	  }
	$per_page = BUTTONS_PER_PAGE;
	$group = 't1.id';
	//$order = 't2.name, t1.name';
	$order = 'price';

	$select = '*,t1.name AS product_name, t1.image AS image, t2.name AS brand_name, t1.id AS product_id, MIN(t3.price) AS price';
	$products = $this->product->getAllCustomPaginateMultiJoinGroup($select, Model::getTable('ProductItem'), Model::getTable('BrandItem'), Model::getTable('ProductVariant'), $sql, $page, $per_page, $group, $order);

	//$products = $this->product->getAllCustom(ProductItem::$table, $sql);	

	//shuffle($products);
	
	//if(sizeof($products) == 1)
	// {
	//    $this->redirect = 'products/?product_id='.$products[0]['id'];
	    // }
	    //else
	    // {
	    foreach($products as $p)
	      {	
		$button['name'] = $p['brand_name'].' '.$p['product_name'];
		$button['image'] = $p['image'];
		$button['product_id'] = $p['product_id'];
		$button['price'] = $p['price'];
		array_push($this->buttons, $button);
	      }	

	    $this->p_nav = $this->product->getPaginatePagesMultiJoinGroup($select, Model::getTable('ProductItem'), Model::getTable('BrandItem'), Model::getTable('ProductVariant'), $sql, $page, $per_page, $group, $order);
	    // }
      }
    else
      {
	$children = $this->category->getChildren($this->category->id);           
	foreach($children as $child)
	  {
	    $button = array();
	    $this->category->id = $child;
	    if($this->category->hasChildren())
	      {
		$this->category->load();
		$button['name'] = $this->category->name;
		$button['image'] = $this->randomImage($this->category->id);
		$button['category_id'] = $this->category->id;
		if($button['image'] != '')
		  {
		    array_push($this->buttons, $button);
		  }
	      }
	    else
	      {
		$this->category->load();
		$button['name'] = $this->category->name;
		$button['category_id'] = $this->category->id;
		
		$products = $this->product->getAllCustom(Model::getTable('ProductItem'), ' WHERE category_id = '.$this->category->id);
		//shuffle($products);
		if(sizeof($products) > 0)
		  {
		    $p = $products[0];
		    $button['image'] = $p['image'];
		    if($button['image'] != '')
		      {
			array_push($this->buttons, $button);
		      }
		  }
	      }
	  }
      }
  }

  public function getPNav()
  {
    return $this->p_nav;
  }

  public function buildByProduct()
  {    
    $button = array();
    
    $variants = $this->variant->getAllCustom(Model::getTable('ProductVariant'), ' WHERE product_id = '.$this->product->id);
    shuffle($variants);
    foreach($variants as $v)
      {		
	$button['name'] = $this->variant->getVariantName($v['id']);
	$button['image'] = $v['image'];
	$button['variant_id'] = $v['id'];
	array_push($this->buttons, $button);
      }   	    
  }

  public function buildBC()
  {
    $cats = array();

    if($this->product->id != 0)
      {
	if(!$this->product->load())
	  {
	    $this->controller->http_error(404);
	  }

	// set image to first variant image
	$variants = $this->variant->getAllCustom(Model::getTable('ProductVariant'), ' WHERE product_id = '.$this->product->id.' ORDER BY id');
	if(sizeof($variants) > 0)
	  {
	    $this->product->image = $variants[0]['image'];
	  }

	$this->category->id = $this->product->category_id;
      }
    elseif($this->variant->id != 0)
      {
	if(!$this->variant->load())
	  {
	    $this->controller->http_error(404);
	  }
	$this->product->id = $this->variant->product_id;
	$this->product->load();
	$this->category->id = $this->product->category_id;
      }
      
    
    if($this->category->id > 0 && !$this->category->load())
      {
	$this->controller->http_error(404);
      }
    
    if($this->category->id != 0)
      {	
	$current['id'] = $this->category->id;
	$current['name'] = $this->category->name;
	array_push($cats, $current);

	if($this->category->category_id > 0)
	  {	    
	    $this->category->buildBreadCrumb($this->category->category_id, $cats);
	  }
      }

    $root['id'] = 0;
    $root['name'] = 'All Products';  
    array_push($cats, $root);
    $this->breadcrumb = array_reverse($cats);
  }

  public function buildByOption()
  {
    $button = array();
    
    $variants = $this->variant->getAllForOption($this->option->id);
    if(sizeof($variants) < 1)
      {
	$this->controller->http_error(404);
      }

    foreach($variants as $v)
      {		
	$button['name'] = $v['name'];
	//	$button['name'] = $this->variant->getVariantName($v['id']);
	$button['image'] = $v['image'];
	$button['variant_id'] = $v['id'];
	array_push($this->buttons, $button);
      }   	    

  }


}
?>