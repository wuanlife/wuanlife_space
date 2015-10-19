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
 * 某人创建的群组widget
 * 用于动态调用分类信息
 */
class SomebodyCreateWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($uid =0)
    {
        $create = D('Group')->where(array('uid' =>$uid , 'status' => 1))->select();
        foreach ($create as &$val) {
            $val['member_count'] = D('GroupMember')->where(array('group_id' => $val['id']))->count();
        }
        $this->assign('my_attendance', $create);
        $this->assign('uid',$uid);
        $this->display(T('Group@Widget/attendance'));

    }

}
