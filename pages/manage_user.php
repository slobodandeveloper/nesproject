<?php
session_start();
if(!isset($_SESSION['route']))
    die('Direct Access is forbidden');
if(!isset($_SESSION['priv']))
    die("Direct Access is forbidden");
$priv = $_SESSION['priv'];
include_once "../mysql.php";

$page = inputParam('pagenum');
$page = (int)($page);
$start = $page * ICON_PAGE_CNT;

$sql = "SELECT COUNT(*) AS cnt FROM users WHERE 1=1";    

$result = mysqli_query($mysql_db, $sql);
$row = $result->fetch_assoc();
$allcnt = $row['cnt'];
$allpage = ($allcnt % ICON_PAGE_CNT == 0) ? (int)($allcnt / ICON_PAGE_CNT) : (int)($allcnt /ICON_PAGE_CNT) + 1;

$sql = "SELECT * FROM users WHERE 1=1 LIMIT $start, ".ICON_PAGE_CNT;
$result = mysqli_query($mysql_db, $sql);	

?>
<style>
.fa.fa-edit,.fa.fa-trash  {
    cursor:pointer;
}
</style>
<div style='opacity:0.9;' class='container'>
    <div style='padding:20px;overflow:hidden'>
        <h3  style='color:#ddd'>Registered Users</h3>
        <table id="exampleg" class="table table-striped table-bordered nowrap" style='width:100%'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User name</th>
                    <th>Email</th>
                    <th>Privilege</th>
                    <th>Status</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody id='main_table_body'>
            <?php
                $no = 1;
                $str1 = array(1=>"Administrator",2=>"Player",4=>"Pro Player",8=>"Player, Creator",16=>"ProPlayer, Creator");
                $str2 = array(1=>"Normal",2=>"Banned");

                while($row = $result->fetch_assoc()) {
                    $id = $row['ID'];	
                    $username = $row['username'];
                    $email = $row['email'];
                    $priv = $row['priv'];
                    $status = $row['status'];
                    $avatar_path = $row['avatar_path'];

                    $stropt="<select id='prv-$id' class='form-control' onchange='changePriv($id);'>";
                    foreach($str1 as $key=>$value) {
                        if($key == $priv) {
                            $stropt.= "<option selected value='$key'>$value</option>";
                        }
                        else {
                            $stropt.= "<option value='$key'>$value</option>";
                        }
                    }
                    $stropt.="</select>";

                    $strstat="<select id='stt-$id' class='form-control' onchange='changeStat($id)'>";
                    foreach($str2 as $key=>$value) {
                        if($key == $status) {
                            $strstat.= "<option selected value='$key'>$value</option>";
                        }
                        else {
                            $strstat.= "<option value='$key'>$value</option>";
                        }
                    }
                    $strstat.="</select>";
                    
                    $delbtn ="<a class='delete_user' data='$id'><i class='fa fa-trash'></i></a>";
                    echo "<tr id='$id'>
                    <td>$no</td>
                    <td>$username</td>
                    <td>$email</td>
                    <td>$stropt</td>
                    <td>$strstat</td>
                    <td>$delbtn</td>                    
                    </tr>";
                    $no++;
                }
            ?>
            </tbody>
        </table>
        <div class='pagination-bar'>
        <p id="pagination-table"></p>
        </div>
    </div>
    <input type='hidden' value='<?php echo $page;?>' name='current_page' id='current_page'/>
    <input type='hidden' value='' name='hid_genid' id='hid_genid'/>
</div>
<script>
    var dtable = null;
    $(document).ready(function() {				
        dtable = $('#exampleg').DataTable({
            responsive:true,
            select:true,
            "paging":   false,
            "searching": false,
            "bInfo" : false
        });
    } );
    function reloadpage(num) {
        showProgress();
        $.ajax({url: "./pages/manage_user.php", 
        data : {
          "pagenum" : num
        },
        type : "post",
        success: function(result){
            hideProgress();
          $("#main_body").html(result);	
          $(window).trigger('resize');  
        }});   
    }
    $('#pagination-table').bootpag({
        total: <?php echo $allpage;?>,          // total pages
        page: <?php echo $page+1;?>,            // default page
        maxVisible: 5,     // visible pagination
        leaps: true         // next/prev leaps through maxVisible
    }).on("page", function(event, num){        	
        num = num -1;
        reloadpage(num); 
    });
    function changePriv(id) {
        var newpriv = $("#prv-"+id).val();
        showProgress();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "changepriv",
                "id":id,
                "prv":newpriv
            },
            type : "post",
            success: function(result){
                hideProgress();
                toastr['success']("Successfully saved.");    
        }});   
    }
    function changeStat(id) {
        var newstt = $("#stt-"+id).val();
        showProgress();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "changestt",
                "id":id,
                "stt":newstt
            },
            type : "post",
            success: function(result){ 
                hideProgress();              
                toastr['success']("Successfully saved.");                
        }});   
    }
    $(".delete_user").on("click", function() {
        var id = $(this).attr("data");        
        var pa = $(this).parents("tr");
        showProgress();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "removeuser",
                "id":id
            },
            type : "post",
            success: function(result){
                dtable
                .row( pa )
                .remove()
                .draw();
                hideProgress();
                toastr['success']("Deleted successfully.");                
        }});   
    });
</script>