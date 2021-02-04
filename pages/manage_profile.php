<?php
session_start();
if(!isset($_SESSION['route']))
    die('Direct Access is forbidden');
if(!isset($_SESSION['priv']))
    die("Direct Access is forbidden");
$priv = $_SESSION['priv'];
include_once "../mysql.php";
$row = getUserInformation();
$priv = $row['priv'];
if($priv == ADMIN)
    $ustr = "Administrator";
else if($priv == PLAYER)
    $ustr = "Player";
else if($priv == PROPLAYER)
    $ustr = "Pro player";
else if($priv == (PLAYER + CREATOR))
    $ustr = "Player, Creator";
else if($priv == (PROPLAYER + CREATOR))
    $ustr = "Pro Player, Creator";
else
    $ustr = "Guest";

$upcnt = getUploadCount();
$downloadcnt = getDownloadCnt();
$favcnt= getFavCnt();

$max = max($upcnt, $downloadcnt, $favcnt);
if($max == 0) {
  $percent1 = 0;
  $percent2 = 0;
  $percent3 = 0;
}
else {
  $percent1 = $upcnt / $max * 100;
  $percent2 = $downloadcnt / $max * 100;
  $percent3 = $favcnt / $max * 100;
}
?>
<style>
    input[type="file"] {
    display: none;
}
.custom-file-upload {    
    display: inline-block;
    padding: 3px 3px;
    cursor: pointer;
    color:#ddd;
}
.card-body label{
    margin-bottom:2px;
}
.default-theme {
    background:#333;
    color:#ddd;
}
</style>
<div class='container'>  
    <div class="row gutters-sm">
      <div class="col-md-4 mb-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex flex-column align-items-center text-center">
            <form id="imageForum" action="./upload.php" method="post" enctype = "multipart/form-data" target='myframe'>
              <img src="<?php echo $row['avatar_path'];?>" alt="Admin" id='avatar_image' class="rounded-circle" width="150">
              <div style='border-radius: 25px; border:1px solid #ddd'><label for="file-upload" class="custom-file-upload">
                  <i class="fa fa-cloud-upload"></i> Change avatar
              </label>
              <input id="file-upload" accept="image/jpeg, image/png" type="file" name='file' id='file' onchange='change_avatar();'/></div>
              <div class="mt-3">
                <h4><?php echo $row['username'];?></h4>                
                <p class="text-secondary mb-1"><?php echo $ustr;?></p>               
                
                <?php if(!($priv == PROPLAYER || $priv==PROPLAYER+CREATOR || $priv == ADMIN)) :?>
                  <button id='upgrade_to_pro' class="btn btn-primary">Upgrade to Pro</button>
                <?php endif;?>
                <?php if($priv < CREATOR && $priv != ADMIN) :?>
                  <button id='upgrade_to_creator' class="btn btn-primary">Upgrade to Creator</button>
                <?php endif;?>
              </div>
              <input type='hidden' name='upload_type' value="profile_image"/>
            </form>
            </div>
          </div>
        </div>
        <div class="card mt-3">
          <ul class="list-group list-group-flush"> 
            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
              <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter mr-2 icon-inline text-info"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>Twitter</h6>
              <span class="text-secondary"><input type='text' id='twitter' type='text' class='form-control default-theme' value="<?php echo $row['twitter'];?>"/></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
              <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram mr-2 icon-inline text-danger"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>Instagram</h6>
              <span class="text-secondary"><input type='text' id='instagram' type='text' class='form-control default-theme' value="<?php echo $row['instagram'];?>"/></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
              <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook mr-2 icon-inline text-primary"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>Facebook</h6>
              <span class="text-secondary"><input type='text' id='facebook' type='text' class='form-control default-theme' value="<?php echo $row['facebook'];?>"/></span>
            </li>
            <li style='text-align:center; margin-top:15px;'>
            <button id='update_profile' class="btn btn-primary">Save profile</button>
            </li>
          </ul>
          
        </div>
      </div>
      <div class="col-md-8">
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-3">
                <h6 class="mb-0">Full Name</h6>
              </div>
              <div class="col-sm-9 text-secondary">
              <input type='text' id='username' type='text' value='<?php echo $row['username'];?>' class='form-control default-theme'/>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <h6 class="mb-0">Email</h6>
              </div>
              <div class="col-sm-9 text-secondary">
                <?php echo $row['email'];?>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <h6 class="mb-0">Phone</h6>
              </div>
              <div class="col-sm-9 text-secondary">
              <input type='text' id='phone' type='text' value='<?php echo $row['phone'];?>' class='form-control default-theme'/>
              </div>
            </div>            
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <h6 class="mb-0">New password</h6>
              </div>
              <div class="col-sm-9 text-secondary">
              <input type='text' id='pwd' type='password' class='form-control default-theme'/>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <h6 class="mb-0">Confirm</h6>
              </div>
              <div class="col-sm-9 text-secondary">
                <input type='text' id='confirm' type='password' class='form-control default-theme'/>
              </div>
            </div>
          </div>
        </div>
        <div class="row gutters-sm">
          <div class="col-sm-12 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <h6 class="d-flex align-items-center mb-3">Status</h6>
                <small>Uploaded games : <?php echo $upcnt;?></small>
                <div class="progress mb-3" style="height: 5px">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percent1;?>%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small>Favorite games :<?php echo $favcnt;?></small>
                <div class="progress mb-3" style="height: 5px">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percent3;?>%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small>Downloaded games :<?php echo $downloadcnt;?> </small>
                <div class="progress mb-3" style="height: 5px">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percent2;?>%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
 
        </div>
      </div>
    </div>
  </div>
</div>
<iframe src='./upload.php' style='width:0px;height:0px;border:0px;' id='myframe' name='myframe' onload='uploadDone();'></iframe>
<script>
function change_avatar() {
  $("#imageForum").submit();
}
function uploadDone() {
  var v = document.getElementById('myframe').contentWindow.document.body.innerHTML;
  if(v == "" || v == "0")
    return;
  $("#avatar_image").attr("src", v);
}
$("#upgrade_to_creator").click(function() {
  $("#BecomeCreator").modal("show");
});
$("#update_profile").click(function(e) {
  var phone = $("#phone").val();
  var pwd = $("#pwd").val();
  var confirm = $("#confirm").val();
  if(pwd != "" && pwd != confirm) {
    toastr['error']("Password mismatch!");
    return;
  } 
  var twitter = $("#twitter").val();
  var instagram = $("#instagram").val();
  var facebook = $("#facebook").val();
  var username = $("#username").val();
  showProgress();
  $.ajax({url: "./database.php", 
        data : {
          "do":"update_profile",
          "phone":phone,
          "pwd":pwd,
          "twitter":twitter,
          "instagram":instagram,
          "facebook":facebook,
          "username":username,
        },
        type : "post",
        success: function(result){
          hideProgress();
    }}); 
});
</script>