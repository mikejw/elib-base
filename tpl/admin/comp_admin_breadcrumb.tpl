
<nav aria-label="breadcrunb">
    <ol class="breadcrumb">


        {* admin *}
        {if $class eq 'admin' && $event eq 'default_event'}Admin{else}
            <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/">Admin</a></li>{/if}



        {* store *}
        {if $class eq 'admin' && $event eq 'store'}
            <li class="breadcrumb-item">Store</li>
        {elseif $class eq 'vendors' || $class eq 'brand' || $class eq 'artist' || $class eq 'orders' || $class eq 'category' || $class eq 'properties' || $class eq 'product' || $class eq 'promo_category'}
            <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/store">Store</a></li>{/if}



        {* products *}
        {if $class eq 'category'}
            <li class="breadcrumb-item">Products</li>
        {elseif $class eq 'artist'}
            {if $event eq 'default_event'}
                <li class="breadcrumb-item">Artists</li>
            {else}
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/artist">Artists</a></li>
                {if $event neq 'add'}
                    <li class="breadcrumb-item"><a
                            href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/artist/{$artist->id}">{if $artist->artist_alias neq ''}{$artist->artist_alias}{else}{$artist->forename} {$artist->surname}{/if}</a>
                    </li>{/if}{/if}
            {if $event eq 'upload_image'}
                <li class="breadcrumb-item">Upload Image</li>
            {elseif $event eq 'add'}
                <li class="breadcrumb-item">Add New Artist</li>
            {elseif $event eq 'edit_bio'}
                <li class="breadcrumb-item">Edit Artist Biography</li>
            {/if}
        {elseif $class eq 'product'}
            <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category">Products</a></li>
            {if $event eq 'default_event'}
                <li class="breadcrumb-item">{$product->name}</li>
            {elseif $event eq 'upload_image'}
                <li class="breadcrumb-item"><a
                            href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a>
                </li>
                <li class="breadcrumb-item">Upload Image</li>
            {elseif $event eq 'edit'}
                <li class="breadcrumb-item"><a
                            href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{if isset($old_product_name)}{$old_product_name}{else}{$product->name}{/if}</a>
                </li>
                <li class="breadcrumb-item">Edit</li>
            {elseif $event eq 'edit_variant'}
                <li class="breadcrumb-item"><a
                            href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a>
                </li>
                <li class="breadcrumb-item">Edit Variant ({$variant->id})</li>
            {elseif $event eq 'upload_variant_image'}
                <li class="breadcrumb-item"><a
                            href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a>
                </li>
                <li class="breadcrumb-item">Upload Variant Image ({$variant->id})</li>
            {elseif $event eq 'variant_properties'}
                <li class="breadcrumb-item"><a
                            href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a>
                </li>
                <li class="breadcrumb-item">Variant Properties ({$variant->id})</li>
            {elseif $event eq 'resize_images'}
                <li class="breadcrumb-item">Resize Product Images</li>
            {/if}
        {elseif $class eq 'properties'}
            {if $event eq 'rename'}
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category">Products</a></li>
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/properties">Properties</a>
                </li>
                <li class="breadcrumb-item">Rename ({$property->id})</li>
            {else}
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category">Products</a></li>
                <li class="breadcrumb-item">Properties</li>
            {/if}
        {elseif $class eq 'vendors'}
            <li class="breadcrumb-item">Vendors</li>
        {elseif $class eq 'brand'}
            <li class="breadcrumb-item">Brands</li>
        {/if}


        {* blog *}
        {if $class eq 'blog'}
            {if $event eq 'default_event'}
                <li class="breadcrumb-item">Blog</li>
            {else}
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/blog">Blog</a></li>{/if}
            {if $event eq 'edit_blog'}
                <li class="breadcrumb-item">Edit Blog Item</li>
            {elseif $event eq 'create'}
                <li class="breadcrumb-item">Create Blog Item</li>
            {/if}
            {if $event eq 'view'}
                <li class="breadcrumb-item">View Blog Item</li>
            {/if}
        {elseif $class eq 'blog_cat'}
            <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/blog">Blog</a></li>
            <li class="breadcrumb-item">Edit Categories</li>
        {/if}

        {* cms *}
        {if $class eq 'dsection'}
            {if $event eq 'default_event' || $event eq 'data_item'}
                <li class="breadcrumb-item">CMS</li>
            {else}
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/dsection">CMS</a></li>
            {/if}

            {if $event eq 'containers'}
                <li class="breadcrumb-item">Edit Containers</li>
            {elseif $event eq 'rename_container'}
                <li class="breadcrumb-item">Rename Container</li>
            {elseif $event eq 'image_sizes'}
                <li class="breadcrumb-item">Edit Image Sizes</li>
            {/if}
        {/if}

        {* password *}
        {if $class eq 'admin' && $event eq 'password'}
            <li class="breadcrumb-item">
                Change Password
            </li>
        {/if}


        {* settings *}
        {if $class eq 'settings'}
            {if $event eq 'default_event'}
                <li class="breadcrumb-item">
                    SEO Settings
                </li>
            {elseif $event eq 'cache'}
                <li class="breadcrumb-item">
                    <a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/settings">SEO Settings</a>
                </li>
                <li class="breadcrumb-item">
                    Cache
                </li>
            {/if}
        {/if}


        {* events *}
        {if $class eq 'events'}
            {if $event eq 'default_event'}
                <li class="breadcrumb-item">Events</li>
            {elseif $event eq 'add_event'}
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/events">Events</a></li>
                <li class="breadcrumb-item">Add Event</li>
            {elseif $event eq 'view_event'}
                <li class="breadcrumb-item"><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/events">Events</a></li>
                <li class="breadcrumb-item">View Event</li>
            {/if}
        {/if}

    </ol>
</nav>


