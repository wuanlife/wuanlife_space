<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Group\Widget;

use Think\Controller;

/**
 * 活跃的会员widget
 * 用于动态调用分类信息
 */
class HotPeopleWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($group_id)
    {

        $hot_people = S('hot_people_'.$group_id);

        if (empty($hot_people)) {
            $hot_people = D('GroupMember')->where(array( 'status' => 1,'group_id'=>$group_id))->limit(8)->order('activity desc')->select();
            foreach ($hot_people as &$val) {
                $val['user'] =  query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), $val['uid']);

            }
            unset($val);
            S('hot_people_'.$group_id , $hot_people, 10);
        }
        $this->assign('hot_people', $hot_people);

        $all = D('GroupMember')->where(array( 'status' => 1,'group_id'=>$group_id))->limit(32)->order('create_time desc')->select();
        foreach ($all as &$val) {
            $val['user'] =  query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), $val['uid']);
        }
        unset($val);
        $this->assign('all', $all);
        $this->display('Widget/hot_people');

    }

}
