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

$sql = "SELECT COUNT(*) AS cnt FROM tags WHERE 1=1";    

$result = mysqli_query($mysql_db, $sql);
$row = $result->fetch_assoc();
$allcnt = $row['cnt'];
$allpage = ($allcnt % ICON_PAGE_CNT == 0) ? (int)($allcnt / ICON_PAGE_CNT) : (int)($allcnt /ICON_PAGE_CNT) + 1;

$sql = "SELECT * FROM tags WHERE 1=1 LIMIT $start, ".ICON_PAGE_CNT;
$result = mysqli_query($mysql_db, $sql);	

?>
<style>
.fa.fa-edit,.fa.fa-trash  {
    cursor:pointer;
}
</style>
<div style='background:#fff; margin:20px; opacity:0.9;'>
    <div style='padding:20px;overflow:hidden'>
        <h3>Add / Edit / Remove tag</h3>
        <div style='margin:10px;'>
            <input type="button" class='btn' data-toggle="modal" data-target="#addTagModal" id='add_tag' value='Add new tag'/>
        </div>
        <table id="exampleg" class="table table-striped table-bordered nowrap" style='width:100%'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tag</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody id='main_table_body'>
            <?php
                $no = 0;
                while($row = $result->fetch_assoc()) {
                    $id = $row['id'];	
                    $tag = $row['tagname'];
                    $playbtn ="<a class='edit_tag' data='$id' data-toggle='modal' data-target='#addTagModal'><i class='fa fa-edit'></i></a>";
                    $delbtn ="<a class='delete_tag' data='$id'><i class='fa fa-trash'></i></a>";
                    echo "<tr id='$id'>
                    <td>$id</td>
                    <td>$tag</td>
                    <td>$playbtn</td>
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
    <input type='hidden' value='' name='hid_tagid' id='hid_tagid'/>
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
        $.ajax({url: "./pages/manage_tag.php", 
        data : {
          "pagenum" : num
        },
        type : "post",
        success: function(result){
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
    $("#addTagModal").on("click", "#save_tag", function() {
        var tagid = $("#hid_tagid").val();
        var tagname = $("#newtag").val();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "tag",
                "id":tagid,
                "name" : tagname
            },
            type : "post",
            success: function(result){
                if(tagid == "0") {
                    dtable.row.add($("<tr><td>" +result+ "</td><td>"+tagname+"</td><td><a class='edit_tag' data='"+result+"' data-toggle='modal' data-target='#addTagModal'><i class='fa fa-edit'></i></a></td><td><a class='delete_tag' data='"+result+"'><i class='fa fa-trash'></i></a></td></tr>")).draw();
                }
                else {
                    var curdata = dtable.row($("#"+tagid)).data();
                    curdata[1] = tagname;
                    dtable.row($("#"+tagid)).data(curdata).draw();
                }
                toastr['success']("Saved successfully.");                
        }});    
    });
    $(".edit_tag").on("click", function() {
        var id = $(this).attr("data");
        var curgen = $("#"+id).children(":nth-child(2)").html();
        $("#newtag").val(curgen);
        $("#hid_tagid").val(id);
    });
    $("#add_tag").on("click", function() {    
        $("#newtag").val("");    
        $("#hid_tagid").val(0);
    });
    $(".delete_tag").on("click", function() {
        var id = $(this).attr("data");        
        var pa = $(this).parents("tr");
        $.ajax({url: "./database.php", 
            data : {
                "do" : "removetag",
                "id":id
            },
            type : "post",
            success: function(result){
                dtable
                .row( pa )
                .remove()
                .draw();
                toastr['success']("Deleted successfully.");                
        }});   
    });
</script>