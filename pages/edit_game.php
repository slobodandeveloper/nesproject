<?php
session_start();
if(!isset($_SESSION['route']))
    die('Direct Access is forbidden');
if(!isset($_SESSION['priv']))
    die("Direct Access is forbidden");
$priv = $_SESSION['priv'];
include_once "../mysql.php";
$id = inputParam('id');
$page = inputParam('pagenum');
$id = base64_decode($id);
$id = (int)($id);
$sql = "SELECT * FROM games WHERE ID='$id'";
$result = mysqli_query($mysql_db, $sql);
$row = $result->fetch_assoc();

$cartridge_available = $row['cartridge_available'];
$free_full_game = $row['free_full_game'];
$demo_only = $row['demo_only'];

$tag = $row['tag'];
$genre = $row['genre'];
$sql = "SELECT * FROM tags WHERE id='$tag'";
$result1 = mysqli_query($mysql_db, $sql);
$row1 = $result1->fetch_assoc();
$tag = $row1['tagname'];

$sql = "SELECT * FROM genre WHERE id='$genre'";
$result2 = mysqli_query($mysql_db, $sql);
$row2 = $result2->fetch_assoc();
$genre = $row2['genrename'];

$sql = "SELECT * FROM genre WHERE 1=1";
$result2 = mysqli_query($mysql_db, $sql);
?>
<style>
    .bootstrap-tagsinput {
        width:100%;
    }
    #back_to_game{
        cursor:pointer;
    }
</style>
<div class="register" style='margin-top:0px'>
    <div class="row">
        <div class="col-md-3 register-left">
            <img src="https://image.ibb.co/n7oTvU/logo_white.png" alt=""/>
            <h3>Welcome</h3>
            <p>You can modify game information here or <span id='back_to_game'>back to game list<span></p>
        </div>
        <div class="loader" id='loading_indicator' style='display:none'></div>
        <div class="col-md-9 register-right">            
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h3 class="register-heading">Game information</h3>
                    <form id="imageForum" action="./upload.php" method="post" enctype = "multipart/form-data" target='myframe'>
                        <input type='hidden' value='<?php echo $id;?>' id='hid_gid' name='hid_gid'/>
                        <div class="row register-form" style='text-align:left'>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Game Name *" name='game_name' id='game_name' value="<?php echo $row['gamename'];?>"/>
                                </div>
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="full_rom" name="full_rom">
                                        <label class="custom-file-label" for="customFile">Full ROM *</label>
                                    </div>                              
                                </div>
                                <div class="form-group">
                                    <input type="email" pattern=".+@globex.com" name='email' id='email' class="form-control" placeholder="Email" value="<?php echo $row['developer_email'];?>" />
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control"  name='description' id='description' placeholder="Description"><?php echo $row['description'];?></textarea>
                                </div>
                                <div class="form-group">


                                    <textarea class="form-control"  name='credit' id='credit' placeholder="Credits"><?php echo $row['credits'];?></textarea>

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
                                    <input type="text" class="form-control" name='developer_name' id='developer_name' placeholder="Developer Name *" value="<?php echo $row['developer_name'];?>" />
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name='external_link' id='external_link' placeholder="External Link " value="<?php echo $row['external_link'];?>" />
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name='genre' id='genre' placeholder="Genre *">
                                    <option disabled value="">Genre</option>
                                    <?php
                                    while($row2 = $result2->fetch_assoc()) {
                                        $val = $row2['genrename'];
                                        if($val == $genre) {
                                            echo "<option value='$val' selected>$val</option>";
                                        }
                                        else {
                                            echo "<option value='$val'>$val</option>";
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type='text' name='tags' id='tags' placeholder="Tags" data-role="tagsinput" value="<?php echo $tag;?>">
                                        
                                    </input>
                                </div>                                
                                <div class="form-group">
                                    <input type="text" name='vlink' id='vlink' class="form-control" placeholder="Gameplay video link" value="<?php echo $row['video_link'];?>" />
                                </div>
                                <div class="form-group">
                                    <div class="maxl">
                                        Cartridge Available&nbsp;&nbsp;                                    
                                        <label class="radio inline"  style='float:right;margin-top:inherit;padding-left:10px;'> 
                                            <?php if($cartridge_available == "0"):?>
                                                <input type="radio" name="cartridge" value="0" checked>
                                            <?php else:?>
                                                <input type="radio" name="cartridge" value="0">
                                            <?php endif;?>
                                            <span> No </span> 
                                        </label>
                                        <label class="radio inline"  style='float:right;margin-top:inherit'> 
                                        <?php if($cartridge_available == "1"):?>
                                            <input type="radio" name="cartridge" value="1" checked>
                                        <?php else:?>
                                            <input type="radio" name="cartridge" value="1">
                                        <?php endif;?>
                                            <span> Yes </span> &nbsp;&nbsp;&nbsp;
                                        </label>    
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="maxl">
                                        Free full game&nbsp;&nbsp;                                    
                                        <label class="radio inline"  style='float:right;margin-top:inherit;padding-left:10px;'> 
                                        <?php if($free_full_game == "0"):?>
                                            <input type="radio" name="fullgame" value="0" checked>
                                        <?php else:?>
                                            <input type="radio" name="fullgame" value="0">
                                        <?php endif;?>
                                            <span> No </span>
                                        </label>
                                        <label class="radio inline"  style='float:right;margin-top:inherit'> 
                                        <?php if($free_full_game == "1"):?>
                                            <input type="radio" name="fullgame" value="1" checked>
                                        <?php else:?>
                                            <input type="radio" name="fullgame" value="1">
                                        <?php endif;?>
                                            <span> Yes </span>  &nbsp;&nbsp;&nbsp;
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="maxl">
                                        Demo only&nbsp;&nbsp;                                    
                                        <label class="radio inline"  style='float:right;margin-top:inherit;padding-left:10px;'> 
                                        <?php if($demo_only == "0"):?>
                                            <input type="radio" name="demoonly" value="0" checked>
                                        <?php else:?>
                                            <input type="radio" name="demoonly" value="0">
                                        <?php endif;?>
                                            <span> No </span> 
                                        </label><label class="radio inline" style='float:right;margin-top:inherit'> 
                                        <?php if($demo_only == "1"):?>
                                            <input type="radio" name="demoonly" value="1" checked>
                                        <?php else:?>
                                            <input type="radio" name="demoonly" value="1">
                                        <?php endif;?>
                                            <span> Yes </span> &nbsp;&nbsp;&nbsp;
                                        </label>
                                    </div>
                                </div>
                                <input class="btnRegister" onclick='submitForm();' value="Save"/>
                            </div>
                        </div>
                    </form>
                    <iframe src='./upload.php' style='width:0px;height:0px' id='myframe' name='myframe' onload='uploadDone();'></iframe>
                </div>                
            </div>
        </div>
    </div>
</div>
<input type='hidden' value='<?php echo $page;?>' name='hid_page' id='hid_page'/>
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
    $("#back_to_game").on("click", function() {
        var num = $("#hid_page").val();
        $.ajax({url: "./pages/manage_game.php", 
        data : {
          "pagenum" : num
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);	
          $(window).trigger('resize');  
        }})
    });
    function submitForm() {
         var gamename = $("#game_name").val();

        var email = $("#email").val();
        var description = $("#description").val();

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

        if(email == "") {
            toastr['error']("You have to input email.");
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