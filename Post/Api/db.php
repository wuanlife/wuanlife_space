<?php
/**
 * 数据库接口服务类
 */
class Api_DB extends PhalApi_Api{
    public function getRules(){
        return array(
            'test' => array(
                'id' => array('name' => 'id', 'type' => 'int',  'desc' => '用户Id'),
            ),
            'getIndexPost' => array(
                'id' => array('name' => 'id', 'type' => 'int',  'desc' => '用户Id'),
            ),
            'getGroupPost' => array(
                'id' => array('name' => 'groupID', 'type' => 'int', 'require' => true, 'desc' => '小组Id'),
            ),
            'getMyGroupPost' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户Id'),
            ),
            'getPostDetail' => array(
                'id' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '帖子Id'),
            ),
        );
    }


    /**
     * test
     * @desc test显示
     * @return array data 结果集
     */
    public function test(){
        $data[]= DI()->notorm->post_base
        ->SELECT('group_base.name')
        ->fetchAll();

        $data[]= DI()->notorm->post_detail
        ->SELECT('post_base.title,post_detail.text,user_base.nickName,post_detail.createTime')
        ->WHERE('floor','1')
        ->order('createTime DESC')
        ->fetchAll();

        return $data;
    }


    /**
     * 主页
     * @desc 主页面帖子显示
     * @return array data 结果集
     */
    public function getIndexPost(){
        $data   = array();

        $domain = new Domain_db();
        $data = $domain->getIndexPost();

        return $data;
    }

    /**
     * 小组页面
     * @desc 小组页面帖子显示
     * @return array data 结果集
     */
    public function getGroupPost(){
        $data   = array();

        $sql = 'SELECT pb.title,pd.ID,pd.text,MAX(pd.createTime) AS createTime,ub.nickName,gb.name,gb.ID as Gid '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.ID=pd.ID AND pb.userID=ub.ID AND pb.groupID=$groupID AND pb.groupID=gb.ID '
             . 'GROUP BY ID '
             . 'ORDER BY MAX(pd.createTime) DESC ';
            // . 'LIMIT $limit_st,$page_num' ;
        $data= DI()->notorm->user_base->queryAll($sql, array());

        return $data;
    }

    /**
     * 我的星球
     * @desc 我的星球页面帖子显示
     * @return array data 结果集
     */
    public function getMyGroupPost(){
        $data   = array();

        $sql = 'SELECT pb.title,pd.ID,pd.text,MAX(pd.createTime) AS createTime,ub.nickName,gb.name,gb.ID as Gid '
             . 'FROM post_detail pd,post_base pb ,group_base gb,user_base ub '
             . 'WHERE pb.ID=pd.ID AND pb.userID=ub.ID AND pb.groupID=gb.ID AND gb.ID IN (SELECT ID FROM group_detail gd WHERE gd.userID = $userID)'
             . 'GROUP BY ID '
             . 'ORDER BY MAX(pd.createTime) DESC';
            // . 'LIMIT $limit_st,$page_num';
        $data= DI()->notorm->user_base->queryAll($sql, array());

        return $data;
    }
    /**
     * 帖子详情
     * @desc 帖子内容显示
     * @return array data 结果集
     */
    public function getPostDetail(){

         $data[]= DI()->notorm->post_base
        ->SELECT('post_base.title,user_base.nickName,group_base.name')
        ->WHERE('post_base.id = ?','1')
        ->fetchRow();
         $data[]= DI()->notorm->post_detail
        ->SELECT('post_detail.text,user_base.nickName,post_detail.createTime')
        ->WHERE('post_base.id = ?','1')
        ->order('floor ASC')
        ->fetchAll();


        return $data;
    }
}