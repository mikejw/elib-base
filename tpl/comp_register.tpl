


<h2>Register</h2>

<p>
    Please note: Usernames are "twitter style". A maximum of 15 characters. Numbers, letters and underscores are
    allowed. You can add more shipping addresses later.
</p>

<form action="" method="post">
    <input type="hidden" name="supply_address" value="1" />
    <fieldset>
        <legend>Account</legend>
        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control {if isset($errors.username)}is-invalid{elseif $submitted}is-valid{/if}" id="username" type="text" name="username" value="{$user->username}" />
            {if isset($errors.username)}
            <div class="invalid-feedback">
                {$errors.username}
            </div>
            {/if}
        </div>
        <div class="form-group">
            <label form="eamil">email address</label>
            <input class="form-control {if isset($errors.email)}is-invalid{elseif $submitted}is-valid{/if}" id="email" type="text" name="email" value="{$user->email}" />
            {if isset($errors.email)}
            <div class="invalid-feedback">
                {$errors.email}
            </div>
            {/if}
        </div>
    </fieldset>
    <fieldset>
        <legend>Shipping Address</legend>
        <div class="form-group">
            <label for="first_name">Firstname(s)</label>
            <input class="form-control {if isset($errors.first_name)}is-invalid{elseif $submitted}is-valid{/if}" id="first_name" type="text" name="first_name" value="{$address->first_name}" />
            {if isset($errors.first_name)}
                <div class="invalid-feedback">
                    {$errors.first_name}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <label for="last_name">Lastname</label>
            <input class="form-control {if isset($errors.last_name)}is-invalid{elseif $submitted}is-valid{/if}" id="last_name" type="text" name="last_name" value="{$address->last_name}" />
            {if isset($errors.last_name)}
                <div class="invalid-feedback">
                    {$errors.last_name}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <label for="address1">First Line of Address</label>
            <input class="form-control {if isset($errors.address1)}is-invalid{elseif $submitted}is-valid{/if}" id="addreess1" type="text" name="address1" value="{$address->address1}" />
            {if isset($errors.address1)}
                <div class="invalid-feedback">
                    {$errors.address1}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <label for="address2">Second Line of Address (Optional)</label>
            <input class="form-control {if isset($errors.address2)}is-invalid{elseif $submitted}is-valid{/if}" id="address2" type="text" name="address2" value="{$address->address2}" />
            {if isset($errors.address2)}
                <div class="invalid-feedback">
                    {$errors.address2}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input class="form-control {if isset($errors.city)}is-invalid{elseif $submitted}is-valid{/if}" id="city" type="text" name="city" value="{$address->city}" />
            {if isset($errors.city)}
                <div class="invalid-feedback">
                    {$errors.city}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <label for="county">County / State</label>
            <input class="form-control {if isset($errors.state)}is-invalid{elseif $submitted}is-valid{/if}" id="county" type="text" name="state" value="{$address->state}" />
            {if isset($errors.state)}
                <div class="invalid-feedback">
                    {$errors.state}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <label for="zip">Post Code / Zip</label>
            <input class="form-control {if isset($errors.zip)}is-invalid{elseif $submitted}is-valid{/if}" id="zip" type="text" name="zip" value="{$address->zip}" />
            {if isset($errors.zip)}
                <div class="invalid-feedback">
                    {$errors.zip}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <label for="country">Country</label>
            <select id="country" name="country" class="form-control {if isset($errors.country)}is-invalid{elseif $submitted}is-valid{/if}">
                {html_options options=$countries selected=$sc}
            </select>
            {if isset($errors.country)}
                <div class="invalid-feedback">
                    {$errors.country}
                </div>
            {/if}
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-sm" type="submit" name="submit">Submit</button>
        </div>
</fieldset>
</form>


