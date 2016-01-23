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
    <title>主页 - 午安网 - 过你想过的生活</title>
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
                        <li><a href="login.html">登录</a></li>
                        <li><a href="reg.html">注册</a></li>
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
        <div class="text-center">
            <h3>找回密码</h3>
        </div>
        <form class="form-signin" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <label for="getEmail" class="sr-only">getEmail</label>
            <input type="text" id="getEmail" class="form-control" placeholder="邮箱：" required="" autofocus="" name="Email">
            <button class="btn  btn-primary btn-block" type="submit">发送邮件</button>
        </form>
    </div>
</div>

<?php
if(!empty($_POST)) {
include_once "conn.php";

    if (!get_magic_quotes_gpc()) {
        $Email = addslashes($_POST['Email']);
    }else {
        $Email = $_POST['Email'];
    }


    $sql="SELECT name,nickName,password FROM user_base WHERE Email='$Email'";

    $query=mysql_query($sql,$conn);
    $arr=mysql_fetch_array($query);

    if($arr=="")
    {
        echo '<script>alert ("请输入正确的邮箱!");</script>';

    }else
    {
        $password=$arr['password'];
        $name=$arr['name'];
        $nickName=$arr['nickName'];
        $massage="尊敬的$nickName：
        若您的用户名为$name。请点击该链接进行密码重置resetpsw.php?password=$password&name=$name&nickName=$nickName&Email=$Email";
        mail($Email,'重置密码',$massage);
        echo '<script>alert ("邮件已发送!");</script>';
    }


}
?>


<script src="js/jquery-1.11.3.min.js"></script>
</body>
</html>