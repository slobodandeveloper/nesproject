<?php
@session_start();
if(!isset($_SESSION['route']))
    die('Direct Access is forbidden');
include_once "../mysql.php";
$iconpages = getIconPagesCount();
$tablepages = getTablePagesCount();	
$allcount = getAllGameCount();
?>
<div style='width:80%;margin-left:10%; background:#000;opacity:0.8;overflow:auto;'>
    <div class="s007">
        <form>
        <div class="inner-form">
            <div class="basic-search">
            <div class="input-field">
                <div class="icon-wrap">              
                <svg style='cursor:pointer' id='basic_search_btn' version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M18.869 19.162l-5.943-6.484c1.339-1.401 2.075-3.233 2.075-5.178 0-2.003-0.78-3.887-2.197-5.303s-3.3-2.197-5.303-2.197-3.887 0.78-5.303 2.197-2.197 3.3-2.197 5.303 0.78 3.887 2.197 5.303 3.3 2.197 5.303 2.197c1.726 0 3.362-0.579 4.688-1.645l5.943 6.483c0.099 0.108 0.233 0.162 0.369 0.162 0.121 0 0.242-0.043 0.338-0.131 0.204-0.187 0.217-0.503 0.031-0.706zM1 7.5c0-3.584 2.916-6.5 6.5-6.5s6.5 2.916 6.5 6.5-2.916 6.5-6.5 6.5-6.5-2.916-6.5-6.5z"></path>
                </svg>               
                </div>
                <input type="text" id='search_name'  placeholder="Search game..." />
                <div class="result-count">
                <span id='searched_count'><?php echo $allcount;?></span>results 
                <i class='fa fa-cog' style='padding:5px;cursor:pointer;font-size: 20px;' data-toggle="modal" data-target="#exampleModal" ></i>               
                </div>
            </div>
            </div>          
        </div>
        </form>
    </div>
    <div class="select-game">		
        <div style='position:relative;margin-top:10px;overflow: hidden;' id="icon_view_div">
        
            <div id='icon_views' style='overflow: hidden ;min-height:450px;'>
            </div>
            <div class='pagination-bar'>
                <p id="pagination-here"></p>
            </div>
        </div>		
        <div style='background:#fff; margin:20px; opacity:0.9; display:none' id='table_view_div'>
            <div style='padding:20px;overflow:hidden;background:#333;color:#eee;'>
                <table id="example" class="table table-striped table-bordered nowrap" style='width:100%'>
                    <thead>
                        <tr>
                            <th>Game name</th>
                            <th>Description</th>
                            <th>Tags</th>
                            <th>Genre</th>
                            <th>Publish date</th>
                            <th>Credits</th>
                            <th>Developer</th>
                            <th>Rating</th>
                            <th>Play</th>
                        </tr>
                    </thead>
                    <tbody id='main_table_body'>				
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Game name</th>
                            <th>Description</th>
                            <th>Tags</th>
                            <th>Genre</th>
                            <th>Publish date</th>
                            <th>Credit</th>
                            <th>Developer</th>
                            <th>Rating</th>
                            <th>Play</th>
                        </tr>
                    </tfoot>
                </table>
                <div class='pagination-bar'>
                <p id="pagination-table"></p>
                </div>
            </div>
        </div>
        <input type='hidden' id='hid_num' name='hid_num' value='1'/>
        <input type='hidden' id='hid_dsc' name='hid_dsc'/>
        <input type='hidden' id='hid_name' name='hid_name'/>
        <input type='hidden' id='hid_tag' name='hid_tag'/>
        <input type='hidden' id='hid_cre' name='hid_cre'/>
        <input type='hidden' id='hid_gen' name='hid_gen'/>
        <input type='hidden' id='hid_dev' name='hid_dev'/>
        <input type='hidden' id='hid_sort' name='hid_sort'/>	
    </div>
</div>
<script>
    var dtable = null;
    var editor; // use a global for the submit and return data rendering in the examples
    $(document).ready(function() {	
        
        $('.nes').on('click', function() { if (!emu.isPlaying()) { $('[type=file]').click(); } });
        dtable = $('#example').DataTable({
            responsive:true,
            select:true,
            "paging":   false,
            "searching": false,
            "bInfo" : false
        });

        load_icon_page(0);
    } );
    
    function load_icon_page(num) {			
        description = $("#hid_dsc").val();
        name = $("#hid_name").val();
        tag = $("#hid_tag").val();
        credits = $("#hid_cre").val();
        genre = $("#hid_gen").val();
        console.log(genre); 
        developer=$("#hid_dev").val();
        sort=$("#hid_sort").val();
        var w = $("#icon_view_div").width();
        showProgress();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "load_icon",
                "pagenum" : num,
                "description":description,
                "name":name,
                "tag":tag,
                "credits":credits,
                "genre":genre,
                "developer":developer,
                "sort":sort,
                "w":w
            },
            type : "post",
            success: function(result){
                hideProgress();
                $("#icon_views").html(result);
                var val = $("#hid_allp").val();
                var cval = $("#hid_allc").val();
                $("#searched_count").html(cval);
                let curpage = $("#hid_num").val();
                if(curpage > cval)
                    $curpage = cval;

                $('#pagination-here').bootpag({total: val, page:curpage});
        }});
    }
    function load_table_page(num) {	
        description = $("#hid_dsc").val();
        name = $("#hid_name").val();
        tag = $("#hid_tag").val();
        credits = $("#hid_cre").val();
        genre = $("#hid_gen").val();
        developer=$("#hid_dev").val();
        sort=$("#hid_sort").val();
        showProgress();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "load_table",
                "pagenum" : num,
                "description":description,
                "name":name,
                "tag":tag,
                "credits":credits,
                "genre":genre,
                "developer":developer,
                "sort":sort
            },
            type : "post",
            success: function(result){	
                hideProgress();				
                dtable.clear().draw();
                dtable.rows.add($(result)).draw();
                window.scrollTo(0,document.body.scrollHeight);
                let cval = $(".table-tr:first-child").attr("allc");
                let val = $(".table-tr:first-child").attr("allp");
                let curpage = $("#hid_num").val();
                if(curpage > cval)
                    $curpage = cval;

                $("#searched_count").html(cval);
                
                $('#pagination-table').bootpag({total: val, page:curpage});
        }});
    }
    $('#pagination-here').bootpag({
        total: <?php echo $iconpages;?>,          // total pages
        page: 1,            // default page
        maxVisible: 5,     // visible pagination
        leaps: true         // next/prev leaps through maxVisible
    }).on("page", function(event, num){
        $("#hid_num").val(num);	
        num = num -1;

        load_icon_page(num);				
        //$(this).bootpag({total: 10, maxVisible: 10});
    });
    $('#pagination-table').bootpag({
        total: <?php echo $tablepages;?>,          // total pages
        page: 1,            // default page
        maxVisible: 5,     // visible pagination
        leaps: true         // next/prev leaps through maxVisible
    }).on("page", function(event, num){
        $("#hid_num").val(num);		
        num = num -1;

        load_table_page(num);			
        
        //$(this).bootpag({total: 10, maxVisible: 10});
    });
    $("#icon_view_div").on('click', '.select-picture',function() {
        var rom = $(this).attr("data");
        showProgress();
        $.ajax({url: "./gamepage.php", 
            data : {
                "rom" : rom					
            },
            type : "post",
            success: function(result){
                hideProgress();
                $("#main_body").html(result);	
                let rom_path = $("#hid_path").val();
                loadfileName(rom_path);				
        }});       
    })
    
    $("#table_view_div").on('click', '.play-btn',function() {
        var rom = $(this).attr("data");
        showProgress();
        $.ajax({url: "./gamepage.php", 
            data : {
                "rom" : rom					
            },
            type : "post",
            success: function(result){
                hideProgress();
                $("#main_body").html(result);	
                let rom_path = $("#hid_path").val();
                loadfileName(rom_path);				
        }});       
    })
</script>
