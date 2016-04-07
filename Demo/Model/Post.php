<?php

class Model_Post extends PhalApi_Model_NotORM {

    public function getIndexPost($page) {

        $num=6;
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':start' =>($page-1)*$num , ':num' =>$num);
        return DI()->notorm->user_base->queryAll($sql, $params);
    }

    public function getGroupPost($groupID,$page) {

        $num=6;
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id=:group_id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
             . 'LIMIT :start,:num ';
        $params = array(':group_id' =>$groupID,':start' =>($page-1)*$num , ':num' =>$num);
        return DI()->notorm->post_base->queryAll($sql, $params);
    }

    public function getMyGroupPost($userID,$page) {

        $num=6;
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id =:user_id )'
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC '
              . 'LIMIT :start,:num ';
        $params = array(':user_id' =>$userID,':start' =>($page-1)*$num , ':num' =>$num );
        return DI()->notorm->post_base->queryAll($sql, $params);
    }

    public function getPostDetail($postID,$page) {

        $num=6;
        $rs   = array();
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.id=:post_id AND pd.floor=1' ;
        $params = array(':post_id' =>$postID );
        $rs[] = DI()->notorm->post_base->queryAll($sql, $params);

        $rs[]= DI()->notorm->post_detail
        ->SELECT('post_detail.text,user_base.nickName,post_detail.createTime')
        ->WHERE('post_base.id = ?',$postID)
        ->AND('post_detail.floor > ?','1')
        ->order('floor ASC')
        ->limit(($page-1)*$num,$num)
        ->fetchAll();

        return $rs;
    }



    protected function getTableName($id) {
        return 'user';
    }
}
