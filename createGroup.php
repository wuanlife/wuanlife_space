<!DOCTYPE html>
<?php
//记录当前url 判断用户是否登陆 若未登录这跳转到登陆界面
$userurl=$_SERVER['REQUEST_URI'];
setcookie('userurl',$userurl);
if(!isset($_COOKIE['nickName'])){
    echo '<script language=javascript>window.location.href="login.php"</script>';
}
?>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index,follow">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="email=no">
    <meta name="format-detection" content="adress=no">
    <title>创建星球 - -午安网 - 过你想过的生活</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/wuan.css">
</head>
<body>
<!-- file="head.html"-->
<!-- head start-->
<div class="nav navbar navbar-fixed-top navbar-head-color navbar-head">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="navbar-brand navbar-header">
                    <a class="" href="index.php">午安网</a>
                </div>
                <div class="pull-left hidden-sm hidden-xs">
                    <ul class="list-inline">
                        <li><a href="index.php">发现</a></li>
                        <li><a href="myGroup.php">我的星球</a></li>
                        <li><a href="groups.php">全部星球</a></li>
                    </ul>
                </div>
                <div class=" pull-right">
                    <ul class="list-inline">
                        <li><?php
                            $nickName=base64_decode($_COOKIE['nickName']);
                            echo '<a href="myGroup.php">';
                            echo $nickName.'</a></li>';
                            ?>
                        <li><?php
                            echo '<a href="exit.php">退出</a></li>';
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
    <div class="container  text-center">
        <h2 >创建星球</h2>
        <form class="form-signin" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
           <label for="groupName" class="sr-only">groupName</label>
           <input type="text" id="groupName" class="form-control" placeholder="星球名字：" required="" autofocus="" name="name">
            <button class="btn pull-right btn-primary" type="submit">创 建</button>
        </form>
    </div>
</div>
<?php
if(!empty($_POST)) {
    $name = $_POST['name'];
    if (!preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{1,20}+$/u', $name)){
        echo "<script>alert('小组名只能为中文、英文、数字或者下划线，但不得超过20字节！');</script>";
    }else {
        include_once "conn.php";
        $check = "select ID from group_base WHERE name='$name'";
        $query=mysql_query($check, $conn);
        $a=mysql_fetch_array($query);
        if (!empty($a)) {
            echo "<script>alert('该星球已存在！');</script>";
        } else {
            mysql_query("set names 'utf8'");
            $sql = "INSERT INTO group_base (name) VALUE ('$name')";
            $retval = mysql_query($sql, $conn);
            $sql2 = "SELECT ID FROM group_base WHERE name='$name'";
            $retval2 = mysql_query($sql2, $conn);
            $arr = mysql_fetch_array($retval2);
            $groupID = $arr['ID'];
            $userID = $_COOKIE['userID'];
            $sql3 = "INSERT INTO group_detail (ID,userID) VALUE ('$groupID','$userID')";
            $retval3 = mysql_query($sql3, $conn);
            if ($retval3) {

                echo "<script>window.location.href='enterLists.php?groupID=$groupID'</script>";
            }
        }
    }
}
    ?>
<script src="js/jquery-1.11.3.min.js"></script>
</body>
</html>