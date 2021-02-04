<?php
    
    session_start();
    if(!isset($_SESSION['route']))
        die('Direct Access is forbidden');
    
    include "mysql.php";
    require "vendor/autoload.php";
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    $developmentMode = true;
$mailer = new PHPMailer($developmentMode);
if (!empty($_POST))
{   
    $do =  inputParam("do");    
    $description = $mysql_db->real_escape_string(inputParam('description'));
    $name = $mysql_db->real_escape_string(inputParam('name'));
    $tag = $mysql_db->real_escape_string(inputParam('tag'));
    $credits = $mysql_db->real_escape_string(inputParam('credits'));
    $genre = $mysql_db->real_escape_string(inputParam('genre'));
    $developer = $mysql_db->real_escape_string(inputParam('developer'));
    $sort = $mysql_db->real_escape_string(inputParam('sort'));

    $sortcon = " ";
    if($sort == 1)
        $sortcon = " ORDER BY gamename ASC";
    else if($sort ==2)
        $sortcon = " ORDER BY date DESC";
    else if($sort ==3)
        $sortcon = " ORDER BY rating DESC";

    $con = " AND gamename LIKE '%$name%' AND tag LIKE '%$tag%' AND credits LIKE '%$credits%' AND genre LIKE '%$genre%' AND developer_name LIKE '%$developer%' AND `description` LIKE '%$description%'".$sortcon;
    switch ($do) {
        case "load_icon":
            $page = inputParam('pagenum');
            $w = inputParam('w');
            $page = (int)($page);
            $start = $page * ICON_PAGE_CNT;

            $sql = "SELECT COUNT(*) AS cnt FROM games WHERE 1=1 $con ";            
            $result = mysqli_query($mysql_db, $sql);
            $row = $result->fetch_assoc();
            $allcnt = $row['cnt'];
            $allpage = ($allcnt % ICON_PAGE_CNT == 0) ? (int)($allcnt / ICON_PAGE_CNT) : (int)($allcnt /ICON_PAGE_CNT) + 1;

            $pic_width = 0;

            if($w <= 400) {
                $pic_width = ($w - 15)/ 2 - 2;
                $pic_height = $pic_width;
                $pg = 2;
                $margin = 5;
            }
            else if($w <= 600) {
                $pic_width = ($w - 40) / 3 - 2;
                $pic_height = $pic_width;
                $pg = 3;
                $margin = 10;
            }
            else if($w <= 768) {
                $pic_width = ($w - 50) / 4 - 3;
                $pic_height = $pic_width;
                $pg = 4;
                $margin = 10;
            }
            else if($w <= 1024) {
                $pic_width = ($w - 90) / 5 - 3;
                $pg = 5;
                $pic_height = $pic_width / 10 * 9;
                $margin = 15;
            }
            else {
                $pic_width = ($w - 105) / 6 - 3;
                $pic_height = $pic_width / 10 * 8;
                $pg = 6;
                $margin = 15;
            }

            $sql = "SELECT * FROM games WHERE 1=1 $con LIMIT $start, ".ICON_PAGE_CNT;    
            //die($sql);        
            $result = mysqli_query($mysql_db, $sql);	
			while($row = $result->fetch_assoc()) {
				$id = $row['ID'];
				$demo_rom = $row['demo_rom'];	
				$game_name = $row['gamename'];
				$promo_image = $row['promo_image'];
				$rating = $row['rating'];
				$ratstr = calcRating($rating);
                $id = base64_encode($id);
                
				echo "<div class='img_divs' style='margin-left:$margin"."px'>
					<div class='select-picture' data='$id'>
					<img src='$promo_image' style='width:".$pic_width."px; height:".$pic_height."px'/>
					</div>
					<div>
					<h5 class='game-title' style='width:".$pic_width."px'>$game_name</h5>
					<div style='text-align:center'>$ratstr</div>
					</div>
				</div>";		
				
			}
            echo "<input type='hidden' id='hid_allc' name='hid_allc' value='$allcnt'/>";
            echo "<input type='hidden' id='hid_allp' name='hid_allp' value='$allpage'/>";
            break;
        case "load_table":
            $page = inputParam('pagenum');
            $page = (int)($page);
            $start = $page * TABLE_PAGE_CNT;
            $sql = "SELECT COUNT(*) AS cnt FROM games WHERE 1=1 $con ";            
            $result = mysqli_query($mysql_db, $sql);
            $row = $result->fetch_assoc();
            $allcnt = $row['cnt'];
            $allpage = ($allcnt % TABLE_PAGE_CNT == 0) ? (int)($allcnt / TABLE_PAGE_CNT) : (int)($allcnt /TABLE_PAGE_CNT) + 1;

            $sql = "SELECT games.demo_rom, games.gamename, games.promo_image, games.description, tags.tagname AS tag, genre.genrename AS genre, games.credits, games.developer_name, games.rating, games.ID, games.date FROM games INNER JOIN genre ON games.genre=genre.id INNER JOIN tags ON tags.id=games.tag WHERE 1=1 $con LIMIT $start, ".TABLE_PAGE_CNT;
           
            $result = mysqli_query($mysql_db, $sql);	
            
            
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
                $developer = $row['developer_name'];
                $rating = $row['rating'];
                $id = $row['ID'];
                $id = base64_encode($id);
                $playbtn ="<a class='play-btn' data='$id'></a>";
                $date = $row['date'];
                $ratstr = calcRating($rating);
                echo "<tr class='table-tr' allp='$allpage' allc='$allcnt'>
                <td>$game_name</td>
                <td>$description</td>
                <td>$tag</td>
                <td>$genre</td>
                <td>$date</td>
                <td>$credit</td>
                <td>$developer</td>
                <td>$ratstr</td>
                <td>$playbtn</td>
                </tr>";
            }
            break;
        case "resend":
            $email = $mysql_db->real_escape_string(inputParam('email'));
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                die("-1");
            }
            $result = mysqli_query($mysql_db, "SELECT * FROM users WHERE email='$email'");
            $cnt = $result->num_rows;
            if($cnt == 0) {
                die ("-2");
            }
            $row = $result->fetch_assoc();
            $id = $row['ID'];

            $emailcode = rand(1000000, 10000000);
            $sql = "UPDATE users SET emailcode='$emailcode' WHERE ID='$id'";
            mysqli_query($mysql_db,$sql);
            try {
                $mailer->SMTPDebug = false;
                $mailer->isSMTP();
            
                if ($developmentMode) {
                $mailer->SMTPOptions = [
                    'ssl'=> [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                    ]
                ];
                }           
            
                $mailer->Host = 'nesemutest.com';
                $mailer->SMTPAuth = true;
                $mailer->Username = 'retroverse@nesemutest.com';
                $mailer->Password = 'G]_8c$jD4Tlw';
                $mailer->SMTPSecure = 'tls';
                $mailer->Port = 587;
            
                $mailer->setFrom('retroverse@nesemutest.com', 'Email verification for NES');
                $mailer->addAddress($email, 'Name of recipient');
            
                $mailer->isHTML(true);
                $mailer->Subject = 'Email verification';
                $mailer->Body = 'Verification code : '.$emailcode;
            
                $mailer->send();
                $mailer->ClearAllRecipients();
            
            } catch (Exception $e) {
            }
            break;
        case "signup":
            $email = $mysql_db->real_escape_string(inputParam('email'));
            $password = $mysql_db->real_escape_string(inputParam('password'));
            $username = $mysql_db->real_escape_string(inputParam('username'));
            $license = $mysql_db->real_escape_string(inputParam('license'));
            $licensepwd = $mysql_db->real_escape_string(inputParam('licensepwd'));
            $password = md5($password);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $priv = PLAYER;
            //if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //    die("-1");
            //}
            if($license != "" && $licensepwd != "") {
                $url = "https://api.softworkz.com/v1/licenses/P0003024/YMM5GRXQPNUAH435";
                $data = array('licensepassword' => 'Nothing0', 'UserIp' => '10.10.10.10');
                $data_string = json_encode($data);
                $apikey = "Basic ".base64_encode("C0001955:APISV4D47J39FTCNQU2JKX4");

                //$postfields = array('field1'=>'value1', 'field2'=>'value2');
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                // Edit: prior variable $postFields should be $postfields;
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                    'Authorization:'.$apikey,
                    'Content-Type: application/json',                                                                                
                    'Content-Length: ' . strlen($data_string))                                                                       
                );
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
                $result = curl_exec($ch);
                $priv = PLAYER + CREATOR;
            }

            $result = mysqli_query($mysql_db, "SELECT * FROM users WHERE email='$email'");
            $cnt = $result->num_rows;
            if($cnt != 0) {
                die ("-2");
            }        
            $emailcode = rand(1000000, 10000000);
            $sql = "INSERT INTO users(username, email, emailcode, validated, pwd, priv,`status`,`avatar_path`) VALUES('$username', '$email','$emailcode','0', '$password','$priv','1','./assets/img/default.png')";
            $result = mysqli_query($mysql_db, $sql);
            //die($sql);
            try {
                $mailer->SMTPDebug = false;
                $mailer->isSMTP();
            
                if ($developmentMode) {
                $mailer->SMTPOptions = [
                    'ssl'=> [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                    ]
                ];
                }           
            
                $mailer->Host = 'nesemutest.com';
                $mailer->SMTPAuth = true;
                $mailer->Username = 'retroverse@nesemutest.com';
                $mailer->Password = 'G]_8c$jD4Tlw';
                $mailer->SMTPSecure = 'tls';
                $mailer->Port = 587;
            
                $mailer->setFrom('retroverse@nesemutest.com', 'Email verification for NES');
                $mailer->addAddress($email, 'Name of recipient');
            
                $mailer->isHTML(true);
                $mailer->Subject = 'Email verification';
                $mailer->Body = 'Verification code : '.$emailcode;
            
                $mailer->send();
                $mailer->ClearAllRecipients();
            
            } catch (Exception $e) {
            }
            die("1");
            break;
        case "send_report":
            $row = getUserInformation();
            $username = $row['username'];
            $email = $row['email'];
            $data = $mysql_db->real_escape_string(inputParam('data'));
            try {
                $mailer->SMTPDebug = false;
                $mailer->isSMTP();
            
                if ($developmentMode) {
                $mailer->SMTPOptions = [
                    'ssl'=> [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                    ]
                ];
                }           
            
                $mailer->Host = 'nesemutest.com';
                $mailer->SMTPAuth = true;
                $mailer->Username = 'retroverse@nesemutest.com';
                $mailer->Password = 'G]_8c$jD4Tlw';
                $mailer->SMTPSecure = 'tls';
                $mailer->Port = 587;
            
                $mailer->setFrom('retroverse@nesemutest.com', 'RETROVERSE REPORT');
                $mailer->addAddress("nesmakerhelp@gmail.com", 'Name of recipient');
            
                $mailer->isHTML(true);
                $mailer->Subject = 'RETROVERSE REPORT';
                $mailer->Body = $data;
                $mailer->Body .= "\nUser name:".$username;
                $mailer->Body .= "\nEmail Address:".$email;

                $mailer->send();
                $mailer->ClearAllRecipients();
            
            } catch (Exception $e) {
            }
            break;
        case "add_to_favor":
            $rid = $mysql_db->real_escape_string(inputParam('rid'));
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT COUNT(*) AS cnt FROM favs WHERE gameid='$rid' AND user_id='$user_id'";
            $result = mysqli_query($mysql_db, $sql);    
            $row = $result->fetch_assoc();
            $exist = $row["cnt"];
            if($exist != 0)
                die("0");
            $sql = "INSERT INTO favs(gameid, user_id) VALUES('$rid','$user_id')";
            mysqli_query($mysql_db, $sql);
            break;
        case "rating":
            $gid = $mysql_db->real_escape_string(inputParam('gid'));
            $score = $mysql_db->real_escape_string(inputParam('score'));
            $comment = $mysql_db->real_escape_string(inputParam('comment'));
            $user_id = $_SESSION['user_id'];

            $sql = "SELECT COUNT(*) AS cnt FROM ratings WHERE game_id='$gid' AND user_id='$user_id'";
            $result = mysqli_query($mysql_db, $sql);
            $row = $result->fetch_assoc();
            $exist = $row['cnt'];
            if($exist == 0) {
                $sql = "INSERT INTO ratings(game_id, user_id, rating,comment) VALUES('$gid','$user_id','$score','$comment')";
                mysqli_query($mysql_db, $sql);

                $sql = "SELECT AVG(rating) AS val FROM ratings WHERE game_id='$gid'";
                $result = mysqli_query($mysql_db, $sql);
                $row = $result->fetch_assoc();
                $val = $row['val'];

                $sql = "UPDATE games SET rating='$val' WHERE ID='$gid'";
                mysqli_query($mysql_db, $sql);
            }
            
            break;
        case "everification":
            $emailcode = $mysql_db->real_escape_string(inputParam('emailcode'));
            $email = $mysql_db->real_escape_string(inputParam('email')); 
            $result = mysqli_query($mysql_db, "SELECT * FROM users WHERE ((email='$email' AND emailcode='$emailcode') OR (username='$email' AND emailcode='$emailcode'))");
            $cnt = $result->num_rows;   
            if($cnt == 0) {
                echo ("0");
            } 
            else {
                $row = $result -> fetch_assoc();
                $priv = $row['priv'];
                $id = $row['ID'];
                $_SESSION['priv'] = $priv; 
                $_SESSION['user_id'] = $id;
                mysqli_query($mysql_db, "UPDATE users SET validated='1' WHERE id='$id'");      
                echo $priv;
            }     
            break;
        case "login":
            $password = $mysql_db->real_escape_string(inputParam('password'));
            $username = $mysql_db->real_escape_string(inputParam('username')); 
            $username = filter_var($username, FILTER_SANITIZE_EMAIL);
            
            if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                die("-3");
            }

            $pwd = md5($password);
            
            $result = mysqli_query($mysql_db, "SELECT * FROM users WHERE ((username='$username' AND pwd='$pwd') OR (email='$username' AND pwd='$pwd'))");
            $cnt = $result->num_rows;
            if($cnt == 0) {
                echo ("0");
            }        
            else {
                $row = $result -> fetch_assoc();
                $status = $row['status'];
                $validated = $row['validated'];
                $priv = $row['priv'];
                if($status == BANNED)
                    die("-1");
                if($validated != 1 && $priv != ADMIN)
                    die("-2");                
                $id = $row['ID'];
                $_SESSION['priv'] = $priv; 
                $_SESSION['user_id'] = $id;      
                echo $priv;
            }
            break;
        case "genre":
            $id = $mysql_db->real_escape_string(inputParam('id'));
            $name = $mysql_db->real_escape_string(inputParam('name'));        
            if($id == "0") {
                $sql = "INSERT INTO genre(genrename) VALUES('$name')";
                $result = mysqli_query($mysql_db, $sql);
                $id = $mysql_db->insert_id;  
            }
            else {
                $sql = "UPDATE genre SET genrename='$name' WHERE id='$id'";
                $result = mysqli_query($mysql_db, $sql);    
            }
            echo $id;
            break;
        case "removegenre":
            $id = $mysql_db->real_escape_string(inputParam('id'));
            
            $sql = "DELETE FROM genre WHERE id='$id'";
            $result = mysqli_query($mysql_db, $sql);    

            break;
        case "tag":
            $id = $mysql_db->real_escape_string(inputParam('id'));
            $name = $mysql_db->real_escape_string(inputParam('name'));        
            if($id == "0") {
                $sql = "INSERT INTO tags(tagname) VALUES('$name')";
                $result = mysqli_query($mysql_db, $sql);
                $id = $mysql_db->insert_id;  
            }
            else {
                $sql = "UPDATE tags SET tagname='$name' WHERE id='$id'";
                $result = mysqli_query($mysql_db, $sql);    
            }
            echo $id;
            break;
        case "removetag":
            $id = $mysql_db->real_escape_string(inputParam('id'));
            
            $sql = "DELETE FROM tags WHERE id='$id'";
            $result = mysqli_query($mysql_db, $sql);    

            break;
        case "removefavs":
            $id = $mysql_db->real_escape_string(inputParam('id'));
                
            $sql = "DELETE FROM favs WHERE ID='$id'";
            $result = mysqli_query($mysql_db, $sql);   

            break;
        case "removeuser":
            $id = $mysql_db->real_escape_string(inputParam('id'));
                
            $sql = "DELETE FROM users WHERE ID='$id'";
            $result = mysqli_query($mysql_db, $sql);   

            break;
        case "removegame":
            $id = $mysql_db->real_escape_string(inputParam('id'));
            $id = base64_decode($id);
            $sql = "DELETE FROM games WHERE ID='$id'";
            $result = mysqli_query($mysql_db, $sql);    

            break;
        case "login_out":
            unset($_SESSION['priv']);
            break;
        case "load_collections": {
            $w = $mysql_db->real_escape_string(inputParam('w'));
            $curpage = $mysql_db->real_escape_string(inputParam('page'));
            $w = (int)$w;
            $pg = 2;
            $pic_width = 0;

            if($w <= 400) {
                $pic_width = ($w - 15)/ 2 - 2;
                $pic_height = $pic_width;
                $pg = 2;
                $margin = 5;
            }
            else if($w <= 600) {
                $pic_width = ($w - 40) / 3 - 2;
                $pic_height = $pic_width;
                $pg = 3;
                $margin = 10;
            }
            else if($w <= 768) {
                $pic_width = ($w - 50) / 4 - 3;
                $pic_height = $pic_width;
                $pg = 4;
                $margin = 10;
            }
            else if($w <= 1024) {
                $pic_width = ($w - 90) / 5 - 3;
                $pg = 5;
                $pic_height = $pic_width / 10 * 9;
                $margin = 15;
            }
            else {
                $pic_width = ($w - 105) / 6 - 3;
                $pic_height = $pic_width / 10 * 8;
                $pg = 6;
                $margin = 15;
            }
            $featuregames = getFeaturedGames($curpage, $pg);
            $topgames = getTopRatedGames($curpage, $pg);
            $newgames = getNewTitledGames($curpage, $pg);
            $progames = getProplayerGames($curpage, $pg);
            
            $fcnt = count($featuregames);            
            if($fcnt > 0) {
                echo "<div class='scale-div'>
                <div class='coll-title' style='margin-left:5px'>
                    <h3 class='color-white'>FEATURED</h3>
                </div>
                <div>";
                foreach($featuregames as $row) {
                    $id = $row['ID'];
                    $demo_rom = $row['demo_rom'];	
                    $game_name = $row['gamename'];
                    $promo_image = $row['promo_image'];
                    $rating = $row['rating'];
                    $ratstr = calcRating($rating);
                    $id = base64_encode($id);
                    
                    echo "<div class='collect_div' style='margin-left:$margin"."px'>
                        <div class='select-picture' data='$id'>
                        <img src='$promo_image' style='width:".$pic_width."px; height:".$pic_height."px'/>
                        </div>
                        <div>
                        <h5 class='game-title' style='width:".$pic_width."px'>$game_name</h5>
                        
                        </div>
                    </div>";		
                }
                echo "</div>
                </div>";
            }
            $topcnt = count($topgames);
            if($topcnt > 0) {
                echo "<div class='scale-div'>
                <div class='coll-title' style='margin-left:5px'>
                    <h3 class='color-white'>TOP RATED</h3>
                </div>
                <div>";
                foreach($topgames as $row) {
                    $id = $row['ID'];
                    $demo_rom = $row['demo_rom'];	
                    $game_name = $row['gamename'];
                    $promo_image = $row['promo_image'];
                    $rating = $row['rating'];
                    $ratstr = calcRating($rating);
                    $id = base64_encode($id);
                    
                    echo "<div class='collect_div' style='margin-left:$margin"."px'>
                        <div class='select-picture' data='$id'>
                        <img src='$promo_image' style='width:".$pic_width."px; height:".$pic_height."px'/>
                        </div>
                        <div>
                        <h5 class='game-title' style='width:".$pic_width."px'>$game_name</h5>
                        
                        </div>
                    </div>";		
                }
                echo "</div>
                </div>";
            }
            $newcnt = count($newgames);
            if($newcnt > 0) {
                echo "<div class='scale-div'>
                <div class='coll-title' style='margin-left:5px'>
                    <h3 class='color-white'>NEW GAMES</h3>
                </div>
                <div>";
                foreach($newgames as $row) {
                    $id = $row['ID'];
                    $demo_rom = $row['demo_rom'];	
                    $game_name = $row['gamename'];
                    $promo_image = $row['promo_image'];
                    $rating = $row['rating'];
                    $ratstr = calcRating($rating);
                    $id = base64_encode($id);
                    
                    echo "<div class='collect_div' style='margin-left:$margin"."px'>
                        <div class='select-picture' data='$id'>
                        <img src='$promo_image' style='width:".$pic_width."px; height:".$pic_height."px'/>
                        </div>
                        <div>
                        <h5 class='game-title' style='width:".$pic_width."px'>$game_name</h5>
                        
                        </div>
                    </div>";		
                }
                echo "</div>
                </div>";
            }
            
            $procnt = count($progames);            
            if($procnt > 0) {
                echo "<div class='scale-div'>
                <div class='coll-title' style='margin-left:5px'>
                    <h3 class='color-white'>PRO GAMES</h3>
                </div>
                <div>";
                foreach($progames as $row) {
                    $id = $row['ID'];
                    $demo_rom = $row['demo_rom'];	
                    $game_name = $row['gamename'];
                    $promo_image = $row['promo_image'];
                    $rating = $row['rating'];
                    $ratstr = calcRating($rating);
                    $id = base64_encode($id);
                    
                    echo "<div class='collect_div' style='margin-left:$margin"."px'>
                        <div class='select-picture' data='$id'>
                        <img src='$promo_image' style='width:".$pic_width."px; height:".$pic_height."px'/>
                        </div>
                        <div>
                        <h5 class='game-title' style='width:".$pic_width."px'>$game_name</h5>
                        
                        </div>
                    </div>";		
                }
                echo "</div>
                </div>";
            }
        }
        break;
        case "getgamelink": {
            $val = $mysql_db->real_escape_string(inputParam('val'));
            $r = base64_encode(base64_encode($val));
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $actual_link .= "/index.php?g=".$r;
            die($actual_link);
        }
        break;
        case "changepriv":
            $priv = $mysql_db->real_escape_string(inputParam('prv'));
            $id = $mysql_db->real_escape_string(inputParam('id'));
            $sql = "UPDATE users SET priv='$priv' WHERE ID='$id'";

            mysqli_query($mysql_db, $sql);
            break;
        case "get_random":
            $sql = "SELECT COUNT(*) AS cnt FROM games";
            $result = mysqli_query($mysql_db, $sql);
            $row = $result->fetch_assoc();
            $cnt = $row['cnt'];

            $val = rand(1, $cnt);
            $sql = "SELECT ID FROM games WHERE 1=1 LIMIT $val, 1";
            $result = mysqli_query($mysql_db, $sql);
            $row = $result->fetch_assoc();
            $id = base64_encode($row['ID']);
            die($id);
            break;
        case "changestt":
            $stt = $mysql_db->real_escape_string(inputParam('stt'));
            $id = $mysql_db->real_escape_string(inputParam('id'));
            $sql = "UPDATE users SET status='$stt' WHERE ID='$id'";
            mysqli_query($mysql_db, $sql);
            break;    
        case "update_profile": {
            $username = $mysql_db->real_escape_string(inputParam('username'));
            $phone = $mysql_db->real_escape_string(inputParam('phone'));
            $pwd = $mysql_db->real_escape_string(inputParam('pwd'));
            $confirm = $mysql_db->real_escape_string(inputParam('confirm'));
            $twitter = $mysql_db->real_escape_string(inputParam('twitter'));
            $instagram = $mysql_db->real_escape_string(inputParam('instagram'));
            $facebook = $mysql_db->real_escape_string(inputParam('facebook'));
            $user_id = $_SESSION['user_id'];
            if($pwd == "") {
                $sql = "UPDATE users SET username='$username', phone='$phone',twitter='$twitter',instagram='$instagram',facebook='$facebook' WHERE ID='$user_id'";
            }
            else {
                $pwd=md5($pwd);
                $sql = "UPDATE users SET username='$username', phone='$phone',twitter='$twitter',instagram='$instagram',facebook='$facebook', pwd='$pwd' WHERE ID='$user_id'";
            }   
            mysqli_query($mysql_db, $sql);
        }
        break;
    }
}

?>