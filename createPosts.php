<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index,follow">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="email=no">
    <meta name="format-detection" content="adress=no">
    <title>发表帖子 - -午安网 - 过你想过的生活</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/wuan.css">
</head>
<body>
<!-- file="head.html"-->
<!-- head start-->
<div class="nav navbar navbar-fixed-top navbar-head-color navbar-head">
    <div class="container ">
        <div class="row">
            <div class="col-md-12">
                <div class="navbar-brand navbar-header">
                    <a class="" href="index.php">午安网</a>
                </div>
                <div class=" pull-right">
                    <ul class="list-inline">
                        <li><?php
                            $userurl=$_SERVER['REQUEST_URI'];
                            setcookie('userurl',$userurl);

                            if(isset($_COOKIE['nickName'])){
                                $nickName=base64_decode($_COOKIE['nickName']);
                                echo '<a href="myGroup.php">';
                                echo $nickName.'</a></li>';
                            }else{
                                echo '<a href="login.php">登录</a></li>';
                            }
                            ?>
                        <li><?php
                            if(isset($_COOKIE['nickName'])){
                                echo '<a href="exit.php">退出</a></li>';
                            }else{
                                echo '<a href="reg.php">注册</a></li>';
                            }
                            ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- head end-->

<!-- framework-->
<!-- content-->
<div class="framework-content">
    <div class="container text-center">
        <h2 >发表帖子</h2>
        <form class="form-signin" method="post" onSubmit="return check_form(this);" action="<?php $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"]?>">
            <label for="postsName" class="sr-only">postsName</label>
            <input type="text" id="postsName" class="form-control" placeholder="标题：" required="" autofocus="" name="title">
            <textarea rows="6" class="form-control" placeholder="内容：" required="" name="postText"></textarea>
            <button class="btn pull-right btn-primary" type="submit">发 表</button>
        </form>
    </div>
</div>

<script src="js/jquery-1.11.3.min.js"></script>

<?php
if(!empty($_POST)){
    if(isset($_COOKIE['nickName'])){
        $g_id= substr($_SERVER['QUERY_STRING'],8,9);
        $u_id = $_COOKIE["userID"];
        $p_title = $_POST['title'];
        $p_text = $_POST['postText'];
        $p_time = date('Y-m-d H:i',time());

        include "conn.php";
        //取得postID
        $sql_getPID = "SELECT ID as postID\n"
            . "FROM post_base\n"
            . "ORDER by ID DESC\n"
            . "LIMIT 0 , 1";
        $result1 = mysql_query($sql_getPID);
        $row1 = mysql_fetch_array($result1);
        $p_id = $row1['postID']+1;

        //插入数据
        $sqlPB = "INSERT INTO post_base (ID,userID,groupID,title) VALUE ('$p_id','$u_id','$g_id','$p_title')";
        $sqlPD = "INSERT INTO post_detail (ID,postID,text,floor,createTime) VALUE ('$p_id','$u_id','$p_text','1','$p_time')";
        mysql_query($sqlPB);
        mysql_query($sqlPD);

        echo "<script>window.location.href=\"posts.php?P_ID=$p_id\"</script>";
    }else if(!isset($_COOKIE['nickName'])){
        echo '<script language="javascript">';
        echo 'alert("请先登录!");';
        echo '</script>';
    }
}


?>
</body>
</html>