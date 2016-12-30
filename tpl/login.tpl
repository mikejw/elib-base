{include file="elib://login_header.tpl"}



   <form action="" method="post" class="form-signin">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Username</label>
        <input name="username" type="text" id="inputEmail" class="form-control" placeholder="Email address" autofocus />
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" />
        <div class="checkbox">
         <!--  <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label> -->
        </div>
        <button class="btn btn-lg btn-primary btn-block" name="login" type="submit">Sign in</button>
    </form>


    {if isset($errors)}

	<div class="alert alert-danger alert-dismissible" role="alert">
  	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  	<strong>Error!</strong>
  		{foreach from=$errors item=e} 
  			<p>{$e}</p>
  		{/foreach}
	</div>

    {/if}




{include file="elib://login_footer.tpl"}