<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "root";
$db_data = "wuan";
$conn=mysql_connect($db_host, $db_user, $db_pass);
if ($conn) {
    //echo "连接成功";
} else {
    echo "连接失败".mysql_error();
}
if (mysql_select_db($db_data)) {
    //echo "选择数据库成功";
} else {
    echo "选择数据库失败";
}
mysql_query("set names 'utf8'");
/**
 * Created by PhpStorm.
 * User: fyhqq
 * Date: 2016/1/20
 * Time: 13:12
 */