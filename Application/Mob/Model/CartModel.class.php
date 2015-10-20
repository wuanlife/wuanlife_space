<?php
namespace Mob\Model;

use Think\Model;
class CartModel extends Model
{
    protected $cart = 0;
    protected $m_cart_list = array();

    function _initialize()
    {
        $this->cart = json_decode(cookie('cart'), true);
        if (!is_array($this->cart)) {
            $this->cart = array(
                'cart_tag' => 1,
                'items' => array()
            );
        }
    }

    function getList($map = '', $num = 10, $order = 'cTime desc')
    {

    }

    function getLimit($map = '', $num = 10, $order = 'cTime desc')
    {

        $abc=json_decode(cookie('cart','',array('prefix'=>'')),true);
        $items =$abc['items'];
        foreach ($items as $key => $v) {
            $items[$key]['good'] = D('StoreInfo')->getById($v['good_id']);
        }
        return $items;


    }

    /**
     *
     * 添加一个物品
     * @param unknown_type $item
     * good_id
     * count
     * cTime
     */
    function addItem($item)
    {

        if($item['count']<=0){
            return false;
        }
        $added = false;
        $goods=json_decode(cookie('cart','',array('prefix'=>'')),true);
      //  dump($abc['items']);
        foreach ($goods['items'] as $key => $v) {
            if ($v['good_id'] == $item['good_id']) {
                $goods['items'][$key]['count'] += $item['count'];
                $added = true;
                break;
            }
        }
        if ($added == false) {
            $goods['items'][] = $item;
        }
       // dump($abc);exit;
        $json_cart = json_encode($goods);
        cookie('cart',$json_cart,array('prefix'=>'','expire'=>3600 * 24 * 30));
    }

    function clear()
    {
        $this->cart = null;
        $this->_save();
    }

    function removeItem($good_id)
    {
        $goods=json_decode(cookie('cart','',array('prefix'=>'')),true);
        foreach ($goods['items'] as $key => $v) {
            if ($v['good_id'] == $good_id) {
                unset($goods['items'][$key]);
                break;
            }
        }
        $json_cart = json_encode($goods);
        cookie('cart',$json_cart,array('prefix'=>'','expire'=>3600 * 24 * 30));

    }

    function setItemCount($good_id, $count)
    {
        foreach ($this->cart['items'] as $key => $v) {
            if ($v['good_id'] == $good_id) {
                $this->cart['items'][$key]['count'] = $count;

                break;
            }
        }
        $this->_save();
    }

    function _save()
    {
        $json_cart = json_encode($this->cart);
        cookie('cart', $json_cart, 3600 * 24 * 30);
    }

    function getById($id)
    {
    }

}