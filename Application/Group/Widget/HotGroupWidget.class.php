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
 * 活跃的群组widget
 * 用于动态调用分类信息
 */
class HotGroupWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists()
    {

        $hot_group = S('hot_group');
        if (empty($hot_group)) {
            $hot_group = D('Group')->where(array( 'status' => 1))->limit(5)->order('activity desc')->select();
            foreach ($hot_group as &$val) {
                $val['member_count'] = D('GroupMember')->where(array('group_id' => $val['id']))->count();
            }
            S('hot_group' , $hot_group, 10);
        }
        $this->assign('hot_group', $hot_group);
        $this->display('Widget/hot_group');

    }

}
