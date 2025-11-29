


{if !($module eq 'user' && $class eq 'user' && $event eq 'login')}
</div>
{/if}

{if !(isset($centerpage) and $centerpage)}
<footer class="mt-5">
    <div class="p-5 pb-1">
        {foreach from=$installed item=lib}
        {$lib.name} <em class="text-secondary">{$lib.version}</em>
        {/foreach}
    </div>
    <div class="p-5 pt-1">
        <a class="text-white" href="https://empathy.sh" target="_blank">emapthy.sh</a>
    </div>
</footer>
{/if}

</div>

<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/js/common.js"></script>
<script type="text/javascript" src="http://{$WEB_ROOT}{$PUBLIC_DIR}/vendor/js/main.min.js?version={$dev_rand}"></script>
</body>
</html>
