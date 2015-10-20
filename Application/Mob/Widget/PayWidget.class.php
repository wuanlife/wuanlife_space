<?php
namespace Mob\Widget;

use Think\Controller;

class PayWidget extends Controller
{
    private function getAmount($order){

        $finally =$order['total_cny'] + $order['adj_cny'];
         $score_id=modC('CURRENCY_TYPE',4,'Store');
        $fields_config = modC('RE_FIELD', "", 'recharge');
        $fields = json_decode($fields_config,true);
        $type = array_search_key($fields,'FIELD',$score_id);
        !$type && $type['UNIT'] =1;

        return number_format($finally/$type['UNIT'], 2, ".", "");
    }

    public function pay($order_id){
        $link = D('order_link')->where(array('order_id'=>$order_id))->cache(60)->find();
        $order = D($link['model'])->where(array('id'=>$order_id))->find();

        $this->assign('amount',$this->getAmount($order));
        $this->assign('order_id',$order_id);
        $this->display(T('Application://Mob@Widget/pay'));
    }


    public function beforePayAlipay($order_id){
        $link = D('order_link')->where(array('order_id'=>$order_id))->cache(60)->find();
        $order = D($link['model'])->where(array('id'=>$order_id))->find();
        $return['total_fee'] =  $this->getAmount($order);
        $return['out_trade_no'] = $order_id;
        return $return;
    }



    public function afterPayAlipay($record){

        $order_id = $record['out_trade_no'];

        //交易状态
        if ($record['trade_status'] == 'TRADE_FINISHED' || $record['trade_status'] == 'TRADE_SUCCESS') {
            $data['condition'] = 1; //置为已支付
            $data['pay_time'] = $record['notify_time'];
            $orderModel =  D('store_order');
            $s_uid = $orderModel->where(array('id'=>$order_id))->getField('s_uid');
            $shopModel = D('Store/StoreShop');
            $shopModel->where(array('uid' => $s_uid))->setInc('order_count', 1);
            $orderModel ->where(array('id'=>$order_id))->save($data);
            return('付款成功，等待卖家'.get_nickname($s_uid).'发货');
        } else {
            return('失败——支付状态出错。' . $record['trade_status'] . $order_id);
            /* $this->error('支付状态出错。' . $record['trade_status']);*/
        }
    }

}