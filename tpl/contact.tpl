{include file="$EMPATHY_DIR/tpl/eheader.tpl"}



<div class="container">
    <div class="page-header">
        <h1><small>Contact</small></h1>
        <p>&nbsp;</p>
    </div>


    {if $thanks_id eq 1}
        <p>Thanks for subscribing to the email newsletter.</p>
    {elseif $thanks_id eq 2}
        <p>Thanks for contacting us.  We will endeavour to respond ASAP.</p>
    {else}

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link{if $contact_type_id eq 1} active{/if}" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/contact/1">Newsletter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link{if $contact_type_id eq 2} active{/if}" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/contact/2">email</a>
            </li>
        </ul>
        <p>&nbsp;</p>

        {if $contact_type_id eq 1}
        <form method="post">
            <h4>Sign up to email newsletter:</h4>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="firstName">First name</label>
                    <input
                       name="first_name"
                       type="text"
                       class="form-control {if isset($errors.first_name)} is-invalid{/if}"
                       id="firstName"
                       placeholder="First name"
                       value="{$contact->first_name|escape}"
                       required
                    />
                    {if isset($errors.first_name)}
                    <div class="invalid-feedback">
                        {$errors.first_name}
                    </div>
                    {/if}
                </div>
                <div class="col-md-4 mb-3">
                    <label for="lastName">Last name</label>
                    <input
                        name="last_name"
                        type="text"
                        class="form-control {if isset($errors.last_name)} is-invalid{/if}"
                        id="lastName"
                        placeholder="Last name"
                        value="{$contact->last_name|escape}"
                        required
                    />
                    {if isset($errors.last_name)}
                        <div class="invalid-feedback">
                            {$errors.last_name}
                        </div>
                    {/if}
                </div>
                <div class="col-md-4 mb-3">
                    <label for="email">email</label>
                    <input
                        name="email"
                        type="text"
                        class="form-control {if isset($errors.email)} is-invalid{/if}"
                        id="email"
                        placeholder="email"
                        value="{$contact->email}"
                        required
                    />
                    {if isset($errors.email)}
                        <div class="invalid-feedback">
                            {$errors.email}
                        </div>
                    {/if}
                </div>
            </div>
            <button class="btn btn-primary" type="submit" name="submit">Sign Up</button>
        </form>

        <hr />
        {else}

        <form method="post">
        <p>&nbsp;</p>
            <h4>Send us an email:</h4>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="firstName">First name</label>
                    <input
                            name="first_name"
                            type="text"
                            class="form-control {if isset($errors.first_name)} is-invalid{/if}"
                            id="firstName"
                            placeholder="First name"
                            value="{$contact->first_name|escape}"
                            required
                    />
                    {if isset($errors.first_name)}
                        <div class="invalid-feedback">
                            {$errors.first_name}
                        </div>
                    {/if}
                </div>
                <div class="col-md-4 mb-3">
                    <label for="lastName">Last name</label>
                    <input
                            name="last_name"
                            type="text"
                            class="form-control {if isset($errors.last_name)} is-invalid{/if}"
                            id="lastName"
                            placeholder="Last name"
                            value="{$contact->last_name|escape}"
                            required
                    />
                    {if isset($errors.last_name)}
                        <div class="invalid-feedback">
                            {$errors.last_name}
                        </div>
                    {/if}
                </div>
                <div class="col-md-4 mb-3">
                    <label for="email">email</label>
                    <input
                            name="email"
                            type="text"
                            class="form-control {if isset($errors.email)} is-invalid{/if}"
                            id="email"
                            placeholder="email"
                            value="{$contact->email}"
                            required
                    />
                    {if isset($errors.email)}
                        <div class="invalid-feedback">
                            {$errors.email}
                        </div>
                    {/if}
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-8 mb-3">
                    <label for="subject">Subject</label>
                    <input
                            name="subject"
                            type="text"
                            class="form-control {if isset($errors.subject)} is-invalid{/if}"
                            id="subject"
                            placeholder="Subject"
                            value="{$contact->subject|escape}"
                            required
                    />
                    {if isset($errors.subject)}
                        <div class="invalid-feedback">
                            {$errors.subject}
                        </div>
                    {/if}
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="body">Message</label>
                    <textarea
                            rows="10"
                            name="body"
                            class="raw form-control {if isset($errors.body)} is-invalid{/if}"
                            id="body"
                            placeholder="Message"
                            required
                    >{$contact->body|replace:"<br />":"\n"}</textarea>
                    {if isset($errors.body)}
                        <div class="invalid-feedback">
                            {$errors.body}
                        </div>
                    {/if}
                </div>
            </div>
            <button class="btn btn-primary" type="submit" name="submit_msg">Submit</button>
        </form>
        {/if}
    {/if}
</div>



{include file="$EMPATHY_DIR/tpl/efooter.tpl"}
