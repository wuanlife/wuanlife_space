<?php
namespace Mob\Model;

use Think\Model;

class OrderModel extends Model
{
    protected $tableName = 'store_order';

    function addOrder($order, $good_ids, $good_counts)
    {
        $hint = '';
        //要对订单进行拆分
        $goods = array(); //商品组
        foreach ($good_ids as $key => $v) {
            $good = D('StoreInfo')->getById($v);
            $good['count'] = $good_counts[$key];
            //对每个商品进行遍历，按照uid分配商品组
            $goods[$good['uid']][] = $good;
        }
        //对商品组分别创建订单
        foreach ($goods as $key => $v) {
            $sum = $this->comput_sum_good($v);
            $t_order = $order; //临时订单

            $t_order['id'] =doubleval(time().create_rand(4,'num'));
            $t_order['total_cny'] = $sum['cny'];
            $t_order['total_count'] = $sum['count'];
            $t_order['s_uid'] = $key; //卖家ID
            $shop = D('StoreShop')->getById($v[0]['shop_id']);
            $rs = $this->add($t_order);
            if ($rs == 0) {
                return array(false, $this->getError());
            }

            D('order_link')->add(array('order_id'=>$rs,'model'=>'store_order','app'=>'store'));
            //给店主发信息
            D('Message')->sendMessage($shop['uid'], $content = '【微店】订单' . $rs . '已创建，等待买家付款！', $title = '微店下单通知', 'store/center/sold',array(), is_login());

            //添加订单完成，添加商品
            foreach ($v as $k => $g) {
                $t_good['good_id'] = $g['id'];
                $t_good['h_price'] = $g['price'];
                $t_good['cTime'] = time();
                $t_good['h_name'] = $g['title'];
                $t_good['h_pic'] = $g['cover_id'];
                $t_good['order_id'] = $rs;
                $t_good['count'] = $g['count'];
                $rs_good = D('store_item')->add($t_good);
                D('Goods')->where('id=' . $g['id'])->setField('sell', $g['sell'] + $g['count']);
                if ($rs_good == 0) {
                    return array(false, $this->getError());
                }
            }

        }
        D('Cart')->clear();
        return array(true, $hint);

    }


    /**获取订单状态文本
     * @param $condition
     * @return string
     * @auth 陈一枭
     */
    public function getConditionText($condition)
    {

        switch ($condition) {
            case 0:
                return '等待买家付款';
            case 1:
                return '等待店主发货';
            case 2 :
                return '等待确认收货';
            case 3:
                return '交易成功';
            case -1:
                return '交易关闭';
            case 5:
                return '超时关闭';
            default:
                return '未知状态';
        }
    }
    public function getConditionHtml($condition)
    {
        switch ($condition) {
            case 0:
                return '<span style="font-weight:bold">等待买家付款</span>';
            case 1:
                return '<span style="color:pink">等待店主发货</span>';
            case 2 :
                return '<span style="color:orange">等待确认收货</span>';
            case 3:
                return '<span style="color:green">交易成功</span>';
            case -1:
                return '<span style="color:#ccc">交易关闭</span>';
            case 5:
                return '<span style="color:#ccc">超时关闭</span>';
            default:
                return '-';
        }
    }
    function comput_sum_good($goods)
    {
        $sum['cny'] = 0;
        $sum['count'] = 0;
        foreach ($goods as $k => $v) {
            $sum['cny'] += $v['price'] * $v['count'];
            $sum['count'] += $v['count'];
        }
        return $sum;
    }

    function comput_sum($items)
    {
        $sum['cny'] = 0;
        $sum['count'] = 0;
        foreach ($items as $k_item => $v_item) {
            $orders['data'][$k_item]['goods'][] = D('Info')->getById($v_item['good_id']);
            $sum['cny'] += $v_item['h_price'] * $v_item['count'];
            $sum['count'] += $v_item['count'];

        }
    }

    /**用账户余额来支付订单
     * @auth 陈一枭
     */
    function pay($order_id)
    {
        $order = $this->getById($order_id);
        if ($order['uid'] != is_login()) {
            $this->error = '该订单不是您的。无法付款。'; //TODO 支持代付
        }
        if ($order['condition'] != 0) {
            $this->error = '该订单不是未支付状态。';
        }
        $cost = getFinalPrice($order);
        $currencyModel = D('Currency');
        if ($currencyModel->pay($cost, is_login())) {

            D('Ucenter/Score')->addScoreLog(is_login(),  $cost, modC('CURRENCY_TYPE', '4', 'Store'), 'dec','recharge_order',$order_id,get_nickname( is_login()).'在微店支付了订单');

            $order['condition'] = 1; //置为已支付
            $order['pay_time'] = time();
            if ($this->save($order)) {
                //设置店铺销量
                $shopModel = D('Store/StoreShop');
                $shop = $this->getShopFromOrder($order);
                $shopModel->where(array('id' => $shop['id']))->setInc('order_count', 1);
                //给店主发信息
                D('Message')->sendMessage($shop['uid'], $content = '【微店】订单' . $order['id'] . '已付款，赶紧发货吧！', $title = '微店订单付款通知', 'store/center/sold',array(), is_login());
                return true;
            } else {
                $this->error = '设置订单付款状态失败。';
                return false;
            }

        } else {
            $this->error = $currencyModel->getError();
            return false;
        }

    }

    /**完成订单，给卖家加钱
     * @param $order_id
     * @auth 陈一枭
     */
    function done($order_id)
    {
        //确保订单无误
        if ($order_id == 0) {
            $this->error('订单不存在。');
        }
        $order = $this->find($order_id);
        //权限检测
        if ($order['uid'] != is_login()) {
            $this->error = '您无权确认收货。';
            return false;
        }

        //订单检测
        if ($order['condition'] != 2) {
            $this->error = '订单状态不正确。';
            return false;
        }
        if ($order) {
            //修改订单状态
            $order['condition'] = 3;
            $r = D('Order')->save($order);
            if ($r) {
                //给卖家加钱
                $currencyModel = D('Currency');
                $rs = $currencyModel->adjust(getFinalPrice($order), $order['s_uid']);
                if ($rs) {
                    D('Ucenter/Score')->addScoreLog($order['s_uid'],  getFinalPrice($order), modC('CURRENCY_TYPE', '4', 'Store'), 'inc','recharge_order',$order_id,get_nickname($order['s_uid']).'收到了'.get_nickname(is_login()).'付的款');

                    $shopModel = D('Store/StoreShop');
                    $shop = $shopModel->where(array('uid' => $order['s_uid']))->find();
                    D('Message')->sendMessage($shop['uid'], $content = '【微店】订单' . $order['id'] . '买家已确认收货，赶紧查查款项吧！', $title = '微店订单确认收货通知', 'store/center/sold',array(), is_login());
                    return $rs;
                } else {

                    $this->error = '给卖家转账失败。';
                    return false;
                }
            } else {
                $this->error = '状态修改失败。';
                return false;

            }
        }
    }

    /**关闭订单
     * @param $aOrderId  订单编号
     * @return bool 成功返回true.失败返回false
     * @auth 陈一枭
     */
    function closeOrder($aOrderId)
    {
        $order = $this->find($aOrderId);
        if (!$order) {
            $this->error = '订单不存在。';
            return false;
        }
        if ($order['uid'] != get_uid() && $order['s_uid'] != get_uid()) {
            //仅卖家和买家可关闭该订单
            $this->error = '您没有订单操作权限。';
            return false;
        }
        if ($order['condition'] != 0) {
            //只能关闭等待付款的订单
            $this->error = '该订单无法关闭。';
            return false;
        }
        if ($order['uid'] == get_uid()) { //买家关
            //买家关
            $map['uid'] = get_uid();
            $map['id'] = $aOrderId;
            $rs = $this->where($map)->setField('condition', -1);
        } else {
            //卖家关
            $map['s_uid'] = get_uid();
            $map['id'] = $aOrderId;
            $rs = $this->where($map)->setField('condition', -1);
        }
        if ($rs) {
            return true;
        } else {
            $this->error = '关闭订单失败，数据库操作失败。';
            return false;
        }
    }

    /**评论订单
     * @param $order_id
     * @param $response 评论，good,normal,bad
     * @param $content
     * @return bool
     * @auth 陈一枭
     */
    function response($order_id, $response, $content)
    {
        $order = $this->find($order_id);

        if ($order['uid'] != is_login()) {
            $this->error = '越权操作。';
            return false;
        }
        //状态检测
        if ($order['condition'] != 3) {
            $this->error = '订单状态有误。';
            return false;
        }


        //超时检测
        $lasts = modC('COMMENT_TIME', 604800, 'store');
        if (intval($order['response_time']) != 0) {
            if (time() - $order['response_time'] > $lasts) {
                $this->error('已经超出可修改时间。');
            }
        }

        //从参数来获取到状态。
        switch ($response) {
            case 'good':
                $order['response'] = 1;
                break;
            case 'normal':
                $order['response'] = 0;
                break;
            case 'bad':
                $order['response'] = -1;
                break;
            default:
                $order['response'] = 0;
        }
        $order['content'] = $content;
        $order['response_time'] = time();
        $rs = $this->save($order);
        if ($rs) {
            $shop = $this->getShopFromOrder($order);
            D('Message')->sendMessage($shop['uid'], $content = '【微店】买家已修改评价' . $order['id'] . '！', $title = '微店订单评价通知', 'store/center/sold',array(), is_login());
            return true;
        } else {
            $this->error = '数据更改错误。';
            return false;
        }


    }

    /**
     * @param array  $map
     * @param int    $num
     * @param string $order
     * @return mixed
     * @auth 陈一枭
     */
    function getList($map = array(), $num = 10, $order = 'create_time desc')
    {
        $orders = $this->where($map)->order($order)->findPage($num);

        foreach ($orders['data'] as $k => $v) {
            $orders['data'][$k] = $this->_check_timeout($v);
            $map_i['order_id'] = $v['id'];
            $items = D('store_item')->where($map_i)->select();
            $orders['data'][$k]['items'] = $items;
            foreach ($items as $k_item => $v_item) {
                $item = array('good' => D('Goods')->getById($v_item['good_id']), 'item' => $v_item);
                $orders['data'][$k]['goods'][] = $item;
            }
            $orders['data'][$k]['b_user'] = query_user(array('nickname', 'space_url', 'avatar64'), $v['uid']);
            $orders['data'][$k]['s_user'] = query_user(array('nickname', 'space_url', 'avatar64'), $v['s_uid']);
        }
        return $orders;
    }


    public function _check_timeout($v)
    {
        $time_limit = modC('time_limit', 18000);
        if ((time() - $v['create_time'] >= $time_limit) && ($v['condition'] == ORDER_CON_WAITFORPAY)) {
            $v['condition'] = ORDER_CON_TIMEOUT;
            D('store_order')->save($v);
            return $v;
        }
        return $v;
    }

    function getLimit($map = '', $num = 10, $order = 'cTime desc')
    {

    }

    function getById($order_id)
    {
        $order = $this->find($order_id);
        $order['user'] = query_user(array('nickname', 'space_url'), $order['uid']);
        $order['s_user'] = query_user(array('nickname', 'space_url'), $order['s_uid']);
        $map_item['order_id'] = $order_id;
        $order['items'] = D('store_item')->where($map_item)->limit(999)->select();
        foreach ($order['items'] as $k => $v) {
            $order['items'][$k]['good'] = D('Info')->getById($v['good_id']);
        }
        return $order;
    }

    private function getShopFromOrder($order)
    {
        $shopModel = D('Store/StoreShop');
        $shop = $shopModel->where(array('uid' => $order['s_uid']))->find();
        return $shop;
    }


}