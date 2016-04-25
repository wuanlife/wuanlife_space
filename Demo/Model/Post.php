<?php

class Model_Post extends PhalApi_Model_NotORM {

    public function getIndexPost($page) {

        $num=6;
        $rs   = array();
        $sql = 'SELECT pb.id AS postID,pb.title,pd.text,pd.createTime,ub.nickname,gb.id AS groupID,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->user_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base ';

        $params = array(':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getGroupPost($groupID,$page) {

        $num=6;
        $rs   = array();
        $groupData=DI()->notorm->group_base
        ->select('id as groupID,name as groupName')
        ->where('id =?',$groupID)
        ->fetchAll();

        $rs['groupID'] = $groupData['0']['groupID'];
        $rs['groupName'] = $groupData['0']['groupName'];

        $sql = 'SELECT  pb.id AS postID,pb.title,pd.text,pd.createTime,ub.id,ub.nickname '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id=:group_id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':group_id' =>$groupID,':start' =>($page-1)*$num , ':num' =>$num);
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb '
             . 'WHERE pb.group_base_id=gb.id AND gb.id=:group_id ';

        $params = array(':group_id' =>$groupID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getMyGroupPost($userID,$page) {

        $num=6;
        $rs   = array();
        $sql = 'SELECT  pb.id AS postID,pb.title,pd.text,pd.createTime,ub.nickname,gb.id AS groupID,gb.name AS groupName '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id =:user_id )'
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
              . 'LIMIT :start,:num ';
        $params = array(':user_id' =>$userID,':start' =>($page-1)*$num , ':num' =>$num );
        $rs['posts'] = DI()->notorm->post_base->queryAll($sql, $params);

        $sql = 'SELECT ceil(count(*)/:num) AS pageCount '
             . 'FROM post_base pb,group_base gb,group_detail gd '
             . 'WHERE pb.group_base_id=gb.id AND gb.id=gd.group_base_id AND gd.user_base_id=:user_id ';

        $params = array(':user_id' =>$userID,':num' =>$num);
        $pageCount = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['pageCount'] = (int)$pageCount[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
        return $rs;
    }

    public function getPostBase($postID) {

        $rs   = array();
        $sql = 'SELECT pb.id AS postID,gb.id AS groupID,gb.name AS groupName,pb.title,pd.text,ub.id,ub.nickname,pd.createTime '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.id=:post_id AND pd.floor=1' ;
        $params = array(':post_id' =>$postID );
        $rs = DI()->notorm->post_base->queryAll($sql, $params);

        return $rs;
    }

    public function getPostReply($postID,$page) {

        $num=9;
        $rs   = array();
        $rs['reply']= DI()->notorm->post_detail
        ->SELECT('post_detail.text,user_base.nickname,post_detail.createTime')
        ->WHERE('post_detail.post_base_id = ?',$postID)
        ->AND('post_detail.floor > ?','1')
        ->order('post_detail.floor ASC')
        ->limit(($page-1)*$num,$num)
        ->fetchALL();

        $rs['postID']=$postID;
        $sql = 'SELECT ceil(count(*)/:num) AS pageCount,count(*) AS replyCount '
         . 'FROM post_detail '
         . 'WHERE post_base_id=:post_id AND post_detail.floor>1 ';

        $params = array(':post_id' =>$postID,':num' =>$num);
        $count = DI()->notorm->user_base->queryAll($sql, $params);
        $rs['replyCount'] = (int)$count[0]['replyCount'];
        $rs['pageCount'] = (int)$count[0]['pageCount'];
        if ($rs['pageCount'] == 0 ){
            $rs['pageCount']=1;
        }
        $rs['currentPage'] = $page;
        return $rs;
    }
    public function PostReply($data) {
        $rs = array();
        $time = date('Y-m-d H:i:s',time());
        //查询最大楼层
        $sql=DI()->notorm->post_detail
        ->select('post_base_id,user_base_id,max(floor)')
        ->where('post_base_id =?',$data['post_base_id'])
        ->fetchone();
        $data['createTime'] = $time;
        $data['floor'] = ($sql['max(floor)'])+1;
        $rs = DI()->notorm->post_detail->insert($data);
        return $rs;
    }
    public function editPost($data) {
        $rs = array();
        $time = date('Y-m-d H:i:s',time());
        $b_data = array(
                'title' => $data['title'],
        );
        $d_data = array(
                'text' => $data['text'],
                'createTime' => $time,
        );
        $sql=DI()->notorm->post_detail
        ->select('user_base_id')
        ->where('post_base_id =?',$data['post_base_id'])
        ->fetchone();
        if($data['user_id']==$sql['user_base_id']) {
            $pb = DI()->notorm->post_base
            ->where('id =?', $data['post_base_id'])
            ->update($b_data);
            $pd = DI()->notorm->post_detail
            ->where('post_base_id =?', $data['post_base_id'])
            ->AND('post_detail.floor = ?','1')
            ->update($d_data);
            $rs['code']=1;
            $rs['info']['post_base_id']=$data['post_base_id'];
            $rs['info']['user_base_id']=$data['user_id'];
            $rs['info']['title']=$data['title'];
            $rs['info']['text']=$data['text'];
            $rs['info']['floor']=1;
            $rs['info']['createTime']=$time;
        }else{
            $rs['code']=0;
            $rs['msg']="您没有权限!";
        }
        return $rs;
    }
    protected function getTableName($id) {
        return 'user';
    }
}
