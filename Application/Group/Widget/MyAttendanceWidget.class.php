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
 * 我加入的群组widget
 * 用于动态调用分类信息
 */
class MyAttendanceWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists()
    {


        $member = D('GroupMember')->where(array('uid' => is_login(), 'status' => 1))->order('last_view desc')->select();
        $group_ids = getSubByKey($member, 'group_id');
        $ids = implode(',', $group_ids);
        $order = "find_in_set( id ,'" . $ids . "') ";
        $my_attendance = D('Group')->where(array('id' => array('in', $group_ids), 'status' => 1))->limit(5)->order($order)->select();
        foreach ($my_attendance as &$val) {
            $val['member_count'] = D('GroupMember')->where(array('group_id' => $val['id']))->count();
        }
        $this->assign('my_attendance', $my_attendance);


        $this->display('Widget/attendance');

    }

}
