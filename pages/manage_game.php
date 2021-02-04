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

$con = "";
if($priv == "1")
    $con = " 1=1";            
else {
    $user_id = $_SESSION['user_id'];
    $con = " user_id='$user_id'";
}

$sql = "SELECT COUNT(*) AS cnt FROM games WHERE $con";    

$result = mysqli_query($mysql_db, $sql);
$row = $result->fetch_assoc();
$allcnt = $row['cnt'];
$allpage = ($allcnt % ICON_PAGE_CNT == 0) ? (int)($allcnt / ICON_PAGE_CNT) : (int)($allcnt /ICON_PAGE_CNT) + 1;

$sql = "SELECT games.demo_rom, games.gamename, games.promo_image, games.description, tag, genre.genrename AS genre, games.credits, games.developer_name, games.rating, games.ID, games.date FROM games INNER JOIN genre ON games.genre=genre.id WHERE $con LIMIT $start, ".ICON_PAGE_CNT;

$result = mysqli_query($mysql_db, $sql);	

?>
<style>
.fa.fa-edit,.fa.fa-trash {
    cursor:pointer;
}
</style>
<div style='margin:20px; opacity:0.9;'>
    <div style='padding:20px;overflow:hidden'>
        <table id="exampleg" class="table table-striped table-bordered nowrap" style='width:100%'>
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
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody id='main_table_body'>
            <?php
                $no = 0;
                while($row = $result->fetch_assoc()) {
                    $demo_rom = $row['demo_rom'];	
                    $game_name = $row['gamename'];
                    $promo_image = $row['promo_image'];
                    $description = $row['description'];
                    $tag = $row['tag'];
                    $genre = $row['genre'];
                    $credit = $row['credits'];

                    $credit = str_replace("\n","<br>",$credit);

                    $description = str_replace("\n","<br>",$description);
                    
                    $tagarr = explode(",",$tag);
                    $tagstr = "";
                    foreach($tagarr as $tag) {
                        $sql = "SELECT * FROM tags WHERE id='$tag'";
                        $result1 = mysqli_query($mysql_db, $sql);
                        $row1 = $result1->fetch_assoc();

                        if(isset($row1['tagname']))
                            $tagstr .= $row1['tagname'];            
                    }
                    
                    $developer = $row['developer_name'];
                    $rating = $row['rating'];
                    $id = $row['ID'];
                    $id = base64_encode($id);
                    $playbtn ="<a class='edit_game' data='$id'><i class='fa fa-edit'></i></a>";
                    $delbtn ="<a class='delete_game' data='$id'><i class='fa fa-trash'></i></a>";
                    $date = $row['date'];
                    $ratstr = calcRating($rating);
                    echo "<tr>
                    <td>$game_name</td>
                    <td>$description</td>
                    <td>$tagstr</td>
                    <td>$genre</td>
                    <td>$date</td>
                    <td>$credit</td>
                    <td>$developer</td>
                    <td>$ratstr</td>
                    <td>$playbtn</td>
                    <td>$delbtn</td>
                    </tr>";
                    $no++;
                }
            ?>
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
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </tfoot>
        </table>
        <div class='pagination-bar'>
        <p id="pagination-table"></p>
        </div>
    </div>
    <input type='hidden' value='<?php echo $page;?>' name='current_page' id='current_page'/>
</div>
<script>
    $(document).ready(function() {				
        dtable = $('#exampleg').DataTable({
            responsive:true,
            select:true,
            "paging":   false,
            "searching": false,
            "bInfo" : false
        });
    } );
    $('#pagination-table').bootpag({
        total: <?php echo $allpage;?>,          // total pages
        page: <?php echo $page+1;?>,            // default page
        maxVisible: 5,     // visible pagination
        leaps: true         // next/prev leaps through maxVisible
    }).on("page", function(event, num){        	
        num = num -1;
        showProgress();
        $.ajax({url: "./pages/manage_game.php", 
        data : {
          "pagenum" : num
        },
        type : "post",
        success: function(result){
            hideProgress();
          $("#main_body").html(result);	
          $(window).trigger('resize');  
        }});    
    });
    $(".edit_game").on("click", function() {
        var id = $(this).attr("data");
        var num =  $("#current_page").val();
        showProgress();
        $.ajax({url: "./pages/edit_game.php", 
            data : {
                "id":id,
                "pagenum" : num
            },
            type : "post",
            success: function(result){
                hideProgress();
                $("#main_body").html(result);	
                
        }});    
    });
    $(".delete_game").on("click", function() {
        var id = $(this).attr("data");        
        var pa = $(this).parents("tr");
        showProgress();
        $.ajax({url: "./database.php", 
            data : {
                "do" : "removegame",
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