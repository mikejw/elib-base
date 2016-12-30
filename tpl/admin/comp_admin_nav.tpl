

 <ul class="dropdown-menu">
   
{foreach from=$installed_libs key=route item=name}

<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/{$route}">{$name}</a></li>

{/foreach}

<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/password">Change My Password</a></li>


{*       
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/blog">Blog</a></li>
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/dsection">Generic Sections</a></li>
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/store">Store</a></li>
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/ban_dir">Banners</a></li>
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/events">Events</a></li>
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/settings">Settings</a></li>

*}
</ul>


