<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Empathy Admin</title>
    <link href="http://{$WEB_ROOT}{$PUBLIC_DIR}/css/style.css" rel="stylesheet">
    <link href="http://{$WEB_ROOT}{$PUBLIC_DIR}/elib/navbar.css" rel="stylesheet">
  </head>
  <body>

  <div class="container">

      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#"><img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/empathy.png" alt="" width="33" /></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          {include file="elib://admin/comp_admin_nav.tpl"}

          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/user/logout">Logout {$current_user}</a>
            </li>
          </ul>
        </div>
      </nav>

	{include file="elib://admin/comp_admin_breadcrumb.tpl"}


  {if isset($help_file)}
  <div id="help_wrapper1">
    <div id="help_wrapper2">
      <div class="grey{if $help_shown eq true} shown{/if}" id="help">
        <a href="#" id="help_tab"><span>Help</span></a>
        <div id="help_inner"><div>
            {include file=$help_file}
          </div>
        </div>
      </div>
    </div>
  </div>
  <p style="line-height: 0.5em;">&nbsp;</p>
{/if}