<?php

namespace ELib\Store;
use ELib\Model;

define('REQUESTS_PER_PAGE', 12);

class PromoCategoryController extends AdminController
{
  public function assertID()
  {
    if(!isset($_GET['id'] ) || !is_numeric($_GET['id']))
      {
	$_GET['id'] = 0;
      }
  }

  public function buildNav()
  {
    //$this->assertID();
    if(!isset($_GET['collapsed']) || !is_numeric($_GET['collapsed']))
      {
	$_GET['collapsed'] = 0;
      }

    $c = Model::load('CategoryItem');
    $c->id = $_GET['id'];
    $c->load();

    $ct = new PromosTree($c, $_GET['collapsed']);
    
    $this->presenter->assign('category', $c);
    $this->presenter->assign('category_has_children', $c->hasChildren());

    $this->presenter->assign('nav', $ct->getMarkup());

    $b = Model::load('BrandItem');
    $this->presenter->assign('brands', $b->getBrands());
  }

  public function default_event()
  {
    $this->setTemplate('elib://admin/promo_category.tpl');
    $ui_array = array('order_by', 'page', 'id');
    $this->loadUIVars('ui_catalogue', $ui_array);
    if(!isset($_GET['page']) || $_GET['page'] == '')
      {
	$_GET['page'] = 1;
      }
    if(!isset($_GET['id']) || $_GET['id'] == '')
      {
	$_GET['id'] = 0;
      }   
    if(!isset($_GET['order_by']) || $_GET['order_by'] == '')
      {
	$_GET['order_by'] = 'id';
      }    


    $this->presenter->assign('order_by', $_GET['order_by']);
    $this->presenter->assign('page', $_GET['page']);
    $this->presenter->assign('category_id', $_GET['id']);

    $this->buildNav();

    if(isset($_GET['id']) && is_numeric($_GET['id']))
    {
      $showCat = $_GET['id'];
    }
    else
    {
      $showCat = 0;
    }
    
    $sql = '';    
    $sql .= 'WHERE category_id = '.$_GET['id'].' ';
	
    $sql .= 'ORDER BY '.$_GET['order_by'];

    $p = Model::load('PromoItem');

    $p_nav = $p->getPaginatePages(Model::getTable('PromoItem'), $sql, $_GET['page'], REQUESTS_PER_PAGE);
    $this->presenter->assign('p_nav', $p_nav);
    $promo = $p->getAllCustomPaginate(Model::getTable('PromoItem'), $sql, $_GET['page'], REQUESTS_PER_PAGE);    

    $c = Model::load('CategoryItem');
    $c->id = $_GET['id'];
    $category = $c->loadIndexed($c->category_id);
    
    /*
    foreach($product as $index => $item)
      {
	// get stock count
	$stock = new StockItem($this);
	$s_count = $stock->getAllCustom(StockItem::$table, 'WHERE product_variant_id = '.$product[$index]['id']);
	$product[$index]['stock'] = sizeof($s_count);

	//$category_id = $product[$index]['category_id'];
	//$product[$index]['category'] = $category[$category_id];
      }
    */

    $this->presenter->assign("promos", $promo);
  }

  public function add()
  {
    if(isset($_GET['id']) && is_numeric($_GET['id']))
      {
	$p = Model::load('PromoItem');
	$p->category_id = $_GET['id'];
	$p->name = 'New Promo';
	$p->hidden = 'DEFAULT';
	$id = $p->insert(Model::getTable('PromoItem'), 1, array(), 0);	
      }
    $this->redirect('admin/promo_category/'.$_GET['id']);
  }


}
?>