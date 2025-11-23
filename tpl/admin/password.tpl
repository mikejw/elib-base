{include file="elib:/admin/admin_header.tpl"}


{if is_array($errors) && count($errors)}
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Error!</strong>
        {foreach from=$errors item=e}
            <p>{$e}</p>
        {/foreach}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}

<h2 class="mt-5 mb-4">Change Password</h2>

<form action="" method="post">
    <div class="mb-3">
        <label for="old_password" class="form-label">Exisiting Password:</label>
        <input name="old_password" type="password" class="form-control form-control-lg" id="old_password">
    </div>
    <div class="mb-3">
        <label for="password1" class="form-label">New Password:</label>
        <input name="password1" type="password" class="form-control form-control-lg" id="password1">
    </div>
    <div class="mb-4">
        <label for="password2" class="form-label">Password (Confirmation):</label>
        <input name="password2" type="password" class="form-control form-control-lg" id="password2">
    </div>
    <div class="mb-4">
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        <button type="submit" name="cancel" class="btn btn-primary">Cancel</button>
    </div>
</form>

<p>&nbsp;</p>



{include file="elib:/admin/admin_footer.tpl"}