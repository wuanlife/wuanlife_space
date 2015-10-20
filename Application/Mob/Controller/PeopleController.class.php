<?php


namespace Mob\Controller;

class  PeopleController extends BaseController
{
    public function index()
    {
        $map = $this->setMap();
        $map['status'] = 1;
        $map['last_login_time'] = array('neq', 0);
        $peoples = S('People_peoples_'.I('page',0,'intval').'_' . serialize($map));
        if (empty($peoples)) {
            $peoples = D('Member')->where($map)->field('uid', 'reg_time', 'last_login_time')->order('last_login_time desc')->findPage(20,false,array(),4);
            foreach ($peoples['data'] as &$v) {
                $v = query_user(array( 'avatar32', 'nickname', 'uid','signature', 'rank_link','fans','is_following','space_mob_url'), $v['uid']);
            }
            unset($v);
            S('People_peoples_' . serialize($map), $peoples,300);
        }
        $this->setMobTitle('会员');
        $this->assign('page_data',$peoples);
        $this->display();
    }

    private function setMap()
    {
        $map = array();
        /*$aTag = I('tag', 0, 'intval');
        if ($aTag) {
            !isset($_GET['tag']) && $_GET['tag'] = $_POST['tag'];
            $map_uids['tags'] = array('like', '%[' . $aTag . ']%');
            $links = D('Ucenter/UserTagLink')->getListByMap($map_uids);
            $uids = array_column($links, 'uid');
            $map['uid'] = array('in', $uids);
            $this->assign('tag_id', $aTag);
        }
        $userTagModel = D('Ucenter/UserTag');
        $tag_list = $userTagModel->getTreeList();
        $this->assign('tag_list', $tag_list);*/

        $nickname = I('keywords', '', 'op_t');
        if ($nickname != '') {
            !isset($_GET['keywords']) && $_GET['keywords'] = $_POST['keywords'];
            $map['nickname'] = array('like', '%' . $nickname . '%');
            $this->assign('nickname', $nickname);
        }
        return $map;
    }
}