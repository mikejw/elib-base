{include file="elib:/admin/admin_header.tpl"}


   <form action="" method="post" class="form-signin">

        <img class="mb-4" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/empathy.png" alt="" width="70" height="70">

        <h2 class="h3 mb-3 font-weight-normal">Please sign in</h2>
        <label for="inputUsername" class="sr-only">Username</label>
        <input name="username" type="text" id="inputUsername" class="form-control" placeholder="Username" autofocus />
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" />
        <div class="checkbox">
        <!--  <label>
        <input type="checkbox" value="remember-me"> Remember me
        </label> -->
        </div>
        <button class="btn btn-sm btn-primary btn-block" name="login" type="submit">Sign in</button>
        <input type="hidden" name="csrf_token" value="{$csrf_token}" />

       {if isset($errors)}

           <p>&nbsp;</p>
           <div class="alert alert-danger alert-dismissible fade show" role="alert">
               <strong>Error!</strong>
               {foreach from=$errors item=e}
                   <p>{$e}</p>
               {/foreach}
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
               </button>
           </div>
       {/if}
    </form>



{include file="elib:/admin/admin_footer.tpl"}