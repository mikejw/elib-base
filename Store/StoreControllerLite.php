<?php

namespace ELib\Store;
use ELib\Model;

use ELib\AuthedController;
use ELib\EController;

use ELib\User\CurrentUser;
use Empathy\Session;

class StoreControllerLite extends EController
{
  public function __construct($boot)
  {
    parent::__construct($boot);
    $this->assign('cart_items', ShoppingCart::getTotalItems());
  }


  public function filterInt($name)
  {
    if(isset($_GET[$name]))
      {
	return (int)$_GET[$name];
      }
    else
      {
	return 0;
      }
  }

  public function default_event()
  {
    $this->setTemplate('elib://store_category.tpl');
  }

  
  public function minimalLayout()
  {
    $page = $this->filterInt('page');
    $vendor_id = $this->filterInt('vendor_id');
    $category_id = $this->filterInt('id');

    $this->assign('top_cats', ProductsLayout::getTopCats());
    if(!isset($_GET['vendor_id']))
      {
	$_GET['vendor_id'] = 0;
      }
    else 
      {
	$_GET['vendor_id'] = (int)$_GET['vendor_id'];
      }
    $p = Model::load('ProductItem');
    $status = '('
      .StoreStatus::AVAILABLE.', '
      .StoreStatus::SOLD_OUT
      .')';
    $sql = ' WHERE status IN'.$status;
    if($vendor_id > 0)
      {
	$sql .= ' AND vendor_id = '.$vendor_id;
      }

    if($category_id > 0)
      {
	$cats = array();
	switch($category_id)
	  {
	  case 1:
	    $cats = array(3,7,4,5,6);
	    break;
	  case 2:
	    $cats = array(8);
	    break;
	  default:
	    break;
	  }

	$sql .= ' AND category_id IN'.$p->buildUnionString($cats);
      }

    $sql .= ' AND t1.vendor_id = t2.id';
    $sql .= ' AND t2.verified IS NOT NULL';
    
    $sql .= ' ORDER BY t1.id DESC';

    if($page < 1)
      {
	$page = 1;
      }
        
    $per_page = 4;
    $select = 't1.id,t1.image,t1.name,t1.min_price';

    $products = $p->getAllCustomPaginateSimpleJoin($select, Model::getTable('ProductItem'), Model::getTable('Vendor'), $sql, $page, $per_page);
    $p_nav = $p->getPaginatePagesSimpleJoin($select, Model::getTable('ProductItem'), Model::getTable('Vendor'), $sql, $page, $per_page); 

    $this->assign('products', $products);      
    $this->assign('p_nav', $p_nav);
    $this->assign('vendor_id', $vendor_id);
    $this->assign('vendor_id', $_GET['vendor_id']);
    $this->assign('category_id', $category_id);
  }



  public function addProductToCart($product_id)
  {      
    $options = array();
    if(isset($_POST['property']))
      {
	foreach($_POST['property'] as $option)
	  {
	    array_push($options, $option);
	  }	    
      }
    
    if(sizeof($options) > 0)
      {
	$v = Model::load('ProductVariant');
	$variant_id = $v->findVariant($options, $product_id);
      }
    else
      {	   
	$sql = ' WHERE product_id = '.$prodcut_id.' LIMIT 0, 1';
	$variant = $v->getAllCustom(Model::getTable('ProductVariant'), $sql);
	if(sizeof($variant) > 0)
	  {
	    $variant_id = $variant[0]['id'];
	  }
      }
    
    if(is_numeric($variant_id) && $variant_id > 0)
      {	    
	$sc = new ShoppingCart();
	$sc->add($variant_id, 1);		      		  
	$this->redirect('store/cart');	    
      }	    
  }



  
  public function minimalProductView()
  {
    $this->setTemplate('store_product.tpl');    	
    $p = Model::load('ProductItem');
    $p->id = $this->filterInt('id'); 
    $p->load();

    if(isset($_POST['add']))
      {       
	$this->addProductToCart($p->id);
      }

    $this->getPropertiesAndOptions($p->id, 0);	
                  	
    $this->assign('product', $p); 
  }  
  
  



  public function cart()
  {
    $this->setTemplate('cart.tpl');
    $c = new ShoppingCart();
    
    if(isset($_POST['update']))
      {
	foreach($_POST['qty'] as $v => $qty)
	  {
	    if(is_numeric($qty) && $qty > 0)
	      {
		$c->update($v, $qty);
	      }
	    elseif(is_numeric($qty) && $qty == 0)
	      {
		$c->remove($v);
	      }
	  }
	$this->redirect('store/cart');
      }
    elseif(isset($_POST['checkout']))
      {
	$this->redirect('store/checkout');
      }

    $items = $c->loadFromCart($this);
    
    if(sizeof($items) > 0)
      {
	$ids = array();
	foreach($items as $item)
	  {
	    array_push($ids, $item['id']);
	  }

	$v = Model::load('ProductVariant');
	$cat_ids = $v->getCategories($ids);
	$cat = Model::load('CategoryItem');
	$calc = new ShippingCalculator($c->calcTotal($items), $cat_ids, $cat, sizeof($items), false);
	$shipping = $calc->getFee();
	$this->assign('shipping', $shipping);
	$this->assign('total', $c->calcTotal($items) + $shipping);
	$this->assign('items', $items);
      }  
    
    $this->assign('last_cat', Session::get('last_cat'));
  }  


  
  public function checkout()
  { 
    $this->setTemplate('checkout.tpl');
    $s = Model::load('ShippingAddress');
    
    $sql = ' WHERE user_id = '.CurrentUser::getUserID().' ORDER BY id DESC';
    $addresses = $s->getAllCustom(Model::getTable('ShippingAddress'), $sql);

    $this->assign('addresses', $addresses);
    
    if(isset($_GET['checkout']))
      {    
	Session::set('shipping_address_id', $_GET['shipping_address_id']);
	$this->redirect('paypal');
      }
  }




  // taken from product admin (variant properties)
  public function getPropertiesAndOptions($product_id, $colours)
  {
    /*
    $v = new ProductVariant($this);
    $v->id = $_GET['id'];
    $v->load(ProductVariant::$table);
    */
   
    $p = Model::load('ProductItem');
    $p->id = $product_id;
    $p->load();

    $c = Model::load('CategoryItem');
    $cats = $c->getAncestorIds($p->category_id, array());

    $cp = Model::load('CategoryProperty');

    array_push($cats, $p->category_id);
    $props = $cp->getPropertiesByCategory($cats);

    if(!$colours && $p->category_id != 8)
      {
	array_push($props, 2); // always allow colour property    
      }

    //    $this->assign('product', $p);
    //$this->assign('variant', $v);


    $opts = array();
    $pv = Model::load('ProductVariantPropertyOption');
    $opts = $pv->buildUnionString($pv->getActiveOptions($p->id));
    
    if(sizeof($props) > 0)
      {
	$property = Model::load('Property');
	$properties = $property->getAllWithOptionsForProduct($props, $opts);
	$this->assign('properties', $properties);

	/*
	$pv = new ProductVariantPropertyOption($this);
	$sql = ' WHERE product_variant_id = '.$_GET['id'];
	$options = $pv->getAllCustom(ProductVariantPropertyOption::$table, $sql);
	$o = array();
	foreach($options as $index => $value)
	  {
	    array_push($o, $value['property_option_id']);
	  }
	$this->assign('options', $o);    
	*/
	
      }
  }






}
?>