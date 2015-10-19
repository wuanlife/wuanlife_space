<?php

namespace Weibo\Controller;

use Think\Controller;

class TopicController extends BaseController
{

    public function index()
    {
        check_auth();
        $aTopic = urldecode(I('topk', '', 'op_t'));
        $aPage = I('page', 1, 'intval');

        $topicModel = D("Topic");
        $weiboModel = D('Weibo');

        $topic = $topicModel->where(array('name' => $aTopic))->find();
        if (!$topic) {
            $this->error('没有这个话题，赶紧去创建吧！', U('Weibo/Index/index'));
        }
        $topicModel->where('name = "' . $aTopic . '"')->setInc('read_count', 1); //浏览正确的话题就应该给该话题+1浏览量

        $param['where']['status'] = 1;
        $param['where']['content'] = array('like', "%#{$aTopic}#%");

        $param['page'] = $aPage;
        $param['count'] = 30;

        $list = $weiboModel->getWeiboList($param);

        if ($topic['uadmin'] != 0) {
            $host = $this->getUserStructure($topic['uadmin']); //话题主持人
            $host['status'] = 1;
        } else {
            $host = $this->getUserStructure(is_login());
            $host['status'] = 0;
        }

        $this->assign('topic', $topic);
        $this->assign('page', $aPage);
        $this->assign('list', $list);
        $this->assign('total_count', $weiboModel->getWeiboCount($param['where']));
        $this->assign('host', $host);
        $this->assignSelf();
        $this->setTitle('{$topic.name|op_t} —— 话题');
        $this->display();

    }

    public function topic()
    {
        $aType = I('type', 1, 'intval');

        $aPage = I('page', 1, 'intval');
        if ($aType == 1) {
            $h = 24;
        } else {
            $h = 24 * 7;
            $aType = 2;
        }
        $this->assign('type', $aType);
        $topics = D('Topic')->getHot($h, 10, $aPage);
        $this->assign('tab', 'topic');
        $this->assign('topics', $topics);

        $this->display();
    }

    private function assignSelf()
    {
        $self = query_user(array('title', 'avatar128', 'nickname', 'uid', 'space_url', 'score', 'title', 'fans', 'following', 'weibocount', 'rank_link'));
        $this->assign('self', $self);
    }


    protected function getUserStructure($uid)
    {
        //请不要在这里增加用户敏感信息，可能会暴露用户隐私
        $fields = array('uid', 'nickname', 'avatar32', 'avatar64', 'avatar128', 'avatar256', 'avatar512', 'space_url', 'rank_link', 'signature', 'score', 'tox_money', 'title', 'weibocount', 'fans', 'following');
        return query_user($fields, $uid);
    }

    public function beAdmin()
    {
        if (!is_login()) {
            $this->error('必须先登录才能申请成为主持人。');
        }


        $this->checkAuth(null, -1, '没有权限成为主持人');


        $tid = I('tid', 0, 'intval');
        $topicModel = D('Topic');
        $topic = $topicModel->find($tid);
        if ($topic) {
            if ($topic['uadmin']) {
                //已经存在管理员
                $this->error('已经有人捷足先登了呢。申请没有成功。');
            } else {
                if (is_administrator() || check_auth('Weibo/Topic/beAdmin')) {
                    $topic['uadmin'] = is_login();
                    $result = $topicModel->save($topic);
                    if ($result) {
                        $this->success('恭喜，您已抢先成为本话题的主持人。', 'refresh');
                    } else {
                        $this->error('抱歉，操作失败。可能是数据库原因导致。请联系管理员。');
                    }
                } else {
                    $this->error('抱歉，您无权申请成为话题主持人。');
                }
            }
        } else {
            $this->error('抱歉，此话题不存在。');
        }

    }

    public function editTopic()
    {
        $aId = I('id', -1, 'intval');
        $aLogo = I('logo', 0, 'intval');
        $aQrcode = I('qrcode', 0, 'intval');
        $aIntro = I('intro', '', 'op_t');
        $aIsTop = I('is_top', 0, 'intval');
        $aUadmin = I('uadmin', 0, 'intval');
        $topicModel = D('Topic');


        $topic = $topicModel->find($aId);
        if (!$topic) {
            $this->error('话题不存在。');
        } else {
            $this->checkAuth(null, $topic['uadmin'], '编辑话题');
            $topic['logo'] = $aLogo;
            $topic['qrcode'] = $aQrcode;
            if ($topic['intro'] != $aIntro && $topic['is_top'] == 1) {
                S('topic_rank', null);
            }
            $topic['intro'] = $aIntro;

            if (check_auth()) {
                if ($topic['is_top'] != $aIsTop) {
                    S('topic_rank', null);
                }
                $topic['uadmin'] = $aUadmin;
                $topic['is_top'] = $aIsTop;

            }
            $result = $topicModel->save($topic);
            if ($result === false) {
                $this->error('设置失败。');
            } else {
                $this->success('设置成功。', 'refresh');
            }
        }
    }
}