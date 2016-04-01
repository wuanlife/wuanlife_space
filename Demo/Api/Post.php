<?php
/**
 * 数据库接口服务类
 */
class Api_Post extends PhalApi_Api{
    public function getRules(){
        return array(
            'getIndexPost' => array(
                'id' => array('name' => 'id', 'type' => 'int',  'desc' => '用户Id'),
            ),
            'getGroupPost' => array(
                'groupID' => array('name' => 'group_id', 'type' => 'int', 'require' => true, 'desc' => '小组Id'),
            ),
            'getMyGroupPost' => array(
                'userID' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户Id'),
            ),
            'getPostDetail' => array(
                'postID' => array('name' => 'post_id', 'type' => 'int', 'require' => true, 'desc' => '帖子Id'),
            ),
        );
    }

    /**
     * 主页
     * @desc 主页面帖子显示
     * @return array data 结果集
     */
    public function getIndexPost(){
        $data   = array();

        $domain = new Domain_Post();
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

        $domain = new Domain_Post();
        $data = $domain->getGroupPost($this->groupID);

        return $data;
    }

    /**
     * 我的星球
     * @desc 我的星球页面帖子显示
     * @return array data 结果集
     */
    public function getMyGroupPost(){
        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getMyGroupPost($this->userID);

        return $data;
    }
    /**
     * 帖子详情
     * @desc 帖子内容显示
     * @return array data 结果集
     */
    public function getPostDetail(){

        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getPostDetail($this->postID);

        return $data;
    }
}