<?php
    if(isset($_GET['g']))
        $g = $_GET['g'];
    else
        $g = "";
    if(isset($_GET['i']))
        $i = $_GET['i'];
    else
        $i = "";
    if($g == "" || $i=="")
        die("0");
    include_once "mysql.php";
    $i = base64_decode($i);
    $file_url = getPath($g, $i);     
    $ext = pathinfo($file_url, PATHINFO_EXTENSION);
    $name = getGameName($i).".".$ext;

    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary"); 
    header("Content-disposition: attachment; filename=\"" . $name . "\""); 
    readfile($file_url);     
?>