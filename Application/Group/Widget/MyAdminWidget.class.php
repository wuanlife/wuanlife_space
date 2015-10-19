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
class MyAdminWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists()
    {

        $my_admin = S('group_my_admin_' . is_login());

        if (empty($my_admin)) {
            $my_admin = D('Group')->where(array('uid' => is_login(), 'status' => 1))->limit(5)->order('rand()')->select();
            foreach ($my_admin as &$val) {
                $val['member_count'] = D('GroupMember')->where(array('group_id' => $val['id']))->count();
            }
            S('group_my_admin_' . is_login(), $my_admin, 60);
        }

        $this->assign('my_admin', $my_admin);


        $this->display('Widget/admin');

    }

}
