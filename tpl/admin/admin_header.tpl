<!DOCTYPE html>
<html lang="en" class="{if isset($centerpage) and $centerpage}centerpage{/if}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>Empathy Admin</title>
    <link href="http://{$WEB_ROOT}{$PUBLIC_DIR}/vendor/css/style.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pattaya&display=swap" rel="stylesheet">
</head>
<body>


{if !($module eq 'user' && $class eq 'user' && $event eq 'login')}
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/admin">
            <img src="http://{$WEB_ROOT}{$PUBLIC_DIR}/img/empathy.png" alt="" width="33"/>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {include file="elib://admin/comp_admin_nav.tpl"}

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {if !isset($help_file)}}disabled{/if}" href="#"
                       data-bs-toggle="modal" data-bs-target="#helpModal"
                    >
                        <i class="far fa-question-circle"></i>
                        Help
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://{$WEB_ROOT}{$PUBLIC_DIR}/user/logout">Logout {$current_user}</a>
                </li>
            </ul>
        </div>
    </nav>

    {include file="elib://admin/comp_admin_breadcrumb.tpl"}


    {if isset($help_file)}
        <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            <i class="far fa-question-circle"></i>
                            Help
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {include file=$help_file}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/if}