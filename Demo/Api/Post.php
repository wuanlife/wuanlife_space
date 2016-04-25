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
                'userID' => array('name' => 'id', 'type' => 'int', 'desc' => '用户ID'),
            ),
            'getPostReply' => array(
                'postID' => array('name' => 'post_id', 'type' => 'int', 'require' => true, 'desc' => '帖子ID'),
                'page' =>array('name' => 'pn', 'type' => 'int',  'desc' => '当前回帖的页码', 'default' => '1'),
            ),
            'PostReply' => array(
                'post_base_id' => array('name' => 'post_id', 'type' => 'int', 'require' => true, 'desc' => '帖子ID'),
                'text'   => array('name' => 'text', 'type' => 'string', 'min' => '1','require' => true, 'desc' => '回复内容'),
                'user_id' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '回复人ID')
            ),
            'editPost'  => array(
                'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
                'post_base_id' => array('name' => 'post_id', 'type' => 'int', 'require' => true, 'desc' => '帖子ID'),
                'title' => array('name' => 'title', 'type' => 'string', 'min' => '1','require' => true, 'desc' => '帖子标题'),
                'text' => array('name' => 'text', 'type' => 'string', 'min' => '1','require' => true, 'desc' => '帖子内容'),
            )
        );
    }

    /**
     * 主页
     * @desc 主页面帖子显示
     * @return int posts.postID 帖子ID
     * @return string posts.title 标题
     * @return string posts.text 内容
     * @return date posts.createTime 发帖时间
     * @return string posts.nickname 发帖人
     * @return int posts.groupID 星球ID
     * @return string posts.groupName 星球名称
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
     * 每个星球页面帖子显示
     * @desc 星球页面帖子显示
     * @return int posts.postID 帖子ID
     * @return string posts.title 标题
     * @return string posts.text 内容
     * @return date posts.createTime 发帖时间
     * @return string posts.nickname 发帖人
     * @return int posts.groupID 星球ID
     * @return string posts.groupName 星球名称
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
     * @return int posts.postID 帖子ID
     * @return string posts.title 标题
     * @return string posts.text 内容
     * @return date posts.createTime 发帖时间
     * @return string posts.nickname 发帖人
     * @return int posts.groupID 星球ID
     * @return string posts.groupName 星球名称
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
     * @return int postID 帖子ID
     * @return int groupID 星球ID
     * @return string groupName 星球名称
     * @return string title 标题
     * @return string text 内容
     * @return int id 用户ID
     * @return string nickname 发帖人
     * @return date createTime 发帖时间
     * @return boolean editRight 编辑权限(0为无权限，1有)
     */
    public function getPostBase(){

        // $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getPostBase($this->postID);

        $userID=$this->userID;
        if($data[0]['id']==$userID)
        {
            $data[0]['editRight']=1;
        }else{
            $data[0]['editRight']=0;
        }
        return $data[0];
    }

    /**
     * 帖子的回复
     * @desc 单个帖子的回复内容显示
     * @return string reply.text 内容
     * @return string reply.nickname 回帖人
     * @return date reply.createTime 回帖时间
     * @return int postID 帖子ID
     * @return int replyCount 回帖数    
     * @return int pageCount 总页数
     * @return int currentPage 当前页
     */
    public function getPostReply(){

        $data   = array();

        $domain = new Domain_Post();
        $data = $domain->getPostReply($this->postID,$this->page);

        return $data;
    }
    /**
     * 帖子的回复
     * @desc 单个帖子的回复操作
     * @return int info.post_base_id 帖子ID
     * @return int info.user_base_id 发帖人ID
     * @return int info.replyid 回复人ID
     * @return string info.text 回复内容
     * @return int info.floor 回复楼层
     * @return date info.createTime 回帖时间
     */
    public function PostReply(){
        $rs = array();
        $data = array(
            'post_base_id'  => $this->post_base_id,
            'user_base_id'  => $this->user_id,
            'replyid'       => NULL,
            'text'          => $this->text,
            'floor'         => '',
            'createTime'    => '',
        );
        $domain = new Domain_Post();
        $rs = $domain->PostReply($data);
        return $rs;
    }
    /**
     * 帖子的编辑
     * @desc 单个帖子的编辑操作
     * @return int code 操作码，1表示编辑成功，0表示编辑失败
     * @return int info.post_base_id 帖子ID
     * @return int info.user_base_id 编辑人ID
     * @return string info.title 编辑帖子标题
     * @return string info.text 编辑帖子内容
     * @return int info.floor 楼主楼层1
     * @return date info.createTime 编辑时间
     */
    public function editPost(){
        $rs = array();
        $data = array(
            'user_id'       => $this->user_id,
            'post_base_id'  => $this->post_base_id,
            'title'         => $this->title,
            'text'          => $this->text,
        );
        $domain = new Domain_Post();
        $rs = $domain->editPost($data);
        return $rs;
    }
}