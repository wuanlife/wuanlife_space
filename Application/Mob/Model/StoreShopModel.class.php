<?php
namespace Mob\Model;

use Think\Model;

class StoreShopModel extends Model
{
    protected $tableName = 'store_shop';
    protected $_auto = array(
        array('status', 'getNeedVerify', 1, 'callback'), // 新增的时候把status字段设置为1
        array('update_time', 'time', 2, 'function'), // 对update_time字段在更新的时候写入当前时间戳
        array('create_time', 'time', 1, 'function')
    );
    protected $_validate = array(
        array('title', '', '店名已经存在！', 0, 'unique', 1),
        array('title', '1,40', '店名长度必须在1-40个字之间', 0, 'length'),
        array('summary', '1,200', '长度必须在1-200个字之间', 0, 'length'),
        array('logo', 'require', '必须上传图标！'),
    );

    /**获取是否需要
     * @return int
     * @auth 陈一枭
     */
    public function getNeedVerify()
    {
        //return 1;
        //TODO 从后台获取到创建店铺是否需要审核
        $status = 1;
        return $status;
    }


    function checkHadCreatedShop($uid)
    {
        $had = $this->where('uid=' . $uid . ' and entity_id=7')->count();
        return $had;
    }

    function  getShopByUid($uid)
    {
        $my_shop = $this->where('uid=' . $uid)->find();
        return $my_shop;
    }

    function  getMyShopCondition($uid)
    {
        $shop = $this->getShopByUid($uid);
        return $shop['status'];
    }

    function getList($map = array('status' => 1), $num = 10, $order = 'create_time desc')
    {
        $list = $this->where($map)->order($order)->findPage($num);
        foreach ($list['data'] as &$v) {
            $v['goods'] = D('Goods')->getLimit(array('shop_id' => $v['id'],'status'=>1), 8);
            $v['user'] = query_user(array('nickname', 'avatar64', 'space_url', 'avatar128', 'avatar32'), $v['uid']);
        }
        unset($v);
        return $list;
    }

    function  getListForSearch($map = array('status' => 1), $order = 'create_time desc'){
        $list = $this->where($map)->order($order)->select();
        foreach ($list as &$v) {
            $v['goods'] = D('Goods')->getLimit(array('shop_id' => $v['id'],'status'=>1), 12);
            $v['goodsCount'] = D('StoreGoods')->where(array('shop_id' => $v['id']))->count();
            $v['user'] = query_user(array('nickname', 'avatar64', 'space_url', 'avatar128', 'avatar32'), $v['uid']);
        }
        unset($v);
        return $list;
    }

    function getLimit($map = array('status' => 1), $num = 10, $order = 'create_time desc')
    {
        $list = $this->where($map)->order($order)->limit($num)->select();
        foreach ($list as &$v) {
            $v['goods'] = D('Goods')->getLimit(array('shop_id' => $v['id'],'status'=>1), 12);
            $v['user'] = query_user(array('nickname', 'avatar64', 'space_url', 'avatar128', 'avatar32'), $v['uid']);
        }
        unset($v);
        return $list;
    }

    function getById($id)
    {
        $shop = $this->find($id);
        if ($shop)
            $shop['user'] = query_user(array('nickname', 'space_url', 'avatar64', 'avatar128', 'avatar32'), $shop['uid']);
        return $shop;
    }
}