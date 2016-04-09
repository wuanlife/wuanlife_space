<?php
/**
 * 数据库接口服务类
 */
class Api_Post extends PhalApi_Api{
    public function getRules(){
        return array(
            'getIndexPost' => array(
                'page' =>array('name' => 'pn', 'type' => 'int',  'desc' => '第几页', 'default' => '1'),
            ),
            'getGroupPost' => array(
                'groupID' => array('name' => 'group_id', 'type' => 'int', 'require' => true, 'desc' => '小组ID'),
                'page' =>array('name' => 'pn', 'type' => 'int',  'desc' => '第几页', 'default' => '1'),
            ),
            'getMyGroupPost' => array(
                'userID' => array('name' => 'id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
                'page' =>array('name' => 'pn', 'type' => 'int',  'desc' => '第几页', 'default' => '1'),
            ),
            'getPostBase' => array(
                'postID' => array('name' => 'post_id', 'type' => 'int', 'require' => true, 'desc' => '帖子ID'),
            ),
            'getPostReply' => array(
                'postID' => array('name' => 'post_id', 'type' => 'int', 'require' => true, 'desc' => '帖子ID'),
                'page' =>array('name' => 'pn', 'type' => 'int',  'desc' => '当前回帖的页码', 'default' => '1'),
            ),
        );
    }

    /**
     * 主页
     * @desc 主页面帖子显示
     * @return int post.id 帖子ID
     * @return string post.title 标题
     * @return string post.text 内容
     * @return date post.createTime 发帖时间
     * @return string post.nickname 发帖人
     * @return string post.groupName 星球名称
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function getIndexPost(){
        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getIndexPost($this->page);

        return $data;
    }

    /**
     * 小组页面
     * @desc 小组页面帖子显示
     * @return int post.id 帖子ID
     * @return string post.title 标题
     * @return string post.text 内容
     * @return date post.createTime 发帖时间
     * @return string post.nickname 发帖人
     * @return string post.groupName 星球名称
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function getGroupPost(){
        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getGroupPost($this->groupID,$this->page);

        return $data;
    }

    /**
     * 我的星球
     * @desc 我的星球页面帖子显示
     * @return int post.id 帖子ID
     * @return string post.title 标题
     * @return string post.text 内容
     * @return date post.createTime 发帖时间
     * @return string post.nickname 发帖人
     * @return string post.groupName 星球名称
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function getMyGroupPost(){
        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getMyGroupPost($this->userID,$this->page);

        return $data;
    }
    /**
     * 帖子的内容
     * @desc 单个帖子的内容显示
     * @return int post.id 帖子ID
     * @return string post.groupName 星球名称
     * @return string post.title 标题
     * @return string post.text 内容
     * @return string post.nickname 发帖人
     * @return date post.createTime 发帖时间
     */
    public function getPostBase(){

        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getPostBase($this->postID);

        return $data;
    }

    /**
     * 帖子的回复
     * @desc 单个帖子的回复内容显示
     * @return int post.id 帖子ID
     * @return string reply.text 内容
     * @return string reply.nickname 回帖人
     * @return date reply.createTime 回帖时间
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function getPostReply(){

        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getPostReply($this->postID,$this->page);

        return $data;
    }
}