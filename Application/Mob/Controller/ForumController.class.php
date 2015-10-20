<?php


namespace Mob\Controller;

use Think\Controller;
use Common\Model\ContentHandlerModel;

define('TOP_ALL', 2);
define('TOP_FORUM', 1);

class ForumController extends BaseController
{
    public function _initialize()
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'home','href'=>U('Mob/Forum/index')),
                array('type'=>'message'),
            ),
            'center'=>array('title'=>'论坛')
        );

        //dump($this->_top_menu_list);exit;
        $this->setMobTitle('论坛');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }



    public function index()
    {
        $order=I('order','reply','text');
        $this->assign('order',$order);
        //取到帖子排序
        if ($order == 'ctime') {
            $order = 'create_time desc';
        } else if ($order == 'reply') {
            $order = 'last_reply_time desc';
        } else {
            $order = 'last_reply_time desc';//默认的
        }
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');

        $forumPostModel=D('ForumPost');

        $list_top = $forumPostModel->where(' status=1 AND is_top=' . TOP_ALL)->order($order)->select();

        //读取帖子列表
        foreach ($list_top as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32'), $v['uid']);
            $v['plate'] = D('Forum')->where(array('status' => 1,'id'=>$v['forum_id']))->find();
        }
        unset($v);
        $this->assign('list_top', $list_top);


        $forum = D('ForumPost')->where(array('status' => 1))->page($aPage, $aCount)->order($order)->select();
        foreach($forum as &$v){
            $v['user']=query_user(array('nickname', 'avatar32'), $v['uid']);

            $v['plate'] = D('Forum')->where(array('status' => 1,'id'=>$v['forum_id']))->find();

        }
        $totalCount =  D('ForumPost')->where(array('status' => 1))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }

        $this->assign('pid',$pid);
        $this->assign('forum',$forum);
        $this->display();
    }


    public function addMoreForum(){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $order=I('order','reply','text');
        $this->assign('order',$order);
        //取到帖子排序
        if ($order == 'ctime') {
            $order = 'create_time desc';
        } else if ($order == 'reply') {
            $order = 'last_reply_time desc';
        } else {
            $order = 'last_reply_time desc';//默认的
        }


        $forum = D('ForumPost')->where(array('status' => 1,))->page($aPage, $aCount)->order($order)->select();
        foreach($forum as &$v){
            $v['user']=query_user(array('nickname', 'avatar32'), $v['uid']);
            $v['plate'] = D('Forum')->where(array('status' => 1,'id'=>$v['forum_id']))->find();

        }
        if ($forum) {
            $data['html'] = "";
            foreach ($forum as $val) {
                $this->assign("vo", $val);
                $data['html'] .= $this->fetch("_forumlist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);

    }
    /**
     * 版块内容渲染
     */
    public function forumtype(){
        $forum_top = D('ForumType')->where(array('status' => 1))->select();        //查找顶级分类pid=0的

        //  dump($issue_top);exit;
        foreach ($forum_top as &$v) {
            $v['lever_two'] = D('Forum')->where(array('status' => 1, 'type_id' => $v['id']))->select();        //查找二级分类pid=$issue_top的id
            $v['count'] = count($v['lever_two']);                //二级分类数量
            foreach ($v['lever_two'] as &$k) {
                $k['count_content'] = D('ForumPost')->where(array('status' => 1, 'forum_id' => $k['id']))->count();
            }
        }

       //  dump($forum_top);exit;
        $this->assign("forum_top", $forum_top);         //顶级分类
        $this->display();
    }

    public function addComment($id=0,$is_edit=0,$reply_id=0){
        if($is_edit==1){
            $reply_id = intval($reply_id);
            $this->checkAuth('Forum/Index/doReplyEdit',$this->get_expect_ids(0,$reply_id,0,0,1),'你没有编辑该评论权限！');
            if ($reply_id) {
                $reply = D('forum_post_reply')->where(array('id' => $reply_id, 'status' => 1))->find();
                $reply['is_edit']=1;
            } else {
                $this->error('参数出错！');
            }
            $this->setMobTitle('编辑评论 —— 论坛');
            //显示页面
            $this->assign("forum", $reply['id']);
            $this->assign("is_edit", 1);
            $this->assign("editreply", $reply);
            $this->display();
        }else{
            $this->assign("forum", $id);
            $this->display();
        }
    }

    public function atComment($uid=0,$username=null,$id=0,$post_id=0,$to_f_reply_id=0){

            $this->assign("uid", $uid);             //to_uid
            $this->assign("atusername", $username);
            $this->assign("id", $id);               //楼层id
            $this->assign("post_id", $post_id);        //帖子ID
            $this->assign("to_f_reply_id", $to_f_reply_id);
            $this->display();
    }

    public function doSendLZLReply($p=1)
    {

        $post_id=I('post.post_id', 0, 'op_t');
        $to_f_reply_id=I('post.to_f_reply_id', 0, 'op_t');
        $to_reply_id=I('post.to_reply_id', 0, 'op_t');
        $to_uid=I('post.uid', 0, 'op_t');
        $content=I('post.content', '', 'op_t');


        //确认用户已经登录
        $this->requireLogin();
        $this->checkAuth('Forum/Lzl/doSendLZLReply',$this->get_expect_ids(0,0,$post_id,0),'你没有回复评论的权限！');
        $this->checkActionLimit('forum_lzl_reply','Forum',null,get_uid());
        //写入数据库
        $model = D('ForumLzlReply');
        $before=getMyScore();
        $result = $model->addLZLReply($post_id, $to_f_reply_id, $to_reply_id, $to_uid, op_t($content),$p);
        $after=getMyScore();
        if (!$result) {
            $this->error('发布失败：' . $model->getError());
        }
        action_log('forum_lzl_reply','Forum',$result,get_uid());
        //显示成功页面
        $totalCount = D('forum_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $to_f_reply_id)->count();
        $limit = 5;
        $pageCount = ceil($totalCount / $limit);
        exit(json_encode(array('status'=>1,'info'=>'回复成功。'.getScoreTip($before,$after),'url'=>$pageCount)));
    }

    public function delLZLReply(){
        $id=I('post.lzlreplyId', 0, 'op_t');
        $this->requireLogin();
        $this->checkAuth('Forum/Lzl/delLZLReply',$this->get_expect_ids($id),'你没有删除回复的权限！');
        $this->checkActionLimit('forum_lzl_del_reply','Forum',null,get_uid());
        $Lzlreply=D('ForumLzlReply')->where('id='.$id)->find();
        $data['post_reply_id']=$Lzlreply['to_f_reply_id'];
        $res= D('ForumLzlReply')->delLZLReply($id);
        $data['lzl_reply_count']=D('ForumLzlReply')->where('is_del=0 and to_f_reply_id='.$data['post_reply_id'])->count();
        action_log('forum_lzl_del_reply','Forum',$id,get_uid());
        $res &&   $this->success($res,'',$data);
        !$res &&   $this->error('');
    }
    /**
     * 获取要排除的uids(版主、自己)
     * @param int $lzl_reply_id
     * @param int $reply_id
     * @param int $post_id
     * @param int $forum_id
     * @param int $with_self 是否包含记录的uid
     * @return array|int|mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function  get_expect_ids($lzl_reply_id=0,$reply_id=0,$post_id=0,$forum_id=0,$with_self=1)
    {
        $uid=0;
        if($lzl_reply_id){
            $lzl_reply=D('ForumLzlReply')->find($lzl_reply_id);
            $uid=$lzl_reply['uid'];
            $post_id=$lzl_reply['post_id'];
        }
        if(!$uid){
            if($reply_id){
                $reply = D('ForumPostReply')->find(intval($reply_id));
                $uid=$reply['uid'];
                $post_id=$reply['post_id'];
            }
        }
        if($post_id){
            $post=D('ForumPost')->where(array('id' => $post_id, 'status' => 1))->find();
            $forum_id=$post['forum_id'];
            if(!$uid){
                $uid=$post['uid'];
            }
        }
        $forum=D('Forum')->find($forum_id);
        if(mb_strlen($forum['admin'],'utf-8')){
            $expect_ids=str_replace('[','',$forum['admin']);
            $expect_ids=str_replace(']','',$expect_ids);
            $expect_ids=explode(',',$expect_ids);
            if($uid&&$with_self){
                if(!in_array($uid,$expect_ids)){
                    $expect_ids=array_merge($expect_ids,array($uid));
                }
            }
        }else{
            if($with_self&&$uid){
                $expect_ids=$uid;
            }else{
                $expect_ids=-1;
            }
        }
        return $expect_ids;
    }

    /**
     * @param $id
     * 版块点击进入该分类
     */
    public function postSectionDetail($id){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $order=I('order','reply','text');
        $this->assign('order',$order);
        //取到帖子排序
        if ($order == 'ctime') {
            $order = 'create_time desc';
        } else if ($order == 'reply') {
            $order = 'last_reply_time desc';
        } else {
            $order = 'last_reply_time desc';//默认的
        }
        //模型初始化
        $forumPostModel = D('ForumPost');

        //读取置顶列表
        $list_top = $forumPostModel->where('status=1 AND (is_top=' . TOP_ALL . ') OR (is_top=' . TOP_FORUM . ' AND forum_id=' . intval($id) . ' and status=1)')->order($order)->select();


        //读取帖子列表
        foreach ($list_top as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32'), $v['uid']);
            $v['plate'] = D('Forum')->where(array('status' => 1,'id'=>$v['forum_id']))->find();
        }
        unset($v);
        $this->assign('list_top', $list_top);

        $forum= D('ForumPost')->where(array('status' => 1, 'forum_id' => $id))->page($aPage, $aCount)->order($order)->select();

        foreach ($forum as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32'), $v['uid']);
            $v['plate'] = D('Forum')->where(array('status' => 1,'id'=>$v['forum_id']))->find();
        }
        $title= D('Forum')->where(array('status' => 1, 'id' => $id))->find();
        $title['pid']=1;                    //设置一个标记，执行过这个function的增加这个标记，页面进行判断。
        $totalCount =  D('ForumPost')->where(array('status' => 1, 'forum_id' => $id))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        $this->assign('forum_id',$id);
        $this->assign('pid',$pid);
        $this->assign('forum',$forum);
        $this->assign("title", $title);
        $this->display(T('Application://Mob@Forum/index'));
    }


    /**
     * @param $id
     * 版块点击进入该分类--查看更多
     */
    public function addMorePostSectionDetail(){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $aId = I('post.id', '', 'op_t');
        $order=I('order','reply','text');
        $this->assign('order',$order);
        //取到帖子排序
        if ($order == 'ctime') {
            $order = 'create_time desc';
        } else if ($order == 'reply') {
            $order = 'last_reply_time desc';
        } else {
            $order = 'last_reply_time desc';//默认的
        }
        $forum= D('ForumPost')->where(array('status' => 1, 'forum_id' => $aId))->page($aPage, $aCount)->order($order)->select();

        foreach ($forum as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32'), $v['uid']);
            $v['plate'] = D('Forum')->where(array('status' => 1,'id'=>$v['forum_id']))->find();
        }
        if ($forum) {
            $data['html'] = "";
            foreach ($forum as $val) {
                $this->assign("vo", $val);
                $data['html'] .= $this->fetch("_forumlist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }
    /**
     * @param $id
     * 帖子详情
     */
    public function postDetail($id){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $aLzlPage = I('post.lzlpage', 1, 'op_t');
        $aLzlCount = I('post.lzlcount', 3, 'op_t');
        $map['id'] = array('eq', $id);
        $map['status']=1;
        $forum_detail = D('ForumPost')->where($map)->find();
        if(!$forum_detail){
            $this->error('贴子不存在或已被删除！');
        }
       // $mapl = array('post_id' => $id, 'status' => 1);
    //    $replyList = D('ForumPostReply')->getReplyList($mapl, 'create_time', $aPage, $aCount);

        D('ForumPost')->where(array('id' => $id))->setInc('view_count');//查看数加1
        $is_add=D('ForumBookmark')->where(array('post_id' => $id,'uid'=>is_login()))->find();       //是否收藏
        if(is_null($is_add)){
            $is_add=0;
        }else{
            $is_add=1;
        }

        $forum_detail['user']=query_user(array('nickname', 'avatar128'), $forum_detail['uid']);
        $forum_detail['content']=$content = parse_expression($forum_detail['content']);
        $forum_detail['support']=D('Support')->where(array('appname'=>'Forum','row'=>$forum_detail['id']))->count();
        $forum_detail['has_support']= D('Support')->where(array('appname'=>'Forum','row'=>$forum_detail['id'],'uid'=>is_login()))->select();              //判断是否已经点赞
        if( $forum_detail['has_support']){
            $forum_detail['has_support']=1;
        }else{
            $forum_detail['has_support']=0;
        }

        $post_detail= D('ForumPostReply')->where(array('status'=>'1','post_id' => $id))->page($aPage, $aCount)->select();
        foreach($post_detail as &$v){
            $v['user']=query_user(array('nickname', 'avatar32'), $v['uid']);
            $v['content']=parse_expression($v['content']);
            if($forum_detail['uid']==$v['uid']){
                $v['floormaster']="楼主";
            }
            //楼中楼内容
            $v['lzllist']= $list = D('Mob/ForumLzlReply')->getLZLReplyList($v['id'],'ctime asc',$aLzlPage,$aLzlCount);
//dump($v['lzllist']);exit;

            $v['lzltotalCount']= $totalCountLzl = D('forum_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $v['id'])->count();
            if($totalCountLzl<=$aLzlPage*$aLzlCount){
                $v['lzlcount'] = 0;
            }else{
                $v['lzlcount'] = 1;
            }
            $data['to_f_reply_id'] = $v['id'];
            $pageCount = ceil($totalCountLzl / $aLzlCount);
            $v['lzlhtml']= $html = getPageHtml('changePage', $pageCount, $data, $aLzlPage);
            //楼中楼内容结束
        }
        $totalCount = D('ForumPostReply')->where(array('status'=>'1','post_id' => $id))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }

        $this->assign("pid", $pid);                     //判断是否需要查看更多
        $this->assign("forum", $forum_detail);      //帖子内容
        $this->assign("postcomment", $post_detail);     //帖子评论
        $this->assign("isadd", $is_add);                //是否已收藏
        $this->display();
    }


    public function addMoreForumComment(){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $aLzlPage = I('post.lzlpage', 1, 'op_t');
        $aLzlCount = I('post.lzlcount', 3, 'op_t');
        $aId=I('post.id', '', 'op_t');
        $map['id'] = array('eq', $aId);


        $forum_detail = D('ForumPost')->where($map)->find();
        $forum_detail['user']=query_user(array('nickname', 'avatar128'), $forum_detail['uid']);
        $post_detail= D('ForumPostReply')->where(array('status'=>'1','post_id' => $aId))->page($aPage, $aCount)->select();
        foreach($post_detail as &$v){
            $v['user']=query_user(array('nickname', 'avatar32'), $v['uid']);
            $v['content']=parse_expression($v['content']);
            if($forum_detail['uid']==$v['uid']){
                $v['floormaster']="楼主";
            }
            //楼中楼内容
         //   dump($v['id']);exit;
            $v['lzllist']= $list = D('Mob/ForumLzlReply')->getLZLReplyList($v['id'],'ctime asc',$aLzlPage,$aLzlCount);
            $v['lzltotalCount']= $totalCount = D('forum_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $v['id'])->count();
            $data['to_f_reply_id'] = $v['id'];
            $pageCount = ceil($totalCount / $aLzlCount);
            $v['lzlhtml']= $html = getPageHtml('changePage', $pageCount, $data, $aLzlPage);
            //楼中楼内容结束
        }
            if ($post_detail) {
                $data['html'] = "";
                foreach ($post_detail as $key=>$val ) {
                    $this->assign("vl", $val);
                    $this->assign("k", ($aPage-1)*$aCount+$key+1);

                    $data['html'] .= $this->fetch("_forumcomment");
                    $data['status'] = 1;
                }
            } else {
                $data['stutus'] = 0;
            }
        $this->ajaxReturn($data);
    }

    public function addMoreLzlreply(){
        $aLzlPage = I('post.lzlpage', 0, 'op_t');
        $aLzlCount = I('post.lzlcount', 3, 'op_t');
        $aId=I('post.id', '', 'op_t');
        $map['id'] = array('eq', $aId);


        $forum_detail = D('ForumPost')->where($map)->find();
        $forum_detail['user']=query_user(array('nickname', 'avatar128'), $forum_detail['uid']);
            //楼中楼内容
            //   dump($v['id']);exit;
            $post_detail['lzllist']= $list = D('Mob/ForumLzlReply')->getLZLReplyList($aId,'ctime asc',$aLzlPage,$aLzlCount);
            $post_detail['lzltotalCount']= $totalCount = D('forum_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $post_detail['id'])->count();
            $data['to_f_reply_id'] = $post_detail['id'];
            $pageCount = ceil($totalCount / $aLzlCount);
            $post_detail['lzlhtml']= $html = getPageHtml('changePage', $pageCount, $data, $aLzlPage);
            //楼中楼内容结束

            if ($post_detail) {
                $data['html'] = "";
                $this->assign("vl", $post_detail);
                $this->assign("vl", $post_detail);
                $data['html'] .= $this->fetch("_lzlreply");
                if($data['html']){
                    $data['status'] = 1;
                }else{
                    $data['stutus'] = 0;
                }
            } else {
                $data['stutus'] = 0;
            }
        $this->ajaxReturn($data);
    }

public function support($id=0, $uid=0){
    //$id是发帖人的微博ID
    //$uid是发帖人的ID
    if (!is_login()) {
        $this->error('请登陆后再进行操作');
    }
    $row = $id;
    $message_uid = $uid;
    $support['appname'] = 'Forum';
    $support['table'] = 'post';
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

            D('Message')->sendMessage($message_uid, $user['username'] . '给您点了个赞。', $title = $user['username'] . '赞了您。', is_login());

            $return['status'] = '1';
        } else {
            $return['status'] = ' 0';
            $return['info'] = '亲，您已经支持过我了！';
        }


    }
    $this->ajaxReturn($return);
}

    public function delPost($id)
    {
        $id = intval($id);
        $post=D('ForumPost')->where(array('id' => $id, 'status' => 1))->find();
        $forum_id=$post['forum_id'];

        $this->checkAuth('Forum/Index/delPost',$this->get_expect_ids(0,0,0,$forum_id,0),'你没有删除评论权限！');
        $this->checkActionLimit('forum_del_post','Forum',null,get_uid());
        $res = M('ForumPost')->where(array('id'=>$id))->setField('status',-1);
        if($res){
            $return['status']=1;
        }else {
            $return['status'] = 0;
            $return['info']="删除失败";
        }
        $this->ajaxReturn($return);
    }

    private function clearCache($support)
    {
        unset($support['uid']);
        unset($support['create_time']);
        $cache_key = "support_count_" . implode('_', $support);
        S($cache_key, null);
    }
    /**
     * 发的帖子内容渲染
     * 判断是否是编辑请求
     * （标题内容渲染）
     */
    public function addPost($isedit=0,$postid=0){
    if($isedit==0){
      $this->setTopTitle('发帖子');
        $forum_list = D('Forum/Forum')->getForumList();
        //判断板块能否发帖
        foreach ($forum_list as &$e) {
            $e['allow_publish'] = $this->isForumAllowPublish($e['id']);
        }
        unset($e);
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url', 'icons_html'), is_login());
    }else{
       $this->setTopTitle('编辑帖子');
        $forum_list = D('Forum/Forum')->getForumList();
        //判断板块能否发帖
        foreach ($forum_list as &$e) {
            $e['allow_publish'] = $this->isForumAllowPublish($e['id']);
        }
        unset($e);
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url', 'icons_html'), is_login());
        $map['id'] = array('eq', $postid);
        $forum_detail = D('ForumPost')->where($map)->find();        $forum_detail['user']=query_user(array('nickname', 'avatar128'), $forum_detail['uid']);
        $forum_detail['content']=$content = parse_expression($forum_detail['content']);
        $forum_detail['is_edit']=1;
        $this->assign('forum_detail', $forum_detail);
    }

        $this->assign('myInfo', $myInfo);
        //赋予论坛列表
        $this->assign('forum_list', $forum_list);
        $types = D('Forum/Forum')->getAllForumsSortByTypes();
        $this->assign('types', $types);


        $this->display();
    }

    /**
     * @param null $reply_id
     * @param $content
     * 编辑回复
     */
    public function doReplyEdit($reply_id = null, $content)
    {
        $reply_id = intval($reply_id);
        //对帖子内容进行安全过滤
        $content = $this->filterPostContent($content);

        $content = filter_content($content);


        $this->checkAuth('Forum/Index/doReplyEdit',get_expect_ids(0,$reply_id,0,0,1),'你没有编辑该评论权限！');

        if (!$content) {
            $this->error("评论内容不能为空！");
        }
        $data['content'] = $content;
        $data['update_time'] = time();
        $post_id = D('forum_post_reply')->where(array('id' => intval($reply_id), 'status' => 1))->getField('post_id');
        $reply = D('forum_post_reply')->where(array('id' => intval($reply_id)))->save($data);
        if ($reply) {
            S('post_replylist_' . $post_id, null);
            $this->success('编辑评论成功', U('Forum/Index/detail', array('id' => $post_id)));
        } else {
            $this->error("编辑评论失败");
        }
    }



    private function isForumAllowPublish($forum_id)
    {
        if (!$this->isLogin()) {
            return false;
        }
        if (!$this->isForumExists($forum_id)) {
            return false;
        }
        if (!$this->isForumAllowCurrentUserGroup($forum_id)) {
            return false;
        }
        return true;
    }
    private function isLogin()
    {
        return is_login() ? true : false;
    }
    private function isForumExists($forum_id)
    {
        $forum_id = intval($forum_id);
        $forum = D('Forum')->where(array('id' => $forum_id, 'status' => 1));
        return $forum ? true : false;
    }
    private function isForumAllowCurrentUserGroup($forum_id)
    {
        $forum_id = intval($forum_id);
        //如果是超级管理员，直接允许
        if (is_login() == 1) {
            return true;
        }

        //如果帖子不属于任何板块，则允许发帖
        if (intval($forum_id) == 0) {
            return true;
        }

        //读取论坛的基本信息
        $forum = D('Forum')->where(array('id' => $forum_id))->find();
        $userGroups = explode(',', $forum['allow_user_group']);

        //读取用户所在的用户组
        $list = M('AuthGroupAccess')->where(array('uid' => is_login()))->select();
        foreach ($list as &$e) {
            $e = $e['group_id'];
        }


        //判断用户组是否有权限
        $list = array_intersect($list, $userGroups);
        return $list ? true : false;
    }


    /**
     * 发帖子功能实现
     */
    public function doAddPost($post_id = null, $forum_id = 0, $title, $content,$attach_ids,$isEdit=0){

        $post_id = intval($post_id);
        $forum_id = intval($forum_id);
        $title = text($title);
        $aSendWeibo = I('sendWeibo', 0, 'intval');
        if($attach_ids){
            $img_ids = explode(',',  $attach_ids);              //把图片和内容结合
            //    dump($img_ids);
            foreach($img_ids as &$v){
                $v = M('Picture')->where(array('status' => 1))->getById($v);
                if(!is_bool(strpos( $v['path'],'http://'))){
                    $v = $v['path'];
                }else{
                    $v =getRootUrl(). substr( $v['path'],1);
                }
                $v='<p><img src="'. $v.'" style=""/></p><br>';
            };
            $img_ids = implode('', $img_ids);
            //  dump($img_ids);
            $content=$img_ids.$content;
            $contentHandler=new ContentHandlerModel();
            $content=$contentHandler->filterHtmlContent($content);    //把图片和内容结合END
        }


       // $content = $content;//op_h($content);


        //判断是不是编辑模式

        $forum_id = intval($forum_id);
        //如果是编辑模式，确认当前用户能编辑帖子
        if ($isEdit==1) {
            $this->requireAllowEditPost($post_id);
        }

        //确认当前论坛能发帖
        $this->requireForumAllowPublish($forum_id);


        if ($title == '') {
            $this->error('请输入标题。');
        }
        if ($forum_id == 0) {
            $this->error('请选择发布的版块。');
        }
        if (strlen($content) < 20) {
            $this->error('发表失败：内容长度不能小于20');
        }


        //   $content = filterBase64($content);
        //检测图片src是否为图片并进行过滤
        //  $content = filterImage($content);

        //写入帖子的内容
        $model = D('Mob/ForumPost');
        if ($isEdit==1) {
            $data = array('id' => intval($post_id), 'title' => $title, 'content' => $content, 'parse' => 0, 'forum_id' => intval($forum_id));
            $result = $model->editPost($data);
            if (!$result) {
                $this->error('编辑失败：' . $model->getError());
            }
        } else {
            $data = array('uid' => is_login(), 'title' => $title, 'content' => $content, 'parse' => 0, 'forum_id' => $forum_id);

            $before = getMyScore();
            $result = $model->createPost($data);
            $after = getMyScore();
            if (!$result) {
                $this->error('发表失败：' . $model->getError());
            }
            $post_id = $result;
        }


        //实现发布帖子发布图片微博(公共内容)
        $type = 'feed';
        $feed_data['attach_ids']=$attach_ids;
        $feed_data['attach_ids'] != false && $type = "image";

        if ($aSendWeibo==1) {
            //开始发布微博
            if ($isEdit) {
                D('Weibo')->addWeibo(is_login(), "我更新了帖子【" . $title . "】：" . U('postdetail', array('id' => $post_id), null, true), $type, $feed_data,$from="手机网页版");
            } else {
                D('Weibo')->addWeibo(is_login(), "我发表了一个新的帖子【" . $title . "】：" . U('postdetail', array('id' => $post_id), null, true), $type, $feed_data,$from="手机网页版");
            }
        }

        //显示成功消息
        if ($result) {
            $return['status'] = 1;
        } else {
            $return['status'] = 0;
            $return['info'] = '发贴失败';
        }
        $this->ajaxReturn($return);

    }

    public function delPostReply()
    {
        $id = I('post.replyId', '', 'op_t');
        $id = intval($id);
        $this->requireLogin();
        $this->checkAuth('Forum/Index/delPostReply',$this->get_expect_ids(0,$id,0,0,1),'你没有删除评论权限！');
        $res = D('Mob/ForumPostReply')->delPostReply($id);
        if($res==1){
            $return['status'] = 1;
        }else{
            $return['status'] = 0;
            $return['info'] = '删除失败';
        }
        $this->ajaxReturn($return);
    }


    private function requireAllowEditPost($post_id)
    {
        $this->requirePostExists($post_id);
        $this->requireLogin();

        if (is_administrator()) {
            return true;
        }
        //确认帖子时自己的
        $post = D('ForumPost')->where(array('id' => $post_id, 'status' => 1))->find();
        if ($post['uid'] != is_login()) {
            $this->error('没有权限编辑帖子');
        }
    }
    private function requireForumAllowPublish($forum_id)
    {
        $this->requireForumExists($forum_id);
        $this->requireLogin();
        $this->requireForumAllowCurrentUserGroup($forum_id);
    }
    private function requireForumExists($forum_id)
    {
        if (!$this->isForumExists($forum_id)) {
            $this->error('论坛不存在');
        }
    }
    private function requireLogin()
    {
        if (!$this->isLogin()) {
            $this->error('需要登录才能操作');
        }
    }
    private function requireForumAllowCurrentUserGroup($forum_id)
    {
        $forum_id = intval($forum_id);
        if (!$this->isForumAllowCurrentUserGroup($forum_id)) {
            $this->error('该板块不允许发帖');
        }
    }


    /**
     * 帖子回复
     */
    public function AddForumComment($is_edit=0){
        $attach_ids=I('post.attach_ids','','text');
        if($attach_ids){
            $aContent = I('post.forumcontent', 0, 'op_t');
            $img_ids = explode(',',  $attach_ids);              //把图片和内容结合
            //    dump($img_ids);
            foreach($img_ids as &$v){
                $v = M('Picture')->where(array('status' => 1))->getById($v);
                if(!is_bool(strpos( $v['path'],'http://'))){
                    $v = $v['path'];
                }else{
                    $v =getRootUrl(). substr( $v['path'],1);
                }
                $v='<p><img src="'. $v.'" style=""/></p><br>';
            };
            $img_ids = implode('', $img_ids);
            //  dump($img_ids);
            $aContent=$img_ids.$aContent;
            $contentHandler=new ContentHandlerModel();
            $aContent=$contentHandler->filterHtmlContent($aContent);    //把图片和内容结合END
        }else{
            $aContent = I('post.forumcontent', 0, 'op_t');
        }
            $aPostId = I('post.forumId', 0, 'intval');

            $post_id= $aPostId;
            $content=$aContent;
        if($is_edit==0){
            $content = $this->filterPostContent($content);
            //确认有权限回复
            $this->requireAllowReply($post_id);


            //检测回复时间限制
            $uid = is_login();
            $near = D('ForumPostReply')->where(array('uid' => $uid))->order('create_time desc')->find();


            $cha = time() - $near['create_time'];
            if ($cha > 10) {

                //添加到数据库
                $model = D('Mob/ForumPostReply');
                $result = $model->addReply($post_id, $content);

                if (!$result) {
                    $this->error('回复失败：' . $model->getError());
                }

                //显示成功消息
                $this->success('回复成功。' , 'refresh');
            } else {
                $this->error('请10秒之后再回复');
            }
        }else{
            $reply_id = intval($post_id);
            //对帖子内容进行安全过滤
            $content = $this->filterPostContent($content);

            $content = filter_content($content);


            $this->checkAuth('Forum/Index/doReplyEdit',$this->get_expect_ids(0,$reply_id,0,0,1),'你没有编辑该评论权限！');

            if (!$content) {
                $this->error("评论内容不能为空！");
            }
            $data['content'] = $content;
            $data['update_time'] = time();
            $post_id = D('forum_post_reply')->where(array('id' => intval($reply_id), 'status' => 1))->getField('post_id');
            $reply = D('forum_post_reply')->where(array('id' => intval($reply_id)))->save($data);

            if ($reply) {
                S('post_replylist_' . $post_id, null);
                $this->success('编辑评论成功', U('Forum/Index/detail', array('id' => $post_id)));
            } else {
                $this->error("编辑评论失败");
            }
        }


    }
    /**过滤输出，临时解决方案
     * @param $content
     * @return mixed|string
     * @auth 陈一枭
     */
    private function filterPostContent($content)
    {
        $content = op_h($content);
        $content = $this->limitPictureCount($content);
        $content = op_h($content);
        return $content;
    }
    private function requireAllowReply($post_id)
    {
        $post_id = intval($post_id);
        $this->requirePostExists($post_id);
        $this->requireLogin();
    }
    private function limitPictureCount($content)
    {
        //默认最多显示10张图片
        $maxImageCount = modC('LIMIT_IMAGE', 10);
        //正则表达式配置
        $beginMark = 'BEGIN0000hfuidafoidsjfiadosj';
        $endMark = 'END0000fjidoajfdsiofjdiofjasid';
        $imageRegex = '/<img(.*?)\\>/i';
        $reverseRegex = "/{$beginMark}(.*?){$endMark}/i";

        //如果图片数量不够多，那就不用额外处理了。
        $imageCount = preg_match_all($imageRegex, $content);
        if ($imageCount <= $maxImageCount) {
            return $content;
        }

        //清除伪造图片
        $content = preg_replace($reverseRegex, "<img$1>", $content);

        //临时替换图片来保留前$maxImageCount张图片
        $content = preg_replace($imageRegex, "{$beginMark}$1{$endMark}", $content, $maxImageCount);

        //替换多余的图片
        $content = preg_replace($imageRegex, "[图片]", $content);

        //将替换的东西替换回来
        $content = preg_replace($reverseRegex, "<img$1>", $content);

        //返回结果
        return $content;
    }
    private function requirePostExists($post_id)
    {
        $post_id = intval($post_id);
        $post = D('ForumPost')->where(array('id' => $post_id))->find();
        if (!$post) {
            $this->error('帖子不存在');
        }
    }

    /**
     * 收藏帖子实现
     */
    public function collection(){
        $aPostId = I('post.post_id', 0, 'intval');
        $aAdd=I('post.add', '', 'op_t');

        $add=$aAdd;

        $post_id=$aPostId;
        $add = intval($add);


        //确认用户已经登录
        $this->requireLogin();

        //写入数据库
        if ($add) {
            $result = D('Forum/ForumBookmark')->addBookmark(is_login(), $post_id);
            if (!$result) {
                $this->error('收藏失败');
            }
        } else {
            $result = D('Forum/ForumBookmark')->removeBookmark(is_login(), $post_id);
            if (!$result) {
                $this->error('取消失败');
            }
        }

        //返回成功消息
        if ($add) {
            $return['status'] = 1;
            $return['info'] = '收藏成功！';
        } else {
            $return['status'] = 0;
            $return['info'] = '已取消收藏！';
        }

        $this->ajaxReturn($return);
    }
}