

<ul class="navbar-nav mr-auto">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Area
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            {foreach from=$installed_libs key=route item=name}
            <a class="dropdown-item" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/{$route}">{$name}</a>
            {/foreach}
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/password">Change Password</a>
        </div>
    </li>
</ul>


