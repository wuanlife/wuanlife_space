<?php
namespace Group\Controller;
use Think\Controller;

class ManageController extends BaseController
{

    private $groupId = '';

    public function _initialize()
    {

        $aGroupId = I('group_id', 0, 'intval');
        $this->groupId = $aGroupId;
        parent::_initialize();
        //判断是否有权限编辑
        if(!is_login()){
            $this->error('请先登陆。');
        }
        $this->checkAuth('Group/Manager/*',get_group_admin($this->groupId),'您没有管理群组的权限。');
        $this->assignNotice($this->groupId);
        $this->assign('group_id', $this->groupId);
        unset($e);
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), is_login());
        $this->assign('myInfo', $myInfo);
        //赋予贴吧列表

        $this->assign('current', 'mygroup');

    }


    public function index()
    {
        $this->assignGroup($this->groupId);
        $this->assignGroupAllType();
        $this->setTitle('群组管理--编辑群组');
        $this->display();
    }

    public function member()
    {
        $aPage = I('get.page', 1, 'intval');
        $aStatus = I('get.status', 1, 'intval');
        $this->assignGroup($this->groupId);
        $member = D('GroupMember')->where(array('group_id' => $this->groupId, 'status' => $aStatus))->page($aPage, 10)->select();
        $totalCount = D('GroupMember')->where(array('group_id' => $this->groupId, 'status' => $aStatus))->count();
        foreach ($member as &$v) {
            $v['user'] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), $v['uid']);
        }
        $this->assign('member', $member);
        $this->assign('status', $aStatus);
        $this->assign('totalCount', $totalCount);
        $this->assign('sh_count', D('GroupMember')->where(array('group_id' => $this->groupId, 'status' => 1))->count());
        $this->assign('wsh_count', D('GroupMember')->where(array('group_id' => $this->groupId, 'status' => 0))->count());
        $this->setTitle('群组管理--群组成员');
        $this->display();
    }


    public function notice()
    {


        $this->assignGroup($this->groupId);
        if (IS_POST) {
            $aNotice = I('post.notice', '', 'text');
            $data['group_id'] = $this->groupId;
            $data['content'] = $aNotice;
            $res = D('GroupNotice')->addNotice($data);
            if ($res) {
                $this->success('添加成功', 'refresh');
            } else {
                $this->error('添加失败');
            }
        } else {

            $this->assign('group_id', $this->groupId);
            $this->setTitle('群组管理--公告');
            $this->display();
        }
    }

    public function category()
    {
        $this->assignGroup($this->groupId);
        $this->assignGroupTypes();
        $this->setTitle('群组管理--帖子分类管理');
        $cate = D('GroupPostCategory')->where(array('group_id' => $this->groupId, 'status' => 1))->select();
        $this->assign('cate', $cate);
        $this->display();
    }


    public function dismiss()
    {
        $this->checkAuth('Group/Manager/dismiss',get_group_creator($this->groupId),'您没有解散群组的权限。');
        $res = D('Group')->delGroup($this->groupId);
        if ($res) {
            $this->success('解散成功', U('group/index/index'));
        } else {
            $this->error('解散失败');
        }
    }


    public function receiveMember()
    {

        $aUid = I('post.uid', 0, 'intval');
        $res = D('GroupMember')->setStatus($aUid, $this->groupId, 1);
        $dynamic['group_id'] = $this->groupId;
        $dynamic['uid'] = $aUid;
        $dynamic['type'] = 'attend';
        $dynamic['create_time'] = time();
        D('GroupDynamic')->add($dynamic);
        if ($res) {
            $group = D('Group')->getGroup($this->groupId);
            D('Message')->sendMessage($aUid,'群组审核通过', get_nickname(is_login()) . "通过了您加入群组【{$group['title']}】的请求",  'group/index/group', array('id' => $this->groupId), is_login());
            S('group_member_count_' . $group['id'],null);
            S('group_is_join_' . $group['id'] . '_' . $aUid, null);
            $this->success('审核成功', 'refresh');
        } else {
            $this->error('审核失败');
        }
    }


    public function removeGroupMember()
    {
        $aUid = I('post.uid', 0, 'intval');
        $res = D('GroupMember')->where(array('uid' => $aUid, 'group_id' => $this->groupId))->delete();
        $dynamic['group_id'] = $this->groupId;
        $dynamic['uid'] = $aUid;
        $dynamic['type'] = 'remove';
        $dynamic['create_time'] = time();
        D('GroupDynamic')->add($dynamic);
        if ($res) {
            $group = D('Group')->getGroup($this->groupId);
            D('Message')->sendMessage($aUid, '移出群组', get_nickname(is_login()) . "将您移出了群组【{$group['title']}】", 'group/index/group', array('id' => $this->groupId), is_login());
            S('group_member_count_' . $group['id'],null);
            S('group_is_join_' . $group['id'] . '_' . $aUid, null);
            $this->success('删除成功', 'refresh');
        } else {
            $this->error('删除失败');
        }
    }


    public function editCate()
    {
        $aCateId = I('post.cate_id', 0, 'intval');
        $aTitle = I('post.title', '', 'text');
        if (empty($aTitle)) {
            $this->error('分类名不能为空');
        }
        if ($aCateId == 0) {
            $res = D('GroupPostCategory')->add(array('group_id' => $this->groupId, 'title' => $aTitle, 'create_time' => time(), 'status' => 1, 'sort' => 0));
            if (!$res) {
                $this->error('添加分类失败');
            }
            $this->success('添加分类成功', 'refresh');
        } else {
            $res = D('GroupPostCategory')->where(array('id' => $aCateId))->save(array('title' => $aTitle));
            if (!$res) {
                $this->error('编辑分类失败');
            }
            $this->success('编辑分类成功', 'refresh');
        }
    }


    public function delCate()
    {
        $aCateId = I('post.cate_id', 0, 'intval');
        $res = D('GroupPostCategory')->where(array('id' => $aCateId))->setField('status', 0);
        if (!$res) {
            $this->error('删除分类失败');
        }
        $this->success('删除分类成功', 'refresh');
    }


}