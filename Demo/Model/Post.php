<?php

class Model_Post extends PhalApi_Model_NotORM {

    public function getIndexPost() {
        $sql = 'SELECT pb.title,pd.text,MAX(pd.createTime) AS createTime,ub.nickName,gb.name '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.id=pd.post_base_id AND pb.user_base_id=ub.id AND pb.group_base_id=gb.id '
             . 'GROUP BY pb.id '
             . 'ORDER BY MAX(pd.createTime) DESC';
        return DI()->notorm->user_base->queryAll($sql, array());
    }



    protected function getTableName($id) {
        return 'user';
    }
}
