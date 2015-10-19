<?php


namespace People\Controller;

use Think\Controller;


class IndexController extends Controller
{

    public function index()
    {
        $map = $this->setMap();
        $map['status'] = 1;
        $map['last_login_time'] = array('neq', 0);
        $peoples = S('People_peoples_'.I('page',0,'intval').'_' . serialize($map));
        if (empty($peoples)) {
            $peoples = D('Member')->where($map)->field('uid', 'reg_time', 'last_login_time')->order('last_login_time desc')->findPage(20);

            $userConfigModel = D('Ucenter/UserConfig');
            $titleModel = D('Ucenter/Title');
            foreach ($peoples['data'] as &$v) {
                $v = query_user(array('title', 'avatar128', 'nickname', 'uid', 'space_url', 'score', 'title', 'fans', 'following', 'rank_link'), $v['uid']);
                $v['level'] = $titleModel->getCurrentTitleInfo($v['uid']);
                //获取用户封面id
                $where = getUserConfigMap('user_cover', '', $v['uid']);
                $where['role_id'] = 0;
                $model = $userConfigModel;
                $cover = $model->findData($where);
                $v['cover_id'] = $cover['value'];
                $v['cover_path'] = getThumbImageById($cover['value'], 273, 80);
            }
            unset($v);
            S('People_peoples_' . serialize($map), $peoples,300);
        }


        $this->assign('tab','index');
        $this->assign('lists', $peoples);
        $this->display();
    }

    private function setMap()
    {
        $aTag = I('tag', 0, 'intval');
        $aRole=I('role',0,'intval');
        $role_list=modC('SHOW_ROLE_TAB','','People');
        if($role_list!=''){
            $role_list=json_decode($role_list,true);
            $role_list=$role_list[1]['items'];
            if(count($role_list)){
                foreach($role_list as &$val){
                    $val['id']=$val['data-id'];
                }
                unset($val);
                $this->assign('role_list', $role_list);
            }else{
                $aRole=0;
            }
        }else{
            $aRole=0;
        }
        $map = array();
        if ($aTag&&$aRole) {//同时选择标签和身份
            !isset($_GET['tag']) && $_GET['tag'] = $_POST['tag'];
            $map_uids['tags'] = array('like', '%[' . $aTag . ']%');
            $tag_links = D('Ucenter/UserTagLink')->getListByMap($map_uids);
            $tag_uids = array_column($tag_links, 'uid');
            $this->assign('tag_id', $aTag);

            !isset($_GET['role']) && $_GET['role'] = $_POST['role'];
            $map_role['role_id'] = $aRole;
            $map_role['status'] = 1;
            $role_links = M('UserRole')->where($map_role)->limit(999)->field('uid')->select();
            $role_uids = array_column($role_links, 'uid');
            $this->assign('role_id', $aRole);
            if($tag_uids&&$role_uids){
                $uids=array_intersect($tag_uids,$role_uids);
            }else{
                $uids=array();
            }
            $map['uid'] = array('in', $uids);
        }else if($aTag){//选择标签，没选择身份
            !isset($_GET['tag']) && $_GET['tag'] = $_POST['tag'];
            $map_uids['tags'] = array('like', '%[' . $aTag . ']%');
            $tag_links = D('Ucenter/UserTagLink')->getListByMap($map_uids);
            $tag_uids = array_column($tag_links, 'uid');
            $map['uid'] = array('in', $tag_uids);
            $this->assign('tag_id', $aTag);
        }else if($aRole){//选择身份，没选择标签
            !isset($_GET['role']) && $_GET['role'] = $_POST['role'];
            $map_role['role_id'] = $aRole;
            $map_role['status'] = 1;
            $role_links = M('UserRole')->where($map_role)->limit(999)->field('uid')->select();
            $role_uids = array_column($role_links, 'uid');
            $map['uid'] = array('in', $role_uids);
            $this->assign('role_id', $aRole);
        }
        $userTagModel = D('Ucenter/UserTag');
        if($aRole){
            $map_tags=getRoleConfigMap('user_tag',$aRole);
            $can_config=M('RoleConfig')->where($map_tags)->field('value')->find();
            if($can_config['value']!=''){
                $tag_list=$userTagModel->getTreeListByIds($can_config['value']);
            }
            else{
                $tag_list =null;
            }
        }else{
            $tag_list = $userTagModel->getTreeList();
        }
        $this->assign('tag_list', $tag_list);
        $nickname = I('keywords', '', 'op_t');
        if ($nickname != '') {
            !isset($_GET['keywords']) && $_GET['keywords'] = $_POST['keywords'];
            $map['nickname'] = array('like', '%' . $nickname . '%');
            $this->assign('nickname', $nickname);
        }
        return $map;
    }
}