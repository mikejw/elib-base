{include file="elib:/admin/admin_header.tpl"}

<p>&nbsp;</p>

{if is_array($errors) && sizeof($errors)}
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

<h4>Change Password</h4>
<form action="" method="post">
    <div class="form-group">
        <label for="old_password">Exisiting Password:</label>
        <input name="old_password" type="password" class="form-control" id="old_password">
    </div>
    <div class="form-group">
        <label for="password1">New Password:</label>
        <input name="password1" type="password" class="form-control" id="password1">
    </div>
    <div class="form-group">
        <label for="password2">Password (Confirmation):</label>
        <input name="password2" type="password" class="form-control" id="password2">
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    <button type="submit" name="cancel" class="btn btn-primary">Cancel</button>
</form>

<p>&nbsp;</p>



{include file="elib:/admin/admin_footer.tpl"}