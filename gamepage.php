<?php
    session_start();
    include_once "mysql.php";
    if(isset($_SESSION['priv']))
        $priv = $_SESSION['priv'];
    else
        $priv = 0;

    $packrom = $rom = $mysql_db->real_escape_string($_POST['rom']);
    $rom = base64_decode($rom);
    $sql = "SELECT * FROM games WHERE ID='$rom'";
    $result = mysqli_query($mysql_db, $sql);
    $row = $result->fetch_assoc();

    /*
$original = imagecreatefromjpeg("ORIGINAL.jpg");
$resized = imagecreatetruecolor(NEW WIDTH, NEW HEIGHT);
imagecopyresampled($resized, $original, 0, 0, 0, 0, NEW WIDTH, NEW HEIGHT, WIDTH, HEIGHT);
imagejpeg($resized, "RESIZED.jpg");
*/
    $tag = $row['tag'];
    $genre = $row['genre'];
    $date = $row['date'];
    $yy = explode("-",$date)[0];

    $tagarr = explode(",",$tag);
    $tagstr = "";
    foreach($tagarr as $tag) {
        $sql = "SELECT * FROM tags WHERE id='$tag'";
        $result1 = mysqli_query($mysql_db, $sql);
        $row1 = $result1->fetch_assoc();

        if(isset($row1['tagname']))
            $tagstr .= $row1['tagname'].",";            
    }
    if(strlen($tagstr) > 0)
        $tagstr = substr($tagstr, 0, strlen($tagstr) - 1);

    $sql = "SELECT * FROM genre WHERE id='$genre'";
    $result2 = mysqli_query($mysql_db, $sql);
    $row2 = $result2->fetch_assoc();
    
    if(isset($row2['genrename']))
        $genre = $row2['genrename'];
    else
        $genre = "";

    $gamename = $row['gamename'];
    $developer = $row['developer_name'];
    $rating = $row['rating'];
    $rating = calcRating($rating);

    $screenshot1 = $row['screenshot1'];
    $screenshot2 = $row['screenshot2'];
    $screenshot3 = $row['screenshot3'];
    $description = $row['description'];
    $externallink = $row['external_link'];
    $credits = $row['credits'];

    $credits = str_replace("\n","<br>",$credits);
    $description = str_replace("\n","<br>",$description);
       
    $cart_avail = $row['cartridge_available'];
    $demo_only = $row['demo_only'];
    $free_full_game = $row['free_full_game'];
    $demo_rom = $row['demo_rom'];
    $full_rom = $row['full_rom'];
    $rompath = ($full_rom == "") ? $demo_rom : $full_rom;

?>
<input type='hidden' id='hid_path' value='<?php echo $rompath;?>'/>
<input type='hidden' id='hid_rid' value='<?php echo $rom;?>'/>
<div class='row'>
    <div class='col-md-5 col-76812' style='padding:30px;'>
        <div>
        <span style='font-size:32px;' class='game-font'><?php echo $gamename;?></span>&nbsp;&nbsp;
            <div class="dropdown">
                <button onclick="myFunction()" class="dropbtn"><i class='fa fa-angle-down'></i>&nbsp;Options</button>
                <div id="myDropdown" class="dropdown-content">
                <?php if($priv != 0):?>
                    <a id='ratefeedback'>Rate & feedback</a>
                    <a id='addtofavor'>Add to favorite</a>
                    <a id='sendReport'>Report</a>
                    <a>Share with Social</a>
                <?php endif;?>
                    <a id='getGameLink'>Get game link</a>
                </div>
            </div>
        </div>
        <div style='padding-top:5px;'>
        <span style='font-size:16px;' class='game-font'><?php echo $developer.", ".$yy;?></span>
        </div>
        <div style='padding-top:5px;'>
        <span style='font-size:20px;'><?php echo $rating;?></span>
        </div>
        <div  style='padding-top:5px;'>
        <?php if($screenshot1 != ""):?>
        <img src='<?php echo $screenshot1;?>' style='padding:5px;width:130px;height:130px'/>
        <?php endif;?>
        <?php if($screenshot2 != ""):?>
        <img src='<?php echo $screenshot2;?>' style='padding:5px;width:130px;height:130px'/>
        <?php endif;?>
        <?php if($screenshot3 != ""):?>
        <img src='<?php echo $screenshot3;?>' style='padding:5px;width:130px;height:130px'/>
        <?php endif;?>
        </div>
        <div style='padding-top:10px;'>
            <span style='font-size:18px;' class='game-font'><?php echo $genre;?></span>            
        </div>
        <div style='padding-top:5px;'>
        <span style='font-size:16px;' class='game-font'><?php echo $description;?></span>
        </div>
        <?php if($credits != ""):?>
        <div style='padding-top:10px;'>
            <span style='font-size:18px;' class='game-font'>CREDITS:</span>
        </div>
        <div style='padding-top:5px;'>
            <span style='font-size:16px;' class='game-font'><?php echo $credits;?></span>
        </div>
        <?php endif;?>
        <?php if($externallink != "") :?>
        <div style='padding-top:10px;'>
            <span style='font-size:18px;' class='game-font'>FIND OUT MORE ABOUT THIS TITLE AT:</span>
        </div>
        <div>
            <span style='font-size:16px;' class='game-font'><?php echo $externallink;?></span>
        </div>
        <?php endif;?>
        <?php if($cart_avail == "1"):?>
        <div style='padding-top:10px;'>
            <input type='button' class='btn btn-danger' value='GET CARTRIDGE' style='color:black;width:190px;'/>
        </div>
        <?php endif;?>
        <?php if($free_full_game == "1"):?>
        <div style='padding-top:10px;'>
            <input type='button' class='btn' id='get_full' value='DOWNLOAD GAME' style='background:green;color:black;width:190px;'/>
        </div> 
        <?php endif;?>       
        <?php if($free_full_game != "1" && $demo_only == "1"):?>
        <div style='padding-top:10px;'>
            <input type='button' class='btn' id='get_demo' value='DOWNLOAD DEMO ROM' style='background:green;color:black;width:190px;'/>
        </div>    
        <?php endif;?>
        <div style='padding-top:10px;'>
            <span style='font-size:18px;' class='game-font'>TAGS:</span>
        </div>
        <div style='padding-top:5px;'>
            <span style='font-size:14px;' class='game-font'><?php echo $tagstr;?></span>
        </div>
    </div>
    <div class="emulator">
        <canvas class="nes" width='512' height='480'></canvas>
        <div class="controls">
            <!-- <div title="Open NES ROM file" class="button open">
                <input class="rom-file" type="file" accept=".nes" onchange="loadfile(this)" />
                <img src="assets/img/open.svg">
            </div> -->
            <div title="Toggle pause" class="button pause" onclick="togglePause()" style="margin-right: auto">
                <img class="pause-state" src="assets/img/pause.svg">
            </div>
            <i class="fas fa-volume-off"></i>
            <input type="range" min="0" max="100" value="50" oninput="emu.volume(this.value / 100); config.volume = this.value; save(); this.focus()">
            
            <div title="Full screen" class="button" onclick="fullscreen()">
                <img src="assets/img/fullscreen.svg">
            </div>
        </div>
    </div>
    <iframe id='download_frame' style='width:0px;height:0px; border:0px;'></iframe>       
</div>
<script>
    $("#get_full").on("click", function() {
        $("#download_frame").attr("src","./download.php?g=full&i=<?php echo $packrom;?>");
    });
    $("#get_demo").on("click", function() {
        $("#download_frame").attr("src","./download.php?g=demo&i=<?php echo $packrom;?>");
    });
    $("#ratefeedback").on("click", function() {
<?php if($priv == PROPLAYER || $priv == PROPLAYER+CREATOR || $priv == ADMIN):?>
        var r = $("#hid_rid").val();
        $.ajax({url: "./pages/rating.php", 
        data : {
            "r" : r,
        },
        type : "post",
        success: function(result){
            $("#main_body").html(result);	
        }});         
<?php else:?>
    toastr['error']("Please become pro player.");    
<?php endif;?>
    });
    $("#getGameLink").on("click", function() {
        var r = $("#hid_rid").val();
        $.ajax({url: "./database.php", 
        data : {
            "do" : "getgamelink",
            "val" : r
        },
        type : "post",
        success: function(result){
            $("#game_link").val(result);	
            $("#GameLinkModal").modal("show");
        }});         
    })
    $("#sendReport").on("click", function() {
        $("#ReportModal").modal("show");               
    })
    $("#addtofavor").on("click", function() {
        $("#QuestionModal").modal("show");               
    })
    function myFunction() {
        document.getElementById("myDropdown").classList.toggle("show");
    }

    // Close the dropdown menu if the user clicks outside of it
    window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
        }
        }
    }
    }
</script>
