<?php


namespace Mob\Controller;

use Think\Controller;
use Common\Model\ContentHandlerModel;

class QuestionController extends BaseController
{

    public function _initialize()
    {
        $this->_top_menu_list = array(
            'left' => array(
                array('type' => 'home', 'href' => U('Mob/Question/index')),
                array('type' => 'message'),
            ),
            'center' => array('title' => '待回答')
        );
        if (is_login()) {
            $this->_top_menu_list['right'][] = array('type' => 'edit', 'href' => U('Mob/Question/addQuestion'));
        } else {
            $this->_top_menu_list['right'][] = array('type' => 'edit', 'info' => '登录后才能操作！');
        }
        //dump($this->_top_menu_list);exit;
        $this->setMobTitle('问答');
        $this->assign('top_menu_list', $this->_top_menu_list);
    }


    public function index($mark = 0,$typeId=0)      //,待回答mark=>0，热门问题=>1，我的问题=>2，全部问题=>3
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');

        switch ($mark) {
            case '0':
                $this->setTopTitle('待回答');
                $questionlist = D('Question')->where(array('status' => 1, 'best_answer' => 0))->order('create_time desc')->page($aPage, $aCount)->select();
                $totalCount = D('Question')->where(array('status' => 1, 'bast_answer' => ""))->count();

                break;
            case '1':
                $this->setTopTitle('热门问题');
                $questionlist = D('Question')->where(array('status' => 1))->order('is_recommend desc,create_time asc,answer_num desc')->page($aPage, $aCount)->select();
                $totalCount = D('Question')->where(array('status' => 1))->count();
                break;
            case '2':
                $this->setTopTitle('我的问答');
                $questionlist = D('Question')->where(array('status' => 1, 'uid' => is_login()))->order('create_time desc')->page($aPage, $aCount)->select();
                $totalCount = D('Question')->where(array('status' => 1, 'uid' => is_login()))->count();
                break;
            case '3':
                $this->setTopTitle('全部问答');
                $questionlist = D('Question')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                $totalCount = D('Question')->where(array('status' => 1))->count();
                break;
            case 'parentType':
                $this->assign('typeId', $typeId);
                $typeName['parent']=D('QuestionCategory')->where(array('id'=>$typeId))->find();

                $typeName['child']=D('QuestionCategory')->where(array('pid'=>$typeName['parent']['id']))->select();
                if(is_null( $typeName['child'])){
                    $this->setTopTitle($typeName['parent']['title']);
                    $questionlist = D('Question')->where(array('category'=>$typeId,'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                    $totalCount = D('Question')->where(array('category'=>$typeId,'status' => 1))->count();
                }else{
                    $typeId=array_column($typeName['child'],'id');
                    $typeId=array_merge($typeId,array($typeName['parent']['id']));

                    $this->setTopTitle($typeName['parent']['title']);
                    $map['category']=array('in',$typeId);
                    $questionlist = D('Question')->where($map,array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                    $totalCount = D('Question')->where($map,array('status' => 1))->count();
                }

                break;
            case 'childType':
                $this->assign('typeId', $typeId);
                $typeName=D('QuestionCategory')->where(array('id'=>$typeId))->find();
                $this->setTopTitle($typeName['title']);
                $questionlist = D('Question')->where(array('category'=>$typeId,'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                $totalCount = D('Question')->where(array('category'=>$typeId,'status' => 1))->count();
                break;
        }

        foreach ($questionlist as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $v['uid']);
            $v['category'] = D('QuestionCategory')->where(array('status' => 1, 'id' => $v['category']))->find();
            $v['description']=parse_expression($v['description']);
          //  $v['description']=D('ContentHandler')->limitPicture($v['description'],1);

        }
        unset($v);
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }

        $parent =  D('QuestionCategory')->where(array('status' => 1,'pid'=>0))->order('sort asc')->select();
        foreach($parent as &$v){
            $v['child'] =  D('QuestionCategory')->where(array('status' => 1,'pid'=>$v['id']))->order('sort asc')->select();
        }
        unset($v);

        $this->assign('parent', $parent);
        $this->assign('pid', $pid);
        $this->assign('mark', $mark);
        $this->assign('questionlist', $questionlist);
        $this->display();
    }

    public function addMoreQuestionList($mark,$typeId=0)
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');

        switch ($mark) {
            case '0':
                $questionlist = D('Question')->where(array('status' => 1, 'best_answer' => 0))->order('create_time desc')->page($aPage, $aCount)->select();
                break;
            case '1':
                $questionlist = D('Question')->where(array('status' => 1))->order('is_recommend desc,create_time asc,answer_num desc')->page($aPage, $aCount)->select();
                break;
            case '2':
                $questionlist = D('Question')->where(array('status' => 1, 'uid' => is_login()))->order('create_time desc')->page($aPage, $aCount)->select();
                break;
            case '3':
                $questionlist = D('Question')->where(array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                break;
            case 'parentType':
                $typeName['parent']=D('QuestionCategory')->where(array('id'=>$typeId))->find();
                $typeName['child']=D('QuestionCategory')->where(array('pid'=>$typeName['parent']['id']))->select();
                if(is_null( $typeName['child'])){
                    $this->setTopTitle($typeName['parent']['title']);
                    $questionlist = D('Question')->where(array('category'=>$typeId,'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                }else{
                    $typeId=array_column($typeName['child'],'id');
                    $typeId=array_merge($typeId,array($typeName['parent']['id']));

                    $this->setTopTitle($typeName['parent']['title']);
                    $map['category']=array('in',$typeId);
                    $questionlist = D('Question')->where($map,array('status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                }
                break;
            case 'childType':
                $typeName=D('QuestionCategory')->where(array('id'=>$typeId))->find();
                $this->setTopTitle($typeName['title']);
                $questionlist = D('Question')->where(array('category'=>$typeId,'status' => 1))->order('create_time desc')->page($aPage, $aCount)->select();
                break;
        }
        foreach ($questionlist as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $v['uid']);
            $v['category'] = D('QuestionCategory')->where(array('status' => 1, 'id' => $v['category']))->find();
            $v['description']=parse_expression($v['description']);
           // $v['description']=D('ContentHandler')->limitPicture($v['description'],1);
        }
        if ($questionlist) {
            $data['html'] = "";
            foreach ($questionlist as $val) {
                $this->assign("vo", $val);
                $data['html'] .= $this->fetch("_questionlist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }

    public function questionDetail($id = 0)
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $questionDetail = D('Question')->where(array('status' => 1, 'id' => $id))->order('create_time desc')->find();
        if ($questionDetail['best_answer'] == 0) {
            $this->setTopTitle('待问答');
        } else {
            $this->setTopTitle('已回答问题');
        }
        $questionDetail['category'] = D('QuestionCategory')->where(array('status' => 1, 'id' => $questionDetail['category']))->find();
        $questionDetail['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $questionDetail['uid']);
        $questionDetail['description']=parse_expression( $questionDetail['description']);
        if ($questionDetail['best_answer']) {
            $questionBestAnswer = D('QuestionAnswer')->where(array('id' => $questionDetail['best_answer'], 'status' => 1))->find();

        }
        $map['id'] = array('neq', $questionDetail['best_answer']);
        $questionComment = D('QuestionAnswer')->where(array('status' => 1, 'question_id' => $questionDetail['id'], $map))->order('support desc,create_time desc')->page($aPage, $aCount)->select();


        $totalCount = D('QuestionAnswer')->where(array('status' => 1, 'question_id' => $questionDetail['id']))->count();
        if ($totalCount <= $aPage * $aCount) {
            $pid['count'] = 0;
        } else {
            $pid['count'] = 1;
        }
        foreach ($questionComment as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $v['uid']);
            $v['content'] = parse_expression($v['content']);
            $v['support_user']=D('QuestionSupport')->where(array('row' => $v['id'],'type'=>1))->select();
            foreach($v['support_user'] as &$a){
                $a['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $a['uid']);
            }
            $v['has_support']=D('QuestionSupport')->where(array('uid' => is_login(), 'row' => $v['id']))->find();        //判断是否支持过或者贬低过
        }

        $questionBestAnswer['user'] = query_user(array('nickname', 'avatar32', 'uid', 'space_mob_url'), $questionBestAnswer['uid']);

        $questionBestAnswer['support_user']=D('QuestionSupport')->where(array('row' => $questionBestAnswer['id'],'type'=>1))->select();//判断最佳回答有没有点赞
        foreach($questionBestAnswer['support_user'] as &$b){
            $b['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $b['uid']);      //最佳回答点过赞的人
        }
        $questionBestAnswer['has_support']=D('QuestionSupport')->where(array('uid' => is_login(), 'row' => $questionBestAnswer['id']))->find();
        $questionBestAnswer['content'] = parse_expression($questionBestAnswer['content']);
        $questionBestAnswer['update_time']=time_format($questionBestAnswer['update_time']);

//dump($questionDetail);exit;
        $this->assign('pid', $pid);
        $this->assign('bestAnswer', $questionBestAnswer);
        $this->assign('question', $questionDetail);
        $this->assign('questionComment', $questionComment);

        $this->display();
    }

    public function addMoreQuestionCommentList($id)
    {
        $aPage = I('post.page', 1, 'op_t');
        $aCount = I('post.count', 10, 'op_t');
        $questionDetail = D('Question')->where(array('status' => 1, 'id' => $id))->order('create_time desc')->find();
        $map['id'] = array('neq', $questionDetail['best_answer']);
        $questioncomment = D('QuestionAnswer')->where(array('status' => 1, 'question_id' => $id,$map))->order('support desc,create_time desc')->page($aPage, $aCount)->select();

        foreach ($questioncomment as &$v) {
            $v['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $v['uid']);
            $v['content']=parse_expression($v['content']);
            $v['support_user']=D('QuestionSupport')->where(array('row' => $v['id'],'type'=>1))->select();
            foreach($v['support_user'] as &$a){
                $a['user'] = query_user(array('nickname', 'avatar32', 'uid','space_mob_url'), $a['uid']);
            }
            $v['has_support']=D('QuestionSupport')->where(array('uid' => is_login(), 'row' => $v['id']))->find();        //判断是否支持过或者贬低过
        }

        if ($questioncomment) {
            $data['html'] = "";
            foreach ($questioncomment as $val) {
                $this->assign("vl", $val);
                $data['html'] .= $this->fetch("_commentlist");
                $data['status'] = 1;
            }
        } else {
            $data['stutus'] = 0;
        }
        $this->ajaxReturn($data);
    }

    public function reply($id=0,$answerId=0,$isEdit=0)
    {
        if($isEdit==0){
            $questiondetail = D('Question')->where(array('status' => 1, 'id' => $id))->order('create_time desc')->find();
            $this->setTopTitle('回答：' . $questiondetail['title']);
            $this->assign('question_id', $id);
            $this->display();
        }else{
            $answerContent= D('QuestionAnswer')->where(array('id' => $answerId, 'status' => 1))->find();
            $this->setTopTitle('编辑回答');
            $this->assign('question_id', $id);
            $this->assign('answer_id', $answerId);
            $this->assign('answerContent', $answerContent);
            $this->display();
        }

    }

    public function addAnswer()
    {

        $aQuestion = $data['question_id'] = I('post.question_id', 0, 'intval');
        $aContent = I('post.content', '', 'filter_content');
        $aImgId = I('post.attach_ids', 0, 'op_t');
        $aAnswerId = I('post.answer_id', 0, 'intval');
        if ($aImgId) {
            $img_ids = explode(',', $aImgId);              //把图片和内容结合
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
            $aContent = $img_ids . $aContent;
            $contentHandler = new ContentHandlerModel();
            $aContent = $data['content'] = $contentHandler->filterHtmlContent($aContent);    //把图片和内容结合END
        } else {
            $aContent = $data['content'] = $aContent;
        }
        if(is_login()){
            if(strlen($aContent)<=intval(modC('QUESTION_ANSWER_MIN_NUM','10','QUESTION'))){
                $result['info'] = '问题回答最少字数限制在'.modC('QUESTION_ANSWER_MIN_NUM','10','QUESTION').'个字符';
                $this->ajaxReturn($result);
            }
        }else{
            $this->error('请先登录');
        }




        if ($aAnswerId) {

            $now_answer = D('Mob/QuestionAnswer')->getData(array('id' => $aAnswerId, 'status' => 1));
            $this->checkAuth('Question/Answer/edit', $now_answer['uid'], '没有编辑该答案的权限');
            $this->checkActionLimit('edit_answer', 'question_answer', $now_answer['id'], get_uid());
            $data['id'] = $aAnswerId;
            $title = "编辑";
        } else {
            $this->checkAuth('Question/Answer/add', -1, '没有回答的权限');
            $this->checkActionLimit('add_answer', 'question_answer', 0, get_uid());
            $title = "发布";
        }
        $result['status'] = 0;
        if (!$aQuestion) {
            $result['info'] = '参数错误！问题不存在。';
            $this->ajaxReturn($result);
        }
        if (mb_strlen($aContent, 'utf-8') < modC('QUESTION_ANSWER_MIN_NUM', 20, 'Question')) {
            $result['info'] = '回答内容不能少于' . modC('QUESTION_ANSWER_MIN_NUM', 20, 'Question') . '个字！';
            $this->ajaxReturn($result);
        }
        $res = D('Mob/QuestionAnswer')->editData($data);
        if ($res) {
            //发送消息
        //    $messageModel = D('Common/Message');
            $user_info = query_user(array('nickname', 'uid'));
            /**
             * @param $to_uid 接受消息的用户ID
             * @param string $content 内容
             * @param string $title 标题，默认为  您有新的消息
             * @param $url 链接地址，不提供则默认进入消息中心
             * @param $int $from_uid 发起消息的用户，根据用户自动确定左侧图标，如果为用户，则左侧显示头像
             * @param int $type 消息类型，0系统，1用户，2应用
             */
            $question = D('Mob/Question')->find($aQuestion);
           // $messageModel->sendMessage($question['uid'], $user_info['nickname'] . '回答了你的问题【' . $question['title'] . '】或编辑了 Ta 的答案，快去看看吧！', '问题被回答', U('Question/Index/detail', array('id' => $aQuestion)), is_login(), 1);
            D('Common/Message')->sendMessage($question['uid'],'问题被回答', $user_info['nickname'] . '回答了你的问题【' . $question['title'] . '】或编辑了 Ta 的答案，快去看看吧！',  'Question/index/detail',array('id' =>$aQuestion), is_login(), 1);
            //发送消息 end
            $result['status'] = 1;
            if ($aAnswerId) {
                $result['url'] = U('Question/Index/detail', array('id' => $aQuestion));
            }
            $result['info'] = $title . '回答成功！' . cookie('score_tip');
        } else {
            $result['info'] = $title . '回答失败！';
        }
        $this->ajaxReturn($result);
    }

    public function setBestAnswer()
    {
        $aAnswerId = I('post.answer_id', 0, 'intval');
        $aQuestion = I('post.question_id', 0, 'intval');
        $question = D('Mob/Question')->getData($aQuestion);
        $this->checkAuth('Question/Answer/setBest', $question['uid'], '没有设置权限！');
        $res['status'] = 0;
        if ($question && $aAnswerId) {
            if ($question['best_answer']) {
                $this->checkAuth('Question/Answer/setBest', -1, '已有最佳答案！不能重复设置');
            }
            $result = D('Mob/Question')->editData(array('id' => $aQuestion, 'best_answer' => $aAnswerId));
            if ($result) {
                $res['status'] = 1;
                $tip = '在问题【' . $question['title'] . '】中你的回答被设为最佳答案。';
                $answer = D('Mob/QuestionAnswer')->getData(array('id' => $aAnswerId));
                D('Common/Message')->sendMessage($answer['uid'], '答案被设为最佳答案', $tip, 'Question/index/detail', array('id' => $aQuestion), is_login(), 1);
            } else {
                $res['info'] = '操作失败！';
            }
        } else {
            $res['info'] = "非法操作！";
        }
        $this->ajaxReturn($res);
    }

    public function addQuestion()
    {
        if(!is_login()){
            $this->error('请先登录！');
        }
        if(IS_POST){
            $this->_doEditQusetion();
        }else{
            $aId=I('id',0,'intval');
            if($aId){
                $data=D('Mob/Question')->getData($aId);
                $this->checkAuth('Question/Index/edit',$data['uid'],'没有编辑该问题权限！');
                $need_audit=modC('QUESTION_NEED_AUDIT',1,'Question');
                if($need_audit){
                    $data['status']=2;
                }
                $questionDetail = D('Question')->where(array('status' => 1, 'id' => $aId))->order('create_time desc')->find();
                $questionDetail['category'] = D('QuestionCategory')->where(array('status' => 1, 'id' => $questionDetail['category']))->find();
                $questionDetail['user'] = query_user(array('nickname', 'avatar32', 'uid'), $questionDetail['uid']);
             // dump($questionDetail);exit;
                $this->assign('questionDetail',$questionDetail);
            }else{
                $data['title']=I('title','','text');
                $this->checkAuth('Question/Index/add',-1,'没有发布问题的权限！');
            }
            $this->assign('data',$data);
            $this->setTopTitle('提问');
            $category=D('Mob/QuestionCategory')->getCategoryList(array('status'=>1),1);
            $this->assign('category',$category);
            $this->assign('current','create');
            $this->display();
        }
    }

    public function support()
    {
        if(!is_login()){
            $this->error('请先登录！');
        }
        $aId=I('post.answer_id',0,'intval');
        $aType=I('post.type',1,'intval');
        $res['status']=0;
        if(!$aId){
            $res['info']="操作失败！";
            $this->ajaxReturn($res);
        }
        $this->checkActionLimit('support_answer','question_answer_support',$aId,get_uid());
        $answer=D('Mob/QuestionAnswer')->getData(array('id'=>$aId,'status'=>1));
        if($answer['uid']==get_uid()){
            $res['info']="不能支持、反对自己的回答！";
            $this->ajaxReturn($res);
        }
        if(!(D('Mob/questionSupport')->where(array('uid'=>get_uid(),'tablename'=>'QuestionAnswer','row'=>$aId))->count())){
            $resultAdd=D('Mob/questionSupport')->addData('QuestionAnswer',$aId,$aType);
        }else{
            $res['info']="你已经支持或反对过该回答，不能重复操作！";
            $this->ajaxReturn($res);
        }
        if($resultAdd){
            $result=D('Mob/questionAnswer')->changeNum($aId,$aType);
        }
        if($result){
            //发送消息
            $question=D('Mob/question')->find($answer['question_id']);
            if($aType){
                $user_info=query_user(array('nickname','uid'));
                $tip = '用户'.$user_info['nickname'].'支持了你关于问题'.$question['title'].'的回答。';
                $title='答案被支持';
            }else{
                $tip = '你的关于问题'.$question['title'].'的回答被某些不同意见的人反对了。';
                $title='答案被反对';
            }
            /**
             * @param $to_uid 接受消息的用户ID
             * @param string $content 内容
             * @param string $title 标题，默认为  您有新的消息
             * @param $url 链接地址，不提供则默认进入消息中心
             * @param $int $from_uid 发起消息的用户，根据用户自动确定左侧图标，如果为用户，则左侧显示头像
             * @param int $type 消息类型，0系统，1用户，2应用
             */
            //D('Common/Message')->sendMessage($answer['uid'],$title, $tip , 'Question/index/Questiondetail',array('id'=>$answer['question_id']), 0, 1);
            D('Common/Message')->sendMessage($answer['uid'],$title, $tip,  'Question/index/detail',array('id' => $answer['question_id']), is_login(), 1);
            //发送消息 end
            action_log('support_answer','question_answer_support',$aId,get_uid());
            $res['info']='操作成功！'.cookie('score_tip');
            $res['status']=1;
        }else{
            $res['info']="操作失败！";
        }
        $this->ajaxReturn($res);
    }



    private function _doEditQusetion()
    {
        $aId=I('post.id',0,'intval');
        $need_audit=modC('QUESTION_NEED_AUDIT',1,'Question');
        if($aId){
            $data['id']=$aId;
            $now_data=D('Mob/Question')->getData($aId);
            $this->checkAuth('Question/Index/edit',$now_data['uid'],'没有编辑该问题权限！');
            if($need_audit){
                $data['status']=2;
            }
            $this->checkActionLimit('edit_question','question',$now_data['id'],get_uid());
        }else{
            $this->checkAuth('Question/Index/add',-1,'没有发布问题的权限！');
            $this->checkActionLimit('add_question','question',0,get_uid());
            $data['uid']=get_uid();
            $data['answer_num']=$data['good_question']=0;
            if($need_audit){
                $data['status']=2;
            }else{
                $data['status']=1;
            }
        }
        $data['title']=I('post.title','','text');
        $data['category']=I('post.category',0,'intval');
        $attach_ids=I('post.attach_ids','0','op_t');
        if($attach_ids){
            $data['description']=I('post.description','','filter_content');
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
            $data['description']=$img_ids. $data['description'];
            $contentHandler=new ContentHandlerModel();
            $data['description']=$contentHandler->filterHtmlContent( $data['description']);    //把图片和内容结合END
        }else{
            $data['description']=I('post.description','','filter_content');
        }



        if(!mb_strlen($data['title'],'utf-8')){
            $this->error('标题不能为空！');
        }

        $res=D('Mob/Question')->editData($data);
        $title=$aId?"编辑":"提";

        if($res){
            if(!$aId){
                $aId=$res;
                if($need_audit){
                    $data['status']=1;
                    $data['info']=$title.'问题成功！'.' 请等待审核~';
                    $this->ajaxReturn($data);
                }
            }
            if(D('Common/Module')->isInstalled('Weibo')){//安装了微博模块
                //同步到微博
                $postUrl = "http://$_SERVER[HTTP_HOST]" . U('Mob/Question/questionDetail',array('id'=>$aId));
                $weiboModel=D('Mob/Weibo');
                $weiboModel->addWeibo("我问了一个问题【" . $data['title'] . "】：" . $postUrl);
            }
            $data['status']=1;
            $data['info']=$title.'问题成功！'.' 请等待审核~';
        }else{
            $data['status']=0;
            $data['info']=$title.'问题失败！';
        }
        $this->ajaxReturn($data);
    }
}