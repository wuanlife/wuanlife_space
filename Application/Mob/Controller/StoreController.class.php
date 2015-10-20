<?php


namespace Mob\Controller;

use Think\Controller;


class StoreController extends BaseController
{

    public function _initialize()
    {
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'home', 'href' => U('Mob/store/index')),
                array('type' => 'message'),
            ),
            'center' => array('title' => '商城')
        );

        //dump($this->_top_menu_list);exit;
        $this->setMobTitle('微店');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }

    public function index()
    {
        //获取广告图片
        $adv = D('StoreAdv')->where(array('status' => 1))->select();
        $this->assign('adv', $adv);

        L('a');
        $this->display();
    }

    public function shop($hotShop = "")
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        //头部
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'back', 'a_class' => '', 'span_class' => ''),

            ),
        );
        // dump($this->_top_menu_list);exit;
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('店铺街');
        //店铺
        if ($hotShop) {
            $shop = D('StoreShop')->where(array('status' => 1))->order('visit_count desc,order_count desc')->page($aPage, $aCount)->select();
            $this->assign('type','hotShop');
        } else {
            $shop = D('StoreShop')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
            $this->assign('type','shop');
        }
        $shopCount = D('StoreShop')->where(array('status' => 1))->count();
        if ($shopCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        $this->assign('pid', $pid);
        foreach ($shop as &$v) {
            $v['goods'] = D('StoreGoods')->where(array('shop_id' => $v['id']))->order('create_time desc, sell desc')->limit(8)->select();
            $v['goodsCount'] = D('StoreGoods')->where(array('shop_id' => $v['id']))->count();
            $v['user'] = query_user(array('nickname', 'avatar32', 'uid', 'space_mob_url'), $v['uid']);
        }
        //dump($shopCount);exit;

        $this->assign('shop', $shop);
        $this->assign('shopCount', $shopCount);

        $this->display();
    }

    public function addMoreShop()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $aType= I('post.type', '', 'op_t');

        if ($aType=='hotShop') {
            $shop = D('StoreShop')->where(array('status' => 1))->order('visit_count desc,order_count desc')->page($aPage, $aCount)->select();
        } else {
            $shop = D('StoreShop')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
        }
        foreach ($shop as &$v) {
            $v['goods'] = D('StoreGoods')->where(array('shop_id' => $v['id']))->order('create_time desc, sell desc')->limit(8)->select();
            $v['goodsCount'] = D('StoreGoods')->where(array('shop_id' => $v['id']))->count();
            $v['user'] = query_user(array('nickname', 'avatar32', 'uid', 'space_mob_url'), $v['uid']);
        }
        if ($shop) {
            $data['html'] = "";
            foreach ($shop as $val) {
                $this->assign("vo", $val);
                $data['html'] .= $this->fetch("_shoplist");
                $data['status'] = 1;
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'info'=>'没有找到相应商品。'));
        }
        $this->ajaxReturn($data);
    }
    public function shopDetail($id = "")
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        //头部
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'back', 'a_class' => '', 'span_class' => ''),

            ),
        );
        // dump($this->_top_menu_list);exit;
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('店铺详情');
        $shopDetail = D('StoreShop')->where(array('id' => $id, 'status' => 1))->find();
        $shopDetail['user'] = query_user(array('nickname', 'avatar32', 'uid', 'space_mob_url'), $shopDetail['uid']);
        //  dump($shopDetail);exit;
        $this->assign('shopdetail', $shopDetail);
        //热卖
        $hotGoods = D('StoreGoods')->where(array('status' => 1, 'shop_id' => $id))->order('sell desc')->limit(8)->select();
        $this->assign('hotGoods', $hotGoods);
        //宝贝
        $goods = D('StoreGoods')->where(array('status' => 1, 'shop_id' => $id))->order('create_time desc')->page($aPage, $aCount)->select();
        $this->assign('goods', $goods);
//dump($goods);exit;
        $this->display();
    }

    public function goodsDetail($goodsId)
    {
        //头部
        $this->setTopTitle('商品详情');
        //详情
        $detail = D('StoreGoods')->where(array('id' => $goodsId))->find();
        $detail['shop'] = D('StoreShop')->where(array('id' => $detail['shop_id']))->find();
        //收藏
        $detail['fav'] = D('StoreFav')->where(array('info_id' => $detail['id']))->count();
        $detail['hasfav'] = D('StoreFav')->where(array('uid' => is_login(), 'info_id' => $detail['id']))->find();


        $goodsModel = D('Goods');
        $goods = $goodsModel->getById($goodsId);
        $goods['read']++;
        D('Goods')->save($goods);

        if ($detail['hasfav']) {
            $detail['hasfav'] = 1;
        }
        //图片处理
        $detail['gallary'] = json_decode($detail['gallary'], true);
        foreach ($detail['gallary'] as $g) {
            $detail['img'][] = array('id' => $g['id'], 'img' => getThumbImageById($g['id'], 500, 500));
        }
        //评论列表
        $comment = D('StoreItem')->where(array('good_id' => $goodsId))->select();
        foreach ($comment as &$v) {
            $v = D('StoreOrder')->where(array('id' => $v['order_id'], 'condition' => '3'))->find();
            if (!is_null($v)) {
                $v['user'] = query_user(array('nickname', 'avatar32', 'uid', 'space_mob_url'), $v['uid']);
            }
            switch ($v['response']) {
                case '0':
                    $v['response'] = "好评";
                    break;
                case '1':
                    $v['response'] = "中评";
                    break;
                case '2':
                    $v['response'] = "差评";
                    break;
            }
        }
        $this->assign('comment', $comment);

        //dump($detail);exit;
        $this->assign('detail', $detail);
        $this->display();
    }

    public function goods($id = "", $title = "")
    {

        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        //头部
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'back', 'a_class' => '', 'span_class' => ''),
            ),
        );
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('最新上架');
        if ($id) {
            //通过分类查看的宝贝
            $goods = D('StoreGoods')->where(array('status' => 1, 'cat2' => $id))->order('create_time desc')->page($aPage, $aCount)->select();
            $totalCount = D('StoreGoods')->where(array('status' => 1, 'cat2' => $id))->count();
            $this->setTopTitle($title);
            //dump($title);exit;
            $this->assign('goodsId', $id);
            $this->assign('title', $title);
            $this->assign('goods', $goods);

        } else {
            //宝贝
            $goods = D('StoreGoods')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
            $totalCount = D('StoreGoods')->where(array('status' => 1))->count();
            $this->assign('goods', $goods);
        }
        //热卖
        $hotGoods = D('StoreGoods')->where(array('status' => 1))->order('sell desc')->limit(8)->select();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        $this->assign('pid',$pid);
        $this->assign('hotGoods', $hotGoods);

        $this->display();
    }

    public function addMoreGoods()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $aId= I('post.goodsId', '', 'op_t');
        $aShopId=I('post.shopId', '', 'op_t');
        if ($aId) {
            //通过分类查看的宝贝
            $goods = D('StoreGoods')->where(array('status' => 1, 'cat2' => $aId))->order('create_time desc')->page($aPage, $aCount)->select();
            //dump($title);exit;
        } else if($aShopId){
            $goods = D('StoreGoods')->where(array('status' => 1, 'shop_id' => $aShopId))->order('create_time desc')->page($aPage, $aCount)->select();
            $this->assign('goods', $goods);
        }else{
         //宝贝
            $goods = D('StoreGoods')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
        }

        if ($goods) {
            $data['html'] = "";
            foreach ($goods as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_goodslist");
                $data['status'] = 1;
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'info'=>'没有找到相应商品。'));
        }
        $this->ajaxReturn($data);
    }

    /**
     * 分类列表
     */
    public function goodsCate()
    {
        //头部
        $this->setTopTitle('商城分类');
        $cat = D('store_category')->where(array('pid' => 0))->select();
        foreach ($cat as &$v) {
            $v['cat2'] = D('store_category')->where(array('pid' => $v['id']))->select();
        }
        $this->assign('cat', $cat);
//dump($cat);exit;
        $this->display();
    }

    public function myCar()
    {
        $this->setMobTitle('我的购物车');
        $this->setTopTitle('我的购物车');
        $items = D('Cart')->getLimit();
        $cny_total = 0;
        foreach ($items as $key => $v) {
            $cny_total += $v['good']['price'] * $v['count'];
        }
        // dump($items);exit;
        $this->assign('items', $items);
        $this->assign('cny_total', $cny_total);
        $this->display();
    }

    public function myCenter()
    {
        $this->setMobTitle('个人中心');
        $this->setTopTitle('个人中心');
        $user = query_user(array('nickname', 'avatar64', 'uid', 'space_mob_url'), is_login());
        $user['money'] = D('Member')->where(array('uid' => is_login()))->field('score4')->find();
        //dump($user);exit;
        $this->assign('center', $user);
        $this->display();
    }

    public function myCollect()
    {
        $this->setMobTitle('我的收藏');
        $goods = D('StoreFav')->where(array('uid' => is_login()))->order('cTime desc')->select();
        foreach ($goods as &$v) {
            $v['goods'] = D('StoreGoods')->where(array('id' => $v['info_id']))->find();
        }
        unset($v);
        //dump($goods);exit;
        $this->assign('goods', $goods);
        $this->display();
    }

    public function soldBuy()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $this->setMobTitle('我买到的宝贝');
        $this->setTopTitle('我买到的宝贝');
        //我购买的商品
        $order = D('StoreOrder')->where(array('uid' => is_login()))->order('create_time desc')->page($aPage, $aCount)->select();
        foreach ($order as &$v) {
            $v['s_user'] = query_user(array('nickname', 'avatar64', 'uid', 'space_mob_url'), $v['s_uid']);
            $v['goods'] = D('StoreItem')->where(array('order_id' => $v['id']))->find();
            $v['goods'] = D('StoreGoods')->where(array('id' => $v['goods']['good_id']))->find();
        }
        unset($v);
        // dump($order);exit;
        $this->assign('order', $order);
        $this->display();
    }

    public function soldSell()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $this->setMobTitle('已卖出的商品');
        $this->setTopTitle('订单管理');
        //我出售的商品
        $sellOrder = D('StoreOrder')->where(array('s_uid' => is_login()))->order('create_time desc')->page($aPage, $aCount)->select();
        foreach ($sellOrder as &$a) {
            $a['b_user'] = query_user(array('nickname', 'avatar64', 'uid', 'space_mob_url'), $a['uid']);
            $a['goods'] = D('StoreItem')->where(array('order_id' => $a['id']))->find();
            $a['goods'] = D('StoreGoods')->where(array('id' => $a['goods']['good_id']))->find();
        }
        unset($a);
//dump($sellOrder);exit;
        $this->assign('sellOrder', $sellOrder);
        $this->display();
    }

    public function response()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $comment = D('StoreOrder')->where(array('s_uid' => is_login(), 'condition' => 3))->order('response_time desc')->page($aPage, $aCount)->select();
        foreach ($comment as &$v) {
            $v['b_user'] = query_user(array('nickname', 'avatar64', 'uid', 'space_mob_url'), $v['uid']);
            $v['s_user'] = query_user(array('nickname', 'avatar64', 'uid', 'space_mob_url'), $v['s_uid']);
        }
        // dump($comment);exit;
        $this->assign('response', $comment);
        $this->display();
    }

    public function addGoods($goodsId = "")
    {
        $cat = D('store_category')->where(array('pid' => 0))->select();
        foreach ($cat as &$v) {
            $v['cat2'] = D('store_category')->where(array('pid' => $v['id']))->select();
        }
        $shop = D('StoreShop')->where(array('uid' => is_login(), 'status' => 1))->find();
        $this->assign('shop', $shop);
        $this->assign('cats', $cat);
        if ($goodsId) {
            $this->setTopTitle('编辑商品');
            $info = D('StoreGoods')->where(array('id' => $goodsId, 'status' => 1))->find();
            $this->assign('info', $info);
            $this->assign('goodsId', $goodsId);

        } else {
            $this->setTopTitle('发布商品');

        }
        $this->display();
    }

    //二级分类选择
    public function selectDropdown($pid)
    {
        $issues = D('store_category')->where(array('pid' => $pid, 'status' => 1))->limit(999)->select();
        exit(json_encode($issues));
    }

    public function doAddInfo()
    {
        unset($_POST['__hash__']);

        $aEntityId = I('post.entity_id', 0, 'intval');
        $aInfoId = I('post.info_id', 0, 'intval');
        $aCat1 = I('post.cat1', 0, 'intval');
        $aCat2 = I('post.cat2', 0, 'intval');
        $aCat3 = I('post.cat3', 0, 'intval');
        $aTitle = I('post.title', '', 'op_t');
        $aShopId = I('post.shop_id', 0, 'intval');
        $aPrice = I('post.price', 0, 'floatval');

        if ($aPrice <= 0) {
            $this->ajaxReturn(array('status' => 0, 'info' => '价格必须大于0'));
        }
        $aCoverId = I('post.cover_id', 0, 'intval');
        if ($aCoverId == 0) {
            $this->ajaxReturn(array('status' => 0, 'info' => '商品主图必须上传。'));
        }
        $aGallary = I('post.attach_ids', '', 'op_t');
        $aGallary = explode(',', $aGallary);
        if (count($aGallary) > 9) {
            $this->ajaxReturn(array('status' => 0, 'info' => '相册图片不能超过9张！'));
        }
        $aTransFee = I('post.trans_fee', 0, 'intval');
        $aDes = I('post.des', '', 'filter_content');
        if ($aDes == '') {
            $this->ajaxReturn(array('status' => 0, 'info' => '商品描述必填。'));
        }


        $entity = $this->requireCanPost($aEntityId);

        $info = D('Goods')->find($aInfoId);
        $info['title'] = $aTitle;
        $info['cat1'] = $aCat1;
        $info['cat2'] = $aCat2;
        $info['cat3'] = $aCat3;
        $info['price'] = $aPrice;
        $info['cover_id'] = $aCoverId;
        $info['trans_fee'] = $aTransFee == 1 ? 1 : 0;

        $info['gallary'] = encodeGallary($aGallary); //implode(',', $aGallary);

        $info['des'] = $aDes;
        if ($info['title'] == '') {
            $this->ajaxReturn(array('status' => 0, 'info' => '必须输入标题'));
        }
        if (mb_strlen($info['title'], 'utf-8') > 40) {
            $this->ajaxReturn(array('status' => 0, 'info' => '标题过长。'));
        }


        if ($aInfoId != 0) {
            $this->checkAuth('Store/Center/postEdit', $info['uid'], '你没有编辑该商品的权限！');
            $this->checkActionLimit('goods_edit', 'store', $aInfoId, is_login());
            $info['update_time'] = time();
            //保存逻辑
            $info['id'] = $aInfoId;
            D('Goods')->save($info);
            $rs_info = $info['id'];
        } else {
            $this->checkAuth('Store/Center/postAdd', -1, '你没有发布商品的权限！');
            $this->checkActionLimit('goods_add', 'store', null, is_login());
            $info['create_time'] = time();
            //新增逻辑
            $info['entity_id'] = $aEntityId;
            $info['uid'] = is_login();
            if ($entity['need_active'] && !is_administrator()) {
                $info['status'] = 2;
            } else {
                $info['status'] = 1;
            }

            //如果是商品就新增字段
            if ($entity['name'] == 'good') {
                $info['shop_id'] = $aShopId;
            }
            $rs_info = D('Goods')->add($info);
        }

        $rs_data = 1;
        if ($rs_info != 0) //如果info保存成功
        {
            if ($aInfoId != 0) {
                action_log('goods_edit', 'store', $aInfoId, is_login());
                $map_data['info_id'] = $aInfoId;
                D('Data')->where($map_data)->delete();
            } else {
                action_log('goods_add', 'store', $rs_info, is_login());
            }

            $dataModel = D('Data');
            foreach ($_POST as $key => $v) {
                $band = 'entity_id,over_time,ignore,info_id,title,__hash__,shop_id,cat1,cat2,cat3,price,cat,cover_id,attach_ids,trans_fee,des,file';
                if (!in_array($key, explode(',', $band))) {
                    if (is_array($v)) {
                        $rs_data = $rs_data && $dataModel->addData($key, implode(',', $v), $rs_info, $aEntityId);
                    } else {
                        $v = op_h($v);
                        $rs_data = $rs_data && $dataModel->addData($key, $v, $rs_info, $aEntityId);
                    }
                }
                // dump($rs_data);exit;
                if ($rs_data == 0) {
                    $this->error($dataModel->getError());
                }
            }
            if ($rs_info && $rs_data) {
                $this->assign('jumpUrl', U('store/Index/info', array('info_id' => $rs_info)));

                if ($entity['need_active']) {

                    // $this->success('发布成功。请耐心等待管理员审核。通过审核后该信息将出现在前台页面中。');
                } else {
                    if ($entity['show_nav']) {
                        $postUrl = U('store/index/info', array('info_id' => $rs_info), null, true);
                    }
                }
            }
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '发布失败。'));
        }

        if ($entity['name'] == 'shop') {
            if ($rs_info == 0) {
                $this->ajaxReturn(array('status' => 0, 'info' => '新增店铺失败。'));
            } elseif ($rs_data == 0) {
                if (I('post.info_id', 0, 'intval')) {
                    $this->ajaxReturn(array('status' => 0, 'info' => '修改店铺信息失败。'));
                } else {
                    $this->ajaxReturn(array('status' => 1, 'info' => '店铺创建成功，但相关信息添加失败，请联系管理员。'));
                }
            } else {
                if (I('post.info_id', 0, 'intval')) {
                    $this->ajaxReturn(array('status' => 1, 'info' => '修改店铺信息成功。'));
                } else {
                    $this->ajaxReturn(array('status' => 1, 'info' => '创建店铺成功。请耐心等待管理员审核。通过审核后即可上传商品。'));
                }
            }
            return;
        }

        if ($entity['name'] == 'good') {
            if ($rs_info && $rs_data) {
                $entity = D('store_entity')->find($info['entity_id']);
                if ($entity['need_active']) {
                    $this->ajaxReturn(array('status' => 1, 'info' => '发布成功。请耐心等待管理员审核。通过审核后该信息将出现在前台页面中。'));
                } else {
                    if ($entity['show_nav']) {
                        if (D('Common/Module')->isInstalled('Weibo')) { //安装了微博模块
                            $weiboModel = D('Weibo');
                            $weiboModel->addWeibo(is_login(), "我上架了一个新的 " . $entity['alias'] . " 【" . $info['title'] . "】：" . $postUrl);
                        }
                    }
                    $this->ajaxReturn(array('status' => 1, 'info' => '发布商品成功'));
                }
            } else {
                $this->ajaxReturn(array('status' => 0, 'info' => '商品发布失败，请联系管理员。'));
            }
        }

    }

    private function requireCanPost($aEntityId)
    {
        $entity = D('store_entity')->find($aEntityId);
        /**权限认证**/
        $can_post = CheckCanPostEntity(is_login(), $aEntityId);
        if (!$can_post) {
            $this->error('对不起，您无权发布。');
            return $entity;
        }
        return $entity;
        /**权限认证end*/
    }


    public function createShop($id = "")
    {
        $this->checkAuth('Store/Center/createShop', -1, '你没有开店权限！');
        if (IS_POST) {
            $aId = I('post.id', '0', 'intval');
            if ($aId != 0) {
                $shop = D('StoreShop')->find($aId);
                if ($shop['uid'] != get_uid()) {
                    $this->error('抱歉，您无编辑该店铺的权限。');
                }
                $this->checkActionLimit('store_edit', 'store', $aId, get_uid());
            }
            $aTitle = I('post.title', '', 'op_t,trim');
            $aSummary = I('post.summary', '', 'op_t,trim');
            $aLogo = I('post.logo', 0, 'intval');
            $aPosition = I('post.position', '', 'op_t');

            $shop['id'] = $aId;
            $shop['title'] = $aTitle;
            $shop['summary'] = $aSummary;
            $shop['logo'] = $aLogo;
            $shop['position'] = $aPosition;
            $shop['uid'] = is_login();

            $shop = D('StoreShop')->create($shop);
            if (!$shop) {
                $this->ajaxReturn(array('status' => 0, 'info' => '店铺创建失败。'));
            }
            if ($aId) {
                D('StoreShop')->save($shop);
                action_log('store_edit', 'store', $aId, get_uid());
                $this->ajaxReturn(array('status' => 1, 'info' => '店铺保存设置成功。'));
            } else {
                D('StoreShop')->add($shop);
                $this->ajaxReturn(array('status' => 1, 'info' => '店铺创建成功。'));
            }
        } else {
            if ($id) {
                $shop = D('StoreShop')->where(array('uid' => is_login()))->find();
                //      dump($shop);exit;
                $this->assign('shop', $shop);
                $this->setTopTitle('修改店铺信息');
                $this->display();
            } else {
                $this->setTopTitle('创建店铺——卖家中心');
                $this->display();
            }
        }
    }

    public function cart_add_item()
    {
        $item = $_POST;
        D('Cart')->addItem($item);
        $this->ajaxReturn(array('status' => 1, 'info' => '成功加入购物车。'));
    }

    public function buy_add_item()
    {
        $item = $_POST;
        D('Cart')->addItem($item);
        $this->ajaxReturn(array('status' => 1, 'info' => '购买成功，赚到结账页面。'));
    }

    public function placeOrder()
    {
        $this->checkAuth('Store/Center/pay', -1, '你没有下单权限！');
        $map['uid'] = is_login();
        $address = D('store_order')->where($map)->order('create_time desc ')->limit(1)->select();
        $this->assign('address', $address[0]);
        $goods = $_POST;
        if (!$goods['good_id']) {
            $this->error('购物车为空，无法结算。');
        }
        foreach ($goods['good_id'] as $v) {
            $goods['goods'][]['good'] = D('StoreInfo')->getById($v);
        }
        //dump($goods['goods']);exit;
        //获取地址
        $address = D('StoreOrder')->where(array('uid' => is_login()))->order('create_time desc')->find();
        $this->assign('address', $address);
        $this->assign('goods', $goods['goods']);
        $this->setTopTitle('确认付款');
        $this->display();
    }

    public function pay_ok()
    {
        $this->checkAuth('Store/Center/pay', -1, '你没有下单权限！');
        $this->checkActionLimit('store_pay', 'store', null, is_login());
        $aGoodsId = I('post.good_id', 0, 'intval');
        $aCount = I('post.count', 0, 'intval');
        $aPost = I('post.r_pos', '', 'op_t');
        if ($aPost == '') {
            $this->error('收件地址必须填写。');
        }
        $aCode = I('post.r_code', 0, 'intval');
        if ($aCode == 0) {
            $this->error('邮编号码必填。');
        }
        $aPhone = I('post.r_phone', '', 'op_t');
        if ($aPhone == '') {
            $this->error('手机号码必须填写。');
        }
        $aName = I('post.r_name', '', 'op_t');
        if ($aName == '') {
            $this->error('收件人姓名必填');
        }
        $order['create_time'] = time();
        $order['uid'] = get_uid();
        $order['r_pos'] = $aPost;
        $order['r_code'] = $aCode;
        $order['r_phone'] = $aPhone;
        $order['condition'] = 0;
        $order['r_name'] = $aName;

        $rs = D('Order')->addOrder($order, $aGoodsId, $aCount);
        if ($rs[0]) {
            action_log('store_pay', 'store', null, is_login());
            $this->ajaxReturn(array('status' => 1, 'info' => '下单成功。'));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '下单失败，错误信息：' . $rs[1]));
        }
    }

    //购物车移出商品
    public function cartRemoveItem()
    {
        $aGoods_id = I('post.id', 0, 'intval');
        if ($aGoods_id == 0) {
            $this->ajaxReturn(array('status' => 0, 'info' => '商品不存在。'));
        }
        D('Cart')->removeItem($aGoods_id);
        $this->ajaxReturn(array('status' => 1, 'info' => '删除商品成功。'));
    }

//收藏
    public function doFav()
    {
        $aGoodIds = I('post.id', 0, 'intval');
        $hasFav = D('StoreFav')->where(array('uid' => is_login(), 'info_id' => $aGoodIds))->find();

        if (!$hasFav) {
            //未收藏，就收藏
            $fav['uid'] = is_login();
            $fav['cTime'] = time();
            $fav['info_id'] = $aGoodIds;
            if (D('StoreFav')->add($fav)) {
                $this->ajaxReturn((array('status' => 1, 'info' => '收藏成功。')));
            };
        } else {
            //已收藏，就取消收藏
            $fav['uid'] = is_login();
            $fav['info_id'] = $aGoodIds;
            if (D('StoreFav')->where($fav)->delete()) {
                $this->ajaxReturn((array('status' => 2, 'info' => '取消成功。')));
            };
        }

        $this->ajaxReturn((array('status' => 0, 'info' => '收藏失败。')));
    }

    //关闭订单
    public function closeOrder()
    {
        $aOrderId = I('post.id', 0, 'floatval');
        $orderModel = D('Order');
        if ($orderModel->closeOrder($aOrderId)) {
            $this->success('关闭成功。');
        } else {
            $this->error($orderModel->getError());
        }
    }

    //发货
    public function send()
    {
        $aId = I('post.order_id', 0, 'floatval');
        $aTransName = I('post.trans_name', '', 'op_t');
        $aTransCode = I('post.trans_code', '', 'op_t');

        $orderModel = D('Order');
        $order = $orderModel->find($aId);
        if ($order['s_uid'] != is_login()) {
            $this->ajaxReturn(array('status' => 1, 'info' => '抱歉，您没有操作此订单的权限。'));
        }
        $order['trans_name'] = $aTransName;
        $order['trans_code'] = $aTransCode;
        $order['condition'] = 2;
        $order['trans_time'] = time();
        $rs = $orderModel->save($order);
        if ($rs) {
            D('Common/Message')->sendMessage($order['uid'], $content = '【微店】订单.' . $order['id'] . '商家已发货！', $title = '微店订单发货通知', 'store/center/orders', array(), is_login());
            $this->ajaxReturn(array('status' => 1, 'info' => '发货成功。'));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '发货失败。'));
        }
    }

    //价格调整
    public function adjPrice()
    {
        $aOrderId = I('post.order_id', 0, 'floatval');
        $aAdjCny = I('post.adj_cny', 0, 'floatval');
        if ($aOrderId == 0) {
            $this->ajaxReturn(array('status' => 0, 'info' => '修改价格失败，订单不存在。'));
        }
        //确认是卖家的订单
        //确认订单状态
        $map['s_uid'] = is_login();
        $map['order_id'] = $aOrderId;
        $map['condition'] = 0;

        $orderModel = D('Order');
        $order = $orderModel->where($map)->find();
        if (!$order) {
            $this->ajaxReturn(array('status' => 0, 'info' => '订单不存在。修改失败。'));
        }

        if ($order['total_cny'] + $aAdjCny <= 0) {
            $this->ajaxReturn(array('status' => 0, 'info' => '调价失败。调价之后价格不能小于0元。'));
        }

        $rs = $orderModel->where($map)->setField('adj_cny', floatval($aAdjCny));
        if ($rs) {
            $this->ajaxReturn(array('status' => 1, 'info' => '修改成功。'));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '修改失败。'));
        }
    }

    //确认收货
    public function buyer_mksure_order()
    {

        $aId = I('post.order_id', 0, 'floatval');
        $orderModel = D('Order');
        if ($orderModel->done($aId)) {
            $this->ajaxReturn(array('status' => 1, 'info' => '确认收货成功。'));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '确认收货失败。'));
        }

    }

    //评价
    public function doresponse()
    {

        $aId = I('post.order_id', 0, 'floatval');
        $aResponse = I('post.response', '', 'op_t');
        $aContent = I('post.content', '', 'op_t');

        $orderModel = D('Order');
        $rs = $orderModel->response($aId, $aResponse, $aContent);
        if ($rs) {
            $this->ajaxReturn(array('status' => 1, 'info' => '修改评价成功。'));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '修改评价失败。'));
        }
    }

    public function search()
    {
        //头部
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'back', 'a_class' => '', 'span_class' => ''),

            ),
        );
        // dump($this->_top_menu_list);exit;
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('商城搜索');

        $aKey = I('post.key', '', 'op_t');
        $aType = I('post.type', '', 'op_t');

        $_GET['key'] = $aKey;
        $_GET['type'] = $aType;
        if ($aType == 'shop') {
            $shop = D("StoreShop")->getListForSearch(array('title' => array('like', '%' . $aKey . '%'), 'status' => 1));
            if ($shop) {
                $data['html'] = "";
                foreach ($shop as $val) {
                    $this->assign("vo", $val);
                    $data['html'] .= $this->fetch("_shoplist");
                    $data['status'] = 1;
                }
            }else{
                $this->ajaxReturn(array('status'=>0,'info'=>'没有找到相应店铺。'));
            }
            $this->ajaxReturn($data);
        }
        if ($aType == 'goods') {
            $goods = D("StoreGoods")->where(array('title' => array('like', '%' . $aKey . '%'), 'status' => 1))->select();
            if ($goods) {
                $data['html'] = "";
                foreach ($goods as $val) {
                    $this->assign("vl", $val);
                    $data['html'] .= $this->fetch("_goodslist");
                    $data['status'] = 1;
                }
            }else{
                $this->ajaxReturn(array('status'=>0,'info'=>'没有找到相应商品。'));
            }
            $this->ajaxReturn($data);
        }

        $this->assign('type', $aType);
        $this->assign('searchKey', $aKey);

        $this->display();
    }

    //支付页面
    public function pay($orderId=0){
        $order=D('StoreOrder')->where(array('id'=>$orderId,'condition'=>0))->find();
        $order['item']=D('StoreItem')->where(array('order_id'=>$order['id']))->select();
        foreach($order['item'] as &$v){
            $v['goods']=D('StoreGoods')->where(array('id'=>$v['good_id']))->find();
        }
        $order['sellUser']=query_user(array('nickname', 'avatar64', 'uid', 'space_mob_url'), $order['s_uid']);
        $order['buyUser']=query_user(array('nickname', 'avatar64', 'uid', 'space_mob_url'), $order['uid']);

        $order['member']=D('Member')->where(array('uid'=>is_login()))->field('score4')->find();
        if($order['member']['score4'] >= ($order['total_cny']+$order['adj_cny'])){
            $order['canPay']=1;
        }else{
            $order['canPay']=0;
        }
         // dump($order);exit;
        $this->assign('order',$order);
        $this->display();
    }

    public function doPay(){
        $aId = I('post.id', 0, 'floatval');
        if ($aId != 0) {
            $orderModel = D('Order');
            if ($orderModel->pay($aId)) {
                $this->ajaxReturn(array('status'=>1,'info'=>'支付成功。'));
            } else {
                $this->ajaxReturn(array('status'=>0,'info'=>'支付失败。'));
            }

        } else {
            $this->ajaxReturn(array('status'=>0,'info'=>'订单信息获取错误。'));
        }
    }
}