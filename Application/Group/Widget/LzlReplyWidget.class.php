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
class LzlReplyWidget extends Controller
{

    /* 显示指定分类的同级分类或子分类列表 */
    public function  lzlReply($reply_id)
    {
        $this->assignData($reply_id);
        $this->display('Widget/lzlreply');
    }

    public function lzlHtml($reply_id)
    {
        $this->assignData($reply_id);
        return $this->fetch('Widget/lzlreply');
    }

    private function assignData($reply_id)
    {
        $reply = D('Group/GroupPostReply')->getReply($reply_id);
        $lzlModel = D('GroupLzlReply');
        $map = array('status' => 1, 'to_f_reply_id' => $reply_id);
        $r = modC('GROUP_LZL_SHOW_COUNT',5,'GROUP');

        $order = modC('GROUP_LZL_REPLY_ORDER',1,'GROUP') == 1 ? 'create_time asc' : 'create_time desc';

        $lzl_list = $lzlModel->getList(array('where' => $map, 'order' => $order, 'page' => 1, 'count' => $r));
        $lzl_total_count = $lzlModel->where($map)->count();
        $data['to_f_reply_id'] = $reply_id;
        $pageCount = ceil($lzl_total_count / $r);
        $html = getPageHtml('group_lzl_page', $pageCount, $data, 1);
        $this->assign('post', D('Group/GroupPost')->getPost($reply['post_id']));
        $this->assign('html', $html);
        $this->assign('lzl_total_count', $lzl_total_count);
        $this->assign('r', $r);
        $this->assign('lzl_list', $lzl_list);
        $this->assign('reply', $reply);
    }


}
