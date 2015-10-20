<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Mob\Widget;

use Think\Controller;

/**
 * Class DynamicWidget  群组动态
 * @package Group\Widget
 * @author:xjw129xjt xjt@ourstu.com
 */
class DynamicWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($dynamic_id='')
    {

        $dynamic = D('Mob/GroupDynamic')->getDynamic($dynamic_id);
        $user= query_user(array('avatar128','avatar64','nickname','uid','space_mob_url'),$dynamic['uid']);
        $this->assign('dynamic', $dynamic);
        $this->assign('user', $user);
        $this->display('Widget/dynamic');

    }

}
