<?php
include "conn.php";
$groupID=6;
$sql="SELECT pb.title,pd.ID,pd.text,MAX(pd.createTime) AS createTime,ub.nickName,gb.name,gb.ID as Gid
                          FROM post_detail pd,post_base pb ,group_base gb,user_base ub
                          WHERE pb.groupID='$groupID' AND pb.userID=ub.ID AND pb.ID=pd.ID AND pb.groupID=gb.ID
                          GROUP BY pd.ID
                          ORDER BY MAX(pd.createTime) DESC";
$result = mysql_query($sql);
while($row = mysql_fetch_assoc($result)){
    print_r($row);
    echo "</br>";
}


