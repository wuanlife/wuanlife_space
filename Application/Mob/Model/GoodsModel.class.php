<?php
/**
 * 所属项目 商业版.
 * 开发者: 陈一枭
 * 创建日期: 14-10-24
 * 创建时间: 上午11:14
 * 版权所有 想天软件工作室(www.ourstu.com)
 */
namespace mob\Model;

use Think\Model;

class GoodsModel extends Model
{
    protected $tableName = 'store_goods';

    function getById($id)
    {
        $map['id'] = $id;
        $goods = $this->find($id);
        $goods['data'] = D('Data')->getByInfoId($id);
        $goods['gallary_images'] = decodeGallary($goods['gallary']);
        return $goods;
    }

    /**
     * @param string $map
     * @param int    $num
     * @param string $order
     * @return mixed
     */
    function getList($map = '', $num = 10, $order = 'create_time desc')
    {

        $rs = $this->where($map)->order($order)->findPage($num);
        foreach ($rs['data'] as $key => $v) {
            $rs['data'][$key]['data'] = D('Data')->getByInfoId($v['id']);
            $rs['data'][$key]['gallary_images']=$this->decodeGallary($v['gallary']);
        }
        return $rs;
    }

    function checkOwner($mid, $info_id)
    {
        $map['uid'] = $mid;
        $map['id'] = $info_id;
        $is = D('Goods')->where($map)->count();
        return $is;
    }

    function getLimit($map = '', $num = 10, $order = 'create_time desc')
    {

        $rs =$this->where($map)->order($order)->limit($num)->select();
        foreach ($rs as $key => $v) {
            $rs[$key]['data'] = D('Data')->getByInfoId($v['id']);
            $rs[$key]['gallary_images']=$this->decodeGallary($v['gallary']);
        }

        return $rs;
    }

    /**解析相册
     * @param $gallary
     * @return array
     * @auth 陈一枭
     */
    function decodeGallary($gallary)
    {
        $gallary = json_decode($gallary, true);
        foreach ($gallary as $g) {
            $gallary_array[] = array('id' => $g['id'], 'img' => getThumbImageById($g['id'], 80, 80));
        }
        return $gallary_array;
    }

} 