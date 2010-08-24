<?php

namespace ELib;
use ELib\Model;
use Empathy\Session;

class AdminController extends EController
{	
  public function __construct($boot)
  {
    parent::__construct($boot);  

    $u = Model::load('UserItem');
           
    $user_id = Session::get('user_id');

    if(is_numeric($user_id) && $user_id > 0)
      {
	$u->id = $user_id;
	$u->load();
	$this->presenter->assign('current_user', $u->username);	
      }
           
    if($this->module == "admin" &&
       ($user_id < 1 || !$u->getAuth($user_id)))
      {      
	$this->redirect("user/login");
      }
    else
      {       
	$this->detectHelp();
      }     
  }

  public function detectHelp()
  {
    if(!Session::get('help_shown'))
      {
	Session::set('help_shown', false);
      }

    $this->presenter->assign('help_shown', Session::get('help_shown'));

    $help_file = 'admin_help/'.$this->class.'_'.$this->event.'.tpl';
    if(file_exists(DOC_ROOT.'/presentation/'.$help_file)
       || file_exists(Util::getLocation().'/tpl/'.$help_file))
      {
	$help_file = 'elib:/'.$help_file;
        $this->presenter->assign('help_file', $help_file);
      }
  }


  public function default_event()
  {        
    $this->setTemplate('elib:/admin/admin.tpl');
  }

  public function store()
  {
    $this->setTemplate('elib:/admin/store.tpl');
  }
  

  
  public function password()
  {
    $this->setTemplate('elib:/admin/password.tpl');
    if(isset($_POST['submit']))
      {
	$errors = array();
	$old_password = md5(SALT.$_POST['old_password'].SALT);
	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];
	
	$u = Model::load('UserItem');
	$u->id = Session::get('user_id');
	$u->load();

	if($old_password != $u->password)
	  {
	    array_push($errors, 'The existing password you have entered is not correct');
	  }

	if($password1 != $password2)
	  {
	    array_push($errors, 'The new password entered does not match the confirmation');
	  }
	
	if(!ctype_alnum($password1) || !ctype_alnum($password2))
	  {
	    array_push($errors, 'Please only use alpha and numeric characters for new passwords');
	  }

	if(sizeof($errors) < 1)
	  {
	    $u->password = md5(SALT.$password1.SALT);
	    $u->save(Model::getTable('UserItem'), array(), 0);
	    $this->redirect('admin');
	  }
	else
	  {
	    $this->presenter->assign('error', $errors);
	  }
      }
    elseif(isset($_POST['cancel']))
      {
	$this->redirect('admin');
      }
  }

  public function toggle_help()
  {
    $help_shown = Session::get('help_shown');
    if($help_shown)
      {
	Session::set('help_shown', false);
      }
    else
      {
	Session::set('help_shown', true);
      }
    echo json_encode(1);
    exit();
  }









}
?>