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
    <title>全部星球 - 午安网 - 过你想过的生活</title>
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
                            //cookie方法

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
<!-- file="nav.html"-->
<!-- mobile nav-->
<div class="hidden-md hidden-lg mobile-nav navbar-fixed-top">
    <div class="container">
        <ul class="list-inline">
            <li ><a href="index.php">发现</a></li>
            <li><a href="myGroup.php">我的星球</a></li>
            <li class="active"><a href="groups.php">全部星球</a></li>
        </ul>
    </div>
</div>
<!-- framework-->
<!-- content-->
<div class="framework-content">
    <div class="container">
        <div class="row">
            <!-- main framework-->
            <div class="col-md-12">
                <section>
                    <div class="delete-float">
                        <h2 class="pull-left">全部星球</h2>
                        <a href="createGroup.php" class="pull-right btn btn-primary">创建星球</a>
                    </div>
                    <ul class="list-unstyled top-ic ">
                        <?php
                        include_once "conn.php";
                        //小组分页
                        $sql="select ID from group_base";
                        $query=mysql_query($sql);
                        $all_num=mysql_num_rows($query);//总条数
                        $page_num=20;//每页条数
                        $page_all_num=ceil($all_num/$page_num);//总页数
                        $page=empty($_GET['page'])?1:$_GET['page'];//当前页数
                        $page=(int)$page;//安全强制转换
                        $limit_st=($page-1)*$page_num;//起始数
                        //显示小组列表
                        $sql="SELECT ID,COUNT(userID) AS count FROM group_detail "
                            ."GROUP BY ID HAVING COUNT(userID)>=1 "
                            ."ORDER BY COUNT(userID) DESC "
                            ."LIMIT $limit_st,$page_num";
                        $query=mysql_query($sql,$conn);
                        while($arr=mysql_fetch_array($query)) {

                            $groupID = $arr['ID'];
                            $count = $arr['count'];
                            $sql2 = "SELECT name FROM  group_base WHERE ID='$groupID'";
                            $result = mysql_fetch_array(mysql_query($sql2));
                            $groupName = $result['name'];
                            ?>
                            <li class="">
                                <a><img src="image/logo-3x.png"></a>

                                <div class="text-center">
                                    <?php
                                    echo "<a href=\"enterLists.php?groupID=" . $groupID . "\"><p>" . $groupName . "</p></a>";
                                    ?>

                                    <p><?php echo $count; ?>个成员</p>
                                </div>
                            </li>
                            <?php
                        }
                        $px=$page>=$page_all_num?$page_all_num:$page+1;//下一页
                        $ps=$page<=1?1:$page-1;//上一页
                        ?>

                    </ul>

                </section>
            </div>

        </div>
    </div>
</div>
<!-- file="page.html"-->
<!-- page-->
<div class="container page-nav ">
    <div class="row">
        <div class="col-md-12 hidden-lg hidden-md">
            <ul class="list-unstyled list-inline ">
                <li><a href="groups.php?page=<?php echo $ps?>">上一页</a></li>
                <li><?php echo  $page." / ".$page_all_num ?></li>
                <li><a href="groups.php?page=<?php echo $px?>">下一页</a></li>
            </ul>
        </div>

    </div>
</div>
<div class="container hidden-sm hidden-xs delete-float">
    <nav class="text-center">
        <ul class="pagination">
            <li>
                <a href="groups.php?page=<?php echo $ps?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php
                if ($page_all_num>=5) {
                    if ($page <= 3) {
                        for ($num = 0; $num < 5; $num++) {
                            $page1= 1 + $num;
                            echo "<li><a href=\"groups.php?page=$page1\">$page1</a></li>";
                        }
                    } elseif ($page > 3 && $page+4<=$page_all_num) {
                        for ($num = 0; $num < 5; $num++) {
                            $page_ = $page + $num - 2;
                            echo "<li><a href=\"groups.php?page=$page_\">$page_</a></li>";
                        }
                    }else {
                            for ($num=0; $num < 5; $num++) {
                                $x=$page_all_num-3;
                                $page_ =$x + $num - 1;
                                echo "<li><a href=\"groups.php?page=$page_\">$page_</a></li>";
                            }
                    }
                }else{
                    for($x=1;$x<=$page_all_num;$x++){
                        echo "<li><a href=\"groups.php?page=$x\">$x</a></li>";
                    }
                }
            ?>
            <li>
                <a href="groups.php?page=<?php echo $px?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
</body>
</html>