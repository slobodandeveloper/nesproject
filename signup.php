<?php
    session_start();
    if(!isset($_SESSION['route']))
        die('Direct Access is forbidden');

?>

<div class="container-login100">
    <div class="wrap-login100 p-t-35 p-b-20">
        <div class="login100-form validate-form">
            <h3 class="m-t-20 m-b-40" style="text-align:center;">Welcome to sign up</h3>
            <div class="wrap-input100 validate-input m-t-15 m-b-35" data-validate="Enter username">
                <input class="input100" type="text" id='username' name="username">
                <span class="focus-input100" data-placeholder="User Name*"></span>
            </div>
            <div class="wrap-input100 validate-input m-t-15 m-b-35" data-validate="Enter email.">
                <input class="input100" type="text" id='email' name="email">
                <span class="focus-input100" data-placeholder="Email*"></span>
            </div>
            <div class="wrap-input100 validate-input m-b-50" data-validate="Enter Password">
                <input class="input100" type="password" id='password' name="password" >
                <span class="focus-input100" data-placeholder="Password*"></span>
            </div>
            <div class="wrap-input100 validate-input m-b-50" data-validate="Enter Password">
                <input class="input100" type="password" id='confirm' name="confirm" >
                <span class="focus-input100" data-placeholder="Confirm*"></span>
            </div>
            <div class="wrap-input101 validate-input m-b-50" data-validate="Enter Password">
                <input class="input101" type="text" id='license' name="license" >
                <span class="focus-input101" data-placeholder="Nesmaker License Code"></span>
            </div>
            <div class="wrap-input101 validate-input m-b-50" data-validate="Enter Password">
                <input class="input101" type="text" id='licensepwd' name="licensepwd" >
                <span class="focus-input101" data-placeholder="License password"></span>
            </div>
            <div class="wrap-input100 validate-input m-b-50" id='error_div' style='color:red; display:none'>
                User name or password not correct!
            </div>
     
            <div class="container-login100-form-btn">
                <button class="login100-form-btn">Sign up</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verificateModal" tabindex="-1" role="dialog"aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Check your email and input verification code </h4>
        <button type="button" class="close" id='btn_close_modal'>
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <input type="text" class="form-control" name='verificode' id='verificode' placeholder="Verification Code" value="" />
      </div>
      <div class="modal-footer">        
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='save_verification'>Submit</button>
        </div>
    </div>
  </div>
</div>
<script>
    $("#btn_close_modal").click(function(e) {
        $("#verificateModal").removeClass("in");
        $("#verificateModal").css("display","none");
    });
    $(".login100-form-btn").click(function(e) {
        var username = $("#username").val();
        var password = $("#password").val();
        var email = $("#email").val();
        var confirm = $("#confirm").val();
        var license = $("#license").val();
        var licensepwd = $("#licensepwd").val();
        if(confirm != password) {
            toastr['error']("Confirm password is not match.");
            return;
        }
        $.ajax({url: "./database.php", 
            data : {
                "do" : "signup",
                "username" : username,
                "password" : password,
                "email" : email,
                "license" : license,
                "licensepwd":licensepwd					
            },
            type : "post",
            success: function(result){   
               if(result == "0"){
                    $("#error_div").fadeIn(300);
               }    
               else if(result == '-1') {
                   toastr['error']("Invalid email address.");
               }
               else if(result == '-2') {
                   toastr['error']("Duplicated email address.");
               }
               else {
                    $("#verificateModal").addClass("in");
                    $("#verificateModal").css("display","block");
               }
        }}); 
    });
    $("#save_verification").click(function(e) {
        var ecode = $("#verificode").val();
        var email = $("#email").val();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "everification",
                "emailcode" : ecode,
                "email" : email,
            },
            type : "post",
            success: function(result){   
               if(result == "0"){
                    toastr['error']("Verification code not matched.");
                    return;
               }    
               else {
                    toastr['success']("Signup success.");
                    $("#verificateModal").removeClass("in");
                    $("#verificateModal").css("display","none");
                    location.href = "./";
               }
        }});
    });
</script>
<script src="./assets/js/loginformvalidate.js"></script>