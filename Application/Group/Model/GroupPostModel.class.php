<?php
namespace Group\Model;
use Think\Model;

class GroupPostModel extends Model {


    protected $tableName='group_post';
    protected $_validate = array(
        array('title', '1,100', '标题长度不合法', self::EXISTS_VALIDATE, 'length'),
        array('content', '1,40000', '内容长度不合法', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('last_reply_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );


    public function getPost($id){
        $post = S('group_post_'.$id);
        if(is_bool($post)){
            $post = $this->where(array('id'=>$id,'status'=>1))->find();
            if($post){
                $post['user'] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), $post['uid']);
                S('group_post_'.$id,$post,60*60);
            }
        }
        return $post;
    }


    public function createPost($data) {
        //新增帖子
        $data = $this->create($data);
        //对帖子内容进行安全过滤
        if(!$data) return false;
        $result = $this->add($data);
        if(!$result) {
            return false;
        }
        action_log('add_group_post','GroupPost',$result,is_login());
        //增加板块的帖子数量
        D('Group')->where(array('id'=>$data['group_id']))->setInc('post_count');
        //返回帖子编号
        return $result;
    }


    public function editPost($data) {

        $data = $this->create($data);
        if(!$data) return false;
        $result = $this->save($data);
        if(!$result) {
            return false;
        }
        action_log('edit_group_post','GroupPost',$data['id'],is_login());
        S('group_post_'.$data['id'],null);
        return $result;
    }


}