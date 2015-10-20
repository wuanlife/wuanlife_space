<?php


namespace Mob\Controller;

use Common\Model\ContentHandlerModel;
use Think\Controller;

class BlogController extends BaseController{

    public function _initialize()
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'home','href'=>U('Mob/Blog/index')),
                array('type'=>'message'),
            ),
            'center'=>array('title'=>'全站资讯')
        );
        if(is_login()){
            $this->_top_menu_list['right'][]=array('type'=>'edit','href'=>U('Mob/Blog/addBlog'));
        }else{
            $this->_top_menu_list['right'][]=array('type'=>'edit','info'=>'登录后才能操作！');
        }
        $this->setMobTitle('资讯');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }


//渲染资讯
    public function index($mark=0,$title='',$title_id='')
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count',10, 'op_t');
        $map['dead_line']=array('gt',time());

        switch ($mark){
            case '0':                   //全站资讯
                $totalCount=D('News')->where(array('status' => 1,$map))->count();
                $blog= D('News')->where(array('status' => 1,$map))->order('create_time desc')->page($aPage, $aCount)->select();
                $blog_mark['mark']=0;
                break;
            case '1':               //热点资讯
                $this->setTopTitle('热点资讯');
                $totalCount=D('News')->where(array('status' => 1,$map))->count();
                $blog= D('News')->where(array('status' => 1,$map))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
                $blog_mark['mark']=1;
                break;
            case '2':                  //我的投稿
                $this->setTopTitle('我的投稿');
                $totalCount= D('News')->where(array('uid'=>is_login()))->count();
                $blog= D('News')->where(array('uid'=>is_login()))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
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
                //dump($blog);exit;
                $blog_mark['mark']=2;
                break;
            case '3':                       //各级分类资讯
                $this->setTopTitle($title);
                $totalCount= D('News')->where(array('status' => 1, 'category' => $title_id,$map))->count();
                $blog = D('News')->where(array('status' => 1, 'category' => $title_id,$map))->page($aPage, $aCount)->order('create_time desc,view desc')->select();
                $blog_mark['mark']=3;
                $blogtitle['title']=$title;
                $blogtitle['title_id']=$title_id;
        }
        foreach ($blog as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','uid'), $v['uid']);
            if(empty($v['cover'])){
                $v['cover_url']='no_img';
            }else{
                $v['cover_url'] = getThumbImageById($v['cover'],119,89);
            }

            $v['count']=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$v['id']))->order('create_time desc')->count();
        }
        if($totalCount<=$aPage*$aCount){
            $pid['count']=0;
        }else{
            $pid['count']=1;
        }
        $this->assign('hotblog',$blog);
        $this->assign('blogmark',$blog_mark);
        $this->assign('blogtitle',$blogtitle);
        $this->assign('pid',$pid);
        $this->display();
    }
    //加载更多资讯（热点资讯）
    public function addMoreBlog(){
        $aPage = I('post.page', 0, 'op_t');
        $aCount = I('post.count',10, 'op_t');
        $aMark= I('post.mark', 0, 'op_t');
        $aTitleId= I('post.titleid', '', 'op_t');

        $map['dead_line']=array('gt',time());
        switch ($aMark){
            case '0':            //全站资讯
                $blog= D('News')->where(array('status' => 1,$map))->order('create_time desc')->page($aPage, $aCount)->select();
                break;
            case '1':            //热点资讯
                $blog= D('News')->where(array('status' => 1,$map))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
                break;
            case '2':           //我的投稿
                $blog= D('News')->where(array('uid'=>is_login()))->order('create_time desc,view desc')->page($aPage, $aCount)->select();
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
                break;
            case '3':           //各级分类资讯
                $blog = D('News')->where(array('status' => 1, 'category' => $aTitleId,$map))->page($aPage, $aCount)->order('create_time desc,view desc')->select();
        }

        foreach ($blog as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','uid'), $v['uid']);
            $v['cover_url'] = getThumbImageById($v['cover'],119,89);
            $v['count']=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$v['id']))->order('create_time desc')->count();
        }
        if ($blog) {
            $data['html'] = "";
            foreach ($blog as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_bloglist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }



    public function blogDetail($id){
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $this->setTopTitle('资讯详情');
        $blog_detail= D('News')->where(array('id'=>$id))->find();

        $blog_detail['meta']['description']=mb_substr($blog_detail['description'],0,50,'UTF-8');//取得前50个字符
        $blog_detail['meta']['keywords']=D('NewsCategory')->where(array('id'=>$blog_detail['category']))->field('title')->find();
        $blog_detail['user'] = query_user(array('nickname', 'avatar32','uid'), $blog_detail['uid']);

//dump($blog_detail);exit;

      if($blog_detail['cover']==0){
          $blog_detail['cover_url']='no_img';
      }else{
      //获得原图
          $bi = M('Picture')->where(array('status' => 1))->getById($blog_detail['cover']);
          if(!is_bool(strpos( $bi['path'],'http://'))){
              $blog_detail['cover_url'] = $bi['path'];
          }else{
              $blog_detail['cover_url'] =getRootUrl(). substr( $bi['path'],1);
          }
      }

        D('News')->where(array('id' => $id))->setInc('view');//查看数加1

        $blog_content= D('NewsDetail')->where(array('news_id'=>$id))->find();
        $blog_content['content']= parse_expression(($blog_content['content']));

        $blog_comment=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$id))->order('create_time desc')->page($aPage, $aCount)->select();
        $totalCount=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$id))->count();
        if($totalCount<=$aPage*$aCount){
            $pid['count']=0;
        }else{
            $pid['count']=1;
        }
        $blog_detail['count']=count($blog_comment);
        foreach ($blog_comment as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','uid'), $v['uid']);
            $v['cover_url'] = getThumbImageById($v['cover']);
            $v['content']=parse_weibo_mobile_content($v['content']);

        }

        if($blog_detail['status']==2||$blog_detail['status']==-1 ){
            $blog_detail['can_edit']=1;
        }
        $this->setMobTitle($blog_detail['title']);
        $this->setMobDescription( $blog_detail['meta']['description']);
        $this->setMobKeywords($blog_detail['user']['nickname']);
//dump($blog_detail);exit;
        $this->assign('blogdetail',$blog_detail);
        $this->assign('blogcontent',$blog_content);
        $this->assign('blogcomment',$blog_comment);
        $this->assign('pid',$pid);              //标识数据是否还有第二页。
        $this->display();
    }

    public function addMoreBlogComment(){
        $aPage = I('post.page', 0, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $aId = I('post.id', '', 'op_t');
        $blog_comment=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'row_id'=>$aId))->order('create_time desc')->page($aPage, $aCount)->select();
        foreach ($blog_comment as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','uid'), $v['uid']);
            $v['cover_url'] = getThumbImageById($v['cover']);
            $v['content']=parse_weibo_mobile_content($v['content']);
        }
        if ($blog_comment) {
            $data['html'] = "";
            foreach ($blog_comment as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_blogcomment");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);


    }


    public function doAddComment(){
        if (!is_login()) {
            $this->error('请您先进行登录', U('Mob/member/index'), 1);
        }

        $aContent = I('post.content', '', 'op_t');              //获取评论内容
        $aBlogId = I('post.blogId', 0, 'intval');             //获取当前专辑ID
        $aUid = I('post.uid', 0, 'intval');


        $uid = is_login();

        if(empty($aContent)){
            $this->error('评论内容不能为空');
        }
        $result = D('LocalComment')->addBlogComment($uid, $aBlogId, $aContent);
        $title =get_nickname(is_login()). '评论了您';
        D('Common/Message')->sendMessage($aUid,$title, "评论内容：$aContent",  'news/index/detail',array('id' => $aBlogId), is_login(), 1);
        action_log('add_issue_comment', 'local_comment', $result, $uid);

        $blog_comment=D('LocalComment')->where(array('app'=>'News','mod'=>'index','status'=>1,'id'=>$result))->order('create_time desc')->select();
        foreach ($blog_comment as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32','uid'), $v['uid']);
            $v['cover_url'] = getThumbImageById($v['cover']);
        }

        if ($blog_comment) {
            $data['html'] = "";
            foreach ($blog_comment as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_blogcomment");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
            $data['info'] = '评论失败!';
        }
        $this->ajaxReturn($data);
    }

    /**
     * @param $id
     * @param $user
     * 资讯评论模态弹窗内的内容
     */
    public function atComment($id, $user,$uid)
    {
        //$id是发帖人的IDa
        //$user是用户名


        $map['id'] = array('eq', $id);
        $blog = D('News')->where(array('status' => 1, $map))->select();
        $blog[0]['user_uid']=$uid;
        // dump($issue);exit;


        foreach ($blog as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar64','uid'), $v['uid']);
            $v['support'] = D('Support')->where(array('appname' => 'Weibo', 'table' => 'weibo', 'row' => $v['id']))->count();

            $v['at_user_id'] = $user;

        }

       // dump($blog);exit;
//dump($blog[0]);
        $this->assign('blog', $blog[0]);
        $this->display(T('Application://Mob@Blog/atcomment'));

    }


    /**
     * 删除评论
     */
    public function delBlogComment()
    {
        $aCommentId = I('post.commentId', 0, 'intval');              //接收评论ID
        $aBlogId = I('post.blogId', 0, 'intval');                   //接收资讯ID
       // dump($aCommentId);
       // dump($aBlogId);exit;


        $blog_uid = D('News')->where(array('status' => 1, 'id' => $aBlogId))->find();//根据资讯ID查找资讯发送人的UID
        $comment_uid = D('LocalComment')->where(array('status' => 1, 'id' => $aCommentId))->find();//根据评论ID查找评论发送人的UID
        if (!is_login()) {
            $this->error('请登陆后再进行操作');
        }


        if (is_administrator(get_uid()) || $blog_uid['uid'] == get_uid() || $comment_uid['uid'] == get_uid()) {                                     //如果是管理员，则可以删除评论
            $result = D('LocalComment')->deleteBlogComment($aCommentId);
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
     * 资讯分类内容显示
     */
    public function blogType()
    {
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'back','need_confirm'=>1,'confirm_info'=>'确定要返回？','a_class'=>'','span_class'=>''),
            ),
        );
        $level = D('NewsCategory')->where(array('status' => 1, 'pid' => 0))->select();        //查找顶级分类pid=0的
        foreach (  $level as &$a){
            $a['two']=D('NewsCategory')->where(array('status' => 1, 'pid' => $a['id']))->select();
        }
        $this->setTopTitle('资讯分类');
        $this->assign('top_menu_list', $this->_top_menu_list);
        $this->assign("level",   $level);         //顶级分类
        $this->display();
    }



    /**
     * 发布帖子页面内容渲染
     */
    public function addBlog($id=0){
        if($id>0){          //根据是否有ID传入判断是否是编辑请求
            $blog_detail= D('News')->where(array('id'=>$id))->find();
            if(empty($blog_detail['cover'])){
                $blog_detail['cover_url']='';
            }else{
                $blog_detail['cover_url']=getThumbImageById($blog_detail['cover'],72,72);
            }
            $blog_detail['detail']= D('NewsDetail')->where(array('news_id'=>$id))->find();
            $this->_top_menu_list =array(
                'left'=>array(
                    array('type'=>'back','need_confirm'=>1,'confirm_info'=>'确定要返回？','a_class'=>'','span_class'=>''),
                ),
            );
            $this->assign('top_menu_list', $this->_top_menu_list);
            $this->setTopTitle('编辑资讯');

        }else{
            $this->_top_menu_list =array(
                'left'=>array(
                    array('type'=>'back','need_confirm'=>1,'confirm_info'=>'确定要返回？','a_class'=>'','span_class'=>''),
                ),
            );
            $this->assign('top_menu_list', $this->_top_menu_list);
            $this->setTopTitle('发布资讯');
            $blog_detail['cover']=0;
        }


            $category=D('News/NewsCategory')->getCategoryList(array('status'=>1,'can_post'=>1),1);
            $this->assign('category',$category);
//dump($blog_detail);exit;
            $this->assign('blog',$blog_detail);
        $this->display();

    }



    public function doSendBlog(){
        $aId=I('post.id',0,'intval');
        $data['category']=I('post.category',0,'intval');
        if($aId){
            $data['id']=$aId;
            $now_data=D('News/News')->getData($aId);
            if($now_data['status']==1){
                $this->error('该资讯已被审核，不能被编辑！');
            }
            $category=D('News/newsCategory')->where(array('status'=>1,'id'=>$data['category']))->find();

            if($category){
                if($category['can_post']){
                    if($category['need_audit']){
                        $data['status']=2;
                    }else{
                        $data['status']=1;
                    }
                }else{
                    $this->error('该分类不能投稿！');
                }
            }else{
                $this->error('该分类不存在或被禁用！');
            }
            $data['status']=2;
            $data['template']=$now_data['detail']['template']?:'';
        }else{
            $this->checkActionLimit('add_news','news',0,is_login(),true);
            $data['uid']=get_uid();
            $data['sort']=$data['position']=$data['view']=$data['comment']=$data['collection']=0;
            $category=D('News/NewsCategory')->where(array('status'=>1,'id'=>$data['category']))->find();

            if($category){
                if($category['can_post']){
                    if($category['need_audit']){
                        $data['status']=2;
                    }else{
                        $data['status']=1;
                    }
                }else{
                    $this->error('该分类不能投稿！');
                }
            }else{
                $this->error('该分类不存在或被禁用！');
            }
            $data['template']='';
        }
        $data['title']=I('post.title','','text');
        $data['cover']=I('post.one_attach_id',0,'intval');
        $data['description']=I('post.description','','text');
        $data['dead_line']=I('post.dead_line','','text');
        $data['content_img']=I('post.attach_ids','','text');
        if($data['dead_line']==''){
            $data['dead_line']=99999999999;
        }else{
            $data['dead_line']=strtotime($data['dead_line']);
        }
        $data['source']=I('post.source','','text');
        $data['content']=I('post.content','','html');



        if(!mb_strlen($data['title'],'utf-8')){
            $this->error('标题不能为空！');
        }
        if(mb_strlen($data['content'],'utf-8')<20){
            $this->error('内容不能少于20个字！');
        }
if( $data['content_img']){
    $img_ids = explode(',',  $data['content_img']);              //把图片和内容结合
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
    $data['content']=$img_ids.$data['content'];
    $contentHandler=new ContentHandlerModel();
    $data['content']=$contentHandler->filterHtmlContent($data['content']);    //把图片和内容结合END
}

       //  dump($data['content']);exit;


        $res=D('News/news')->editData($data);
          // dump(D('News/newsModel')->getLastSql());exit;

        $title=$aId?"编辑":"新增";
        if($res){
            if(!$aId){
                $aId=$res;
                if($category['need_audit']){
                    $return['status'] = 1;
                    $return['info'] =  $title.'资讯成功！请等待审核~';
                }
            }
            $return['status'] = 1;
            $return['info'] =  $title.'资讯成功！';

        }else{
            $return['status'] = 0;
            $return['info'] = $title.'资讯失败！';
        }
        $this->ajaxReturn($return);
    }
} 