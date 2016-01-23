<!DOCTYPE html>
<?php
include_once "conn.php";
$password=$_GET['password'];
$name=$_GET['name'];
$nickName=$_GET['nickName'];
$Email=$_GET['Email'];
$sql="SELECT password,ID FROM user_base WHERE name='$name',nickName='$nickName',Email='$Email'";
$arr=mysql_fetch_array(mysql_query($sql,$conn));
if($arr['password']==$password){


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
            <input type="text" id="getEmail" class="form-control" placeholder="请输入新密码" required="" autofocus="" name="rePassword">
            <button class="btn  btn-primary btn-block" type="submit">确定</button>
        </form>
    </div>
</div>
<?php
    $rePassword=$_POST['rePassword'];
    if (preg_match('/^[\s|\S]{6,18}$/u', $rePassword)){
        $password=MD5($rePassword);
        $sql2="UPDATE user_base password='$password' WHERE name='$name',nickName='$nickName',Email='$Email'";
        $result=mysql_query($sql2,$conn);
        if($result){
            $nickName=urlencode($arr['nickName']);
            $userID=$arr['ID'];

            setcookie('nickName',$nickName,time()+3600*24*7*2);
            setcookie('userID',$userID,time()+3600*24*7*2);
            if(isset($_COOKIE['userurl'])){
                $url=$_COOKIE['userurl'];
            }else{
                $url="index.php";
            }

            echo "<script>window.location.href=\"$url\"</script>";
            echo  '<script>alert ("密码重置成功!");</script>';

        }
    }else{
        echo "<script>alert('密码长度为6-18位！');</script>";
    }

} else{
    echo "<script>window.location.href=\"index.php\"</script>";
}
?>
<script src="js/jquery-1.11.3.min.js"></script>
</body>
</html>
