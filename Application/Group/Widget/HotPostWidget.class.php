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
 * 分类widget
 * 用于动态调用分类信息
 */
class HotPostWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function lists($group_id)
    {
        $group_id=intval($group_id);
        $posts = S('group_hot_posts_' . $group_id);

        $map['status']=1;
        $time=time()-604800;//一周以内
        $map['create_time']=array('gt',$time);
        if (empty($posts)) {
            if ($group_id == 0) {
                $posts = D('GroupPost')->where($map)->order('reply_count desc')->limit(9)->select();
            } else {
                $map['group_id']=$group_id;

                $posts = D('GroupPost')->where($map)->order('reply_count desc')->limit(9)->select();
            }
            S('group_hot_posts_' . $group_id, $posts, 300);
        }

        $this->assign('posts', $posts);
        $this->display('Widget/hot');

    }

}
