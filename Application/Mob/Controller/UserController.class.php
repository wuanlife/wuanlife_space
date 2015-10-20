<?php


namespace Mob\Controller;

use Think\Controller;


class UserController extends BaseController
{
    public function _initialize()
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'home','href'=>U('Mob/Weibo/index')),
                array('type'=>'message'),
            ),

        );
        $this->setMobTitle('个人中心');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }

    public function index($uid=null,$tab='weibo')
    {
        if(empty($uid)){
            $uid=is_login();
        } else{
            $uid=$uid;
        }
        $user_info = query_user(array('avatar64', 'nickname', 'uid', 'space_url','space_mob_url', 'icons_html', 'score', 'title', 'fans', 'following', 'weibocount', 'rank_link', 'signature'), $uid);
        //获取用户封面id
        $map=getUserConfigMap('user_cover','',$uid);
        $map['role_id']=0;
        $model=D('Ucenter/UserConfig');
        $cover=$model->findData($map);
        $user_info['cover_id']=$cover['value'];
        if(empty($cover['value'])){
            $user_info['cover_path']="";
        }else{
            $user_info['cover_path']=getThumbImageById($cover['value'],1140,230);
        }


        $user_info['tags']=D('Ucenter/UserTagLink')->getUserTag($uid);

        switch($tab){
            case 'weibo':
                $weibo= $this->_myWeibo($uid);
                $this->assign("weibo", $weibo);
                $this->assign("uid", $uid);
                break;
            case 'news':
                $blog= $this->_myBlog($uid);
                $this->assign("blog", $blog);
                $this->assign("uid", $uid);
                break;
            case 'userhead':
                $userhead=$this->_userHead($uid);
                $this->assign("userhead", $userhead);
                break;
            case 'userdata':
                $userdata=$this->_userData($uid);
                $profile_group_list = $this->_profile_group_list($uid);
                foreach ($profile_group_list as &$val) {
                    $val['info_list'] = $this->_info_list($val['id'], $uid);
                }
              //  dump($profile_group_list);exit;
                $this->assign('profile_group_list', $profile_group_list);
                $this->assign("userdata", $userdata);
                break;
            case 'focus':
                $following=$this->_myFocus($uid);
                $this->assign("following", $following);
                break;
        }
        $this->setTopTitle('个人中心');
      //  dump($user_info);
        $this->assign("uid", $uid);
        $this->assign('user_info', $user_info);
        $this->display();
    }

    /**
     * @param $uid
     * @return mixed
     * 获取所有微博信息
     */
    private  function _myWeibo($uid){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');

        $weibo = D('Weibo')->where(array('status' => 1, 'uid'=>$uid))->order('create_time desc')->page($aPage, $aCount)->select();//我关注的人的微博

        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
            $v['rand_title']=mob_get_head_title( $v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            $v['content']=parse_weibo_mobile_content($v['content']);
            if(empty($v['from'])){
                $v['from']="网站端";
            }
            $totalCount=D('Weibo')->where(array('status' => 1, 'uid'=>$uid))->count();
            if($totalCount<=$aPage*$aCount){
                $v['pid_count']=0;

            }else{
                $v['pid_count']=1;
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
            $v['sourceId_user'] = query_user(array('nickname','space_mob_url'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            $v['sourceId_content']=parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数

            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源
            if(empty($v['sourceId_from']['from'])){
                $v['sourceId_from']="网站端";
            }else{
                $v['sourceId_from']="手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b,100,100);                      //获得缩略图
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if(!is_bool(strpos( $bi['path'],'http://'))){
                    $v['sourceId_img_big'][] = $bi['path'];
                }else{
                    $v['sourceId_img_big'][] =getRootUrl(). substr( $bi['path'],1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a,100,100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if(!is_bool(strpos( $bi['path'],'http://'))){
                    $v['sourceId_img_big'][] = $bi['path'];
                }else{
                    $v['sourceId_img_big'][] =getRootUrl(). substr( $bi['path'],1);
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
        //dump($weibo);exit;
        return $weibo;

    }


    public  function addmoreWeibo(){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $uid = I('post.uid', '', 'op_t');
        $weibo = D('Weibo')->where(array('status' => 1, 'uid'=>$uid))->order('create_time desc')->page($aPage, $aCount)->select();//我关注的人的微博

        $support['appname'] = 'Weibo';                              //查找是否点赞
        $support['table'] = 'weibo';
        $support['uid'] = is_login();
        $is_zan = D('Support')->where($support)->select();
        $is_zan = array_column($is_zan, 'row');

        foreach ($weibo as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64','space_mob_url'), $v['uid']);
            $v['rand_title']=mob_get_head_title( $v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();
            $v['content']=parse_weibo_mobile_content($v['content']);
            if(empty($v['from'])){
                $v['from']="网站端";
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
            $v['sourceId_user'] = query_user(array('nickname','space_mob_url'), $v['sourceId_user']);

            $v['sourceId_content'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('content')->find();          //源微博内容
            $v['sourceId_content']=parse_weibo_mobile_content($v['sourceId_content']['content']);                                          //把表情显示出来。

            $v['sourceId_repost_count'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('repost_count')->find();    //源微博转发数

            $v['sourceId_from'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('from')->find();       //源微博来源
            if(empty($v['sourceId_from']['from'])){
                $v['sourceId_from']="网站端";
            }else{
                $v['sourceId_from']="手机网页版";
            }

            $v['sourceId_img'] = D('Weibo')->where(array('status' => 1, 'id' => $v['sourceId']))->field('data')->find();    //为了获取源微图片
            $v['sourceId_img'] = unserialize($v['sourceId_img']['data']);
            $v['sourceId_img'] = explode(',', $v['sourceId_img']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['sourceId_img'] as &$b) {
                $v['sourceId_img_path'][] = getThumbImageById($b,100,100);                      //获得缩略图
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if(!is_bool(strpos( $bi['path'],'http://'))){
                    $v['sourceId_img_big'][] = $bi['path'];
                }else{
                    $v['sourceId_img_big'][] =getRootUrl(). substr( $bi['path'],1);
                }
            }


            $v['cover_url'] = explode(',', $v['data']['attach_ids']);        //把attach_ids里的图片ID转出来
            foreach ($v['cover_url'] as &$a) {
                $v['img_path'][] = getThumbImageById($a,100,100);
                //获得原图
                $bi = M('Picture')->where(array('status' => 1))->getById($b);
                if(!is_bool(strpos( $bi['path'],'http://'))){
                    $v['sourceId_img_big'][] = $bi['path'];
                }else{
                    $v['sourceId_img_big'][] =getRootUrl(). substr( $bi['path'],1);
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
                $this->assign("vo", $val);
                $data['html'] .= $this->fetch("_myweibo");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);

    }


    /**
     * @param $uid
     * @return mixed
     * 获取所有资讯信息
     */
    private function _myBlog($uid){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        if($uid==is_login()){
            $blog= D('News')->where(array('uid'=>$uid))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
        }else{
            $blog= D('News')->where(array('uid'=>$uid,'status'=>1))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
        }

        foreach($blog as &$a){
            switch ($a['status']){
                case '1':
                    $a['approval']='审核通过';
                    break;
                case '2':
                    $a['approval']='待审核';
                    break;
                case '-1':
                    $a['approval']='审核失败';
                    break;
            }if($a['dead_line']<=time()){
                $a['approval']='已过期';
            }
        }
        foreach ($blog as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','space_mob_url'), $v['uid']);
            $totalCount=D('Weibo')->where(array('status' => 1, 'uid'=>$uid))->count();
            if($totalCount<=$aPage*$aCount){
                $v['pid_count']=0;

            }else{
                $v['pid_count']=1;
            }
            if(empty($v['cover'])){
                $v['cover_url']='no_img';
            }else{
                $v['cover_url'] = getThumbImageById($v['cover'],119,89);
            }
            $v['count']=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$v['id']))->order('create_time desc')->count();
        }


        return $blog;
    }

    public  function addMoreBlog(){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $uid = I('post.uid', '', 'op_t');
        if($uid==is_login()){
            $blog= D('News')->where(array('uid'=>$uid))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
        }else{
            $blog= D('News')->where(array('uid'=>$uid,'status'=>1))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
        }
        foreach($blog as &$a){
            switch ($a['status']){
                case '1':
                    $a['approval']='审核通过';
                    break;
                case '2':
                    $a['approval']='待审核';
                    break;
                case '-1':
                    $a['approval']='审核失败';
                    break;
            }if($a['dead_line']<=time()){
                $a['approval']='已过期';
            }
        }
        foreach ($blog as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','space_mob_url'), $v['uid']);
            if(empty($v['cover'])){
                $v['cover_url']='no_img';
            }else{
                $v['cover_url'] = getThumbImageById($v['cover'],119,89);
            }
            $v['count']=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$v['id']))->order('create_time desc')->count();
        }

        if ($blog) {
            $data['html'] = "";
            foreach ($blog as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_myblog");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }

    /**
     * @param $uid
     * @return mixed
     * 获取所有头衔信息
     */
    private function _userHead($uid){
        //获得以后头衔
        $user_title['have_title'] = D('RankUser')->where(array('status' => 1, 'uid' => $uid))->select();
        foreach($user_title['have_title'] as &$v){
            $v['title'] = D('Rank')->where(array( 'id' => $v['rank_id']))->find();
            $v['logo']= getThumbImageById($v['title']['logo']);
        }
        //获得待审核头衔
        $user_title['ready_title'] = D('RankUser')->where(array('status' => 0, 'uid' => $uid))->select();
        foreach($user_title['ready_title'] as &$v){
            $v['title'] = D('Rank')->where(array( 'id' => $v['rank_id']))->find();
            $v['logo']= getThumbImageById($v['title']['logo']);
        }
        //获得审核失败头衔
        $user_title['defeat_title'] = D('RankUser')->where(array('status' => -1, 'uid' => $uid))->select();
        foreach($user_title['defeat_title'] as &$v){
            $v['title'] = D('Rank')->where(array( 'id' => $v['rank_id']))->find();
            $v['logo']= getThumbImageById($v['title']['logo']);
        }
        //申请头衔
        $user_title['apply_title'] = D('RankUser')->where(array( 'uid' => $uid))->select();
        $user_title['apply_title']=array_column($user_title['apply_title'],'rank_id');
        if(empty( $user_title['apply_title'])){
            $user_title['apply_title']=D('Rank')->where(array( 'status' => 1))->select();
        }else{
            $user_title['apply_title'] = D('Rank')->where(array( 'status' => 1,'id'=>array('not in',$user_title['apply_title'])))->select();
        }

        foreach($user_title['apply_title'] as &$v){
          //  $v['title'] = D('Rank')->where(array( 'id' => $v['rank_id']))->find();
            $v['logo']= getThumbImageById($v['logo']);
        }

     //  dump($user_title);exit;
        return $user_title;
    }

    /**
     * 取消申请头衔
     */
    public function rankVerifyCancel()
    {

        $aRand_id=$rank_id= I('post.rank_id', null, 'intval');
        $rank_id = intval($rank_id);
        if (is_login() && $rank_id) {
            $map['rank_id'] = $rank_id;
            $map['uid'] = is_login();
            $map['status'] = 0;
            $result = D('rank_user')->where($map)->delete();
            if ($result) {
                D('Message')->sendMessageWithoutCheckSelf(is_login(),'取消头衔申请',  '头衔申请取消成功', 'Ucenter/Message/message', array('tab' => 'system'));
                $data['status']=1;
                $data['info']='取消成功';

            } else {
                $data['status']=0;
                $data['info']='取消失败';
            }
        }
        $this->ajaxReturn($data);
    }

    /**
     * 申请头衔或重新申请头衔
     */
    public function verify()
    {
        $aRand_id=$rank_id= I('post.rank_id', null, 'intval');
        $aRreason=$reason= I('post.reason', null, 'op_t');
        $aRank_user_id=$rank_user_id= I('post.rank_user_id', null, 'intval');
        $rank_id = intval($rank_id);
        $reason = op_t($reason);
        $rank_user_id = intval($rank_user_id);
        if (!$rank_id) {
            $this->error('请选择要申请的头衔');
        }
        if ($reason == null || $reason == '') {
            $this->error('请填写申请理由');
        }
        $data['rank_id'] = $rank_id;
        $data['reason'] = $reason;
        $data['uid'] = is_login();
        $data['is_show'] = 1;
        $data['create_time'] = time();
        $data['status'] = 0;
        if ($rank_user_id) {
            $model = D('rank_user')->where(array('id' => $rank_user_id));
            if (!$model->select()) {
                $this->error('请正确选择要重新申请的头衔');
            }
            $result = D('rank_user')->where(array('id' => $rank_user_id))->save($data);
        } else {
            $result = D('rank_user')->add($data);
        }
        if ($result) {
            D('Message')->sendMessageWithoutCheckSelf(is_login(),'头衔申请', '头衔申请成功,等待管理员审核',  'Ucenter/Message/message', array('tab' => 'system'));
            $data['status']=1;
            $data['info']='头衔申请成功,等待管理员审核';
        } else {
            $data['status']=0;
            $data['info']='申请失败';
        }
        $this->ajaxReturn($data);
    }

    /**
     * @param $uid
     * @return mixed
     * 用户资料
     */
    private function _userData($uid){
        $userdata=D('Member')->where(array('uid'=>$uid))->find();
        $userdata['user']=query_user(array('nickname','email', 'avatar64','signature','space_mob_url'), $uid);
        if ($userdata['pos_province'] != 0) {
            $userdata['pos_province'] = D('district')->where(array('id' => $userdata['pos_province']))->getField('name');
            $userdata['pos_city'] = D('district')->where(array('id' => $userdata['pos_city']))->getField('name');
            $userdata['pos_district'] = D('district')->where(array('id' => $userdata['pos_district']))->getField('name');
            $userdata['pos_community'] = D('district')->where(array('id' => $userdata['pos_community']))->getField('name');
        }
      // dump($userdata);exit;
        return $userdata;
    }

    private function _myFocus($uid){
        $following['follow'] = D('Follow')->getFollowing($uid, $page=1,  array('avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title','space_mob_url'));
        $following['fans'] = D('Follow')->getFans($uid, $page=1, array('avatar128', 'uid', 'nickname', 'fans', 'following', 'weibocount', 'space_url', 'title','space_mob_url'));
    //    dump($following);exit;
        return   $following;
    }

    /**扩展信息分组列表获取
     * @param null $uid
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _profile_group_list($uid = null)
    {
        $profile_group_list=array();
        $fields_list=$this->getRoleFieldIds($uid);
        if($fields_list){
            $fields_group_ids=D('FieldSetting')->where(array('id'=>array('in',$fields_list),'status' => '1'))->field('profile_group_id')->select();
            if($fields_group_ids){
                $fields_group_ids=array_unique(array_column($fields_group_ids,'profile_group_id'));
                $map['id']=array('in',$fields_group_ids);

                if (isset($uid) && $uid != is_login()) {
                    $map['visiable'] = 1;
                }
                $map['status'] = 1;
                $profile_group_list = D('field_group')->where($map)->order('sort asc')->select();
            }
        }
        return $profile_group_list;
    }

    private function getRoleFieldIds($uid=null){
        $role_id=get_role_id($uid);
        $fields_list=S('Role_Expend_Info_'.$role_id);
        if(!$fields_list){
            $map_role_config=getRoleConfigMap('expend_field',$role_id);
            $fields_list=D('RoleConfig')->where($map_role_config)->getField('value');
            if($fields_list){
                $fields_list=explode(',',$fields_list);
                S('Role_Expend_Info_'.$role_id,$fields_list,600);
            }
        }
        return $fields_list;
    }

    public function _info_list($id = null, $uid = null)
    {
        $fields_list=$this->getRoleFieldIds($uid);
        $info_list = null;

        if (isset($uid) && $uid != is_login()) {
            //查看别人的扩展信息
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'visiable' => '1','id'=>array('in',$fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = $uid;
        } else if (is_login()) {
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1','id'=>array('in',$fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = is_login();

        } else {
            $this->error('请先登录！');
        }
        foreach ($field_setting_list as &$val) {
            $map['field_id'] = $val['id'];
            $field = D('field')->where($map)->find();
            $val['field_content'] = $field;
            unset($map['field_id']);
            $info_list[$val['id']] = $this->_get_field_data($val);
            //当用户扩展资料为数组方式的处理@MingYangliu
            $vlaa = explode('|', $val['form_default_value']);
            $needle =':';//判断是否包含a这个字符
            $tmparray = explode($needle,$vlaa[0]);
            if(count($tmparray)>1){
                foreach ($vlaa as $kye=>$vlaas){
                    if(count($tmparray)>1){
                        $vlab[] = explode(':', $vlaas);
                        foreach ($vlab as $key=>$vlass){
                            $items[$vlass[0]] = $vlass[1];
                        }
                    }
                    continue;
                }
                $info_list[$val['id']]['field_data'] = $items[$info_list[$val['id']]['field_data']];
            }
            //当扩展资料为join时，读取数据并进行处理再显示到前端@MingYang
            if($val['child_form_type'] == "join"){
                $j = explode('|',$val['form_default_value']);
                $a = explode(' ',$info_list[$val['id']]['field_data']);
                $info_list[$val['id']]['field_data'] = get_userdata_join($a,$j[0],$j[1]);
            }
        }
        return $info_list;
    }
    public function _get_field_data($data = null)
    {
        $result = null;
        $result['field_name'] = $data['field_name'];
        $result['field_data'] = "还未设置";
        switch ($data['form_type']) {
            case 'input':
            case 'radio':
            case 'textarea':
            case 'select':
                $result['field_data'] = isset($data['field_content']['field_data']) ? $data['field_content']['field_data'] : "还未设置";
                break;
            case 'checkbox':
                $result['field_data'] = isset($data['field_content']['field_data']) ? implode(' ', explode('|', $data['field_content']['field_data'])) : "还未设置";
                break;
            case 'time':
                $result['field_data'] = isset($data['field_content']['field_data']) ? date("Y-m-d", $data['field_content']['field_data']) : "还未设置";
                break;
        }
        $result['field_data'] = op_t($result['field_data']);
        return $result;
    }
}