<?php


namespace Mob\Controller;
define('IT_SINGLE_TEXT', 0);
define('IT_MULTI_TEXT', 1);
define('IT_SELECT', 2);
define('IT_EDITOR', 6);
define('IT_DATE', 5);
define('IT_RADIO', 3);
define('IT_PIC', 7);
define('IT_CHECKBOX', 4);
use Think\Controller;
use Common\Model\ContentHandlerModel;

class CatController extends BaseController
{
    public function _initialize()
    {
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'home', 'href' => U('Mob/cat/index')),
                array('type' => 'message'),
            ),
            'center' => array('title' => '分类信息')
        );

        //dump($this->_top_menu_list);exit;
        $this->setMobTitle('分类信息');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }


    public function index()
    {
        $aMark = I('mark', 'Job', 'text');
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
//dump($aMark);exit;
        //头部内容显示
        $entity = $this->getEntityList();
      //  dump($entity);exit;
        $this->assign('entity', $entity);
        S('INFO_LIST', null);
//顶部标题展示
        $toptitle = D('CatEntity')->where(array('name' => $aMark, 'status' => 1))->field('alias')->find();
        $this->setTopTitle($toptitle['alias']);
        $this->assign('mark', $aMark);
//查询不同mark对应的内容
        $list = D('cat_entity')->where(array('name' => $aMark))->field('id')->find();
        $map['entity_id'] = $list['id'];
        $map['status'] = 1;
     //   dump($map);exit;
        $list = D('Cat')->getList($map, $aPage, $aCount);
        // dump($list);exit;
        //dump($list[0]);exit;
        //查看更多是否显示，pid为1时显示
        if ($list[1] <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        //发布信息内容渲染
        $type = D('CatEntity')->where(array('status' => 1))->select();
        foreach ($type as &$v) {
            $name = 'add' . $v['name'];
            $entity = 'entityId' . "/" . $v['id'];
            $v['addurl'] = U('Mob/Cat/' . $name . "/" . $entity);
        }
    //    dump($list[0]);exit;
        $this->assign('type', $type);
        $this->assign('pid', $pid);
        $this->assign('catList', $list[0]);
        $this->display();
    }

    //获取后台配置 获取entity列表
    private function getEntityList()
    {
        $entity_list = D('CatEntity')->where(array('status' => 1))->select();
        foreach ($entity_list as &$v) {
            $v['url'] = U('mob/cat/index', array('mark' => $v['name']));
        }
        return $entity_list;
    }

    //创建我发布的信息新链接
    private function getMySendMsg()
    {
        $entity_list = D('CatEntity')->where(array('status' => 1))->field('alias,name')->select();
        foreach ($entity_list as &$v) {
            $v['url'] = U('mob/cat/release', array('mark' => $v['name']));
        }
        return $entity_list;
    }

    //创建我收藏的信息新链接
    private function getMyCollectMsg()
    {
        $entity_list = D('CatEntity')->where(array('status' => 1))->field('alias,name')->select();
        foreach ($entity_list as &$v) {
            $v['url'] = U('mob/cat/collection', array('mark' => $v['name']));
        }
        return $entity_list;
    }

    /**
     * 查看更多index 主页的信息
     */
    public function addMoreList()
    {
        $aMark = I('mark', '', 'text');
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $aMyText= I('post.myText', '', 'op_t');     //如果mySend，就是我的发送信息的查看更多,如果是myCollect,就是我的收藏查看更多
        S('INFO_LIST', null);
        $list = D('cat_entity')->where(array('name' => $aMark))->field('id')->find();
        $map['entity_id'] = $list['id'];
        $map['status'] = 1;
        if($aMyText=='mySend'){
            $map['uid']=is_login();
        }
        if($aMyText=='myCollect'){
            $catFav=D('catFav')->where(array('uid'=>is_login()))->field('info_id')->select();
            $catFav=array_column($catFav,'info_id');
            $map['id']=array('in',$catFav);
        }
        $list = D('Cat')->getList($map, $aPage, $aCount);
        switch ($aMark) {
            case 'House':
                if ($list[0]) {
                    $data['html'] = "";
                    foreach ($list[0] as $val) {
                        $this->assign("vl", $val);
                        $data['html'] .= $this->fetch("_house");
                        $data['status'] = 1;
                    }
                } else {
                    $data['stutus'] = 0;
                }
                break;
            case 'Job':
                if ($list[0]) {
                    $data['html'] = "";
                    foreach ($list[0] as &$val) {
                        $this->assign("vl", $val);
                        $data['html'] .= $this->fetch("_job");
                        $data['status'] = 1;
                    }
                } else {
                    $data['stutus'] = 0;
                }
                break;
            case 'PTJob':
                if ($list[0]) {
                    $data['html'] = "";
                    foreach ($list[0] as $val) {
                        $this->assign("vl", $val);
                        $data['html'] .= $this->fetch("_ptjob");
                        $data['status'] = 1;
                    }
                } else {
                    $data['stutus'] = 0;
                }
                break;
            case 'jianli':
                if ($list[0]) {
                    $data['html'] = "";
                    foreach ($list[0] as $val) {
                        $this->assign("vl", $val);
                        $data['html'] .= $this->fetch("_jianli");
                        $data['status'] = 1;
                    }
                } else {
                    $data['stutus'] = 0;
                }
                break;
            default:
                if ($list[0]) {
                    $data['html'] = "";
                    foreach ($list[0] as $val) {
                        $this->assign("vl", $val);
                        $data['html'] .= $this->fetch("_coustom");
                        $data['status'] = 1;
                    }
                } else {
                    $data['stutus'] = 0;
                }
                break;
        }
        $this->ajaxReturn($data);
    }

    public function detail($info_id = "")
    {
        /*检查是否在可阅读组内*/
        $can_post = CheckCanRead(is_login(), $info_id);
        if (!$can_post) {
            $this->assign('jumpUrl', U('cat/Index/index'));
            $this->error('对不起，您无权阅读。');
        }
        /*检查是否在可阅读组内end*/
        if (is_login()) {
            $map_read['uid'] = is_login();
            $map_read['info_id'] = $info_id;

            $has_read = D('cat_read')->where($map_read)->count();
            if ($has_read) {
                D('cat_read')->where($map_read)->setField('cTime', time());
            } else {
                $map_read['cTime'] = time();
                D('cat_read')->add($map_read);
            }
        }

        /*得到实体信息*///查找最近访问的人。
        $map['info_id'] = $info_id;

        $read = D('cat_read')->where($map)->order('cTime desc')->limit(10)->select();;
        foreach ($read as $key => $v) {
            $read[$key]['user'] = query_user(array('uid', 'nickname', 'space_url', 'avatar64'), $v['uid']);
        }
        $this->assign('read', $read);
        /*最近访问的人内容结束*/
        /*新增阅读数，并更改标题*/
        $info = D('cat_info')->find(I('get.info_id', 0, 'intval'));
        // dump($info);exit;
        $info['fav'] = D('CatFav')->where(array('info_id' => $info_id))->count();//获得收藏数
        $info['user'] = query_user(array('uid', 'nickname', 'space_mob_url', 'avatar64'), $info['uid']);
        $now = time();                                                      //判断是否过期
        if ($now > $info['over_time']) {
            $info['overed'] = 1;
        } else {
            $info['overed'] = 0;
        }
        $info['rate'] = D('CatRate')->where(array('info_id' => $info_id))->field('score')->select();
        $info['rate'] = array_column($info['rate'], 'score');
        $info['ratecount'] = D('CatRate')->where(array('info_id' => $info_id))->count();
        $info['rate'] = array_sum($info['rate']) / $info['ratecount'];


        //$info['over_time']=time_format($info['over_time']);


        $this->settopTitle($info['title']);
        $info['read']++;
        D('cat_info')->save($info);    //新增阅读数

        // $entity = D('cat_entity')->find($info['entity_id']);;


        //取出全部的字段数据
        //    $map_field['entity_id'] = $entity['id'];
        $map_field['status'] = 1;
        $fields = D('cat_field')->where($map_field)->order('sort desc')->select();
        //确定是否过期


        //获取到信息的数据
        $detail = D('Data')->getByInfoId($info['id']);
        $info['id'] = $info_id;

      //  dump($detail);exit;
        $this->assign('info', $info);
        $this->assign('detail', $detail);
        $this->display();
    }

//打分
    public function doScore()
    {
        $this->checkAuth('Cat/Index/doScore', -1, '你没有打分的权限！');
        $info_id = I('post.info_id', 0, 'intval');
        $rate['info_id'] = $info_id;
        $info = D('cat_info')->find($info_id);
        if ($info['uid'] == is_login()) {
            $this->error('不能给自己打分。');
        }
        $rate['uid'] = is_login();
        $map = $rate;
        if (D('cat_rate')->where($map)->count()) {
            $this->error('已经打过分。');
        }

        if (I('post.score', 'floatval') > 5 || I('post.score', 'floatval') < 0) {
            $this->error('分数有误。');
        }
        $rate['score'] = I('post.score', 'floatval');
        $rate['create_time'] = time();
        $rs = D('cat_rate')->add($rate);


        if ($rs) {
            $map_rate['info_id'] = $info_id;
            $count = D('cat_rate')->where($map_rate)->Avg('score');
            $map_info['id'] = I('post.info_id', 0, 'intval');
            D('cat_info')->where($map_info)->setField('rate', $count);

            $this->success('打分成功。');
        } else {
            $this->error('打分失败。');
        }

    }

    //收藏
    public function doFav()
    {
        $this->checkAuth('Cat/Index/doFav', -1, '你没有收藏分类信息的权限！');
        if (!D('Fav')->checkFav(is_login(), intval(I('post.id', 0, 'intval')))) {
            //未收藏，就收藏
            if (D('Fav')->doFav(is_login(), I('post.id', 0, 'intval'))) {
                $this->ajaxReturn((array('status' => 1, 'info' => '收藏成功')));
            };
        } else {
            //已收藏，就取消收藏
            if (D('Fav')->doDisFav(is_login(), I('post.id', 0, 'intval'))) {
                $this->ajaxReturn((array('status' => 2, 'info' => '取消收藏成功')));
            };

        }

        $this->ajaxReturn((array('status' => 0, 'info' => '收藏失败')));
    }


    //渲染发布岗位页面并实现功能
    public function addJob($edit = "", $infoId = "", $entity_id = "")
    {
        if ($edit) {
            $detail = D('Data')->getByInfoId($infoId);
            $info = D('cat_info')->find($infoId);
            // dump($detail);exit;
            $this->assign('info', $info);
            $this->assign('detail', $detail);
        }
        $map['name'] = array('in', array('reward', 'place'));
        $selcet = D('CatField')->where(array('status' => 1, $map))->select();
        $field_list_l = array_column($selcet, 'name');
        $selcet = array_combine($field_list_l, $selcet);
        foreach ($selcet as &$v) {
            $v['option'] = explode("\n", $v['option']);
        }
        $alias = D('CatEntity')->where(array('id' => $entity_id))->field('alias')->find();
        $this->assign('reward', $selcet['reward']);
        $this->assign('place', $selcet['place']);
        $this->setTopTitle('发布' . $alias['alias']);
        $this->assign("entityId", $entity_id);
        $this->display();
    }

    //房产
    public function addHouse($edit = "", $infoId = "", $entity_id = "")
    {
        if ($edit) {
            $detail = D('Data')->getByInfoId($infoId);
            $info = D('cat_info')->find($infoId);
            // dump($info);
            $this->assign('info', $info);
            $this->assign('detail', $detail);
        }
        $map['name'] = array('in', array('fangshi', 'leixing', 'zhuangxiu'));
        $selcet = D('CatField')->where(array('status' => 1, $map))->select();
        $field_list_l = array_column($selcet, 'name');
        $selcet = array_combine($field_list_l, $selcet);
        foreach ($selcet as &$v) {
            $v['option'] = explode("\n", $v['option']);
        }
        $alias = D('CatEntity')->where(array('id' => $entity_id))->field('alias')->find();
        $this->setTopTitle('发布' . $alias['alias']);
        $this->assign('fangshi', $selcet['fangshi']);
        $this->assign('leixing', $selcet['leixing']);
        $this->assign('zhuangxiu', $selcet['zhuangxiu']);
        $this->assign("entityId", $entity_id);
//dump($detail);exit;
        $this->display();
    }

    public function addPtjob($edit = "", $infoId = "", $entity_id = "")
    {
        if ($edit) {
            $detail = D('Data')->getByInfoId($infoId);
            $info = D('cat_info')->find($infoId);
            // dump($info);
            $this->assign('info', $info);
            $this->assign('detail', $detail);
        }
        $alias = D('CatEntity')->where(array('id' => $entity_id))->field('alias')->find();
        $this->setTopTitle('发布' . $alias['alias']);
        $this->assign("entityId", $entity_id);
//dump($detail);exit;
        $this->display();
    }

    public function addJianLi($edit = "", $infoId = "", $entity_id = "")
    {
        if ($edit) {
            $detail = D('Data')->getByInfoId($infoId);
            $info = D('cat_info')->find($infoId);
            $this->assign('info', $info);
            $this->assign('detail', $detail);
        }
        $alias = D('CatEntity')->where(array('id' => $entity_id))->field('alias')->find();
        $this->setTopTitle('发布' . $alias['alias']);
        $this->assign("entityId", $entity_id);

        $this->display();
    }

//其他自定义发布页面数据渲染。
    public function addBase($edit = "", $infoId = "", $entity_id = "")//$edit判断是否是编辑，默认为空，当有任何值传过来时就为编辑模式。$infoId编辑的内容ID。$entity_id，入口ID
    {
        if ($edit) {
            $detail = D('Data')->getByInfoId($infoId);
            $info = D('cat_info')->find($infoId);
            // dump($detail);exit;
            $this->assign('info', $info);
            $this->assign('detail', $detail);
        }
        $map['name'] = array('in', array('reward', 'place'));
        $selcet = D('CatField')->where(array('status' => 1, $map))->select();
        $field_list_l = array_column($selcet, 'name');
        $selcet = array_combine($field_list_l, $selcet);
        foreach ($selcet as &$v) {
            $v['option'] = explode("\n", $v['option']);
        }
        $alias = D('CatEntity')->where(array('id' => $entity_id))->field('alias')->find();
        $this->assign('reward', $selcet['reward']);
        $this->assign('place', $selcet['place']);
        $this->setTopTitle('发布' . $alias['alias']);
        $this->assign("entityId", $entity_id);
        $this->display();
    }

    public function doAddInfo()
    {
        unset($_POST['__hash__']);
        $entity_id = I('post.entity_id', 0, 'intval');
        $info_id = I('post.info_id', 0, 'intval');
        $aOverTime = I('post.over_time', '', 'op_t');
        $entity = D('cat_entity')->find($entity_id);

        /**权限认证**/
        $can_post = CheckCanPostEntity(is_login(), $entity_id);
        if (!$can_post) {
            $this->error('对不起，您无权发布。');
        }
        /**权限认证end*/


        $info['title'] = I('post.title', '', 'op_t');
        if ($info['title'] == '') {
            $this->error('必须输入标题');

        }
        if (mb_strlen($info['title'], 'utf-8') > 40) {
            $this->error('标题过长。');
        }
        $info['create_time'] = time();

        if ($info_id != 0) {
            //保存逻辑
            $info = D('cat_info')->find($info_id);
            $this->checkAuth('Cat/Index/editInfo', $info['uid'], '你没有编辑该条信息的权限！');
            $this->checkActionLimit('cat_edit_info', 'cat_info', $info['id']);
            if ($aOverTime != '') {
                $info['over_time'] = strtotime($aOverTime);
            }

            $info['id'] = $info_id;
            $res = D('cat_info')->save($info);
            $rs_info = $info['id'];
            if ($res) {
                action_log('cat_edit_info', 'cat_info', $info['id']);
            }
        } else {
            $this->checkAuth('Cat/Index/addInfo', -1, '你没有发布信息的权限！');
            $this->checkActionLimit('cat_add_info', 'cat_info');
            //新增逻辑
            $info['entity_id'] = $entity_id;
            $info['uid'] = is_login();
            if ($entity['need_active'] && !is_administrator()) {
                $info['status'] = 2;
            } else {
                $info['status'] = 1;
            }
            if (isset($_POST['over_time'])) {
                $info['over_time'] = strtotime($_POST['over_time']);
            }

            $rs_info = D('cat_info')->add($info);
            if ($rs_info) {
                action_log('cat_add_info', 'cat_info');
            }
        }

        $rs_data = 1;
        if ($rs_info != 0) //如果info保存成功
        {
            if ($info_id != 0) {
                $map_data['info_id'] = $info_id;
                D('Data')->where($map_data)->delete();
            }

            $dataModel = D('Data');
            //处理房屋的图片
            if ($entity_id == 2) {
                $listl = array('zhaopian1' => "", 'zhaopian2' => "", 'zhaopian3' => "", 'zhaopian4' => "", 'zhaopian5' => "");
                $list = $_POST;
                foreach ($list as $key => &$v) {
                    $array = explode(",", $list['zhaopian']);
                    foreach ($array as $k => $val) {
                        $list['zhaopian' . ($k + 1)] = $val;
                    }
                    unset($k, $val);
                }
                unset ($list['zhaopian']);
                $list = array_merge($listl, $list);
            } //处理房屋的图片END
            else if ($entity_id == 3) {
                $list = $_POST;
                if ($list['zhaopian']) {
                    $img_ids = explode(',', $list['zhaopian']);              //把图片和内容结合

                    foreach ($img_ids as &$v) {
                        $v = M('Picture')->where(array('status' => 1))->getById($v);
                        if (!is_bool(strpos($v['path'], 'http://'))) {
                            $v = $v['path'];
                        } else {
                            $v = getRootUrl() . substr($v['path'], 1);
                        }
                        $v = '<p><img src="' . $v . '" style=""/></p><br>';
                    };
                    $img_ids = implode('', $img_ids);
                    $list['jieshao'] = $img_ids . $list['jieshao'];
                    $contentHandler = new ContentHandlerModel();
                    $list['jieshao'] = $data['content'] = $contentHandler->filterHtmlContent($list['jieshao']);    //把图片和内容结合END
                }
                unset ($list['zhaopian']);
            } else {
                $list = $_POST;
                if ($list['zhaopian']) {
                    $img_ids = explode(',', $list['zhaopian']);              //把图片和内容结合

                    foreach ($img_ids as &$v) {
                        $v = M('Picture')->where(array('status' => 1))->getById($v);
                        if (!is_bool(strpos($v['path'], 'http://'))) {
                            $v = $v['path'];
                        } else {
                            $v = getRootUrl() . substr($v['path'], 1);
                        }
                        $v = '<p><img src="' . $v . '" style=""/></p><br>';
                    };
                    $img_ids = implode('', $img_ids);
                    $list['des'] = $img_ids . $list['des'];
                    $contentHandler = new ContentHandlerModel();
                    $list['des'] = $data['content'] = $contentHandler->filterHtmlContent($list['des']);    //把图片和内容结合END
                }
                unset ($list['zhaopian']);
            }
            // dump($list);exit;
            foreach ($list as $key => $v) {

                if ($key != 'entity_id' && $key != 'over_time' && $key != 'ignore' && $key != 'info_id' && $key != 'title' && $key != '__hash__' && $key != 'file') {
                    if (is_array($v)) {
                        $rs_data = $rs_data && $dataModel->addData($key, implode(',', $v), $rs_info, $entity_id);
                    } else {
                        $v = filter_content($v);
                        $rs_data = $rs_data && $dataModel->addData($key, $v, $rs_info, $entity_id);
                    }
                }
                if ($rs_data == 0) {
                    $this->error($dataModel->getError());
                }
            }
            if ($rs_info && $rs_data) {
                $this->assign('jumpUrl', U('Cat/Index/info', array('info_id' => $rs_info)));

                if ($entity['need_active']) {
                    $this->success('发布成功。' . cookie('score_tip') . ' 请耐心等待管理员审核。通过审核后该信息将出现在前台页面中。');
                } else {
                    if ($entity['show_nav']) {
                        if (D('Common/Module')->isInstalled('Weibo')) {//安装了微博模块
                            $postUrl = U('detail', array('info_id' => $rs_info), null, true);
                            $weiboModel = D('Weibo');
                            $weiboModel->addWeibo(is_login(), "我发布了一个新的 " . $entity['alias'] . "信息 【" . $info['title'] . "】：" . $postUrl);
                        }
                    }
                    $this->success('发布成功。' . cookie('score_tip'));
                }

            }
        } else {
            $this->error('发布失败。');
        }

    }

    public function delInfo()
    {

        $map['info_id'] = I('post.info_id', 0, 'intval'); // $_POST['info_id'];
        $info = D('cat_info')->find($map['info_id']);
        $this->checkAuth('Cat/Index/delInfo', $info['uid'], '你没有删除分类信息的权限！');

        $rs = D('cat_info')->where(array('id' => I('post.info_id', 0, 'intval')))->delete();
        if ($rs) {
            D('cat_data')->where($map)->delete();
            D('cat_com')->where($map)->delete();
        }
        if ($rs) {
            $this->ajaxReturn(array('status' => 1, 'info' => '删除成功！'));
        } else {
            $this->ajaxReturn(array('status' => 0, 'info' => '删除失败！'));
        }
    }

    public function userContent()
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'back','a_class'=>'','span_class'=>''),
            ),
        );
        // dump($this->_top_menu_list);exit;
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('用户中心');

        $sendMsg = D('catInfo')->where(array('uid' => is_login(), 'status' => 1))->count();
        $favdMsg = D('catFav')->where(array('uid' => is_login()))->count();
        $user = query_user(array('uid', 'nickname', 'space_mob_url', 'avatar64'), is_login());
        $this->assign('user', $user);
        //dump($user);exit;
        $this->assign('sendMsg', $sendMsg);
        $this->assign('favMsg', $favdMsg);
        $this->display();
    }


    public function release(){

        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'back','a_class'=>'','span_class'=>''),
            ),
        );
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('发布的信息');

        $aMark = I('mark', 'Job', 'text');
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
//dump($aMark);exit;
        //头部内容显示
        $entity = $this->getMySendMsg();
        //dump($entity);exit;
        $this->assign('entity', $entity);
        S('INFO_LIST', null);
//顶部标题展示
        $toptitle = D('CatEntity')->where(array('name' => $aMark, 'status' => 1))->field('alias')->find();
        $this->setTopTitle('我发布的'.$toptitle['alias']);
        $this->assign('mark', $aMark);
//查询不同mark对应的内容
        $list = D('cat_entity')->where(array('name' => $aMark))->field('id')->find();
        $map['entity_id'] = $list['id'];
        $map['status'] = 1;
        $map['uid']=is_login();
        $list = D('Cat')->getList($map, $aPage, $aCount);
        // dump($list);exit;
        //dump($list[0]);exit;
        //查看更多是否显示，pid为1时显示
        if ($list[1] <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        //发布信息内容渲染
        $type = D('CatEntity')->where(array('status' => 1))->select();
        foreach ($type as &$v) {
            $name = 'add' . $v['name'];
            $entity = 'entityId' . "/" . $v['id'];
            $v['addurl'] = U('Mob/Cat/' . $name . "/" . $entity);
        }
       // dump($type);exit;
        $this->assign('type', $type);
        $this->assign('pid', $pid);
        $this->assign('catList', $list[0]);
        $this->display();
    }

    public function collection(){

        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'back','a_class'=>'','span_class'=>''),
            ),
        );
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('发布的信息');

        $aMark = I('mark', 'Job', 'text');
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
//dump($aMark);exit;
        //头部内容显示
        $entity = $this->getMyCollectMsg();
        //dump($entity);exit;
        $this->assign('entity', $entity);
        S('INFO_LIST', null);
//顶部标题展示
        $toptitle = D('CatEntity')->where(array('name' => $aMark, 'status' => 1))->field('alias')->find();
        $this->setTopTitle('我收藏的'.$toptitle['alias']);
        $this->assign('mark', $aMark);
//查询不同mark对应的内容
        $list = D('cat_entity')->where(array('name' => $aMark))->field('id')->find();
        //设置我的收藏查找条件
        $catFav=D('catFav')->where(array('uid'=>is_login()))->field('info_id')->select();
        $catFav=array_column($catFav,'info_id');
        $map['id']=array('in',$catFav);

        $map['entity_id'] = $list['id'];
        $map['status'] = 1;

      //  dump($map);exit;
        $list = D('Cat')->getList($map, $aPage, $aCount);
        // dump($list);exit;
        //dump($list[0]);exit;
        //查看更多是否显示，pid为1时显示
        if ($list[1] <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        //发布信息内容渲染
        $type = D('CatEntity')->where(array('status' => 1))->select();
        foreach ($type as &$v) {
            $name = 'add' . $v['name'];
            $entity = 'entityId' . "/" . $v['id'];
            $v['addurl'] = U('Mob/Cat/' . $name . "/" . $entity);
        }
        // dump($type);exit;
        $this->assign('type', $type);
        $this->assign('pid', $pid);
        $this->assign('catList', $list[0]);
        $this->display();
    }


    //我的信箱
    public function myMailBox(){
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'back','a_class'=>'','span_class'=>''),
            ),
        );
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('我的信箱');

        //收到的信息数据渲染
        $rec=D('catSend')->where(array('rec_uid'=>is_login()))->order('create_time desc')->select();
        foreach($rec as &$v){
            $v['info']=D('catInfo')->where(array('id'=>$v['info_id']))->find();
            $v['user']= query_user(array('uid', 'nickname', 'space_mob_url', 'avatar64'), $v['send_uid']);
        }unset ($v);
        $this->assign('rec',$rec);

        //发送的信息数据渲染
        $send=D('catSend')->where(array('send_uid'=>is_login()))->order('create_time desc')->select();
        foreach($send as &$a){
            $a['info']=D('catInfo')->where(array('id'=>$a['info_id']))->find();
            $a['user']= query_user(array('uid', 'nickname', 'space_mob_url', 'avatar64'), $a['send_uid']);
        }unset ($a);
        $this->assign('read',$send);

        //发布信息数据渲染
        $follows = D('Follow')->where(array('who_follow' => is_login(), 'follow_who' => is_login(), '_logic' => 'or'))->limit(999)->select();
        $uids = array();
        foreach ($follows as &$e) {
            $uids[] = $e['who_follow'];
            $uids[] = $e['follow_who'];
        }
        unset($e);
        $uids = array_unique($uids);
        $users = array();
        foreach ($uids as $uid) {
            $user = query_user(array('nickname', 'id', 'avatar32'), $uid);
            $user['search_key'] = $user['nickname'] . D('PinYin')->Pinyin($user['nickname']);
            $users[] = $user;
        }
        $this->assign('users',$users);      //接收人

        //选择信息
        $tree = $this->getEntityList();
        foreach($tree as &$v){
            $v['_']=D('catInfo')->where(array('entity_id'=>$v['id']))->select();
        }
       // dump($tree);exit;
        $this->assign('tree',$tree);

        $this->display();
    }
    //二级分类选择
    public function selectDropdown($pid)
    {
        $issues = D('catInfo')->where(array('entity_id' => $pid, 'status' => 1,'uid'=>is_login()))->limit(999)->select();
        exit(json_encode($issues));
    }

    public function doSendInfo()
    {
        $this->checkAuth('Cat/Center/doSendInfo',-1,'你没有发送消息的权限！');
        $this->checkActionLimit('cat_center_send_info','cat_center');

        $send['info_id'] = I('post.infoId','','op_t');
        $uids = I('post.uids');
        $array = array_unique($uids);
        $send['send_uid'] = is_login();
        $send['create_time'] = time();
        $send['content'] = I('post.content', '', 'op_t');
        $rs = 1;
        $user_info=query_user(array('nickname'));
        $tip = "用户{$user_info['nickname']}在分类信息中给你发了消息，附言：".$send['content'];
        foreach ($array as $v) {
            if ($array != '') {
                    $t_send = $send;
                    $t_send['rec_uid'] =$v;
                    $rs = $rs && D('cat_send')->add($t_send);
                    //发送消息
                    /**
                     * @param $to_uid 接受消息的用户ID
                     * @param string $content 内容
                     * @param string $title 标题，默认为  您有新的消息
                     * @param $url 链接地址，不提供则默认进入消息中心
                     * @param $int $from_uid 发起消息的用户，根据用户自动确定左侧图标，如果为用户，则左侧显示头像
                     * @param int $type 消息类型，0系统，1用户，2应用
                     */
                    D('Common/Message')->sendMessage($v,"分类信息发送消息", $tip , 'Cat/Center/rec',array(), get_uid(), 1);
              //  }
            }
        }
        if ($rs) {
            action_log('cat_center_send_info','cat_center');
            $this->ajaxReturn(array('status'=>1,'info'=>'发送成功。'));
        } else {
            $this->ajaxReturn(array('status'=>0,'info'=>'发送失败。'));
        }
    }


    public function search($entity_id = 0, $name = '' ){

        $entity_id = I('get.entity_id', 0, 'intval');

        if ($entity_id != 0) {
            $map['entity_id'] = $entity_id;
        }
        if ($name != '') {
            $map['name'] = $name;
        }


        $entity = D('cat_entity')->where($map)->find();
      //  dump($entity['alias']);exit;
        $this->assign('current', 'category_' . $entity['id']);
        $this->setTopTitle('搜索'.$entity['alias']);
        $map_s_field['entity_id'] = $entity['id'];
        $map_s_field['can_search'] = '1';
        $map_s_field['status'] = 1;
        $search_fields = D('cat_field')->where($map_s_field)->order('sort desc')->select();
        foreach ($search_fields as $key => $v) {
            $search_fields[$key]['values'] = parseOption($v['option']);
        }
        $data['search_fields'] = $search_fields;
       // dump($data);exit;
        $this->assign($data);
        $this->assign('entity', $entity);
        $this->display();
    }


    public function doSearch()
    {
        $data= $_POST;
        if($data['mark']==1){
             //修改数组跟网站端一致
            $data['map']=array('name'=>$_POST['name'],'title'=>$_POST['title']);
            unset ($data['title'],$data['mark']);
        }else{
            //修改数组跟网站端一致
           $array=array_filter($data);
            unset($array['mark']);
            $data=array('name'=>$array['name'],'type'=>$array['type'],'class'=>$array['class']);
            $array1=array('name','class','type');
            $data['map']['name']=$array['name'];
            foreach($array as $key=>$v){
                if(!in_array($key,$array1)){
                    $data['map'][$key]=$v;
                }
            }
        }


        define('NORES', '');
        /*初始化所有的参数*/
        // 从$data中读取参数
        $this->_type = isset($data['type']) ? $data['type'] : 'limit';
        $this->_class = isset($data['class']) ? $data['class'] : 'cat_ul_list';
        $num = isset($data['num']) ? $data['num'] : 50;
        $data['order'] = op_t($data['order']);
        $order = $data['order'] != '' ? $data['order'] : 'update_time desc,create_time desc';
        $recom = isset($data['recom']) ? $data['recom'] : false;
        // 置顶排序
        $order = 'top desc,' . $order;
        $map = array();

        if ($recom) {
            $map['recom'] = 1;
        }
        if(I('post.title','','op_t')){
            $map['title']=array('like','%'.I('post.title','','op_t').'%');
        }

        /*初始化所有的参数end*/
        $map['status'] = 1;
        /*获取到查询的条件*/
        $entity = $this->getEntity($data);


        $map['entity_id'] = $entity['id'];
        /*获取到查询的条件end*/


        $filted_ids = null; //初始的情况下是不做限定的
        if (isset($data['map']) && count($data['map'])) {
            /*清除干扰条件*/
            $map_params = $this->unsetOtherParm($data);

            /*清除干扰条件end*/

            foreach ($map_params as $param_name => $param_value) {

                if ($param_value == '' || $param_name == 'p') {
                    //如果这个参数是没有值的，就略过
                    continue;
                }
                /*查出field的field_id*/
                $field_map['name'] = $param_name;
                $field_map['entity_id'] = $map['entity_id'];
                $search_field = D('cat_field')->where($field_map)->find();
                $field_id = $search_field['id'];
                /*查出field的field_idend*/
                $data_map['field_id'] = $field_id;
                if ($search_field['input_type'] == IT_SINGLE_TEXT || $search_field['input_type'] == IT_MULTI_TEXT || $search_field['input_type'] == IT_EDITOR || $search_field['input_type']==IT_SELECT) {
                    $data_map['value'] = array('like', '%' . $param_value . '%');
                } else if ($search_field['input_type'] == IT_CHECKBOX) { //处理多选框，无法实现隔项选择查询的功能
                    //重新升序整理数组
                    $value_array = explode(',', $param_value);
                    sort($value_array, SORT_NUMERIC);
                    $sear_value = implode(',', $value_array);
                    $data_map['value'] = array('like', '%' . $sear_value . '%');
                } else {
                    $data_map['value'] = $param_value;
                }

                if ($filted_ids != null) {
                    //如果不是第一次被过滤，就需要把ids作为条件，在此基础上查询
                    $data_map['info_id'] = array('in', implode(',', $filted_ids));
                }
                //进行查询

                $cat_data = D('cat_data')->where($data_map)->select();
                //更新info_ids
                if (count($cat_data) == 0) {
                    $data['stutus'] = 0;
                    $data['info'] = '搜索不到相应数据';
                    $this->ajaxReturn($data);

                }

                $filted_ids = getSubByKey($cat_data, 'info_id');


            }
        }

        if ($filted_ids != null) {
            //如果不为nul，意味着已经被前面的查询影响到了，就需要把过滤结果更新到条件中
            $map['id'] = array('in', implode(',', $filted_ids));
/*            if ($filted_ids == '') {
                echo NORES; //如果发现已经受影响，并且为空
                return;
            }*/

        }
        //获取数据
        if ($this->_type == 'list') {
            $infos = D('Info')->getList($map, $num, $order);
            $info_count = count($infos['data']);
        } else {
            $infos = D('Info')->getLimit($map, $num, $order);
            $info_count = count($infos);
        }
       //dump($infos['data']);exit;
if($infos['data']){
    switch ($_POST['name']) {
        case 'Job':
            if ($infos['data']) {
                $data['html'] = "";
            //    dump($infos['data']);exit;
                foreach ($infos['data'] as $val) {
                    $val['user']=query_user(array('avatar32','nickname'),$val['uid']);
                    $this->assign("vl", $val);
                    $data['html'] .= $this->fetch("_job");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
                $data['info'] = '搜索不到相应数据';
            }
            break;
        case 'House':
            if ($infos['data']) {
                $data['html'] = "";
                foreach ($infos['data'] as $val) {
                    $val['user']=query_user(array('avatar32','nickname'),$val['uid']);
                    $this->assign("vl", $val);
                    $data['html'] .= $this->fetch("_house");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
                $data['info'] = '搜索不到相应数据';
            }

            break;
        case 'PTJob':
            if ($infos['data']) {
                $data['html'] = "";
                foreach ($infos['data'] as $val) {
                    $val['user']=query_user(array('avatar32','nickname'),$val['uid']);
                    $this->assign("vl", $val);
                    $data['html'] .= $this->fetch("_ptjob");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
                $data['info'] = '搜索不到相应数据';
            }
            break;
        case 'jianli':
            if ($infos['data']) {
                $data['html'] = "";
                foreach ($infos['data'] as $val) {
                    $val['user']=query_user(array('avatar32','nickname'),$val['uid']);
                    $this->assign("vl", $val);
                    $data['html'] .= $this->fetch("_jianli");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
                $data['info'] = '搜索不到相应数据';
            }
            break;

        default:
            if ($infos['data']) {
                $data['html'] = "";
                foreach ($infos['data'] as $val) {
                    $val['user']=query_user(array('avatar32','nickname'),$val['uid']);
                    $this->assign("vl", $val);
                    $data['html'] .= $this->fetch("_custom");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
                $data['info'] = '搜索不到相应数据';
            }
    }
}else{
    $data['stutus'] = 0;
    $data['info'] = '搜索不到相应数据';
}


        $this->ajaxReturn($data);

    }


    private function getEntity($data)
    {
        if (intval($data['entity_id']) != 0) {
            //获取预置的模板
            $entity = D('cat_entity')->find(intval($data['entity_id']));
            return $entity;
        } else {
            //通过name查到entity,和entity的id
            $map_t['name'] = $data['name'];
            $entity = D('cat_entity')->where($map_t)->find();
            return $entity;
        }
    }

    public function unsetOtherParm($data)
    {
        $li = $data['map'];
        unset($li['name']);
        unset($li['entity_id']);
        unset($li['app']);
        unset($li['act']);
        unset($li['mod']);
        unset($li['page']);
        unset($li['title']);
        unset($li['__hash__']);
        return $li;
    }
}