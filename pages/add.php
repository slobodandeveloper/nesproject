<?php
session_start();
if(!isset($_SESSION['route']))
    die('Direct Access is forbidden');
if(!isset($_SESSION['priv']))
    die("Direct Access is forbidden");
$priv = $_SESSION['priv'];
?>
<style>
    .bootstrap-tagsinput {
        width:100%;
    }
</style>
<div class="register" style='margin-top:0px'>
    <div class="row">
        <div class="col-md-3 register-left">
            <img src="https://image.ibb.co/n7oTvU/logo_white.png" alt=""/>
            <h3>Welcome</h3>
            <p>You can upload .nes files here.</p>
        </div>
        <div class="loader" id='loading_indicator' style='display:none'></div>
        <div class="col-md-9 register-right">            
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h3 class="register-heading">Game information</h3>
                    <form id="imageForum" action="./upload.php" method="post" enctype = "multipart/form-data" target='myframe'>
                        <div class="row register-form" style='text-align:left'>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Game Name *" value="" name='game_name' id='game_name'/>
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="full_rom" name="full_rom">
                                        <label class="custom-file-label" for="customFile">Full ROM *</label>
                                    </div>                              
                                </div>
                                <div class="form-group">
                                    <input type="email" pattern=".+@globex.com" name='email' id='email' class="form-control" placeholder="Email *" value="" />
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control"  name='description' id='description' placeholder="Description"></textarea>
                                </div>
                                <div class="form-group"> 
                                    <textarea class="form-control"  name='credit' id='credit' placeholder="Credits"></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" accept="image/jpeg, image/png" class="custom-file-input" name='promo_image' id='promo_image'>
                                        <label class="custom-file-label" for="customFile">Promo Image *</label>
                                    </div>                           
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" accept="image/jpeg, image/png" class="custom-file-input" name='screenshot1' id='screenshot1'>
                                        <label class="custom-file-label" for="customFile">ScreenShot1</label>
                                    </div>                              
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" accept="image/jpeg, image/png" class="custom-file-input" name='screenshot2' id='screenshot2'>
                                        <label class="custom-file-label" for="customFile">ScreenShot2</label>
                                    </div>                               
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" accept="image/jpeg, image/png" class="custom-file-input" name='screenshot3' id='screenshot3'>
                                        <label class="custom-file-label" for="customFile">ScreenShot3</label>
                                    </div>                               
                                </div>                            
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name='demo_rom' id='demo_rom'>
                                        <label class="custom-file-label" for="customFile">Demo ROM *</label>
                                    </div>                            
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name='developer_name' id='developer_name' placeholder="Developer Name *" value="" />
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name='external_link' id='external_link' placeholder="External Link " value="" />
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name='genre' id='genre' placeholder="Genre *">
                                        <option disabled selected value="">Genre</option>
                                        <option value="ACTION">ACTION</option>
                                        <option value="ADVENTURE">ADVENTURE</option>
                                        <option value="RPG">RPG</option>
                                        <option value="SPORTS">SPORTS</option>
                                        <option value="STRATEGY">STRATEGY</option>
                                        <option value="SHOOTER">SHOOTER</option>
                                        <option value="VERSUS">VERSUS</option>
                                        <option value="OTHER">OTHER</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type='text' name='tags' id='tags' placeholder="Tags" data-role="tagsinput">
                                        
                                    </input>
                                </div>                                
                                <div class="form-group">
                                    <input type="text" name='vlink' id='vlink' class="form-control" placeholder="Gameplay video link" value="" />
                                </div>
                                <div class="form-group">
                                    <div class="maxl">
                                        Cartridge Available&nbsp;&nbsp;                                    
                                        <label class="radio inline"  style='float:right;margin-top:inherit;padding-left:10px;'> 
                                            <input type="radio" name="cartridge" value="0" checked>
                                            <span> No </span> 
                                        </label>
                                        <label class="radio inline"  style='float:right;margin-top:inherit'> 
                                            <input type="radio" name="cartridge" value="1">
                                            <span> Yes </span> &nbsp;&nbsp;&nbsp;
                                        </label>    
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="maxl">
                                        Free full game&nbsp;&nbsp;                                    
                                        <label class="radio inline"  style='float:right;margin-top:inherit;padding-left:10px;'> 
                                            <input type="radio" name="fullgame" value="0" checked>
                                            <span> No </span>
                                        </label>
                                        <label class="radio inline"  style='float:right;margin-top:inherit'> 
                                            <input type="radio" name="fullgame" value="1">
                                            <span> Yes </span>  &nbsp;&nbsp;&nbsp;
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="maxl">
                                        Demo only&nbsp;&nbsp;                                    
                                        <label class="radio inline"  style='float:right;margin-top:inherit;padding-left:10px;'> 
                                            <input type="radio" name="demoonly" value="0">
                                            <span> No </span> 
                                        </label><label class="radio inline" style='float:right;margin-top:inherit'> 
                                            <input type="radio" name="demoonly" value="1" checked>
                                            <span> Yes </span> &nbsp;&nbsp;&nbsp;
                                        </label>
                                    </div>
                                </div>
                                <input class="btnRegister" onclick='submitForm();' value="Publish"/>
                            </div>
                        </div>
                    </form>
                    <iframe src='./upload.php' style='width:0px;height:0px' id='myframe' name='myframe' onload='uploadDone();'></iframe>
                </div>                
            </div>
        </div>
    </div>
</div>
<script src="./assets/js/bootstrap-tagsinput.js"></script>
<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-tagsinput.css">
<script>
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
    function uploadDone() {
        v = document.getElementById('myframe').contentWindow.document.body.innerHTML;
        $("#loading_indicator").css("display","none"); 
        if(v == "1") {               
            toastr['success']("Uploaded");
            $("#game_name").val("");
        }
        else if(v == "-1") {
            toastr['error']("Select .nes file.");
        }
    }
    function submitForm() {
        var gamename = $("#game_name").val();
        var fullromex = $("#full_rom")[0].files.length;
        var demoromex = $("#demo_rom")[0].files.length;
        var email = $("#email").val();
        var description = $("#description").val();
        var promoex = $("#promo_image")[0].files.length;
        var scr1ex = $("#screenshot1")[0].files.length;
        var scr2ex = $("#screenshot2")[0].files.length;
        var scr3ex = $("#screenshot3")[0].files.length;
        var devname = $("#developer_name").val();
        var extlink = $("#external_link").val();
        var genre = $("#genre").val();
        var tag = $("#tags").val();
        var credit = $("#credit").val();
        var vlink = $("#vlink").val();

        if(gamename == "") {
            toastr['error']("You have to input game name correctly.");
            return;
        }        

        if(fullromex == 0 && demoromex == 0) {
            toastr['error']("You have to select .nes file");
            return;
        }  
        if(email == "") {
            toastr['error']("You have to input email.");
            return;
        } 
        if(promoex == 0) {
            toastr['error']("You have to select promo image.");
            return;
        }
        if(devname == 0) {
            toastr['error']("You have to input developer name.");
            return;
        } 

        if(genre == 0) {
            toastr['error']("You have to select genre.");
            return;
        } 
        $("#loading_indicator").css("display","block");
        document.getElementById("imageForum").submit();
    }
</script>