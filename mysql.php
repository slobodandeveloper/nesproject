<?php
    @session_start();
    if(!isset($_SESSION['route']))
        die('Direct Access is forbidden');

    define("ICON_PAGE_CNT",   16);
    define("TABLE_PAGE_CNT",   8);
    define('ADMIN',             1);
    define('PLAYER',            2);
    define('PROPLAYER',         4);
    define('CREATOR',           8);    

    define('BANNED',           2);
    $server_name = "localhost";
    //$user_name = "theret14_man";
    //$user_password = "dXh4RfwqfpCnYhZ";
    //$db_name = "theret14_nesdb";
    $user_name = "root";
    $user_password = "";
    $db_name = "nes_db";
    
    // create mysql connection;
    $mysql_db = new mysqli($server_name, $user_name, $user_password,$db_name);
    // Check connection
    if ($mysql_db->connect_error) {
        die("Connection failed: " . $user_name.$user_password);
    }
	
	/*$dbExist = $mysql_db->select_db($db_name);
	if($dbExist == FALSE) {
		$result = mysqli_query($mysql_db, "CREATE DATABASE ".$db_name);
		$mysql_db->select_db($db_name);		
	}*/
    $mysql_db->set_charset('utf8');
	
	$result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `users` (
        `ID` int NOT NULL AUTO_INCREMENT,
        `username` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'User name',
        `pwd` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'pwd',
        `priv` INT NOT NULL,
        `acct` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'NES maker credential',
        `validated` TINYINT NOT NULL COMMENT 'validated flag',
        `emailcode` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'current email code',
        `email` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'email',
        `phone` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'phone',
        `twitter` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'twitter',
        `instagram` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'instagram',
        `facebook` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'facebook',
        `avatar_path` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'avatar',
        `status` TINYINT NOT NULL COMMENT 'status',
        PRIMARY KEY (`ID`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");
    
    $result = mysqli_query($mysql_db, "SELECT COUNT(*) FROM users");
    $row = $result -> fetch_array(MYSQLI_NUM);
    $exist = $row[0];
    if($exist == 0) {
        $pwd = md5("user");
        mysqli_query($mysql_db, "INSERT INTO users(username, pwd, priv) VALUES('user', '$pwd', '".PLAYER."')");
        $pwd = md5("admin");
        mysqli_query($mysql_db, "INSERT INTO users(username, pwd, priv, email) VALUES('admin', '$pwd', '1','nesmakerhelp@gmail.com')");
    }

    $result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `downloads` (
        `id` int NOT NULL AUTO_INCREMENT,
        `gameid` INT NOT NULL COMMENT 'game table id',
        `user_id` INT NOT NULL COMMENT 'user table id',
        PRIMARY KEY (`id`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");

    $result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `favs` (
        `id` int NOT NULL AUTO_INCREMENT,
        `gameid` INT NOT NULL COMMENT 'game table id',
        `user_id` INT NOT NULL COMMENT 'user table id',
        PRIMARY KEY (`id`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");

    $result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `tags` (
        `id` int NOT NULL AUTO_INCREMENT,
        `tagname` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'TAG name',        
        PRIMARY KEY (`id`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");
    
    $result = mysqli_query($mysql_db, "SELECT COUNT(*) FROM tags");
    $row = $result -> fetch_array(MYSQLI_NUM);
    $exist = $row[0];
    if($exist == 0) {       
        mysqli_query($mysql_db, "INSERT INTO tags(tagname) VALUES('FEATURED')");
        mysqli_query($mysql_db, "INSERT INTO tags(tagname) VALUES('CHRISTMAS')");       
    }

    $result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `ratings` (
        `id` int NOT NULL AUTO_INCREMENT,
        `game_id` INT NOT NULL COMMENT 'game id',
        `user_id` INT NOT NULL COMMENT 'user id',
        `rating` INT NOT NULL COMMENT 'rating score 0~5',
        `comment` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'Genre name',        
        PRIMARY KEY (`id`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");

    $result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `genre` (
        `id` int NOT NULL AUTO_INCREMENT,
        `genrename` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'Genre name',        
        PRIMARY KEY (`ID`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");
    
    $result = mysqli_query($mysql_db, "SELECT COUNT(*) FROM genre");
    $row = $result -> fetch_array(MYSQLI_NUM);
    $exist = $row[0];
    if($exist == 0) {       
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('ACTION')");
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('ADVENTURE')");
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('RPG')");
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('SPORT')");
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('STRATEGY')");
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('SHOOTER')");
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('VERSUS')");
        mysqli_query($mysql_db, "INSERT INTO genre(genrename) VALUES('OTHER')");       
    }

    $result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `games` (
        `ID` int NOT NULL AUTO_INCREMENT,
        `gamename` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'the actual published name of the game.',
        `demo_rom` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'the public, playable demo.  THIS is what the emulator we are creating will see',
        `full_rom` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'hidden from the public',
        `developer_name` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'the legal name of the developer',
        `developer_email` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'official email for developer',
        `external_link` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'If developer has an external link',
        `date` DATE NOT NULL COMMENT 'publish date',
        `genre` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'The style of game',
        `description` TEXT CHARACTER SET utf8 NOT NULL COMMENT '256 character description',
        `credits` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'Credits for anyone involved in this game',
        `promo_image` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'The image associated with this game',
        `screenshot1` TEXT CHARACTER SET utf8 NOT NULL COMMENT '1',
        `screenshot2` TEXT CHARACTER SET utf8 NOT NULL COMMENT '2',
        `screenshot3` TEXT CHARACTER SET utf8 NOT NULL COMMENT '3',
        `video_link` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'A link to gameplay video',
        `rating` DOUBLE NOT NULL COMMENT 'The user rating for this game',
        `cartridge_available` TINYINT NOT NULL COMMENT 'A toggle that either lists that the cartridge is available or unavailable for purchase.',
        `free_full_game` TINYINT NOT NULL COMMENT 'a toggle that will be used in a later milestone.',
        `demo_only` TINYINT NOT NULL COMMENT 'a toggle that will be used in a later milestone',
        `tag` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'Game select tag',
        `cat` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'cat number',
        `user_id` INT NOT NULL COMMENT 'account for next use',
        `rate_users` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'rated users, separated by ,',
        `rate_count` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'rated user count',
        PRIMARY KEY (`ID`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");

    $result = mysqli_query($mysql_db, "SELECT COUNT(*) FROM games");
    $row = $result -> fetch_array(MYSQLI_NUM);
    $exist = $row[0];
    if($exist == 0) {
    //    mysqli_query($mysql_db, "INSERT INTO games(gamename, demo_rom, promo_image) VALUES('MysticOrigins', './uploads/ROM/MysticOrigins.nes', './uploads/images/MysticOrigins_0.png')");
    //    mysqli_query($mysql_db, "INSERT INTO games(gamename, demo_rom, promo_image) VALUES('TrollBurner', './uploads/ROM/TrollBurner.nes', './uploads/images/TrollBurner_0.png')");
    }

    $result = mysqli_query($mysql_db, "CREATE TABLE IF NOT EXISTS `directories` (
        `ID` int NOT NULL AUTO_INCREMENT,
        `rompath` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'ROM URL',
        `imgpath` TEXT CHARACTER SET utf8 NOT NULL COMMENT 'IMAGE URL',        
        PRIMARY KEY (`ID`)) ENGINE=MYISAM DEFAULT CHARSET=utf8");
    
    function getTags() {
        global $mysql_db;

        $sql = "SELECT * FROM tags WHERE 1=1";
        $result = mysqli_query($mysql_db, $sql);
        return $result;
    }
    function getGenre() {
        global $mysql_db;

        $sql = "SELECT * FROM genre WHERE 1=1";
        $result = mysqli_query($mysql_db, $sql);
        return $result;
    }

    function getGenreId($genre) {
        global $mysql_db;

        $sql = "SELECT id FROM genre WHERE genrename='$genre'";
        $result = mysqli_query($mysql_db, $sql);
        $nRow = $result->num_rows;
        if($nRow != 0) {
            $row = $result -> fetch_array(MYSQLI_NUM);
            $id = $row[0];
        }
        else {
            $sql ="INSERT INTO genre(genrename) VALUES('$genre')";
            mysqli_query($mysql_db, $sql);
            $id= $mysql_db->insert_id;

        }
        return $id;
    }
    function getTagId($tagstr) {
        $tagsarr = explode(",",$tagstr);
        global $mysql_db;
        $ids = "";
        $nlen = count($tagsarr);
        foreach($tagsarr as $tag) {
            $sql = "SELECT id FROM tags WHERE tagname='$tag'";
            $result = mysqli_query($mysql_db, $sql);
            $nRow = $result->num_rows;
            if($nRow != 0) {
                $row = $result -> fetch_array(MYSQLI_NUM);
                $id = $row[0];
            }
            else {
                $sql ="INSERT INTO tags(tagname) VALUES('$tag')";
                mysqli_query($mysql_db, $sql);
                $id= $mysql_db->insert_id;
            }
            $ids .= $id.",";            
        }
        return $ids;        
    }
    function getPath($g, $i) {
        global $mysql_db;

        $sql = "SELECT demo_rom, full_rom FROM games WHERE ID='$i'";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_assoc();
        $demo = $row['demo_rom'];
        $full = $row['full_rom'];
        if($g == "demo")
            return $demo;
        else if($g=="full")
            return $full;
        return "";
    }
    function getUserInformation() {
        global $mysql_db;
        $user_id = $_SESSION['user_id'];

        $sql = "SELECT * FROM users WHERE ID='$user_id'";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_assoc();
        return $row;
    }
    function getGameName($i) {
        global $mysql_db;

        $sql = "SELECT gamename FROM games WHERE ID='$i'";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_assoc();
        $name = $row['gamename'];
        return $name;
    }
    function getAllGameCount() {
        global $mysql_db;

        $sql = "SELECT COUNT(*) AS cnt FROM games WHERE 1=1";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_array(MYSQLI_NUM);
        $cnt = $row[0];
        return $cnt;
    }
    function getUploadCount() {
        global $mysql_db;
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT COUNT(*) AS cnt FROM games WHERE user_id='$user_id'";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_array(MYSQLI_NUM);
        $cnt = $row[0];
        return $cnt;
    }
    function getDownloadCnt() {
        global $mysql_db;
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT COUNT(*) AS cnt FROM downloads WHERE user_id='$user_id'";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_array(MYSQLI_NUM);
        $cnt = $row[0];
        return $cnt;
    }
    function getFavCnt() {
        global $mysql_db;
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT COUNT(*) AS cnt FROM favs WHERE user_id='$user_id'";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_array(MYSQLI_NUM);
        $cnt = $row[0];
        return $cnt;
    }
    function getFeaturedGames($pgindex = 0, $pg=0) {
        
        global $mysql_db;

        $sql = "SELECT id FROM tags WHERE tagname='featured'";
        $result = mysqli_query($mysql_db, $sql);
        $row = $result -> fetch_array(MYSQLI_NUM);
        $id = $row[0].",";
        $sql = "SELECT * FROM games WHERE tag LIKE '%$id%' LIMIT 0, 10";
        $result = mysqli_query($mysql_db, $sql);       
        $cnt = $result->num_rows;
        $arr = array();
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $arr[$i] = $row;
            $i++;
        }       
     
        if($cnt <= $pg)
            return $arr;
        $val = $pgindex * $pg;
        $result = array();
        $idx=0;
        for($j = 0; $j < $pg; $j++) {            
            $i = ($val + $j) % $cnt;
            $result[$idx] = $arr[$i];
            $idx++;
        } 
        return $result;
    }
    function getTopRatedGames($pgindex = 0,$pg=0) {        
        global $mysql_db;      
        $sql = "SELECT * FROM games ORDER BY rating DESC LIMIT 0, 10";
        $result = mysqli_query($mysql_db, $sql);
        $cnt = $result->num_rows;
        $arr = array();
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $arr[$i] = $row;
            $i++;
        }       
     
        if($cnt <= $pg)
            return $arr;
        $val = $pgindex * $pg;
        $result = array();
        $idx=0;
        for($j = 0; $j < $pg; $j++) {            
            $i = ($val + $j) % $cnt;
            $result[$idx] = $arr[$i];
            $idx++;
        }
        return $result;
    }
    function getNewTitledGames($pgindex = 0,$pg=0) {
        global $mysql_db;        
        $sql = "SELECT * FROM games ORDER BY id DESC LIMIT 0, 10";
        $result = mysqli_query($mysql_db, $sql);
        $cnt = $result->num_rows;
        $arr = array();
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $arr[$i] = $row;
            $i++;
        }       
     
        if($cnt <= $pg)
            return $arr;
        $val = $pgindex * $pg;
        $result = array();
        $idx=0;
        for($j = 0; $j < $pg; $j++) {            
            $i = ($val + $j) % $cnt;
            $result[$idx] = $arr[$i];
            $idx++;
        }
        return $result;
    }
    function getProplayerGames($pgindex = 0,$pg=0) {
        global $mysql_db;
        
            $start = 0;
            $lim = 10;
        
        $sql = "SELECT id FROM users WHERE (priv='".PROPLAYER."' OR priv='".(PROPLAYER+CREATOR)."')";
        $result = mysqli_query($mysql_db, $sql);
        $progames = array();
        while($row = $result -> fetch_array(MYSQLI_NUM)) {
            $id = $row[0];
            $sql = "SELECT id FROM games WHERE user_id='$id'";
            $res = mysqli_query($mysql_db, $sql);
            $row1 = $res -> fetch_array(MYSQLI_NUM);
            if(count($row1) != 0) {
                $id = $row[0];
                array_push($progames, $id);
            }
        }
        $procnt = count($progames);
        if($procnt <= 10)
            $getarray = $progames;
        else {
            $getarray = array();            
            while(1) {
                $randomval = rand(0, $procnt - 1);
                $bex = 0;
                for($i=0;$i<$getarray.count(); $i++) {
                    if($getarray[$i] == $randomval) {
                        $bex = 1;
                        break;
                    }
                }
                if($bex == 0)
                    array_push($getarray, $randomval);
                if(count($getarray) >= 10)
                    break;
            }    
        }
        $sql = "SELECT * FROM games WHERE (1=0";
        for($i = 0; $i < count($getarray); $i++) {
            $id = $getarray[$i];
            $sql .= " OR user_id='$id'";
        }
        $sql .= ") LIMIT $start, $lim";
        $result = mysqli_query($mysql_db, $sql);
        
        $cnt = $result->num_rows;
        $arr = array();
        $i = 0;
        while($row = $result->fetch_assoc()) {
            $arr[$i] = $row;
            $i++;
        }       
     
        if($cnt <= $pg)
            return $arr;
        $val = $pgindex * $pg;
        $result = array();
        $idx=0;
        for($j = 0; $j < $pg; $j++) {            
            $i = ($val + $j) % $cnt;
            $result[$idx] = $arr[$i];
            $idx++;
        }
        return $result;
    }
    function getIconPagesCount() {
        $cnt = (int)(getAllGameCount());
        $page = ($cnt % ICON_PAGE_CNT == 0) ? (int)($cnt / ICON_PAGE_CNT) : (int)($cnt /ICON_PAGE_CNT) + 1;
        return $page;
    }
    function getTablePagesCount() {
        $cnt = (int)(getAllGameCount());
        $page = ($cnt % TABLE_PAGE_CNT == 0) ? (int)($cnt / TABLE_PAGE_CNT) : (int)($cnt /TABLE_PAGE_CNT) + 1;
        return $page;
    }
    function calcRating($rating) {
        if($rating > 4.5) {
            return "<span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>";
        }
        else if($rating > 3.5) {
            return "<span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star'></span>";
        }
        else if($rating > 2.5) {
            return "<span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>";
        }
        else if($rating > 1.5) {
            return "<span class='fa fa-star checked'></span>
            <span class='fa fa-star checked'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>";
        }
        else if($rating > 0.5) {
            return "<span class='fa fa-star checked'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>";
        }
        return "<span class='fa fa-star'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>
            <span class='fa fa-star'></span>";
    }
    function inputParam($v) {
        if(isset($_POST[$v])) {
            return $_POST[$v];
        }
        return "";
    }
?>