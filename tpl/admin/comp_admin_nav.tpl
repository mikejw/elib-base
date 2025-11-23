

<ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Area
        </a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            {foreach from=$installed_libs key=route item=name}
            <li><a class="dropdown-item" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/{$route}">{$name}</a></li>
            {/foreach}
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/password">Change Password</a></li>
        </ul>
    </li>
</ul>


