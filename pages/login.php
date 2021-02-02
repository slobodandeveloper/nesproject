<?php
    session_start();
    if(!isset($_SESSION['route']))
        die('Direct Access is forbidden');

?>

<div class="container-login100">
    <div class="wrap-login100 p-t-35 p-b-20">
        <div class="login100-form validate-form">
            <h3 class="m-t-20 m-b-40" style="text-align:center;">Welcome to NES Player</h3>
            <div class="wrap-input100 validate-input m-t-15 m-b-35" data-validate="Enter email address">
                <input class="input100" type="text" id='email'>
                <span class="focus-input100" data-placeholder="Email address"></span>
            </div>
            <div class="wrap-input100 validate-input m-b-50" data-validate="Enter Password">
                <input class="input100" type="password" id='password' name="password" >
                <span class="focus-input100" data-placeholder="Password"></span>
            </div>
            
            <div class="wrap-input100 validate-input m-b-50" id='error_div' style='color:red; display:none'>
                
            </div>
     
            <div class="container-login100-form-btn">
                <button class="login100-form-btn">Login</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(".login100-form-btn").click(function(e) {
        var username = $("#email").val();
        var password = $("#password").val();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "login",
                "username" : username,
                "password" : password					
            },
            type : "post",
            success: function(result){   
               if(result == "0"){
                $("#error_div").html("Email address or password not correct!");
                    $("#error_div").fadeIn(300);
               }
               else if(result == "-1") {
                    $("#error_div").html("Account is banned.Please contact support team.");
                    $("#error_div").fadeIn(300);               
               }
               else if(result == "-3") {
                    $("#error_div").html("Invalid email address.");
                    $("#error_div").fadeIn(300);               
               }
               else if(result == "-2") {
                $("#error_div").html("Please verify email.");
                $("#error_div").fadeIn(300);   
                $("#verificateModal").modal("show");
               }
               else {
                   location.href = "./";
               }
        }}); 
    });
</script>
<script src="./assets/js/loginformvalidate.js"></script>
