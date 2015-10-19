<?php
namespace Group\Widget;
use Think\Controller;
/**
 * Class DetailWidget  群组详情widget
 * @package Group\Widget
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
class DetailWidget extends Controller
{

    public function group($group_id,$param=null)
    {
        $this->assignGroup($group_id,$param);
        $this->display( T('Group@default/Widget/detail/groupdetail'));

    }

    public function post($post_id,$param=null){
        $this->assignPost($post_id,$param);
        $this->display(T('Group@default/Widget/detail/postdetail'));
    }


    public function reply($reply_id,$param=null){
        $this->assignReply($reply_id,$param);
        $this->display( T('Group@default/Widget/detail/replydetail'));
    }


    public function lzlReply($lzl_id,$param=null){
        $this->assignLzlReply($lzl_id,$param);
        $this->display( T('Group@default/Widget/detail/lzldetail'));
    }


    public function lzlReplyHtml($lzl_id,$param=null){
        $this->assignLzlReply($lzl_id,$param);
        return $this->fetch( T('Group@default/Widget/detail/lzldetail'));
    }

    private function assignGroup($group_id,$param=null){
        $group_id=intval($group_id);
        $group = D('Group/Group')->getGroup($group_id);
        $this->assign('group', $group);
        $this->assign($param);
    }
    private function assignPost($post_id,$param=null){
        $post_id=intval($post_id);
        $post = D('Group/GroupPost')->getPost($post_id);
        $this->assign('post', $post);
        $this->assign($param);
    }
    private function assignReply($reply_id,$param=null){
        $reply_id=intval($reply_id);
        $reply = D('Group/GroupPostReply')->getReply($reply_id);
        $reply['content'] = D('ContentHandler')->displayHtmlContent($reply['content']);
        $this->assign('reply', $reply);
        $this->assign($param);
    }
    private function assignLzlReply($lzl_id,$param=null){
        $lzl_id=intval($lzl_id);
        $lzl = D('Group/GroupLzlReply')->getLzlReply($lzl_id);
        $this->assign('lzl', $lzl);
        $this->assign($param);
    }


}
