{include file="elib:/admin/admin_header.tpl"}


<form action="" method="post" class="form-signin">

    <img class="mb-4" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/empathy.png" alt="" width="70" height="70">

    <h2 class="mb-3">Please sign in</h2>
    <div class="mb-3">
        <label for="inputUsername" class="visually-hidden">Username</label>
        <input name="username" type="text" id="inputUsername" class="form-control" placeholder="Username" autofocus/>
    </div>
    <div class="mb-3">
        <label for="inputPassword" class="visually-hidden">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password"/>
    </div>
    <!--
     <div class="checkbox">
     <label>
     <input type="checkbox" value="remember-me"> Remember me
     </label>
     </div>
     -->
    <button class="mb-4 btn btn-sm btn-primary btn-block" name="login" type="submit">Sign in</button>
    <input type="hidden" name="csrf_token" value="{$csrf_token}"/>
</form>

{if isset($errors)}
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Error</strong>
        {foreach from=$errors item=e}
            <p>{$e}</p>
        {/foreach}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}



{include file="elib:admin/admin_footer.tpl"}