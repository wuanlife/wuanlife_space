<?php


namespace Mob\Controller;

use Think\Controller;
use Think\Hook;

class WeiboController extends BaseController
{

    public function _initialize()
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'home','href'=>U('Mob/Weibo/index')),
                array('type'=>'message'),
            ),
            'center'=>array('title'=>'微博')
        );
        if(is_login()){
            if(check_auth('Weibo/Index/doSend')){
                $this->_top_menu_list['right'][]=array('type'=>'edit','href'=>U('Mob/Weibo/addWeibo'));
            }else{
                $this->_top_menu_list['right'][]=array('type'=>'edit','info'=>'你没有权限发布微博！');
            }
        }else{
            $this->_top_menu_list['right'][]=array('type'=>'edit','info'=>'登录后才能操作！');
        }
        //dump($this->_top_menu_list);exit;
        $this->setMobTitle('微博');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }

    /**
     * 主页面显示
     */
    public function index()
    {

        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count',10, 'op_t');
        $totalCount = D('Weibo')->where(array('status' => 1))->count();
        $weibo = D('Weibo')->where(array('status' => 1,))->page($aPage, $aCount)->order('create_time desc')->select();


        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);
            $v['rand_title'] = mob_get_head_title($v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();

            if($v['type']==="repost"){
                $v['content']= A('Mob/WeiboType')->fetchRepost($v);
            }else if($v['type']==="xiami"||$v['type']=="video"){
                $v['content'] =Hook::exec('Addons\\Insert' . ucfirst($v['type']) . '\\Insert' . ucfirst($v['type']) . 'Addon', 'fetch' . ucfirst($v['type']), $v);

            }else{
                $v['content'] = parse_weibo_mobile_content($v['content']);
            }

         //   $v['content'] = parse_weibo_mobile_content($v['content']);


            if (empty($v['from'])) {
                $v['from'] = "网站端";
            }
            $v['data'] = unserialize($v['data']);              //字符串转换成数组,获取微博源ID
            if ($v['data']['sourceId']) {                        //判断是否是源微博
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';
            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
           if(!is_null($v['sourceId_content'])){
               $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。
           }


            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数
            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源

            if (empty($v['sourceId_from']['from'])) {
                $v['sourceId_from'] = "网站端";
            } else {
                $v['sourceId_from'] = "手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b, 100, 100);                      //获得缩略图
//获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a, 100, 100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            if (in_array($v['id'], $is_zan)) {                         //判断是否已经点赞
                $v['is_support'] = '1';
            } else {
                $v['is_support'] = '0';
            }

            if (empty($v['data']['attach_ids'])) {            //判断是否是图片
                $v['is_img'] = '0';
            } else {
                $v['is_img'] = '1';
            }
            if (empty($v['sourceId_img']['0'])) {
                $v['sourceId_is_img'] = '0';
            } else {
                $v['sourceId_is_img'] = '1';
            }

        }


        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        $pid['is_allweibo'] = 1;

//dump($weibo);exit;
        $this->assign("weibo", $weibo);
        $this->assign("pid", $pid);
        $this->display();

    }

    /**
     * 查看更多功能实现
     */

    public function addMoreWeibo()
    {

        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $weibo = D('Weibo')->where(array('status' => 1,))->page($aPage, $aCount)->order('create_time desc')->select();

        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);
            $v['rand_title'] = mob_get_head_title($v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            if($v['type']==="repost"){
                $v['content']= A('Mob/WeiboType')->fetchRepost($v);
            }else if($v['type']==="xiami"||$v['type']=="video"){
                $v['content'] =Hook::exec('Addons\\Insert' . ucfirst($v['type']) . '\\Insert' . ucfirst($v['type']) . 'Addon', 'fetch' . ucfirst($v['type']), $v);

            }else{
                $v['content'] = parse_weibo_mobile_content($v['content']);
            }
            if (empty($v['from'])) {
                $v['from'] = "网站端";
            }

            $v['data'] = unserialize($v['data']);              //字符串转换成数组,获取微博源ID
            if ($v['data']['sourceId']) {                        //判断是否是源微博
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';

            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);


            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            if(!is_null($v['sourceId_content'])){
                $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。
            }
            $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数
            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源
            if (empty($v['sourceId_from']['from'])) {
                $v['sourceId_from'] = "网站端";
            } else {
                $v['sourceId_from'] = "手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b, 100, 100);
//获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a, 100, 100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            if (in_array($v['id'], $is_zan)) {                         //判断是否已经点赞
                $v['is_support'] = '1';
            } else {
                $v['is_support'] = '0';
            }

            if (empty($v['data']['attach_ids'])) {            //判断是否是图片
                $v['is_img'] = '0';
            } else {
                $v['is_img'] = '1';
            }
            if (empty($v['sourceId_img']['0'])) {
                $v['sourceId_is_img'] = '0';
            } else {
                $v['sourceId_is_img'] = '1';
            }
        }
        if ($weibo) {
            $data['html'] = "";
            foreach ($weibo as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_weibolist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);

    }

    /**
     * @param $id
     * 微博细节
     */

    public function weiboDetail($id)
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $this->setTopTitle('微博详情');
        $weibodetail = D('Weibo')->where(array('id'=>$id,'status'=>1))->find();
        if(is_null($weibodetail)){
            $this->error('微博不存在');
        }
        $weibodetail['meta']['description'] = mb_substr($weibodetail['content'], 0, 50, 'UTF-8');//取得前50个字符

        $support['appname'] = 'Weibo';
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        if (empty($weibodetail['from'])) {
            $weibodetail['from'] = "网站端";
        }

        $weibodetail['user'] = query_user(array('nickname', 'avatar64', 'uid'), $weibodetail['uid']);
        $weibodetail['rand_title'] = mob_get_head_title($weibodetail['uid']);

        $weibodetail['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $weibodetail['id']))->count();


        $weibodetail['data'] = unserialize($weibodetail['data']);              //字符串转换成数组,获取微博源ID
        if ($weibodetail['data']['sourceId']) {                        //
            $weibodetail['sourceId'] = $weibodetail['data']['sourceId'];
            $weibodetail['is_sourceId'] = '1';
        } else {
            $weibodetail['sourceId'] = $weibodetail['id'];
            $weibodetail['is_sourceId'] = '0';
        }



        $weibodetail['content'] = parse_weibo_mobile_content($weibodetail['content']);

        $weibodetail['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $weibodetail['sourceId']))->find();           //源微博用户名

        $weibodetail['sourceId_user'] = $weibodetail['sourceId_user']['uid'];
        $weibodetail['sourceId_user'] = query_user(array('nickname', 'uid'), $weibodetail['sourceId_user']);
        $weibodetail['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $weibodetail['sourceId']))->field('content')->find();          //源微博内容

        if(!is_null($weibodetail['sourceId_content'])){
            $weibodetail['sourceId_content'] = parse_weibo_mobile_content($weibodetail['sourceId_content']['content']);                                          //把表情显示出来。
        }
        $weibodetail['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $weibodetail['sourceId']))->field('repost_count')->find();    //源微博转发数
        $weibodetail['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $weibodetail['sourceId']))->field('from')->find();       //源微博来源

        if (empty($weibodetail['sourceId_from']['from'])) {
            $weibodetail['sourceId_from'] = "网站端";
        } else {
            $weibodetail['sourceId_from'] = "手机网页版";
        }

        $weibodetail['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $weibodetail['sourceId']))->field('data')->find();    //为了获取源微图片
        $weibodetail['sourceId_img'] = unserialize($weibodetail['sourceId_img']['data']);
        $weibodetail['sourceId_img'] = explode(',', $weibodetail['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
        foreach ($weibodetail['sourceId_img'] as &$b) {                                     //取得转发后源微博图片
            $weibodetail['sourceId_img_path'][] = getThumbImageById($b, 100, 100);
            //获得原图
            $bi = M('Picture')->where(array('status' => 1))->getById($b);
            if (!is_bool(strpos($bi['path'], 'http://'))) {
                $weibodetail['sourceId_img_big'][] = $bi['path'];
            } else {
                $weibodetail['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
            }

        }

        $weibodetail['cover_url'] = explode(',', $weibodetail['data']['attach_ids']);        //把attach_ids里的图片ID转出来
        foreach ($weibodetail['cover_url'] as &$a) {                                   //取得转发的微博的图片
            $weibodetail['img_path'][] = getThumbImageById($a, 100, 100);

        }

        if (empty($weibodetail['data']['attach_ids'])) {            //判断是否是图片
            $weibodetail['is_img'] = '0';
        } else {
            $weibodetail['is_img'] = '1';
        }

        if (in_array($weibodetail['id'], $is_zan)) {                         //判断是否已经点赞
            $weibodetail['is_support'] = '1';
        } else {
            $weibodetail['is_support'] = '0';
        }

        if (empty($weibodetail['sourceId_img']['0'])) {                     //判断源微博是否有图片
            $weibodetail['sourceId_is_img'] = '0';
        } else {
            $weibodetail['sourceId_is_img'] = '1';
        }

        $mapl['weibo_id'] = array('eq', $id);
        $weibocomment = D('Weibo_comment')->where(array('status' => 1, $mapl))->page($aPage, $aCount)->order('create_time desc')->select();
        $totalCount = D('Weibo_comment')->where(array('status' => 1, $mapl))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        foreach ($weibocomment as &$k) {
            $k['user'] = query_user(array('nickname', 'avatar32', 'uid'), $k['uid']);
            $k['rand_title'] = mob_get_head_title($k['uid']);
            $k['content'] = parse_weibo_mobile_content($k['content']);
        }
        $this->setMobTitle($weibodetail['user']['nickname']);
        $this->setMobDescription($weibodetail['meta']['description']);
        $this->setMobKeywords($weibodetail['user']['nickname']);
//dump($weibodetail);exit;
        $this->assign("weibodetail", $weibodetail);
        $this->assign("pid", $pid);           //判断评论数量是否大于10
        $this->assign('weibocomment', $weibocomment);                //微博评论

        $this->display();
    }

    /**
     * @param $id
     * 微博细节
     */

    public function addMoreComment()
    {

        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');

        $aId = I('post.id', '', 'op_t');

        $map['weibo_id'] = array('eq', $aId);
        $weibocomment = D('WeiboComment')->where(array('status' => 1, $map))->page($aPage, $aCount)->order('create_time desc')->select();

        foreach ($weibocomment as &$k) {
            $k['user'] = query_user(array('nickname', 'avatar32', 'uid'), $k['uid']);
            $k['rand_title'] = mob_get_head_title($k['uid']);
            $k['content'] = parse_weibo_mobile_content($k['content']);
        }

        if ($weibocomment) {
            $data['html'] = "";
            foreach ($weibocomment as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_weibocomment");

                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }

    /**
     * 渲染发布微博页面
     */

    public function addWeibo(){

        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'back','need_confirm'=>1,'confirm_info'=>'确定要返回？','a_class'=>'','span_class'=>''),
            ),
        );
       // dump($this->_top_menu_list);exit;
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->setTopTitle('发布微博');
        $this->display();
    }
    /**
 * 发微博
 */
    public function doSend()
    {
        // dump(is_login());exit;
        $aContent = I('post.content', '', 'op_t');
        $aType = I('post.type', 'image', 'op_t');
        $aAttachIds = I('post.attach_ids', '', 'op_t');
        //  dump($aContent);exit;

        //权限判断
        if (!is_login()) {
            $this->error('请登陆后再进行操作');
        }


        if (!check_auth('Weibo/Index/doSend')) {
            $this->error('您无微博发布权限。');
        }
        if (empty($aContent)) {
            $this->error('发布内容不能为空。');
        }

        $return = check_action_limit('add_weibo', 'weibo', 0, is_login(), true);
        if ($return && !$return['state']) {
            $this->error($return['info']);
        }
        $feed_data = array();
        $feed_data['attach_ids'] = $aAttachIds;
        if (empty($aAttachIds)) {
            $aType = 'feed';
        }


        // 执行发布，写入数据库
        $weibo_id = send_mob_weibo($aContent, $aType, $feed_data, $from = '手机网页版');

        if ($weibo_id) {
            $return['status'] = '1';
        } else {
            $return['status'] = ' 0';
            $return['info'] = '发布失败！';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 我的关注
     */
    public function myFocus()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $this->setTopTitle('我的关注');
        $follow_who_ids = D('Follow')->where(array('who_follow' => is_login()))->field('follow_who')->select();
        $follow_who_ids = array_column($follow_who_ids, 'follow_who');//简化数组操作。
        $follow_who_ids = array_merge($follow_who_ids, array(is_login()));//加上自己的微博
        $map['uid'] = array('in', $follow_who_ids);
        $weibo = D('Weibo')->where(array('status' => 1, $map))->page($aPage, $aCount)->order('create_time desc')->select();//我关注的人的微博
        $totalCount = D('Weibo')->where(array('status' => 1, $map))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }

        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);
            $v['rand_title'] = mob_get_head_title($v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            if($v['type']==="repost"){
                $v['content']= A('Mob/WeiboType')->fetchRepost($v);
            }else if($v['type']==="xiami"||$v['type']=="video"){
                $v['content'] =Hook::exec('Addons\\Insert' . ucfirst($v['type']) . '\\Insert' . ucfirst($v['type']) . 'Addon', 'fetch' . ucfirst($v['type']), $v);

            }else{
                $v['content'] = parse_weibo_mobile_content($v['content']);
            }
            if (empty($v['from'])) {
                $v['from'] = "网站端";
            }

            $v['data'] = unserialize($v['data']);              //字符串转换成数组,获取微博源ID
            if ($v['data']['sourceId']) {                        //判断是否是源微博
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';

            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            if(!is_null($v['sourceId_content'])){
                $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。
            }

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数

            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源
            if (empty($v['sourceId_from']['from'])) {
                $v['sourceId_from'] = "网站端";
            } else {
                $v['sourceId_from'] = "手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b, 100, 100);                      //获得缩略图
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a, 100, 100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            if (in_array($v['id'], $is_zan)) {                         //判断是否已经点赞
                $v['is_support'] = '1';
            } else {
                $v['is_support'] = '0';
            }

            if (empty($v['data']['attach_ids'])) {            //判断是否是图片
                $v['is_img'] = '0';
            } else {
                $v['is_img'] = '1';
            }
            if (empty($v['sourceId_img']['0'])) {
                $v['sourceId_is_img'] = '0';
            } else {
                $v['sourceId_is_img'] = '1';
            }

        }

        $pid['is_myfocus'] = 1;

        $this->assign("weibo", $weibo);
        $this->assign("pid", $pid);
        $this->display(T('Application://Mob@Weibo/index'));

    }

    /**
     * 加载更多我的关注
     */

    public function addMoreMyFocus()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $follow_who_ids = D('Follow')->where(array('who_follow' => is_login()))->field('follow_who')->select();
        $follow_who_ids = array_column($follow_who_ids, 'follow_who');//简化数组操作。
        $follow_who_ids = array_merge($follow_who_ids, array(is_login()));//加上自己的微博
        $map['uid'] = array('in', $follow_who_ids);
        $weibo = D('Weibo')->where(array('status' => 1, $map))->page($aPage, $aCount)->order('create_time desc')->select();


        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);
            $v['rand_title'] = mob_get_head_title($v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            if($v['type']==="repost"){
                $v['content']= A('Mob/WeiboType')->fetchRepost($v);
            }else if($v['type']==="xiami"||$v['type']=="video"){
                $v['content'] =Hook::exec('Addons\\Insert' . ucfirst($v['type']) . '\\Insert' . ucfirst($v['type']) . 'Addon', 'fetch' . ucfirst($v['type']), $v);

            }else{
                $v['content'] = parse_weibo_mobile_content($v['content']);
            }
                if (empty($v['from'])) {
                $v['from'] = "网站端";
            }

            $v['data'] = unserialize($v['data']);              //字符串转换成数组,获取微博源ID
            if ($v['data']['sourceId']) {                        //判断是否是源微博
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';

            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            if(!is_null($v['sourceId_content'])){
                $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。
            }

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数

            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源
            if (empty($v['sourceId_from']['from'])) {
                $v['sourceId_from'] = "网站端";
            } else {
                $v['sourceId_from'] = "手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b, 100, 100);                      //获得缩略图
//获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a, 100, 100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            if (in_array($v['id'], $is_zan)) {                         //判断是否已经点赞
                $v['is_support'] = '1';
            } else {
                $v['is_support'] = '0';
            }

            if (empty($v['data']['attach_ids'])) {            //判断是否是图片
                $v['is_img'] = '0';
            } else {
                $v['is_img'] = '1';
            }
            if (empty($v['sourceId_img']['0'])) {
                $v['sourceId_is_img'] = '0';
            } else {
                $v['sourceId_is_img'] = '1';
            }

        }


        if ($weibo) {
            $data['html'] = "";
            foreach ($weibo as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_weibolist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);


    }

    /**
     * @param $id
     * @param $uid
     * 点赞
     */
    public function support($id, $uid)
    {
        //$id是发帖人的微博ID
        //$uid是发帖人的ID
        if (!is_login()) {
            $this->error('请登陆后再进行操作');
        }
        $row = $id;
        $message_uid = $uid;
        $support['appname'] = 'Weibo';
        $support['table'] = 'weibo';
        $support['row'] = $row;
        $support['uid'] = is_login();

        if (D('Support')->where($support)->count()) {
            $return['status'] = '0';
            $return['info'] = '亲，您已经支持过我了！';
        } else {
            $support['create_time'] = time();
            if (D('Support')->where($support)->add($support)) {

                $this->clearCache($support);

                $user = query_user(array('username', 'uid'));

                D('Common/Message')->sendMessage($message_uid, $user['username'] . '给您点了个赞。', $title = $user['username'] . '赞了您。', 'Weibo/Index/weiboDetail',array('id' => $id), is_login(), 1);
                $return['status'] = '1';
            } else {
                $return['status'] = ' 0';
                $return['info'] = '亲，您已经支持过我了！';
            }


        }
        $this->ajaxReturn($return);
    }


    private function clearCache($support)
    {
        unset($support['uid']);
        unset($support['create_time']);
        $cache_key = "support_count_" . implode('_', $support);
        //S($cache_key, null);
    }

    /**
     * @param $id
     * @param $uid
     * 转发内容获取展示
     */

    public function forward($id, $uid)
    {

        //$id是发帖人的微博ID
        //$uid是发帖人的ID

        $map['id'] = array('eq', $id);
        $weibo = D('Weibo')->where(array('status' => 1, $map))->order('create_time desc')->select();
        // dump($weibo);


        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);

            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();

            $v['data'] = unserialize($v['data']);              //字符串转换成数组
            if ($v['data']['sourceId']) {
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';
            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);
            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();
        }


        $this->assign('weibo', $weibo[0]);
        $this->display(T('Application://Mob@Weibo/forward'));

    }

    /**
     * 转发功能实现
     */
    public function  doForward()
    {
        if (!is_login()) {
            $this->error('请您先登录', U('Mob/member/index'), 1);
        }

        $aContent = I('post.content', '', 'op_t');              //说点什么的内容
        $aType = I('post.type', '', 'op_t');                    //类型
        $aSoueseId = I('post.sourceId', 0, 'intval');           //获取该微博源ID
        $aWeiboId = I('post.weiboId', 0, 'intval');             //要转发的微博的ID
        $aBeComment = I('post.release', 'false', 'op_t');       //是否作为评论发布

        if (empty($aContent)) {
            $this->error('转发内容不能为空');
        }

        $this->checkAuth('Weibo/Index/doSendRepost', -1, '您无微博转发权限。');

        $return = check_action_limit('add_weibo', 'weibo', 0, is_login(), true);
        if ($return && !$return['state']) {
            $this->error($return['info']);
        }

        $weiboModel = D('Weibo');
        $feed_data = '';
        $source = $weiboModel->getWeiboDetail($aSoueseId);

        $sourceweibo = $source['weibo'];
        $feed_data['source'] = $sourceweibo;
        $feed_data['sourceId'] = $aSoueseId;

        $new_id = send_mob_weibo($aContent, $aType, $feed_data, $from = '手机网页版');        //发布微博


        if ($new_id) {
            D('weibo')->where('id=' . $aSoueseId)->setInc('repost_count');
            $aWeiboId != $aSoueseId && D('weibo')->where('id=' . $aWeiboId)->setInc('repost_count');
          //  S('weibo_' . $aWeiboId, null);
          //  S('weibo_' . $aSoueseId, null);
        }
// 发送消息
        $user = query_user(array('nickname', 'uid'), is_login());
        $toUid = D('weibo')->where(array('id' => $aWeiboId))->getField('uid');
        D('Common/Message')->sendMessage($toUid, '转发提醒' ,  $user['nickname'] . '转发了您的微博！',  'Weibo/Index/weiboDetail',array('id' => $new_id), is_login(), 1);
        // 发布评论

        if ($aBeComment == 'on') {
            send_comment($aWeiboId, $aContent);
        }


        //转发后的微博内容获取
        $weibo = D('Weibo')->where(array('status' => 1, 'id' => $new_id))->order('create_time desc')->select();
        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);
            $v['rand_title'] = mob_get_head_title($v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            if($v['type']==="repost"){
                $v['content']= A('Mob/WeiboType')->fetchRepost($v);
            }else if($v['type']==="xiami"||$v['type']=="video"){
                $v['content'] =Hook::exec('Addons\\Insert' . ucfirst($v['type']) . '\\Insert' . ucfirst($v['type']) . 'Addon', 'fetch' . ucfirst($v['type']), $v);

            }else{
                $v['content'] = parse_weibo_mobile_content($v['content']);
            }

            $v['data'] = unserialize($v['data']);              //字符串转换成数组,获取微博源ID
            if ($v['data']['sourceId']) {                        //判断是否是源微博
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';
            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            if(!is_null($v['sourceId_content'])){
                $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。
            }

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数
            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源

            if (empty($v['sourceId_from']['from'])) {
                $v['sourceId_from'] = "网站端";
            } else {
                $v['sourceId_from'] = "手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b, 100, 100);                      //获得缩略图
//获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a, 100, 100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            if (in_array($v['id'], $is_zan)) {                         //判断是否已经点赞
                $v['is_support'] = '1';
            } else {
                $v['is_support'] = '0';
            }

            if (empty($v['data']['attach_ids'])) {            //判断是否是图片
                $v['is_img'] = '0';
            } else {
                $v['is_img'] = '1';
            }
            if (empty($v['sourceId_img']['0'])) {
                $v['sourceId_is_img'] = '0';
            } else {
                $v['sourceId_is_img'] = '1';
            }

        }

        if ($weibo) {
            $data['html'] = "";
            foreach ($weibo as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_weibolist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
            $data['info'] = '转发失败！';
        }
        $this->ajaxReturn($data);

    }

    /**
     * @param $id
     * @param $user
     * 增加评论时显示的信息
     */
    public function addComment($id, $user)
    {
        //$id是发帖人的微博ID
        //$uid是发帖人的ID

        $map['id'] = array('eq', $id);
        $weibo = D('Weibo')->where(array('status' => 1, $map))->order('create_time desc')->select();


        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);

            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();

            $v['data'] = unserialize($v['data']);              //字符串转换成数组
            if ($v['data']['sourceId']) {
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';
            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);
            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();


            $v['at_user_id'] = $user;

        }
//dump($weibo);exit;
        $this->assign('weibo', $weibo[0]);
        $this->display(T('Application://Mob@Weibo/addcomment'));

    }

    /**
     * 增加评论实现
     */
    public function doAddComment()
    {
        if (!is_login()) {
            $this->error('请您先登录', U('Mob/member/index'), 1);
        }

        $aContent = I('post.weibocontent', '', 'op_t');              //说点什么的内容
        $aWeiboId = I('post.weiboId', 0, 'intval');             //要评论的微博的ID
        $aCommentId = I('post.comment_id', 0, 'intval');

        if (empty($aContent)) {
            $this->error('评论内容不能为空。');
        }

        $this->checkAuth('Weibo/Index/doComment', -1, '您无微博评论权限。');
        $return = check_action_limit('add_weibo_comment', 'weibo_comment', 0, is_login(), true);//行为限制
        if ($return && !$return['state']) {
            $this->error($return['info']);
        }
        $new_id = send_comment($aWeiboId, $aContent, $aCommentId);        //发布评论


        $weibocomment = D('WeiboComment')->where(array('status' => 1, 'id' => $new_id))->order('create_time desc')->select();

        foreach ($weibocomment as &$k) {
            $k['user'] = query_user(array('nickname', 'avatar32', 'uid'), $k['uid']);
            $k['rand_title'] = mob_get_head_title($k['uid']);
            $k['content'] = parse_weibo_mobile_content($k['content']);
        }

        if ($weibocomment) {
            $data['html'] = "";
            foreach ($weibocomment as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_weibocomment");

                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }


    public function delComment()
    {
        $comment_id = I('post.commentId', 0, 'intval');              //接收评论ID
        $weibo_id = I('post.weiboId', 0, 'intval');                   //接收微博ID

        $weibo_uid = D('Weibo')->where(array('status' => 1, 'id' => $weibo_id))->find();//根据微博ID查找微博发送人的UID
        $comment_uid = D('WeiboComment')->where(array('status' => 1, 'id' => $comment_id))->find();//根据评论ID查找评论发送人的UID

        if (!is_login()) {
            $this->error('请登陆后再进行操作');
        }


        if (is_administrator(get_uid()) || $weibo_uid['uid'] == get_uid() || $comment_uid['uid'] == get_uid()) {                                     //如果是管理员，则可以删除评论
            $result = D('WeiboComment')->deleteComment($comment_id);
        }
        if ($result) {
            $return['status'] = 1;
        } else {
            $return['status'] = 0;
            $return['info'] = '删除失败';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 热门微博
     */
    public function hotWeibo()
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $this->setTopTitle('热门微博');
        $hot_left = modC('HOT_LEFT', 3);
        $time_left = get_some_day($hot_left);
        $param['create_time'] = array('gt', $time_left);
        $param['status'] = 1;
        $param['is_top'] = 0;
        $weibo = D('Weibo')->where(array('status' => 1, $param))->page($aPage, $aCount)->order('comment_count desc')->select();
        $totalCount = D('Weibo')->where(array('status' => 1, $param))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }

        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);
            $v['rand_title'] = mob_get_head_title($v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            if($v['type']==="repost"){
                $v['content']= A('Mob/WeiboType')->fetchRepost($v);
            }else if($v['type']==="xiami"||$v['type']=="video"){
                $v['content'] =Hook::exec('Addons\\Insert' . ucfirst($v['type']) . '\\Insert' . ucfirst($v['type']) . 'Addon', 'fetch' . ucfirst($v['type']), $v);

            }else{
                $v['content'] = parse_weibo_mobile_content($v['content']);
            }
            if (empty($v['from'])) {
                $v['from'] = "网站端";
            }

            $v['data'] = unserialize($v['data']);              //字符串转换成数组,获取微博源ID
            if ($v['data']['sourceId']) {                        //判断是否是源微博
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';

            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            if(!is_null($v['sourceId_content'])){
                $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。
            }

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数

            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源
            if (empty($v['sourceId_from']['from'])) {
                $v['sourceId_from'] = "网站端";
            } else {
                $v['sourceId_from'] = "手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b, 100, 100);                      //获得缩略图
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a, 100, 100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            if (in_array($v['id'], $is_zan)) {                         //判断是否已经点赞
                $v['is_support'] = '1';
            } else {
                $v['is_support'] = '0';
            }

            if (empty($v['data']['attach_ids'])) {            //判断是否是图片
                $v['is_img'] = '0';
            } else {
                $v['is_img'] = '1';
            }
            if (empty($v['sourceId_img']['0'])) {
                $v['sourceId_is_img'] = '0';
            } else {
                $v['sourceId_is_img'] = '1';
            }

        }

        $pid['is_hotweibo'] = 1;

        $this->assign("weibo", $weibo);
        $this->assign("pid", $pid);
        $this->display(T('Application://Mob@Weibo/index'));

    }

    /**
     * 加载更多热门微博
     */
    public function addMoreHotWeibo()
    {
        $aPage = I('post.page',1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $hot_left = modC('HOT_LEFT', 3);
        $time_left = get_some_day($hot_left);
        $time_left = get_some_day($hot_left);
        $param['create_time'] = array('gt', $time_left);
        $param['status'] = 1;
        $param['is_top'] = 0;
        $weibo = D('Weibo')->where(array('status' => 1, $param))->page($aPage, $aCount)->order('comment_count desc')->select();


        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64', 'uid'), $v['uid']);
            $v['rand_title'] = mob_get_head_title($v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            if($v['type']==="repost"){
                $v['content']= A('Mob/WeiboType')->fetchRepost($v);
            }else if($v['type']==="xiami"||$v['type']=="video"){
                $v['content'] =Hook::exec('Addons\\Insert' . ucfirst($v['type']) . '\\Insert' . ucfirst($v['type']) . 'Addon', 'fetch' . ucfirst($v['type']), $v);

            }else{
                $v['content'] = parse_weibo_mobile_content($v['content']);
            }
            if (empty($v['from'])) {
                $v['from'] = "网站端";
            }

            $v['data'] = unserialize($v['data']);              //字符串转换成数组,获取微博源ID
            if ($v['data']['sourceId']) {                        //判断是否是源微博
                $v['sourceId'] = $v['data']['sourceId'];
                $v['is_sourceId'] = '1';
            } else {
                $v['sourceId'] = $v['id'];
                $v['is_sourceId'] = '0';

            }
            $v['sourceId_user'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->find();           //源微博用户名
            $v['sourceId_user'] = $v['sourceId_user']['uid'];
            $v['sourceId_user'] = query_user(array('nickname', 'uid'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            if(!is_null($v['sourceId_content'])){
                $v['sourceId_content'] = parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。
            }

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数

            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源
            if (empty($v['sourceId_from']['from'])) {
                $v['sourceId_from'] = "网站端";
            } else {
                $v['sourceId_from'] = "手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b, 100, 100);                      //获得缩略图
//获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a, 100, 100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if (!is_bool(strpos($bi['path'], 'http://'))) {
                    $v['sourceId_img_big'][] = $bi['path'];
                } else {
                    $v['sourceId_img_big'][] = getRootUrl() . substr($bi['path'], 1);
                }
            }

            if (in_array($v['id'], $is_zan)) {                         //判断是否已经点赞
                $v['is_support'] = '1';
            } else {
                $v['is_support'] = '0';
            }

            if (empty($v['data']['attach_ids'])) {            //判断是否是图片
                $v['is_img'] = '0';
            } else {
                $v['is_img'] = '1';
            }
            if (empty($v['sourceId_img']['0'])) {
                $v['sourceId_is_img'] = '0';
            } else {
                $v['sourceId_is_img'] = '1';
            }

        }

        if ($weibo) {
            $data['html'] = "";
            foreach ($weibo as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_weibolist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);


    }


}