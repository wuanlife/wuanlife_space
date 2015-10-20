<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-6-10
 * Time: 下午1:10
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Mob\Controller;


use Think\Controller;

class BaseController extends Controller
{
    /**seo参数  郑钟良  ThinkOX
     * @var array
     */
    public $_mob_seo = array();
    public $_top_menu_list = array();

    public function _initialize()
    {


        $this->setMobTitle(L(CONTROLLER_NAME));
/*
        if($this->is_weixin()){
            $moduleModel = D('Common/Module');
            if ($moduleModel->checkInstalled('Weixin')) {
                $config = D('Weixin/WeixinConfig')->getWeixinConfig();

                $redirect =urlencode(U('Weixin/Index/callback','',true,true));

                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$config['APP_ID']}&redirect_uri={$redirect}&response_type=code&scope=snsapi_base&state=123#wechat_redirect";

                dump($config);

                dump($url);
                redirect($url);
                exit;
            }


        }*/
        /*
        下面为顶部导航设置示例，值为空的部分是选填项（例：'a_class'=>''），需要时再填
        $this->_top_menu_list =array(
            'left'=>array(
                array('type'=>'home','href'=>U('Mob/Weibo/index'),'a_class'=>'','i_class'=>'','html'=>''),
                array('title'=>'back','need_confirm'=>1,'confirm_info'=>'确定要返回？','a_class'=>'','span_class'=>''),
                array('type'=>'message'),
            ),
            'center'=>array('title'=>'微博','h1_class'=>'','i_class'=>'')
        );
        if(is_login()){
            if(check_auth('Weibo/Index/doSend')){
                $this->_top_menu_list['right'][]=array('type'=>'edit','href'=>U('Mob/Weibo/addWeibo'),'a_class'=>'','i_class'=>'');
            }else{
                $this->_top_menu_list['right'][]=array('type'=>'edit','info'=>'你没有权限发布微博！','a_class'=>'','i_class'=>'');
            }
        }else{
            $this->_top_menu_list['right'][]=array('type'=>'edit','info'=>'登录后才能操作！','a_class'=>'','i_class'=>'');
        }
        $this->assign('top_menu_list', $this->_top_menu_list);

        */
    }


    public function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }


    public function setMobTitle($title)
    {
        $this->_mob_seo['title'] = $title;
        $this->assign('mob_seo', $this->_mob_seo);
    }

    public function setMobKeywords($keywords)
    {
        $this->_mob_seo['keywords'] = $keywords;
        $this->assign('mob_seo', $this->_mob_seo);
    }

    public function setMobDescription($description)
    {
        $this->_mob_seo['description'] = $description;
        $this->assign('mob_seo', $this->_mob_seo);
    }

    public function setTopTitle($title)
    {
        $this->_top_menu_list['center']['title'] = $title;
        $this->assign('top_menu_list', $this->_top_menu_list);
    }


    public function addLocalComment()
    {
        $aPath = I('post.path', '', 'urldecode');
        $aPath = explode('/', $aPath);
        $aApp = $aPath[0];
        $aMod = $aPath[1];
        $aRowId = $aPath[2];

        $aUrl = I('post.this_url', '', 'text');
        $aExtra = I('post.extra', '', 'text');
        parse_str($aExtra);
        $field = empty($field) ? 'id' : $field;

        if (!is_login()) {
            $this->error('请登录后评论。');
        }
        $aCountModel = I('get.count_model', '', 'text');
        $aCountField = I('get.count_field', '', 'text');

        $aContent = I('content', '', 'text');
        $aUid = I('get.uid', '', 'intval');
        if (empty($aContent)) {
            $this->error('评论内容不能为空');
        }
        $commentModel = D('Addons://LocalComment/LocalComment');;

        $data = array('app' => $aApp, 'mod' => $aMod, 'row_id' => $aRowId, 'content' => $aContent, 'uid' => is_login(), 'ip' => get_client_ip(1));
        $res = $commentModel->addComment($data);


        if ($res) {
            D($aCountModel)->where(array('id' => $aRowId))->setInc($aCountField);

            $comment = $commentModel->getComment($res);
            $this->assign('localcomment', $comment);
            $html = $this->fetch('localcomment');

            if ($aUid) {
                $user = query_user(array('nickname', 'uid'), is_login());
                $title = $user['nickname'] . '评论了您';
                $message = '评论内容：' . $aContent;
                D('Common/Message')->sendMessage($aUid, $title, $message, $aUrl, array($field => $aRowId));
            }

            //通知被@到的人
            $uids = get_at_uids($aContent);
            $uids = array_unique($uids);
            $uids = array_subtract($uids, array($aUid));
            foreach ($uids as $uid) {
                $user = query_user(array('nickname', 'uid'), is_login());
                $title = $user['nickname'] . '@了您';
                $message = '评论内容：' . $aContent;

                D('Common/Message')->sendMessage($uid, $title, $message, $aUrl, array($field => $aRowId));
            }
            $result['status'] = 1;
            $result['data'] = $html;
            $result['info'] = '评论成功';
            $this->ajaxReturn($result);

        } else {
            $result['status'] = 0;
            $result['data'] = '';
            $result['info'] = '评论失败';
            $this->ajaxReturn($result);
        }
    }


    public function getLocalCommentList($path, $page = 1)
    {
        $aPath = explode('/', $path);
        $aApp = $aPath[0];
        $aMod = $aPath[1];
        $aRowId = $aPath[2];
        $model = D('Addons://LocalComment/LocalComment');
        $map = array('app' => $aApp, 'mod' => $aMod, 'row_id' => $aRowId, 'status' => 1);
        $param['where'] = $map;
        $param['page'] = $page;
        $param['count'] = 10;

        $sort = modC($aMod . '_LOCAL_COMMENT_ORDER', 0, $aApp) == 0 ? 'desc' : 'asc';

        $param['order'] = 'create_time ' . $sort;

        $param['field'] = 'id';
        $list = $model->getList($param);
        $html = '';

        foreach ($list as &$v) {
            $comment = $model->getComment($v);
            $this->assign('localcomment', $comment);
            $html .= $this->fetch('Base/localcomment');
        }


        unset($v);
        if (IS_AJAX) {
            $this->ajaxReturn($html);
        } else {
            return $html;
        }

    }


    public function delLocalComment()
    {
        $aId = I('post.id');
        $aCountModel = I('post.count_model', '', 'text');
        $aCountField = I('post.count_field', '', 'text');

        $model = D('Addons://LocalComment/LocalComment');

        $comment = $model->getComment($aId);
        if (empty($comment) || $aId <= 0) {
            $this->error('删除评论失败。评论不存在。');
        }
        if (!is_login()) {
            $this->error('请登陆后再操作！');
        }
        if (!check_auth('deleteLocalComment', $comment['uid'])) {
            $this->error('删除评论失败！权限不足');
        }

        $res = $model->deleteComment($aId);
        if ($res) {
            $aCountModel && $aCountField && D($aCountModel)->where(array('id' => $comment['row_id']))->setDec($aCountField);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

} 