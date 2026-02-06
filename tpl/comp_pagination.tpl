
{if count($p_nav) > 1}
    {assign var="lastPage" value=0}
    <nav aria-label="Page navigation">
        <ul class="pagination pt-2 pb-5 justify-content-end">
            <li class="page-item{if $page eq 1} disabled{/if} {if $full}flex-grow-1{/if}">
                <a class="page-link text-center" href="?page={$page - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    {if !$minimal}
                        <span class="sr-only">Previous</span>
                    {/if}
                </a>
            </li>
            {foreach from=$p_nav key=k item=v}
                <li class="page-item{if $v} disabled{/if} {if $full}flex-grow-1{/if}">
                    <a class="text-center page-link" href="?page={$k}">
                        {$k}
                    </a>
                </li>
                {assign var="lastPage" value=$k}
            {/foreach}
            <li class="page-item{if $page eq $lastPage} disabled{/if} {if $full}flex-grow-1{/if}">
                <a class="text-center page-link" href="?page={$page + 1}" aria-label="Next">
                    {if !$minimal}
                        <span class="sr-only">Next</span>
                    {/if}
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
{/if}