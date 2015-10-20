<?php


namespace Mob\Controller;

use Think\Controller;

class ShopController extends BaseController
{
    public function _initialize()
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'home','href'=>U('Mob/shop/index')),
                array('type'=>'message'),
            ),
            'center'=>array('title'=>'商城')
        );
        $this->setMobTitle('全部商品');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }

    public function index($mark = 'allGoods', $typeId = 0)
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $hot_num = modC('SHOP_HOT_SELL_NUM', 10, 'Shop');//获取最热条件
        switch ($mark) {
            case 'allGoods':
                $this->setTopTitle('全部商品');
                $goods = D('Shop')->where(array('status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                $totalCount = D('Shop')->where(array('status' => 1))->count();
                break;
            case 'newGoods':
                $this->setTopTitle('最新商品');
                $goods = D('Shop')->where(array('status' => 1,'is_num'=>1))->order('createtime desc')->page($aPage, $aCount)->select();
                $totalCount = D('Shop')->where(array('status' => 1,'is_num'=>1))->count();
                break;
            case "hotGoods":
                $this->setTopTitle('最热商品');
                $map['sell_num']=array('egt',$hot_num);
                $goods = D('Shop')->where(array('status' => 1,$map))->order('createtime desc')->page($aPage, $aCount)->select();
                $totalCount = D('Shop')->where(array('status' => 1,$map))->count();
                break;
            case 'parentType':
                $this->assign('typeId', $typeId);
                $typeName['parent']=D('ShopCategory')->where(array('id'=>$typeId))->find();
                $typeName['child']=D('ShopCategory')->where(array('pid'=>$typeName['parent']['id']))->select();
                if(is_null( $typeName['child'])){
                    $this->setTopTitle($typeName['parent']['title']);
                    $goods = D('Shop')->where(array('category_id'=>$typeId,'status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                    $totalCount = D('Shop')->where(array('category_id'=>$typeId,'status' => 1))->count();
                }else{
                    $typeId=array_column($typeName['child'],'id');
                    $typeId=array_merge($typeId,array($typeName['parent']['id']));

                    $this->setTopTitle($typeName['parent']['title']);
                    $map['category_id']=array('in',$typeId);
                    $goods = D('Shop')->where($map,array('status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                    $totalCount = D('Shop')->where($map,array('status' => 1))->count();
                }
                break;
            case 'childType':
                $this->assign('typeId', $typeId);
                $typeName=D('ShopCategory')->where(array('id'=>$typeId))->find();
                $this->setTopTitle($typeName['title']);
                //dump($typeId);exit;
                $goods = D('Shop')->where(array('category_id'=>$typeId,'status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                $totalCount = D('Shop')->where(array('category_id'=>$typeId,'status' => 1))->count();
                break;
                break;
        }
        //获取分类
        $parent = D('ShopCategory')->where(array('status' => 1, 'pid' => 0))->order('create_time asc')->select();
        foreach ($parent as &$v) {
            $v['child'] = D('ShopCategory')->where(array('status' => 1, 'pid' => $v['id']))->order('create_time asc')->select();
        }
        unset($v);
        //获取分类END
        //获取判断是否最热条件并判断

        foreach ($goods as &$v) {
            if ($v['sell_num'] >= $hot_num) {
                $v['is_hot'] = 1;
            }
        }
        unset($v);
        //判断是否有查看更多
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        //   dump($goods);exit;
        $this->assign('parent', $parent);   //分类
        $this->assign('goods', $goods);     //商品信息
        $this->assign('pid', $pid);   //查看更多
        $this->assign('mark',$mark); //在页面上显示标记的信息
        $this->display();
    }

    public function addMoreGoods($mark,$typeId=0){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $hot_num = modC('SHOP_HOT_SELL_NUM', 10, 'Shop');//获取最热条件
        switch ($mark) {
            case 'allGoods':
                $goods = D('Shop')->where(array('status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                break;
            case 'newGoods':
                $goods = D('Shop')->where(array('status' => 1,'is_num'=>1))->order('createtime desc')->page($aPage, $aCount)->select();
                break;
            case "hotGoods":
                $map['sell_num']=array('egt',$hot_num);
                $goods = D('Shop')->where(array('status' => 1,$map))->order('createtime desc')->page($aPage, $aCount)->select();
                break;
            case 'parentType':
                $typeName['parent']=D('ShopCategory')->where(array('id'=>$typeId))->find();
                $typeName['child']=D('ShopCategory')->where(array('pid'=>$typeName['parent']['id']))->select();
                if(is_null( $typeName['child'])){
                    $this->setTopTitle($typeName['parent']['title']);
                    $goods = D('Shop')->where(array('category_id'=>$typeId,'status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                }else{
                    $typeId=array_column($typeName['child'],'id');
                    $typeId=array_merge($typeId,array($typeName['parent']['id']));

                    $this->setTopTitle($typeName['parent']['title']);
                    $map['category_id']=array('in',$typeId);
                    $goods = D('Shop')->where($map,array('status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                }
                break;
            case 'childType':
                $typeName=D('ShopCategory')->where(array('id'=>$typeId))->find();
                $this->setTopTitle($typeName['title']);
                //dump($typeId);exit;
                $goods = D('Shop')->where(array('category_id'=>$typeId,'status' => 1))->order('createtime desc')->page($aPage, $aCount)->select();
                break;
                break;
        }
        //获取分类
        $parent = D('ShopCategory')->where(array('status' => 1, 'pid' => 0))->order('create_time asc')->select();
        foreach ($parent as &$v) {
            $v['child'] = D('ShopCategory')->where(array('status' => 1, 'pid' => $v['id']))->order('create_time asc')->select();
        }
        unset($v);
        //获取分类END
        //获取判断是否最热条件并判断

        foreach ($goods as &$v) {
            if ($v['sell_num'] >= $hot_num) {
                $v['is_hot'] = 1;
            }
        }
        unset($v);
        if ($goods) {
            $data['html'] = "";
            foreach ($goods as $val) {
                $this->assign("vo", $val);
                $data['html'] .= $this->fetch("_goodslist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }

    public function buy($id){
        $this->assign('id',$id);
        $address=D('ShopAddress')->where(array('uid'=>is_login()))->order('create_time desc')->find();
       // dump($address);exit;
        $this->assign('address',$address);
        $this->display();
    }

    /**
     * 购买商品
     * @param int $id
     * @param int $num
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsBuy($id = 0, $name = '', $address = '', $zipcode = '', $phone = '', $address_id = '')
    {
        $address = op_t($address);
        $address_id = intval($address_id);
        $num = 1;
        if (!is_login()) {
            $this->error('请先登录！');
        }
        $this->checkAuth('Shop/Index/goodsBuy',-1,'你没有购买、兑换商品的权限！');
        $this->checkActionLimit('shop_goods_buy','shop',$id,is_login());
        $goods = D('shop')->where(array('id'=>$id))->find();
        if ($goods) {
            if($num<=0){
                $this->error('商品购买数量不能为负数。');
            }
            //验证开始
            //判断商品余量
            if ($num > $goods['goods_num']) {
                $this->error('商品余量不足');
            }

            //扣money
            $ScoreModel=D('Ucenter/Score');
            $score_type=modC('SHOP_SCORE_TYPE','1','Shop');
            $money_type=$ScoreModel->getType(array('id'=>$score_type));

            $money_need = $num * $goods['money_need'];
            $my_money = query_user('score'.$score_type);
            if ($money_need > $my_money) {
                $this->error('你的' . $money_type['title'] . '不足');
            }

            //用户地址处理
            if ($name == '' || !preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $name)) {
                $this->error('请输入正确的用户名');
            }
            if ($address == '') {
                $this->error('请填写收货地址');
            }
            if ($zipcode == '' || strlen($zipcode) != 6 || !is_numeric($zipcode)) {
                $this->error('请正确填写邮编');
            }
            if ($phone == '' || !preg_match("/^1[3458][0-9]{9}$/", $phone)) {
                $this->error('请正确填写手机号码');
            }
            $shop_address['phone'] = $phone;
            $shop_address['name'] = $name;
            $shop_address['address'] = $address;
            $shop_address['zipcode'] = $zipcode;
            if ($address_id) {
                $address_save = D('shop_address')->where(array('id' => $address_id))->save($shop_address);
                if ($address_save) {
                    D('shop_address')->where(array('id' => $address_id))->setField('change_time', time());
                }
                $data['address_id'] = $address_id;
            } else {
                $shop_address['uid'] = is_login();
                $shop_address['create_time'] = time();
                $data['address_id'] = D('shop_address')->add($shop_address);
            }
            //验证结束

            $data['goods_id'] = $id;
            $data['goods_num'] = $num;
            $data['status'] = 0;
            $data['uid'] = is_login();
            $data['createtime'] = time();

            $ScoreModel->setUserScore(array(is_login()),$money_need,$score_type,'dec','shop',$id ,get_nickname(is_login()).'购买了商品');
            $res = D('shop_buy')->add($data);
            if ($res) {
                //商品数量减少,已售量增加
                D('shop')->where('id=' . $id)->setDec('goods_num', $num);
                D('shop')->where('id=' . $id)->setInc('sell_num', $num);
                //发送系统消息
                $message = $goods['goods_name'] . "购买成功，请等待发货。";
                D('Common/Message')->sendMessageWithoutCheckSelf(is_login(),'购买成功通知', $message,  'Shop/Index/myGoods', array('status' => '0'));

                //商城记录
                $shop_log['message'] = '用户[' . is_login() . ']' . query_user('nickname', is_login()) . '在' . time_format($data['createtime']) . '购买了商品<a href="index.php?s=/Shop/Index/goodsDetail/id/' . $goods['id'] . '.html" target="_black">' . $goods['goods_name'] . '</a>';
                $shop_log['uid'] = is_login();
                $shop_log['create_time'] = $data['createtime'];
                D('shop_log')->add($shop_log);

                action_log('shop_goods_buy','shop',$id,is_login());

                $this->ajaxReturn(array('status'=>1,'info'=>'购买成功！花费了' . $money_need . $money_type['title'], $_SERVER['HTTP_REFERER']));
            } else {
                $this->ajaxReturn(array('status'=>0,'info'=>'购买失败！'));
            }
        } else {
            $this->ajaxReturn(array('status'=>0,'info'=>'请选择要购买的商品！'));
        }
    }

    public function myOrder(){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $hot_num = modC('SHOP_HOT_SELL_NUM', 10, 'Shop');//获取最热条件
        //未完成的订单
        $goodsBuy=D('ShopBuy')->where(array('uid'=>is_login(),'status'=>0))->order('createtime desc')->page($aPage, $aCount)->select();
        $totalCount = D('ShopBuy')->where(array('uid'=>is_login(),'status'=>0))->count();
        foreach($goodsBuy as &$v){
            $v['goods']=D('Shop')->where(array('id'=>$v['goods_id']))->find();
                if ($v['goods']['sell_num'] >= $hot_num) {
                    $v['goods']['is_hot'] = 1;
            }
        }unset($v);
        //已完成的订单
        $goodsHasBuy=D('ShopBuy')->where(array('uid'=>is_login(),'status'=>1))->order('createtime desc')->page($aPage, $aCount)->select();
        $totalCountHasBuy = D('ShopBuy')->where(array('uid'=>is_login(),'status'=>1))->count();
        foreach($goodsHasBuy as &$v){
            $v['goods']=D('Shop')->where(array('id'=>$v['goods_id']))->find();
            if ($v['goods']['sell_num'] >= $hot_num) {
                $v['goods']['is_hot'] = 1;
            }
        }unset($v);
        if ($totalCount <= $aPage * $aCount) {//未完成的订单查看更多
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        if ($totalCountHasBuy <= $aPage * $aCount) {//完成的订单查看更多
            $pid['countHasBuy'] = 0;
        } else {
            $pid['countHasBuy'] = 1;
        }
    //    dump($goodsBuy);exit;
        $this->assign('pid',$pid);
        $this->assign('goods',$goodsBuy);
        $this->assign('goodshasbuy',$goodsHasBuy);
        $this->display();
    }

    public function addMoreGoodsOrder($mark=""){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $hot_num = modC('SHOP_HOT_SELL_NUM', 10, 'Shop');//获取最热条件
        if($mark){
            //已完成的订单
            $goodsHasBuy=D('ShopBuy')->where(array('uid'=>is_login(),'status'=>1))->order('createtime desc')->page($aPage, $aCount)->select();
            foreach($goodsHasBuy as &$v){
                $v['goods']=D('Shop')->where(array('id'=>$v['goods_id']))->find();
                if ($v['goods']['sell_num'] >= $hot_num) {
                    $v['goods']['is_hot'] = 1;
                }
            }unset($v);
            if ($goodsHasBuy) {
                $data['html'] = "";
                foreach ($goodsHasBuy as $val) {
                    $this->assign("vo", $val);
                    $data['html'] .= $this->fetch("_goodsbuylist");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
            }
            $this->ajaxReturn($data);
        }else{
            //未完成的订单
            $goodsBuy=D('ShopBuy')->where(array('uid'=>is_login(),'status'=>0))->order('createtime desc')->page($aPage, $aCount)->select();
            foreach($goodsBuy as &$v){
                $v['goods']=D('Shop')->where(array('id'=>$v['goods_id']))->find();
                if ($v['goods']['sell_num'] >= $hot_num) {
                    $v['goods']['is_hot'] = 1;
                }
            }unset($v);
            if ($goodsBuy) {
                $data['html'] = "";
                foreach ($goodsBuy as $val) {
                    $this->assign("vo", $val);
                    $data['html'] .= $this->fetch("_goodsbuylist");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
            }
            $this->ajaxReturn($data);
        }
    }

    public function goodsDetail($id=""){
        $goodsDetail=D('Shop')->where(array('id'=>$id))->find();
      //  dump($goodsDetail);exit;
        $this->assign('detail',$goodsDetail);

        $recommendGoods=D('Shop')->where(array('status'=>1))->order('sell_num desc,createtime desc')->limit(10)->select();
      //  dump($recommendGoods);exit;
        $this->assign('recommendGoods',$recommendGoods);
        $this->display();
    }

}