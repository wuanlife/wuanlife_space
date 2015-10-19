<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-6-8
 * Time: 下午4:37
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Group\Widget;


use Group\Model\GroupMemberModel;
use Group\Model\GroupModel;
use Think\Controller;

class HomeBlockWidget extends Controller{
    public function render()
    {
        $this->assignGroup();
        $this->assignGroupPost();
        $this->display(T('Application://Group@Widget/homeblock'));
    }

    private function assignGroup()
    {
        $data = S('GROUP_SHOW_DATA');
        if (empty($data)) {
            $group_ids = modC('GROUP_SHOW', '', 'Group');
            $cache_time = modC('GROUP_SHOW_CACHE_TIME', 600, 'Group');
            $group_ids=explode('|', $group_ids);
            $groupModel = new GroupModel();
            $group= $groupModel->where(array('status' => 1,'id' => array('in',$group_ids)))->select();
            $group=array_combine(array_column($group,'id'),$group);
            $data=array();
            foreach($group_ids as $val){
                if($val!=''&&$group[$val]){
                    $data[]=$group[$val];
                }
            }
            if(!count($data)){
                $data=1;
            }
            S('GROUP_SHOW_DATA', $data,$cache_time);
        }
        if($data==1){
            $data=null;
        }
        $groupMemberModel=new GroupMemberModel();
        foreach($data as &$val){
            $val['is_attend']=$groupMemberModel->where(array('group_id'=>$val['id'],'uid'=>get_uid(),'status'=>1))->count();
        }
        $this->assign('group_show', $data);
    }

    private function assignGroupPost()
    {
        $list = S('GROUP_POST_SHOW_DATA');
        if (empty($list)) {
            $order_key=modC('FORUM_POST_ORDER','last_reply_time', 'Forum');
            $order_type=modC('FORUM_POST_TYPE','desc', 'Forum');
            $limit=modC('FORUM_POST_SHOW_NUM',5, 'Forum');
            $cache_time = modC('FORUM_POST_CACHE_TIME', 600, 'Forum');

            $groupModel =  new GroupModel();
            $group_ids = $groupModel->where(array('status' => 1))->field('id')->select();
            $group_ids = getSubByKey($group_ids, 'id');
            $list = M('GroupPost')->where(array('status' => 1, 'group_id' => array('in', $group_ids)))->order($order_key.' '.$order_type)->limit($limit)->select();
            foreach($list as &$val){
                $val['group']=$groupModel->getGroup($val['group_id']);
            }
            unset($val);
            S('GROUP_POST_SHOW_DATA', $list,$cache_time);
        }
        $this->assign('group_post_list', $list);
    }
} 