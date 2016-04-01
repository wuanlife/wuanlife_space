<?php

class Model_Post extends PhalApi_Model_NotORM {

    public function getIndexPost() {
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC';
        return DI()->notorm->user_base->queryAll($sql, array());
    }

    public function getGroupPost($groupID) {
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.group_base_id=:group_id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC ';
        $params = array(':group_id' =>$groupID );
        return DI()->notorm->post_base->queryAll($sql, $params);
    }

    public function getMyGroupPost($userID) {
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'AND gb.id in (SELECT group_base_id FROM group_detail gd WHERE gd.user_base_id =:user_id )'
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC ';
        $params = array(':user_id' =>$userID );
        return DI()->notorm->post_base->queryAll($sql, $params);
    }

    public function getPostDetail($postID) {
        $rs   = array();
        $sql = 'SELECT pb.title,pd.text,pd.createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id AND pb.id=:post_id AND pd.floor=1' ;
        $params = array(':post_id' =>$postID );
        $rs[] = DI()->notorm->post_base->queryAll($sql, $params);

        $rs[]= DI()->notorm->post_detail
        ->SELECT('post_detail.text,user_base.nickName,post_detail.createTime')
        ->WHERE('post_base.id = ?','1')
        ->AND('post_detail.floor > ?','1')
        ->order('floor ASC')
        ->fetchAll();

        return $rs;
    }



    protected function getTableName($id) {
        return 'user';
    }
}
