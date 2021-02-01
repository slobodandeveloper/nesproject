<?php
    session_start();
    if(!isset($_SESSION['route']))
        die('Direct Access is forbidden');

include "mysql.php";
if (!empty($_POST))
{   
    $gid = inputParam('hid_gid');
    $demorom = $_FILES['demo_rom']['name'];    
    $demolink = "";
    if($demorom != "") {
        $ext = pathinfo($demorom, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if($ext != "nes")
            die("-1");
        $demolink = "./uploads/ROM/".uniqId().uniqId().uniqId().".".$ext;
        move_uploaded_file($_FILES['demo_rom']['tmp_name'], $demolink);
    }
    $fullrom = $_FILES['full_rom']['name'];    
    $fulllink="";
    if($fullrom != "") {
        $ext = pathinfo($fullrom, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        if($ext != "nes")
            die("-1");
        $fulllink = "./uploads/ROM/".uniqId().uniqId().uniqId().".".$ext;
        move_uploaded_file($_FILES['full_rom']['tmp_name'], $fulllink);
    }
    $promo_image = $_FILES['promo_image']['name'];    
    $promolink="";
    if($promo_image != "") {
        $ext = pathinfo($promo_image, PATHINFO_EXTENSION);
        $promolink = "./uploads/Image/".uniqId().uniqId().uniqId().".".$ext;

        list($width, $height) = getimagesize($_FILES['promo_image']['tmp_name']);
        if(strtolower($ext) == "png")
            $original = imagecreatefrompng($_FILES['promo_image']['tmp_name']);
        else
            $original = imagecreatefromjpeg($_FILES['promo_image']['tmp_name']);

        $resized = imagecreatetruecolor(150, 150);
        imagecopyresampled($resized, $original, 0, 0, 0, 0,150 ,150, $width, $height);
        if(strtolower($ext) == "png")
            imagepng($resized, $promolink);
        else
            imagejpeg($resized, $promolink);
        //move_uploaded_file($_FILES['promo_image']['tmp_name'], $promolink);
    }
    $screenshot1 = $_FILES['screenshot1']['name'];    
    $shot1="";
    if($screenshot1 != "") {
        $ext = pathinfo($screenshot1, PATHINFO_EXTENSION);
        $shot1 = "./uploads/Image/".uniqId().uniqId().uniqId().".".$ext;

        list($width, $height) = getimagesize($_FILES['screenshot1']['tmp_name']);
        if(strtolower($ext) == "png")
            $original1 = imagecreatefrompng($_FILES['screenshot1']['tmp_name']);
        else
            $original1 = imagecreatefromjpeg($_FILES['screenshot1']['tmp_name']);

        $resized1 = imagecreatetruecolor(130,130);
        imagecopyresampled($resized1, $original1, 0, 0, 0, 0,130,130, $width, $height);
        if(strtolower($ext) == "png")
            imagepng($resized1, $shot1);
        else
            imagejpeg($resized1, $shot1);
        //move_uploaded_file($_FILES['screenshot1']['tmp_name'], $shot1);
    }
    $screenshot2 = $_FILES['screenshot2']['name'];    
    $shot2="";
    if($screenshot2 != "") {
        $ext = pathinfo($screenshot2, PATHINFO_EXTENSION);
        $shot2 = "./uploads/Image/".uniqId().uniqId().uniqId().".".$ext;

        list($width, $height) = getimagesize($_FILES['screenshot2']['tmp_name']);
        if(strtolower($ext) == "png")
            $original2 = imagecreatefrompng($_FILES['screenshot2']['tmp_name']);
        else
            $original2 = imagecreatefromjpeg($_FILES['screenshot2']['tmp_name']);
        $resized2 = imagecreatetruecolor(130,130);
        imagecopyresampled($resized2, $original2, 0, 0, 0, 0,130,130, $width, $height);
        if(strtolower($ext) == "png")
            imagepng($resized2, $shot2);
        else
            imagejpeg($resized2, $shot2);
        //move_uploaded_file($_FILES['screenshot2']['tmp_name'], $shot2);
    }
    $screenshot3 = $_FILES['screenshot3']['name'];    
    $shot3="";
    if($screenshot3 != "") {
        $ext = pathinfo($screenshot3, PATHINFO_EXTENSION);
        $shot3 = "./uploads/Image/".uniqId().uniqId().uniqId().".".$ext;

        list($width, $height) = getimagesize($_FILES['screenshot3']['tmp_name']);
        if(strtolower($ext) == "png")
            $original3 = imagecreatefrompng($_FILES['screenshot3']['tmp_name']);
        else
            $original3 = imagecreatefromjpeg($_FILES['screenshot3']['tmp_name']);
        $resized3 = imagecreatetruecolor(130,130);
        imagecopyresampled($resized3, $original3, 0, 0, 0, 0,130,130, $width, $height);
        if(strtolower($ext) == "png")
            imagepng($resized3, $shot3);
        else
            imagejpeg($resized3, $shot3);
        //move_uploaded_file($_FILES['screenshot3']['tmp_name'], $shot3);
    }

    $gamename = $_POST['game_name'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $tag = $_POST['tags'];
    $developer_name = $_POST['developer_name'];
    $external_link = $_POST['external_link'];

    if(isset($_POST['genre']))
       $genre = $_POST['genre'];
    else
       $genre = "ACTION";
    $credit = $_POST['credit'];
    $tag = $_POST['tags'];
    $vlink = $_POST['vlink'];
    $cartridge = $_POST['cartridge'];
    $fullgame = $_POST['fullgame'];
    $demoonly = $_POST['demoonly'];   
   
    $gamename = $mysql_db->real_escape_string($gamename);
    $email =$mysql_db->real_escape_string($email);
    $date =date("Y-m-d");
    $year = date("Y");
    $year = $year % 100;

    $description = $mysql_db->real_escape_string($description);
   
    $developer_name = $mysql_db->real_escape_string($developer_name);
    $external_link = $mysql_db->real_escape_string($external_link);
    $genre = $mysql_db->real_escape_string($genre);
    $tag = $mysql_db->real_escape_string($tag);
    $credit =$mysql_db->real_escape_string($credit);
    $vlink =$mysql_db->real_escape_string($vlink);
    $cartridge = $mysql_db->real_escape_string($cartridge);
    $fullgame = $mysql_db->real_escape_string($fullgame);
    $demoonly = $mysql_db->real_escape_string($demoonly);
    $user_id = $_SESSION['user_id'];

    if($fulllink != "")
        $fullgame = 1;
    else
        $fullgame = 0;
    if($demolink != "")
        $demoonly = 1;
    else
        $demoonly = 0;

    $sql = "SELECT cat FROM games ORDER BY ID DESC LIMIT 0,1";
    $result = mysqli_query($mysql_db, $sql);
    $nRows = $result->num_rows;
    if($nRows == 0) {
        $newcat = "NM-".$year."-000000";
    }
    else {
        $row = $result -> fetch_array(MYSQLI_NUM);
        $cat = $row[0];
        $arrs = explode("-", $cat);
        if((int)($arrs[1]) == $year)
            $nextid = (int)($arrs[2]) + 1;
        else
            $nextid = 0;
        $nextid = sprintf("%06d", $nextid);
        $newcat = "NM-".$year."-".$nextid;
    }
    $genre = getGenreId($genre);
    $tag = getTagId($tag);

    if($gid == "") {
        $sql = "INSERT INTO games(gamename,demo_rom,full_rom,developer_name,developer_email,external_link,date,genre,description,credits,promo_image,screenshot1, screenshot2, screenshot3,video_link,cartridge_available,free_full_game,demo_only,cat,tag,user_id) VALUES('$gamename','$demolink','$fulllink','$developer_name','$email','$external_link','$date','$genre','$description','$credit','$promolink','$shot1','$shot2','$shot3','$vlink','$cartridge','$fullgame','$demoonly','$newcat','$tag','$user_id')";
        mysqli_query($mysql_db, $sql);
    }
    else {
        $sql = "UPDATE games SET gamename='$gamename', developer_name='$developer_name', developer_email='$email', external_link='$external_link',description='$description',genre='$genre', credits='$credit', video_link='$vlink',cartridge_available='$cartridge',free_full_game='$fullgame',demo_only='$demoonly',tag='$tag' WHERE ID='$gid'";
        mysqli_query($mysql_db, $sql);

        if($demolink != "") {
            $sql = "UPDATE games SET demo_rom='$demolink' WHERE ID='$gid'";
            mysqli_query($mysql_db, $sql);
        }
        if($fulllink != "") {
            $sql = "UPDATE games SET full_rom='$fulllink' WHERE ID='$gid'";
            mysqli_query($mysql_db, $sql);
        }
        if($promolink != "") {
            $sql = "UPDATE games SET promo_image='$promolink' WHERE ID='$gid'";
            mysqli_query($mysql_db, $sql);
        }
        if($shot1 != "") {
            $sql = "UPDATE games SET screenshot1='$shot1' WHERE ID='$gid'";
            mysqli_query($mysql_db, $sql);
        }
        if($shot2 != "") {
            $sql = "UPDATE games SET screenshot2='$shot2' WHERE ID='$gid'";
            mysqli_query($mysql_db, $sql);
        }
        if($shot3 != "") {
            $sql = "UPDATE games SET screenshot3='$shot3' WHERE ID='$gid'";
            mysqli_query($mysql_db, $sql);
        }
    }
    die("1");
}
echo "0";
?>