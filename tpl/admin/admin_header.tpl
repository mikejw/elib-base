<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Empathy Admin</title>

    <link href="http://{$WEB_ROOT}{$PUBLIC_DIR}/vendor/css.css" rel="stylesheet">
    <link href="navbar.css" rel="stylesheet">
  </head>

  <body>

<div class="container">



      <!-- Static navbar -->
      <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin">Empathy Admin</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li {if $module eq 'admin' && $class eq 'admin' && $event eq 'default_event'}class="active"{/if}><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin">Home</a></li>

              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
               	 {include file="elib://admin/comp_admin_nav.tpl"}
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">              
              <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/user/logout">Logout {$current_user}</a></li>
              
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </nav>



	{include file="elib://admin/comp_admin_breadcrumb.tpl"}


