

<ol class="breadcrumb">
 

{* admin *}
{if $class eq 'admin' && $event eq 'default_event'}Admin{else}
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/">Admin</a></li>{/if}



{* store *}
{if $class eq 'admin' && $event eq 'store'} 
<li>Store</li>
{elseif $class eq 'vendors' || $class eq 'brand' || $class eq 'artist' || $class eq 'orders' || $class eq 'category' || $class eq 'properties' || $class eq 'product' || $class eq 'promo_category'}
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/store">Store</a></li>{/if}



{* products *}
{if $class eq 'category'} <li>Products</li>
{elseif $class eq 'artist'}
  {if $event eq 'default_event'} <li>Artists</li>
  {else} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/artist">Artists</a></li>
    {if $event neq 'add'}<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/artist/{$artist->id}">{if $artist->artist_alias neq ''}{$artist->artist_alias}{else}{$artist->forename} {$artist->surname}{/if}</a></li>{/if}{/if}
  {if $event eq 'upload_image'} <li>Upload Image</li>
  {elseif $event eq 'add'} <li>Add New Artist</li>
  {elseif $event eq 'edit_bio'} <li>Edit Artist Biography</li>
  {/if}
{elseif $class eq 'product'} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category">Products</a></li>
  {if $event eq 'default_event'} <li>{$product->name}</li>
  {elseif $event eq 'upload_image'} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a></li>
  <li>Upload Image</li>
  {elseif $event eq 'edit'} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{if isset($old_product_name)}{$old_product_name}{else}{$product->name}{/if}</a></li> <li>Edit</li>
  {elseif $event eq 'edit_variant'} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a></li>
  <li>Edit Variant ({$variant->id})</li>
  {elseif $event eq 'upload_variant_image'} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a></li>
  <li>Upload Variant Image ({$variant->id})</li>
  {elseif $event eq 'variant_properties'} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/product/{$product->id}">{$product->name}</a></li>
  <li>Variant Properties ({$variant->id})</li>
  {elseif $event eq 'resize_images'} <li>Resize Product Images</li>
  {/if}
{elseif $class eq 'properties'}
  {if $event eq 'rename'} <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category">Products</a></li>
  <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/properties">Properties</a></li>
  <li>Rename ({$property->id})</li>
  {else}
  <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/category">Products</a></li>
  <li>Properties</li>
  {/if}
{elseif $class eq 'vendors'} <li>Vendors</li>
{elseif $class eq 'brand'} <li>Brands</li>
{/if}


{* blog *}
{if $class eq 'blog'} 
  {if $event eq 'default_event'}
  <li>Blog</li>
  {else}
  <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/blog">Blog</a></li>{/if}
  {if $event eq 'edit_blog'} <li>Edit Blog Item</li>{elseif $event eq 'create'}
  <li>Create Blog Item</li>{/if}
  {if $event eq 'view'}
  <li>View Blog Item</li>{/if}
{elseif $class eq 'blog_cat'}
<li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/blog">Blog</a></li>
 <li>Edit Categories</li>
{/if}

{* cms *}
{if $class eq 'dsection'}
  {if $event eq 'default_event' || $event eq 'data_item'}
  <li>Generic Sections</li>
  {else}
  <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/dsection">Generic Sections</a></li>
  {/if}

  {if $event eq 'containers'}
  <li>Edit Containers</li>
  {elseif $event eq 'rename_container'}
  <li>Rename Container</li>
  {elseif $event eq 'image_sizes'}
  <li>Edit Image Sizes</li>
  {/if}
{/if}

{* password *}
{if $class eq 'admin' && $event eq 'password'}
 <li>
 Change My Password
 </li>
{/if}

{* events *}
{if $class eq 'events'}
{if $event eq 'default_event'}
 <li>Events</li>
{elseif $event eq 'add_event'}
 <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/events">Events</a></li>
 <li>Add Event</li>
{elseif $event eq 'view_event'}
 <li><a href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin/events">Events</a></li>
 <li>View Event</li>
{/if}
{/if}

</ol>


