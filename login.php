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
                        <li><a href="login.php">登录</a></li>
                        <li><a href="reg.php">注册</a></li>
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
        <div class="text-center form-logo">
        </div>
        <form class="form-signin" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <label for="userName" class="sr-only">userName</label>
            <input type="text" id="userName" class="form-control" placeholder="用户名" required="" autofocus="" name="name">
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" id="inputPassword" class="form-control" placeholder="密码" required="" name="password">
            <button class="btn  btn-primary btn-block" type="submit">登录</button>
            <div class="footer ">
                <a class="pull-right text-16" href="rePassword.php">找回密码</a>
            </div>
        </form>

    </div>
</div>
<?php
$loginurl=$_SERVER['REQUEST_URI'];
setcookie('loginurl',$loginurl);
if(!empty($_POST)) {
    include_once "conn.php";

    if (!get_magic_quotes_gpc()) {
        $name = addslashes($_POST['name']);
        $password = addslashes($_POST['password']);
    }else {
        $name = $_POST['name'];
        $password = $_POST['password'];
    }

    $sql="SELECT password,nickName,ID FROM user_base WHERE name='$name'";
    $query=mysql_query($sql,$conn);
    $arr=mysql_fetch_array($query);
    $password=md5($password);

    if($arr=="")
    {
        echo '<script>alert ("用户名不存在!");</script>';
    }elseif($arr['password']==$password)
    {
        $nickName=base64_encode($arr['nickName']);
        $userID=$arr['ID'];

        setcookie('nickName',$nickName,time()+3600*24*7*2);
        setcookie('userID',$userID,time()+3600*24*7*2);
        if(isset($_COOKIE['userurl'])){
            $url=$_COOKIE['userurl'];
        }else{
            $url="index.php";
        }

        echo "<script>window.location.href=\"$url\"</script>";
    }else
    {

        echo '<script>alert ("账号或密码错误!");</script>';
    }


}
?>
<script src="js/jquery-1.11.3.min.js"></script>
</body>
</html>