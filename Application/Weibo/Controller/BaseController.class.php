<?php
/**
 * Created by PhpStorm.
 * User: Yixiao Chen
 * Date: 2015/5/5 0005
 * Time: 上午 9:47
 */

namespace Weibo\Controller;


use Think\Controller;

class BaseController extends  Controller{

    public function _initialize(){
        $sub_menu =
            array(
                'left' =>
                    array(
                        array('tab' => 'index', 'title' => "我的" . $this->MODULE_ALIAS, 'href' =>  U('index/index')),
                        array('tab' => 'hot', 'title' => '热门'.$this->MODULE_ALIAS, 'href' => U('index/index',array('type'=>'hot'))),
                        array('tab' => 'topic', 'title' => "热门话题" , 'href' => U('topic/topic')),
                    ),
                'right'=>array(
                    array('type'=>'search', 'input_title' => '输入关键字','input_name'=>'keywords','from_method'=>'post', 'action' =>U('Weibo/index/search')),
                )
            );
        $this->assign('sub_menu', $sub_menu);
        $this->assign('current', 'index');
    }
}