<?php
@session_start();
if(!isset($_SESSION['route']))
    die('Direct Access is forbidden');

?>
<div style='width:80%;margin-left:10%; overflow:auto;'>		
<div style='margin:5px;position:absolute;right:10%;'>
        <a id='left_collect' class='collect-nav'><i class="fa fa-chevron-circle-left nav-left-right"></i></a>
        <a id='right_collect'  class='collect-nav'><i class="fa fa-chevron-circle-right nav-left-right"></i></a>
    </div>
<div id='collections_div' >    
</div>
</div>
<input type='hidden' id='collect-page' name='collect-page'/>	
<script>
    var curpage = 0;
    function adjust_pictures() {
        var w = $("#collections_div").width();
        var h = $("#collections_div").height();        
        $.ajax({url: "./database.php", 
            data : {
                "do" : "load_collections",
                "w" : w,
                "h":h,
                "page" : curpage
            },
            type : "post",
            success: function(result){                
                $("#collections_div").html(result);              
                
        }});
    }
    $(document).ready(function() {	        
        adjust_pictures();
        window.addEventListener("resize", adjust_pictures);
    } );    
    $("#left_collect").on("click", function() {
        curpage--;
        var w = $("#collections_div").width();
        var h = $("#collections_div").height();        
        $.ajax({url: "./database.php", 
            data : {
                "do" : "load_collections",
                "w" : w,
                "h":h,
                "page" : curpage
            },
            type : "post",
            success: function(result){                
                $("#collections_div").html(result);              
                
        }});
    });
    $("#right_collect").on("click", function() {
        curpage++;
        var w = $("#collections_div").width();
        var h = $("#collections_div").height();        
        $.ajax({url: "./database.php", 
            data : {
                "do" : "load_collections",
                "w" : w,
                "h":h,
                "page" : curpage
            },
            type : "post",
            success: function(result){                
                $("#collections_div").html(result);              
                
        }});
    });
    
    
</script>
