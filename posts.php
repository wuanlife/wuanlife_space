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
    <!-- file="nav.html"-->
    <!-- mobile nav-->
    <div class="hidden-md hidden-lg mobile-nav navbar-fixed-top">
        <div class="container">
            <ul class="list-inline">
                <li class="active"><a href="index.php">发现</a></li>
                <li><a href="myGroup.php">我的星球</a></li>
                <li><a href="groups.php">全部星球</a></li>
            </ul>
        </div>
    </div>
    <!-- framework-->
    <!-- content-->
    <div class="framework-content">
        <div class="container">
            <div class="row">
                <!-- main framework-->
                <?php
                $postID=empty($_GET['P_ID'])?1:$_GET['P_ID'];
                include "conn.php";
                //小组分页
                $sql="select ID from post_detail pb where pb.ID = $postID";
                $query=mysql_query($sql);
                $all_num=mysql_num_rows($query);//总条数
                $page_num=30;//每页条数
                $page_all_num=ceil($all_num/$page_num);//总页数
                $page=empty($_GET['page'])?1:$_GET['page'];//当前页数
                $page=(int)$page;//安全强制转换
                $limit_st=($page-1)*$page_num;//起始数
                //显示帖子列表

                $sql="SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name\n"
                    . " FROM post_base pb,post_detail pd,group_base gb,user_base ub\n"
                    . " WHERE ub.ID = pd.postID\n"
                    . " AND pb.groupID = gb.ID\n"
                    . " AND pb.ID = pd.ID\n"
                    . " AND pd.ID = $postID\n"
                    . " ORDER BY pd.floor\n"
                    . "LIMIT $limit_st,$page_num";
                $result = mysql_query($sql);
                $row = mysql_fetch_array($result);
                ?>

                <div class="col-md-12">
                    <section>
                        <article>
                            <?php
                            echo "<h3>". $row['title'] ."</h3>";
                            ?>
                            <footer class="footer">
                                <?php
                                echo "<span class=\"pull-left\"><a href=\"\">". $row['nickName'] ."</a> 发表于 <a href=\"\">". $row['name'] ."</a></span>";
                                echo "<span class=\"pull-right\">". $row['createTime'] ."</span>";
                                ?>
                            </footer>
                            <div>
                                <?php
                                echo "<p>". $row['text'] ."</p>";
                                ?>
                            </div>




                            <!-- reply list-->
                            <!--  -->
                            <?php
                            while($row = mysql_fetch_array($result))
                            {
                            ?>
                            <section class="reply-list">
                                <div>

                                    <footer class="footer">
                                        <?php
                                        echo "<span class=\"pull-left\"><a href=\"\">". $row['nickName'] ."</a></span>";
                                        echo "<span class=\"pull-right\">". $row['createTime'] ."</span>";
                                        ?>
                                    </footer>
                                    <div>
                                        <?php
                                        echo "<p>". $row['text'] ."</p>";
                                        ?>
                                   </div>
                               </div>
                               <?php
                                }
                               $px=$page>=$page_all_num?$page_all_num:$page+1;//下一页
                               $ps=$page<=1?1:$page-1;//上一页
                                ?>
                        </section>

                        <form method="post" class="form-group form-max-none" onSubmit="return check_form(this);" action="<?php $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"]?>">
                            <textarea class="form-control" placeholder="输入回复内容" required="" rows="4" name="replyTest"></textarea>
                            <button type="submit" class="pull-right btn btn-primary">回复</button>
                        </form>

                    </article>
                </section>
            </div>

        </div>
    </div>
</div>

<?php
if(!empty($_POST)){
    if(isset($_COOKIE['nickName'])){
        $u_id = $_COOKIE["userID"];
        $p_id = $postID;
        $p_text = $_POST['replyTest'];
        $p_time = date('Y-m-d H:i',time());

        include "conn.php";
    //取得楼层
        $sql1 = "SELECT count(*) as floor \n"
        . "FROM `post_detail` \n"
        . "where ID=$page";
        $result1 = mysql_query($sql1);
        $row1 = mysql_fetch_array($result1);
        $p_floor = $row1['floor']+1;

    //插入数据
        $sql2 = "INSERT INTO post_detail (ID,postID,text,floor,createTime) VALUE ('$p_id','$u_id','$p_text','$p_floor','$p_time')";
        mysql_query($sql2);
        echo '<script language="javascript">';
        echo "location.href=window.location.href";
        echo '</script>';
    }else if(!isset($_COOKIE['nickName'])){
        echo '<script language="javascript">';
        echo 'alert("请先登录!");';
        echo '</script>';
    }
}


?>


<!-- file="page.html"-->
<!-- page-->
<div class="container page-nav ">
    <div class="row">
        <div class="col-md-12 hidden-lg hidden-md">
            <ul class="list-unstyled list-inline ">
                <li><a href="posts.php?P_ID=<?php echo $postID?>&page=<?php echo $ps?>">上一页</a></li>
                <li><?php echo  $page." / ".$page_all_num ?></li>
                <li><a href="posts.php?P_ID=<?php echo $postID?>&page=<?php echo $px?>">下一页</a></li>
            </ul>
        </div>

    </div>
</div>
<div class="container hidden-sm hidden-xs delete-float">
    <nav class="text-center">
        <ul class="pagination">
            <li>
                <a href="posts.php?P_ID=<?php echo $postID?>&page=<?php echo $ps?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php
            if ($page_all_num>=5) {
                if ($page <= 3) {
                    for ($num = 0; $num < 5; $num++) {
                        $page1= 1 + $num;
                        echo "<li><a href=\"posts.php?P_ID=".$postID."&page=".$page1."\">".$page1."</a></li>";
                    }
                } elseif ($page > 3 && $page+4<=$page_all_num) {
                    for ($num = 0; $num < 5; $num++) {
                        $page_ = $page + $num - 2;
                        echo "<li><a href=\"posts.php?P_ID=".$postID."&page=".$page_."\">".$page_."</a></li>";
                    }
                }else {
                    for ($num=0; $num < 5; $num++) {
                        $x=$page_all_num-3;
                        $page_ =$x + $num - 1;
                        echo "<li><a href=\"posts.php?P_ID=".$postID."&page=".$page_."\">".$page_."</a></li>";
                    }
                }
            }else{
                for($x=1;$x<=$page_all_num;$x++){
                    echo "<li><a href=\"posts.php?P_ID=".$postID."&page=".$x."\">".$x."</a></li>";
                }
            }
            ?>
            <li>
                <a href="posts.php?P_ID=<?php echo $postID?>&page=<?php echo $px?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
</body>
</html>
