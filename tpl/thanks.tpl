{include file="header.tpl"}

<div id="content_inner">

<h2>Thanks...</h2>
{if $id eq 1}
<p>...for registering. Please check your email for the confirmation link. Do not close this window if you have items in your cart.</p>
{elseif $id eq 2}
<p>...for confirming your registration. You are now logged in. Your full login details have been emailed to you.

<p><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/">Continue to home area.</a></p>

{*If you have items
in your cart you can now go through the <a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/store/checkout">checkout</a>.
*}
</p>
{/if}

</div>


{include file="footer.tpl"}