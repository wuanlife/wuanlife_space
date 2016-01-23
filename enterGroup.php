<?php
include_once "conn.php";
$groupID=$_GET['groupID'];
$userID=$_COOKIE['userID'];
$sql3="INSERT INTO group_detail (ID,userID) VALUE ('$groupID','$userID')";
$retval3=mysql_query($sql3,$conn);
if($retval3){
    if(isset($_COOKIE['userurl'])){
        $url=$_COOKIE['userurl'];
    }else{
        $url="index.php";
    }

    echo "<script>window.location.href=\"$url\"</script>";
}




/**
 * Created by PhpStorm.
 * User: fyhqq
 * Date: 2016/1/20
 * Time: 16:02
 */