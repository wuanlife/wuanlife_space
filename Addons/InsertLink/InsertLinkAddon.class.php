<?php

namespace Addons\InsertLink;

use Common\Controller\Addon;

class InsertLinkAddon extends Addon
{

    public $info = array(
        'name' => 'InsertLink',
        'title' => '插入网址链接',
        'description' => '用于分享网页',
        'status' => 1,
        'author' => '駿濤',
        'version' => '1.0.0'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的InsertImage钩子方法
    public function weiboType($param)
    {
        $this->display('insertLink');
    }



    public function fetchLink($weibo)
    {
        $weibo_data = unserialize($weibo['data']);
        $this->assign('weibo',$weibo);
        $this->assign('weibo_data',$weibo_data);
        return $this->fetch('display');
    }


    public function parseExtra(&$extra = array()){
        $extra['title'] = text($extra['title']);
        $extra['description'] = text($extra['description']);
        $extra['keywords'] = text($extra['keywords']);
        return $extra;
    }



}