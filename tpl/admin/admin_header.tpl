<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{$TITLE}</title>
<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/elib/init.min.css" type="text/css" media="all" />

<link rel="stylesheet" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/elib/admin.css" type="text/css" media="all" />

<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/elib/jquery.min.js"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/common.js"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/elib/admin.js"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/elib/tiny_mce/tiny_mce.js"></script>

</head>

<body>

{*<h1 style="font-size:4em; text-transform:uppercase;"><br />{$NAME}</h1>*}
<div id="page">

<div id="admin_head">
<form action="http://{$WEB_ROOT}{$PUBLIC_DIR}/user/logout" method="post">
<p>{$current_user} <button type="submit" name="logout">Logout</button></p>
</form>

<p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}"><img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/elib/new_twitter.png" alt="" /></a></p>

{include file="elib://admin/comp_admin_breadcrumb.tpl"}

</div>


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

