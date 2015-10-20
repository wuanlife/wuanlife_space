<?php

namespace Mob\Controller;

use Think\Controller;

class WeiboTypeController extends Controller
{
    /**
     * imageBox
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function imageBox()
    {
        $data['unid'] = substr(strtoupper(md5(uniqid(mt_rand(), true))), 0, 8);
        $data['status'] = 1;
        $data['total'] = 9;
        // 设置渲染变量
        $var['unid'] = $data['unid'];

        $var['fileSizeLimit'] = floor(2 * 1024).'KB';
        $var['total'] = $data['total'];
        $this->assign($var);
        $data['html'] = $this->fetch('imagebox');
        exit(json_encode($data));

    }

    /**
     * fetchImage  渲染图片微博
     * @param $weibo
     * @return string
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function fetchImage($weibo)
    {
        $weibo_data = unserialize($weibo['data']);
        $weibo_data['attach_ids'] = explode(',', $weibo_data['attach_ids']);

        foreach ($weibo_data['attach_ids'] as $k_i => $v_i) {
            $weibo_data['image'][$k_i]['small'] = getThumbImageById($v_i, 100, 100);
            $bi = M('Picture')->where(array('status' => 1))->getById($v_i);
            $weibo_data['image'][$k_i]['big']  = get_pic_src($bi['path']) ;
            $param['weibo'] = $weibo;
            $param['weibo']['weibo_data'] = $weibo_data;
        }
        $this->assign($param);
        return $this->fetch(T('Application://Mob@Type/fetchimage'));
    }

    /**
     * fetchRepost   渲染转发微博
     * @param $weibo
     * @return string
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function fetchRepost($weibo)
    {
        $weibo_data = unserialize($weibo['data']);
        $weibo_data['attach_ids'] = explode(',', $weibo_data['attach_ids']);

        $source_weibo = D('Mob/Weibo')->getWeiboDetail($weibo_data['sourceId']);

        $source_weibo['user']=query_user(array('uid', 'nickname',  'avatar32', 'space_url',  'rank_link', 'title'), $source_weibo['uid']);
        $param['weibo'] = $weibo;
        $param['weibo']['source_weibo'] = $source_weibo;
        $this->assign($param);
        return $this->fetch(T('Application://Mob@Type/fetchrepost'));

    }

}