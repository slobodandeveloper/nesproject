<?php
@session_start();
if(!isset($_SESSION['route']))
    die('Direct Access is forbidden');
if(isset($_SESSION['priv']))
    $priv = $_SESSION['priv'];
else
    $priv = 0;
include_once "./mysql.php";
?>
<!DOCTYPE html>
<html style='font-size:16px;'>
    <head>
        <meta charset="utf-8">
        <title>NES Player</title>
        <meta name="keyword" content="chat room">
        <meta name="description" content="chat room">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/util.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/main.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/toastr.min.css">
        <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/searchbox.css">
        

        <script src="./assets/js/jquery-3.2.1.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="./assets/js/toastr.min.js"></script>
        <script src="./assets/js/choices.js"></script>        
        <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.15.0/css/all.css"/>         
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css"/> 
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.dataTables.min.css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>     
        <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
        <script type="text/javascript" src="./assets/js/jquery.bootpag.js"></script>
        <script type="text/javascript" src="assets/js/emulator.js"></script>	
    </head>
    <style>
        @media only screen and (max-width: 400px) {
            .my-logo {
                width:100%;
            }
        }
        .nav-link {
            cursor:pointer;
            padding: 5px 13px !important;      
        }
        .nav-item {
            text-align:center;
            width:90px;
        }
        .my-logo {
            padding:10px;
        }
        .input-group.md-form.form-sm.form-1 input{
        border: 1px solid #bdbdbd;
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
        }
        .input-group.md-form.form-sm.form-2 input {
        border: 1px solid #bdbdbd;
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
        }
        .input-group.md-form.form-sm.form-2 input.red-border {
        border: 1px solid #ef9a9a;
        }
        .input-group.md-form.form-sm.form-2 input.lime-border {
        border: 1px solid #cddc39;
        }
        .input-group.md-form.form-sm.form-2 input.amber-border {
        border: 1px solid #ffca28;
        }
        .input_text_field {
            height: inherit !important;
            padding: 6px  !important;
            font-size: inherit  !important;
            border-bottom: 2px solid #c0c0c0  !important;
            border-radius: 0px !important;
        }
        .fa-star.checked {
        color: orange;
        }    
        
    </style>
    <body style="">
    <nav class="navbar navbar-expand-md navbar-dark" style='background-color:black;width:100%;margin-bottom:0px;'>
        <div>    
    <img class='my-logo' id='go_home' src="./assets/img/logo.png"/></div>
        <div class="d-flex w-50 order-0">              
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
        <div class="navbar-collapse collapse justify-content-center order-2" id="collapsingNavbar">            
            <ul class="navbar-nav" style='float:right'>
                <li class="nav-item">
                    <a class="nav-link fabuttons" href='http://www.TheNew8bitHeroes.com' style=><img src='./assets/img/NESmakerLogo.png' style='height:48px;margin-top:-2px;'/><p style='font-size:12px;margin-top:-2px;'>LEARN NESMAKER</p></a>
                </li>   
                <li class="nav-item">
                    <a class="nav-link fabuttons" id='show_all'><i class='fa fa-list'></i><br>Show All Games</a>
                </li>
                <li class="nav-item" style='display:none;'>
                    <a class="nav-link fabuttons" id='show_col'><i class='fa fa-th'></i><br>Show Collections</a>
                </li>
                <?php if($priv == 0) :?>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='login_pro'><i class='fa fa-sign-in-alt'></i><br>SignIn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='signup_pro'><i class='fa fa-user-plus'></i><br>SignUp</a>
                    </li>
                <?php endif;?>
                <?php if($priv == 1) :?>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='managetags'><i class='fa fa-tag'></i><br>Tags</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='managegenres'><i class='fa fa-book'></i><br>Genres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='managegames'><i class='fa fa-gamepad'></i><br>Games</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='upload_data'><i class='fa fa-upload'></i><br>Upload</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='manageuser'><i class='fa fa-user'></i><br>Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='manageprofile'><i class='fa fa-cog'></i><br>Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='login_out'><i class='fa fa-sign-out-alt'></i><br>Logout</a>
                    </li>
                <?php endif;?>
                <?php if($priv == 2) :?>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='upload_data'><i class='fa fa-upload'></i><br>Upload</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='managegames'><i class='fa fa-gamepad'></i><br>Games</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='manageprofile'><i class='fa fa-cog'></i><br>Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fabuttons" id='login_out'><i class='fa fa-sign-out-alt'></i><br>Logout</a>
                    </li>
                <?php endif;?> 
                
            </ul>
        </div>       
    </nav>
    <?php
    $tags = getTags();
    $genres = getGenre();
    $allcount = getAllGameCount();
    ?>

<div id='main_body'>
    <?php
    require_once "./pages/show_collections.php";
    ?>
</div>
