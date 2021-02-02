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
$user_id = $_SESSION['user_id'];

$r = inputParam('r');
$sql = "SELECT COUNT(*) AS cnt FROM ratings WHERE game_id='$r' AND user_id='$user_id'";
$result = mysqli_query($mysql_db, $sql);
$row = $result->fetch_assoc();
$exist = $row['cnt'];

$sql = "SELECT COUNT(*) AS cnt FROM ratings WHERE game_id='$r'";
$result = mysqli_query($mysql_db, $sql);
$row = $result->fetch_assoc();
$allcnt = $row['cnt'];

$allpage = ($allcnt % ICON_PAGE_CNT == 0) ? (int)($allcnt / ICON_PAGE_CNT) : (int)($allcnt /ICON_PAGE_CNT) + 1;

$sql = "SELECT users.ID, users.username, users.avatar_path, ratings.rating, ratings.comment FROM ratings INNER JOIN users ON users.id=ratings.user_id WHERE game_id='$r' LIMIT $start, ".ICON_PAGE_CNT;
$result = mysqli_query($mysql_db, $sql);
?>
<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
<style amp-custom>
  .rating {
    --star-size: 3;  /* use CSS variables to calculate dependent dimensions later */
    padding: 0;  /* to prevent flicker when mousing over padding */
    border: none;  /* to prevent flicker when mousing over border */
    unicode-bidi: bidi-override; direction: rtl;  /* for CSS-only style change on hover */
    text-align: left;  /* revert the RTL direction */
    user-select: none;  /* disable mouse/touch selection */
    font-size: 3em;  /* fallback - IE doesn't support CSS variables */
    font-size: calc(var(--star-size) * 1em);  /* because `var(--star-size)em` would be too good to be true */
    cursor: pointer;
    /* disable touch feedback on cursor: pointer - http://stackoverflow.com/q/25704650/1269037 */
    -webkit-tap-highlight-color: rgba(0,0,0,0);
    -webkit-tap-highlight-color: transparent;
    margin-bottom: 1em;
  }
  /* the stars */
  .rating > label {
    display: inline-block;
    position: relative;
    width: 1.1em;  /* magic number to overlap the radio buttons on top of the stars */
    width: calc(var(--star-size) / 3 * 1.1em);
  }
  .rating > *:hover,
  .rating > *:hover ~ label,
  .rating:not(:hover) > input:checked ~ label {
    color: transparent;  /* reveal the contour/white star from the HTML markup */
    cursor: inherit;  /* avoid a cursor transition from arrow/pointer to text selection */
  }
  .rating > *:hover:before,
  .rating > *:hover ~ label:before,
  .rating:not(:hover) > input:checked ~ label:before {
    content: "★";
    position: absolute;
    left: 0;
    color: gold;
  }
  .rating > input {
    position: relative;
    transform: scale(3);  /* make the radio buttons big; they don't inherit font-size */
    transform: scale(var(--star-size));
    /* the magic numbers below correlate with the font-size */
    top: -0.5em;  /* margin-top doesn't work */
    top: calc(var(--star-size) / 6 * -1em);
    margin-left: -2.5em;  /* overlap the radio buttons exactly under the stars */
    margin-left: calc(var(--star-size) / 6 * -5em);
    z-index: 2;  /* bring the button above the stars so it captures touches/clicks */
    opacity: 0;  /* comment to see where the radio buttons are */
    font-size: initial; /* reset to default */
  }
  form.amp-form-submit-error [submit-error] {
    color: red;
  }

</style>
<div style='background:#222;opacity:0.8;min-height:350px;overflow:hidden' class='container'>
    <div style='overflow:hidden'>
        <h3  style='color:#ddd'>Ratings and comment</h3>
        <?php
        $no = 0;
        while($row = $result->fetch_assoc()) {
            $id = $row['ID'];
            $username = $row['username'];
            $avatar = $row['avatar_path'];
            $rating = $row['rating'];
            $comment = $row['comment'];
            $rating = calcRating($rating);
            echo "<hr><div class='row' style='padding:0 20px;' >
                <div class='col-md-3 col-sm-3'><div>
                <label style='color:#ddd'>User : $username</label><br>
                <img src='$avatar' style='width:100px;height:100px;border-radius:50%;'/></div>
                </div>
                <div class='col-md-3 col-sm-3'>
                <label style='color:#ddd'>Rating</label><div style='font-size:20px'>
                $rating</div>
                </div>
                <div class='col-md-6 col-sm-6'>
                <label style='color:#ddd'>Comment</label><div style='font-size:20px; color:#ddd'>
                $comment</div>
                </div>
            </div>";
            $no++;
        }
        if($no == 0) {
            echo "<div style='color:#ddd'>There are no rating and comment yet.</div>";
        }
        ?>
        <div class='pagination-bar'>
        <p id="pagination-table"></p>
        </div>
    </div>    
    <?php if($exist == 0) :?>
        <hr>
    <div class='myfeedback'>
        <h4 style='color:#ddd'>My rating</h4>
        <div class='col-md-5 col-sm-5'>
            <label style='color:#ddd;'>Game Score</label>
            <form id="rating"
                method="post"
                target="_blank">
                <fieldset class="rating">
                    <input name="rating"
                    type="radio"
                    id="rating5"
                    value="5">
                    <label for="rating5"
                    title="5 stars">☆</label>

                    <input name="rating"
                    type="radio"
                    id="rating4"
                    value="4">
                    <label for="rating4"
                    title="4 stars">☆</label>

                    <input name="rating"
                    type="radio"
                    id="rating3"
                    value="3">
                    <label for="rating3"
                    title="3 stars">☆</label>

                    <input name="rating"
                    type="radio"
                    id="rating2"
                    value="2"
                    checked="checked">
                    <label for="rating2"
                    title="2 stars">☆</label>

                    <input name="rating"
                    type="radio"
                    id="rating1"
                    value="1">
                    <label for="rating1"
                    title="1 stars">☆</label>
                </fieldset> 
            </form>
        </div>
        <div class='col-md-7 col-sm-7'>
            <label style='color:#ddd;'>Comment</label>
            <div>
            <textarea class='form-control' id='comments' style='background: #333;color: #ddd;'></textarea>
            </div>
        </div>
        <div style='float:right;margin-top:15px;'>
        <a class='btn btn-secondary' id='returnToGame' style='color:#eee'>Back to game</a>
        <a class='btn btn-primary' id='btnPublish' style='color:#eee'>Publish</a>
        </div>
    </div>  
    <?php endif;?>
</div>

<script>
    $(document).ready(function() {				

    });
    $("#btnPublish").on("click", function() {        
        var comment = $("#comments").val();  
        var i;
        var score = 0;
        for(i = 1; i <= 5; i+=1) {
            var sib = window.getComputedStyle($("#rating"+i).next()[0], ':before').content;
            if(sib != "none")
                score++;
        }
        var gid = <?php echo $r;?>;
        $.ajax({url: "./database.php", 
        data : {
          "do" : "rating",
          "gid" : gid,
          "score":score,
          "comment":comment
        },
        type : "post",
        success: function(result){
            toastr['info']("Successfully saved.");
        }});
    });
    function reloadpage(num) {
        $.ajax({url: "./pages/rating.php", 
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
    
</script>